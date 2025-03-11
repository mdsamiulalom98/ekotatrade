<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Util\BkashCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\OrderDetails;
use App\Models\PaymentGateway;
use App\Models\ProductVariable;
use App\Models\SmsGateway;
use App\Models\Customercheckout;
use App\Models\Contact;

class BkashController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;
    public function __construct()
    {
        $bkash_gateway = PaymentGateway::where(['status' => 1, 'type' => 'bkash'])->first();
        if ($bkash_gateway) {
            $this->base_url = $bkash_gateway->base_url;
            $this->app_key = $bkash_gateway->app_key; // bKash Merchant API APP KEY
            $this->app_secret = $bkash_gateway->app_secret; // bKash Merchant API APP SECRET
            $this->username = $bkash_gateway->username; // bKash Merchant API USERNAME
            $this->password = $bkash_gateway->password; // bKash Merchant API PASSWORD
        } else {
            $this->base_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
            $this->app_key = ''; // bKash Merchant API APP KEY
            $this->app_secret = ''; // bKash Merchant API APP SECRET
            $this->username = ''; // bKash Merchant API USERNAME
            $this->password = ''; // bKash Merchant API PASSWORD
        }
    }

    public function authHeaders()
    {
        return array(
            'Content-Type:application/json',
            'Authorization:' . $this->grant(),
            'X-APP-Key:' . $this->app_key
        );
    }
    private function contact()
    {
        return Contact::select('id', 'hotmail', 'phone', 'email')->first();
    }

    public function curlWithBody($url, $header, $method, $body_data_json)
    {
        $curl = curl_init($this->base_url . $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function grant()
    {
        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );
        $header_data_json = json_encode($header);

        $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret);
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/token/grant', $header, 'POST', $body_data_json);

        $token = json_decode($response)->id_token;

        return $token;
    }

    public function create(Request $request)
    {
        $order_info = Customercheckout::where('id', $request->order_id)->first();
        $amount = $order_info->amount;
        // $amount = 1;
        $orderId = $request->order_id;

        $header = $this->authHeaders();
        $body_data = array(
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => 'https://ekotatrade.com.bd/bkash/checkout-url/callback?orderId=' . $orderId,
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "Inv" . Str::random(10)
        );
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/create', $header, 'POST', $body_data_json);

        //dd($response);

        Session::forget('paymentID');
        Session::put('paymentID', json_decode($response)->paymentID);

        return redirect((json_decode($response)->bkashURL));
    }

    public function execute($paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID
        );
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/execute', $header, 'POST', $body_data_json);

        return $response;
    }

    public function query($paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID,
        );
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('/tokenized/checkout/payment/status', $header, 'POST', $body_data_json);
        return $response;
    }

    public function callback(Request $request)
    {
        $allRequest = $request->all();
        if (isset($allRequest['status']) && $allRequest['status'] == 'failure') {
            Toastr::error('Opps, Your bkash payment failed', 'Failed!');
            return redirect('customer/checkout');
        } elseif (isset($allRequest['status']) && $allRequest['status'] == 'cancel') {
            Toastr::error('Opps, Your bkash payment cancelld', 'Cancelled!');
            return redirect('customer/checkout');
        } else {
            $response = $this->execute($allRequest['paymentID']);
            $response2 = json_decode($response, true);
            // order data process starts here
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal = str_replace(',', '', $subtotal);
            $subtotal = str_replace('.00', '', $subtotal);
            $discount = Session::get('discount') + Session::get('coupon_amount');
            $shippingfee = Session::get('shipping') ?? 0;
            $shipping_area = Customercheckout::where('id', $allRequest['orderId'])->first();

            if (Auth::guard('customer')->user()) {
                $customer_id = Auth::guard('customer')->user()->id;
            } else {
                $exits_customer = Customer::where('phone', $shipping_area->phone)->select('phone', 'id')->first();
                if ($exits_customer) {
                    $customer_id = $exits_customer->id;
                } else {
                    $store = new Customer();
                    $store->name = $shipping_area->name;
                    $store->slug = $shipping_area->name;
                    $store->phone = $shipping_area->phone;
                    $store->password = bcrypt($shipping_area->phone);
                    $store->verify = 1;
                    $store->status = 'active';
                    $store->save();
                    $customer_id = $store->id;
                }
            }

            $order = Order::orderBy('id', 'desc')->first();
            $lastId = $order ? $order->invoice_id + 1 : 10001;

            // order data save
            $order = new Order();
            $order->invoice_id = str_pad($lastId, 5, '0', STR_PAD_LEFT);
            $order->amount = $shipping_area->amount;
            $order->discount = $discount ?? 0;
            $order->shipping_charge = $shippingfee;
            $order->customer_id = $customer_id;
            $order->customer_ip = $request->ip();
            $order->paid = $shipping_area->amount;
            $order->due = 0;
            $order->order_status = 1;
            $order->note = $request->note;
            $order->save();

            // shipping data save
            $shipping = new Shipping();
            $shipping->order_id = $order->id;
            $shipping->customer_id = $customer_id;
            $shipping->name = $shipping_area->name;
            $shipping->phone = $shipping_area->phone;
            $shipping->address = $shipping_area->address;
            $shipping->area = $shipping_area->area;
            $shipping->save();

            // payment data save
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->customer_id = $customer_id;
            $payment->payment_method = $shipping_area->payment_method;
            $payment->trx_id = $response2['trxID'];
            $payment->sender_number = $response2['customerMsisdn'];
            $payment->amount = $order->amount;
            $payment->payment_status = 'paid';
            $payment->save();

            // order details data save
            foreach (Cart::instance('shopping')->content() as $cart) {
                $order_details = new OrderDetails();
                $order_details->order_id = $order->id;
                $order_details->product_id = $cart->id;
                $order_details->product_name = $cart->name;
                $order_details->purchase_price = $cart->options->purchase_price;
                $order_details->product_color = $cart->options->product_color;
                $order_details->product_size = $cart->options->product_size;
                $order_details->product_type = $cart->options->product_type;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->save();
            }
            //  return $order_details;
            $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty', 'product_type', 'product_size', 'product_color', 'product_id')->where('order_id', $order->id)->get();
            foreach ($orders_details as $order_detail) {
                // return $order_detail;
                if ($order_detail->product_type == 1) {
                    $product = Product::find($order_detail->product_id);
                    if ($product->stock >= $order_detail->qty) {  // Check if stock is sufficient
                        $product->stock -= $order_detail->qty;
                        $product->save();
                    } else {
                        Toastr::error('This product stock is not sufficient', 'Failed!');
                        return redirect()->back();
                    }
                } else {
                    $product = ProductVariable::where('product_id', $order_detail->product_id);
                    if ($order_detail->product_color) {
                        $product->where('color', $order_detail->product_color);
                    }
                    if ($order_detail->product_size) {
                        $product->where('size', $order_detail->product_size);
                    }

                    $product = $product->first();
                    if ($product && $product->stock >= $order_detail->qty) {
                        $product->stock -= $order_detail->qty;
                        $product->save();
                    } else {
                        Toastr::error('This product stock is not sufficient', 'Failed!');
                        return redirect()->back();
                    }
                }
            }
            if ($request->email) {
                $data = [
                    'email' => $request->email,
                    'order_id' => $order->id,
                ];
                Mail::send('emails.order_place', $data, function ($textmsg) use ($data) {
                    $textmsg->to($data['email']);
                    $textmsg->subject('Your order successfully placed To Ekota Trade');
                });
            }
            $data2 = [
                'email' => $this->contact()->hotmail,
                'order_id' => $order->id,
            ];

            Mail::send('emails.order_place', $data2, function ($textmsg2) use ($data2) {
                $textmsg2->to($data2['email']);
                $textmsg2->subject('A order successfully placed To Ekota Trade');
            });

            Toastr::success('Thanks, Your order place successfully', 'Success!');
            $sms_gateway = SmsGateway::where(['status' => 1, 'order' => '1'])->first();
            if ($sms_gateway) {
                $curl = curl_init();
                curl_setopt_array(
                    $curl,
                    array(
                        CURLOPT_URL => "$sms_gateway->url",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array(
                            'api_key' => "$sms_gateway->api_key",
                            'msg' => "প্রিয় $shipping_area->name \r\n আপনার অর্ডার #$order->invoice_id টি সম্পন্ন হয়েছে! অর্ডারটি ট্র্যাক করতে ক্লিক করুন: https://www.ekotatrade.com.bd/customer/order-track/result?phone=$shipping_area->phone&invoice_id=$order->invoice_id \r\n একতা ট্রেড এর সাথে থাকার জন্য ধন্যবাদ!",
                            'to' => $shipping_area->phone
                        ),
                    )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            }

            Session::forget('coupon_amount');
            Session::forget('coupon_used');
            Session::forget('discount');
            Toastr::success('Thanks, Your bkash payment successfully done', 'Success!');
            return redirect('customer/order-success/' . $order->id);
        }
    }

    public function getRefund(Request $request)
    {
        return view('CheckoutURL.refund');
    }

    public function refund(Request $request)
    {
        $header = $this->authHeaders();
        $body_data = array(
            'paymentID' => $request->paymentID,
            'amount' => $request->amount,
            'trxID' => $request->trxID,
            'sku' => 'sku',
            'reason' => 'Quality issue'
        );
        $body_data_json = json_encode($body_data);
        $response = $this->curlWithBody('/tokenized/checkout/payment/refund', $header, 'POST', $body_data_json);
        // your database operation
        return view('CheckoutURL.refund')->with([
            'response' => $response,
        ]);
    }

    public function getRefundStatus(Request $request)
    {
        return view('CheckoutURL.refund-status');
    }

    public function refundStatus(Request $request)
    {
        Session::forget('bkash_token');
        $token = $this->grant();
        Session::put('bkash_token', $token);
        $header = $this->authHeaders();
        $body_data = array(
            'paymentID' => $request->paymentID,
            'trxID' => $request->trxID,
        );
        $body_data_json = json_encode($body_data);
        $response = $this->curlWithBody('/tokenized/checkout/payment/refund', $header, 'POST', $body_data_json);
        return view('CheckoutURL.refund-status')->with([
            'response' => $response,
        ]);
    }
}