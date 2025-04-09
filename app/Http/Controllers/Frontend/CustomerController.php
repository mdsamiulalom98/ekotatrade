<?php

namespace App\Http\Controllers\Frontend;

use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Customer;
use App\Models\District;
use App\Models\Order;
use App\Models\ShippingCharge;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Review;
use App\Models\Reviewimage;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\Contact;
use App\Models\Question;
use App\Models\ProductVariable;
use App\Models\Product;
use App\Models\CouponCode;
use App\Models\Customercheckout;

class CustomerController extends Controller
{
    function __construct()
    {
        $this->middleware('customer', ['except' => ['register', 'store', 'verify', 'resendotp', 'account_verify', 'login', 'signin', 'logout', 'checkout', 'forgot_password', 'forgot_verify', 'forgot_reset', 'forgot_store', 'forgot_resend', 'order_save', 'order_success', 'order_track', 'order_track_result', 'customer_coupon', 'coupon_remove']]);
    }

    private function setting()
    {
        return GeneralSetting::select('name')->first();
    }

    private function contact()
    {
        return Contact::select('id', 'hotmail', 'phone', 'email')->first();
    }

    public function review(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'ratting' => 'required',
            'review' => 'required',
        ]);


        // Get the product ID and customer ID
        $productId = $request->product_id;

        // Check if the user is authenticated
        if (!Auth::guard('customer')->check()) {
            Toastr::error('You need to be logged in to submit a review', 'Error!');
            return redirect()->back();
        }

        $customerId = Auth::guard('customer')->user()->id;

        // Check if the customer is eligible to review the product
        $is_reviewable = Order::where('order_status', 6)
            ->where('customer_id', $customerId)
            ->whereHas('orderdetails', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        // If the customer is eligible, save the review
        if ($is_reviewable) {
            $review = new Review();
            $review->name = Auth::guard('customer')->user()->name ?? 'N / A';
            $review->email = Auth::guard('customer')->user()->email ?? 'N / A';
            $review->product_id = $request->product_id;
            $review->review = $request->review;
            $review->ratting = $request->ratting;
            $review->customer_id = $customerId;
            $review->status = 'pending';
            $review->save();
            // image upload functions
            $images = $request->file('image');
            if ($images) {
                foreach ($images as $key => $image) {
                    $name = time() . '-' . $image->getClientOriginalName();
                    $name = strtolower(preg_replace('/\s+/', '-', $name));
                    $uploadPath = 'public/uploads/product/';
                    $image->move($uploadPath, $name);
                    $imageUrl = $uploadPath . $name;

                    $pimage = new Reviewimage();
                    $pimage->review_id = $review->id;
                    $pimage->image = $imageUrl;
                    $pimage->save();
                }
            }
            Toastr::success('Thanks, your review has been submitted successfully', 'Success!');
        } else {
            // Handle the case where the customer is not eligible to review the product
            Toastr::error('You are not eligible to review this product', 'Error!');
        }

        // Redirect back to the previous page
        return redirect()->back();
    }

    public function question(Request $request)
    {
        // return $request;
        $this->validate($request, [
            'comment' => 'required',
            'product_id' => 'required',
        ]);

        // data save
        $question = new Question();
        $question->name = Auth::guard('customer')->user()->name ?? 'N / A';
        $question->email = Auth::guard('customer')->user()->email ?? 'N / A';
        $question->product_id = $request->product_id;
        $question->comment = $request->comment;
        $question->customer_id = Auth::guard('customer')->user()->id;
        $question->status = 'pending';
        $question->save();

        Toastr::success('Thanks, Your Comment send successfully', 'Success!');
        return redirect()->back();
    }

    public function login(Request $request)
    {
        if ($request->route()->getName() !== 'login') {
            Session::put('previous_url', url()->previous());
        }

        return view('frontEnd.layouts.customer.login');
    }

    public function signin(Request $request)
    {
        $auth_check = Customer::where('phone', $request->phone)->first();
        if ($auth_check) {
            if (Auth::guard('customer')->attempt(['phone' => $request->phone, 'password' => $request->password])) {
                Toastr::success('You are login successfully', 'success!');
                if ($request->details == 'review') {
                    Session::put('details_page', 'review');
                    return redirect()->back();
                }
                if ($request->details == 'question') {
                    Session::put('details_page', 'question');
                    return redirect()->back();
                }

                if (Cart::instance('shopping')->count() > 0) {
                    return redirect()->route('customer.checkout');
                }
                return redirect()->intended('customer/account');
            }
            Toastr::error('message', 'Opps! your phone or password wrong');
            return redirect()->back();
        } else {
            Toastr::error('message', 'Sorry! You have no account');
            return redirect()->back();
        }
    }

    public function register()
    {
        return view('frontEnd.layouts.customer.register');
    }

    public function store(Request $request)
    {
        $sms_gateway = SmsGateway::where(['status' => 1, 'otp_login' => 1])->first();
        if ($sms_gateway) {
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required|unique:customers',
                'otp' => 'required|min:4'
            ]);
            if (Session::get('otp_code') != $request->otp) {
                Toastr::error('Sorry', 'Your OTP not match');
                return redirect()->back();
            }
            $last_id = Customer::orderBy('id', 'desc')->first();
            $last_id = $last_id ? $last_id->id + 1 : 1;
            $store = new Customer();
            $store->name = $request->name;
            $store->slug = strtolower(Str::slug($request->name . '-' . $last_id));
            $store->phone = $request->phone;
            $store->password = bcrypt($request->phone);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();

            $auth_check = Customer::where('phone', $request->phone)->first();
            if ($auth_check) {
                if (Auth::guard('customer')->attempt(['phone' => $request->phone, 'password' => $request->phone])) {
                    Toastr::success('Account Create Successfully and You are logged in', 'success!');
                    if (Cart::instance('shopping')->count() > 0) {
                        return redirect()->route('customer.checkout');
                    }
                    return redirect()->intended('customer/account');
                }
            }
        } else {
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required|unique:customers',
                'password' => 'required|min:6'
            ]);

            $last_id = Customer::orderBy('id', 'desc')->first();
            $last_id = $last_id ? $last_id->id + 1 : 1;
            $store = new Customer();
            $store->name = $request->name;
            $store->slug = strtolower(Str::slug($request->name . '-' . $last_id));
            $store->phone = $request->phone;
            $store->email = $request->email;
            $store->password = bcrypt($request->password);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();

            Toastr::success('Success', 'Account Create Successfully');
            return redirect()->route('customer.login');
        }
    }

    public function verify()
    {
        return view('frontEnd.layouts.customer.verify');
    }
    public function resendotp(Request $request)
    {
        $customer_info = Customer::where('phone', Session::get('verify_phone'))->first();
        $customer_info->verify = rand(1111, 9999);
        $customer_info->save();
        $sms_gateway = SmsGateway::where('status', 1)->first();
        if ($sms_gateway) {
            // Dear $customer_info->name!\r\nYour account verify OTP is $customer_info->verify \r\nThank you for using " . $this->setting()->name,

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => "$sms_gateway->url",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'api_key' => "$sms_gateway->api_key",
                        'msg' => 'Test',
                        'to' => $customer_info->phone
                    ),
                )
            );

            $response = curl_exec($curl);

            curl_close($curl);
        }
        Toastr::success('Success', 'Resend code send successfully');
        return redirect()->back();
    }
    public function account_verify(Request $request)
    {
        $this->validate($request, [
            'otp' => 'required',
        ]);
        $customer_info = Customer::where('phone', Session::get('verify_phone'))->first();
        if ($customer_info->verify != $request->otp) {
            Toastr::error('Sorry', 'Your OTP not match');
            return redirect()->back();
        }

        $customer_info->verify = 1;
        $customer_info->status = 'active';
        $customer_info->save();
        Auth::guard('customer')->loginUsingId($customer_info->id);
        return redirect()->route('customer.account');
    }
    public function forgot_password()
    {
        return view('frontEnd.layouts.customer.forgot_password');
    }

    public function forgot_verify(Request $request)
    {
        $customer_info = Customer::where('phone', $request->phone)->first();
        if (!$customer_info) {
            Toastr::error('Your phone number not found');
            return back();
        }
        $customer_info->forgot = rand(1111, 9999);
        $customer_info->save();
        $sms_gateway = SmsGateway::where(['status' => 1, 'forget_pass' => 1])->first();
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
                        'msg' => "Dear $customer_info->name!\r\nYour forgot password verify OTP is $customer_info->forgot \r\nThank you for using " . $this->setting()->name,
                        'to' => $customer_info->phone
                    ),
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
        }

        Session::put('verify_phone', $request->phone);
        Toastr::success('Your OTP sent successfully');
        return redirect()->route('customer.forgot.reset');
    }

    public function forgot_resend(Request $request)
    {
        $customer_info = Customer::where('phone', $request->phone)->first();
        $customer_info->forgot = rand(1111, 9999);
        $customer_info->save();
        $sms_gateway = SmsGateway::where(['status' => 1])->first();
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
                        "msg" => "Dear $customer_info->name!\r\nYour forgot password verify OTP is $customer_info->forgot \r\nThank you for using " . $this->setting()->name,
                        'to' => $customer_info->phone
                    ),
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
        }

        return response()->json(['status' => 'success', 'data' => $customer_info->phone]);
    }
    public function forgot_reset()
    {
        if (!Session::get('verify_phone')) {
            Toastr::error('Something wrong please try again');
            return redirect()->route('customer.forgot.password');
        }
        ;
        return view('frontEnd.layouts.customer.forgot_reset');
    }
    public function forgot_store(Request $request)
    {

        $customer_info = Customer::where('phone', Session::get('verify_phone'))->first();

        if ($customer_info->forgot != $request->otp) {
            Toastr::error('Success', 'Your OTP not match');
            return redirect()->back();
        }

        $customer_info->forgot = 1;
        $customer_info->password = bcrypt($request->password);
        $customer_info->save();
        if (Auth::guard('customer')->attempt(['phone' => $customer_info->phone, 'password' => $request->password])) {
            Session::forget('verify_phone');
            Toastr::success('You are login successfully', 'success!');
            return redirect()->intended('customer/account');
        }
    }
    public function account()
    {
        return view('frontEnd.layouts.customer.account');
    }
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        Toastr::success('You are logout successfully', 'success!');
        return redirect()->route('customer.login');
    }
    public function checkout()
    {
        $shippingcharge = ShippingCharge::where(['status' => 1, 'pos' => 0])->get();
        $select_charge = ShippingCharge::where(['status' => 1, 'pos' => 0])->first();
        $bkash_gateway = PaymentGateway::where(['status' => 1, 'type' => 'bkash'])->first();
        $shurjopay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        $currentdata = date('Y-m-d');
        $couponcodes = CouponCode::where('status', 1)->where('expiry_date', '>=', $currentdata)->count();
        Session::put('shipping', $select_charge->amount);
        if (Cart::instance('shopping')->count() <= 0) {
            Toastr::error('Your cart is empty. Please go to shop to get your desired product', 'Ooops!');
        }
        return view('frontEnd.layouts.customer.checkout', compact('shippingcharge', 'bkash_gateway', 'shurjopay_gateway', 'couponcodes'));
    }
    public function order_save(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
        ]);
        if ($request->order_type != 'campaign') {
            $sms_gateway = SmsGateway::where(['status' => 1, 'otp_login' => 1])->first();
            if ($sms_gateway) {
                if (!Auth::guard('customer')->check()) {
                    Toastr::error('message', 'Sorry! You are not logged in.');
                    return redirect()->route('customer.login');
                }
            }
        }
        if (Cart::instance('shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = Session::get('discount') + Session::get('coupon_amount');
        $shippingfee = Session::get('shipping');
        $shipping_area = ShippingCharge::where(['pos' => 0, 'id' => $request->area])->first();

        if ($request->payment_method != 'bkash') {

            if (Auth::guard('customer')->user()) {
                $customer_id = Auth::guard('customer')->user()->id;
            } else {
                $exits_customer = Customer::where('phone', $request->phone)->select('phone', 'id')->first();
                if ($exits_customer) {
                    $customer_id = $exits_customer->id;
                } else {
                    $password = rand(111111, 999999);
                    $store = new Customer();
                    $store->name = $request->name;
                    $store->slug = $request->name;
                    $store->phone = $request->phone;
                    $store->email = $request->email;
                    $store->password = bcrypt($password);
                    $store->verify = 1;
                    $store->status = 'active';
                    $store->save();
                    $customer_id = $store->id;
                }
            }

            $order = Order::orderBy('id', 'desc')->first();
            $lastId = $order ? $order->invoice_id + 1 : 10001;
            $paid = $request->paid ?? 0;
            // order data save
            $order = new Order();
            $order->invoice_id = str_pad($lastId, 5, '0', STR_PAD_LEFT);
            $order->amount = ($subtotal + $shippingfee) - $discount;
            $order->discount = $discount ?? 0;
            $order->shipping_charge = $shippingfee ?? 0;
            $order->customer_id = $customer_id;
            $order->customer_ip = $request->ip();
            $order->paid = $paid;
            $order->due = $order->amount - $paid;
            $order->order_status = 1;
            $order->note = $request->note;
            $order->save();

            // shipping data save
            $shipping = new Shipping();
            $shipping->order_id = $order->id;
            $shipping->customer_id = $customer_id;
            $shipping->name = $request->name;
            $shipping->phone = $request->phone;
            $shipping->email = $request->email;
            $shipping->address = $request->address;
            $shipping->area = $shipping_area->name;
            $shipping->save();

            // payment data save
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->customer_id = $customer_id;
            $payment->payment_method = $request->payment_method;
            $payment->amount = $order->amount;
            $payment->payment_status = 'pending';
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
                try {
                    Mail::send('emails.order_place', $data, function ($textmsg) use ($data) {
                        $textmsg->to($data['email']);
                        $textmsg->subject('Your order successfully placed To Ekota Trade');
                    });

                    // Status for successful email
                    $emailStatus = 'success';
                } catch (\Exception $e) {
                    // Log the error for debugging
                    \Log::error('Mail sending failed: ' . $e->getMessage());

                    // Status for failed email
                    $emailStatus = 'failed';
                }
            }
            $data2 = [
                'email' => $this->contact()->hotmail,
                'order_id' => $order->id,
            ];

            try {
                Mail::send('emails.order_place', $data, function ($textmsg) use ($data) {
                    $textmsg->to($data['email']);
                    $textmsg->subject('Your order successfully placed To Ekota Trade');
                });

                // Status for successful email
                $emailStatus = 'success';
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Mail sending failed: ' . $e->getMessage());

                // Status for failed email
                $emailStatus = 'failed';
            }

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
                            "msg" => "আপনার অর্ডারটি নিশ্চিত করা হয়েছে। ইনভয়েস দেখতে নিচের লিংকটি ভিজিট করুন: \n https://www.ekotatrade.com.bd/customer/invoice?$order->id \n Ekota Trade-এর সাথে থাকার জন্য ধন্যবাদ।",
                            'to' => $request->phone
                        ),
                    )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            }
            Session::forget('coupon_amount');
            Session::forget('coupon_used');
            Session::forget('discount');
        }

        if ($request->payment_method == 'bkash') {
            $check = new Customercheckout();
            $check->name = $request->name;
            $check->phone = $request->phone;
            $check->address = $request->address;
            $check->area = $shipping_area->name;
            $check->amount = ($subtotal + $shippingfee) - $discount;
            $check->payment_method = $request->payment_method;
            $check->save();
            return redirect('/bkash/checkout-url/create?order_id=' . $check->id);
        } elseif ($request->payment_method == 'shurjopay') {
            $info = array(
                'currency' => "BDT",
                'amount' => 1,
                'order_id' => uniqid(),
                'discsount_amount' => 0,
                'disc_percent' => 0,
                'client_ip' => $request->ip(),
                'customer_name' => $request->name,
                'customer_phone' => $request->phone,
                'email' => "customer@gmail.com",
                'customer_address' => $request->address,
                'customer_city' => $request->area,
                'customer_state' => $request->area,
                'customer_postcode' => "1212",
                'customer_country' => "BD",
                'value1' => $order->id
            );
            $shurjopay_service = new ShurjopayController();
            return $shurjopay_service->checkout($info);
        } else {
            Cart::instance('shopping')->destroy();
            return redirect('customer/order-success/' . $order->id);
        }
    }

    public function orders()
    {
        $orders = Order::where('customer_id', Auth::guard('customer')->user()->id)->with('status')->latest()->get();
        return view('frontEnd.layouts.customer.orders', compact('orders'));
    }
    public function order_success($id)
    {
        $order = Order::where('id', $id)->with('orderdetails', 'payment', 'shipping', 'customer')->firstOrFail();
        return view('frontEnd.layouts.customer.order_success', compact('order'));
    }
    public function invoice(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'customer_id' => Auth::guard('customer')->user()->id])->with('orderdetails', 'payment', 'shipping', 'customer')->firstOrFail();
        return view('frontEnd.layouts.customer.invoice', compact('order'));
    }
    public function courier_invoice(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'customer_id' => Auth::guard('customer')->user()->id])->with('orderdetails', 'payment', 'shipping', 'customer')->firstOrFail();
        return view('frontEnd.layouts.customer.courierinvoice', compact('order'));
    }
    public function order_note(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'customer_id' => Auth::guard('customer')->user()->id])->firstOrFail();
        return view('frontEnd.layouts.customer.order_note', compact('order'));
    }
    public function profile_edit(Request $request)
    {
        $profile_edit = Customer::where(['id' => Auth::guard('customer')->user()->id])->firstOrFail();
        $districts = District::distinct()->select('district')->get();
        $areas = District::where(['district' => $profile_edit->district])->select('area_name', 'id')->get();
        return view('frontEnd.layouts.customer.profile_edit', compact('profile_edit', 'districts', 'areas'));
    }
    public function profile_update(Request $request)
    {
        $update_data = Customer::where(['id' => Auth::guard('customer')->user()->id])->firstOrFail();

        $image = $request->file('image');
        if ($image) {
            // image with intervention
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(Str::slug($name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 120;
            $height = 120;
            $img->resize($width, $height);
            $img->save($imageUrl);
        } else {
            $imageUrl = $update_data->image;
        }

        $update_data->name = $request->name;
        $update_data->phone = $request->phone;
        $update_data->email = $request->email;
        $update_data->address = $request->address;
        $update_data->district = $request->district;
        $update_data->area = $request->area;
        $update_data->image = $imageUrl;
        $update_data->save();

        Toastr::success('Your profile update successfully', 'Success!');
        return redirect()->route('customer.account');
    }

    public function order_track()
    {
        return view('frontEnd.layouts.customer.order_track');
    }

    public function order_track_result(Request $request)
    {
        $phone = $request->phone;
        $invoice_id = $request->invoice_id;

        if ($phone != null && $invoice_id == null) {
            $order = Order::whereHas('shipping', function ($query) use ($request) {
                $query->where('phone', $request->phone);
            })->get();
        } else if ($invoice_id && $phone) {
            $order = Order::whereHas('shipping', function ($query) use ($request) {
                $query->where('phone', $request->phone);
            })->where('invoice_id', $request->invoice_id)->get();
        }

        if ($order->count() == 0) {
            Toastr::error('message', 'Something Went Wrong !');
            return redirect()->back();
        }

        return view('frontEnd.layouts.customer.tracking_result', compact('order'));
    }


    public function change_pass()
    {
        return view('frontEnd.layouts.customer.change_password');
    }

    public function password_update(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $customer = Customer::find(Auth::guard('customer')->user()->id);
        $hashPass = $customer->password;

        if (Hash::check($request->old_password, $hashPass)) {

            $customer->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('customer.account');
        } else {
            Toastr::error('Failed', 'Old password not match!');
            return redirect()->back();
        }
    }

    public function wishlist()
    {
        $data = Cart::instance('wishlist')->content();
        return view('frontEnd.layouts.customer.wishlist', compact('data'));
    }
    public function customer_coupon(Request $request)
    {
        $findcoupon = CouponCode::where('coupon_code', $request->couponcode)->first();
        if ($findcoupon == null) {
            Toastr::error('Oops! Your entered promo code is not valid');
            return back();
        }

        $currentdata = date('Y-m-d');
        $expairdate = $findcoupon->expiry_date;

        if ($currentdata > $expairdate) {
            Toastr::error('Oops! Sorry, your promo code has expired');
            return back();
        }

        $totalcart = Cart::instance('shopping')->subtotal();
        $totalcart = str_replace('.00', '', $totalcart);
        $totalcart = str_replace(',', '', $totalcart);

        // Check if the coupon is for a specific product or for the entire cart
        if ($findcoupon->product_id != 0) {
            // Find the product in the cart
            $product = Cart::instance('shopping')->content()->where('id', $findcoupon->product_id)->first();
            if (!$product) {
                Toastr::error('The promo code is only applicable to a specific product, which is not in your cart.');
                return back();
            }

            // Calculate the discount for the specific product
            $productPrice = $product->price * $product->qty;
            if ($productPrice >= $findcoupon->buy_amount) {
                if ($findcoupon->offertype == 1) {
                    $discountamount = ($productPrice * $findcoupon->amount) / 100;
                } else {
                    $discountamount = $findcoupon->amount;
                }
                $discountedProductId = $findcoupon->product_id;

                Session::put('coupon_amount', $discountamount);
                Session::put('coupon_used', $findcoupon->coupon_code);
                Session::put('discounted_product_id', $discountedProductId);
                Toastr::success('Success! Your promo code was accepted for the product.');
                return back();
            } else {
                Toastr::error('You need to buy a minimum of ' . $findcoupon->buy_amount . ' Taka for this product to get the offer.');
                return back();
            }
        } else {
            // Coupon applies to the entire cart
            if ($totalcart >= $findcoupon->buy_amount) {
                if ($findcoupon->offertype == 1) {
                    $discountamount = ($totalcart * $findcoupon->amount) / 100;
                } else {
                    $discountamount = $findcoupon->amount;
                }

                Session::forget('coupon_amount');
                Session::put('coupon_amount', $discountamount);
                Session::put('coupon_used', $findcoupon->coupon_code);

                Toastr::success('Success! Your promo code was accepted.');
                return back();
            } else {
                Toastr::error('You need to buy a minimum of ' . $findcoupon->buy_amount . ' Taka to get the offer.');
                return back();
            }
        }
    }


    public function coupon_remove(Request $request)
    {
        Session::forget('coupon_amount');
        Session::forget('coupon_used');
        Session::forget('discount');
        Session::forget('discounted_product_id');
        Toastr::success('Success', 'Your coupon remove successfully');
        return back();
    }
}
