<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Courierapi;
use App\Models\Expense;
use App\Models\ExpenseCategories;
use App\Models\ProductVariable;
use App\Models\Contact;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\PaymentMethod;

class OrderController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:order-list|order-create|order-edit|order-delete', ['only' => ['index', 'order_store', 'order_edit']]);
        $this->middleware('permission:order-create', ['only' => ['order_store', 'order_create']]);
        $this->middleware('permission:order-edit', ['only' => ['order_edit', 'order_update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
        $this->middleware('permission:order-invoice', ['only' => ['invoice']]);
        $this->middleware('permission:order-process', ['only' => ['process', 'order_process']]);
        $this->middleware('permission:order-process', ['only' => ['process']]);
    }
    private function contact()
    {
        return Contact::select('id', 'hotmail', 'phone', 'email')->first();
    }
    private function setting()
    {
        return GeneralSetting::select('name')->first();
    }
    public function search(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'product_type')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%")->orWhere('pro_barcode', 'LIKE', '%' . $request->keyword . "%");
        }
        $products = $products->get();

        if (empty($request->keyword)) {
            $products = [];
        }
        return view('backEnd.order.search', compact('products'));
    }
    public function product_print(Request $request)
    {
        $product = Product::where('status', 1)->select('id', 'product_qr', 'name')->get();
        return view('backEnd.order.product_slip', compact('product'));
    }
    public function index($slug, Request $request)
    {
        if ($slug == 'all') {
            $order_status = (object) [
                'name' => 'All',
                'orders_count' => Order::count(),
            ];
            $show_data = Order::latest()->with('shipping', 'status');
            if ($request->keyword) {
                $show_data = $show_data->where(function ($query) use ($request) {
                    $query->orWhere('invoice_id', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhereHas('shipping', function ($subQuery) use ($request) {
                            $subQuery->where('phone', $request->keyword);
                        });
                });
            }
            $show_data = $show_data->paginate(50);
        } else {
            $order_status = OrderStatus::where('slug', $slug)->withCount('orders')->first();
            $show_data = Order::where(['order_status' => $order_status->id])->latest()->with('shipping', 'status')->paginate(50);
        }
        $users = User::get();
        $steadfast = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        // pathao courier
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/countries/1/city-list');
            $pathaocities = $response->json();
            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Content-Type' => 'application/json',
            ])->get($pathao_info->url . '/api/v1/stores');
            $pathaostore = $response2->json();
        } else {
            $pathaocities = [];
            $pathaostore = [];
        }
        return view('backEnd.order.index', compact('show_data', 'order_status', 'users', 'steadfast', 'pathaostore', 'pathaocities'));
    }

    public function pathaocity(Request $request)
    {
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/cities/' . $request->city_id . '/zone-list');
            $pathaozones = $response->json();
            return response()->json($pathaozones);
        } else {
            return response()->json([]);
        }
    }

    public function pathaozone(Request $request)
    {
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/zones/' . $request->zone_id . '/area-list');
            $pathaoareas = $response->json();
            return response()->json($pathaoareas);
        } else {
            return response()->json([]);
        }
    }

    public function order_pathao(Request $request)
    {

        $order = Order::with('shipping')->find($request->id);
        $order_count = OrderDetails::select('order_id')->where('order_id', $order->id)->count();
        // pathao
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if ($pathao_info) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($pathao_info->url . '/api/v1/orders', [
                        'store_id' => $request->pathaostore,
                        'merchant_order_id' => $order->invoice_id,
                        'sender_name' => 'Test',
                        'sender_phone' => $order->shipping->phone ?? '',
                        'recipient_name' => $order->shipping->name ?? '',
                        'recipient_phone' => $order->shipping->phone ?? '',
                        'recipient_address' => $order->shipping->address ?? '',
                        'recipient_city' => $request->pathaocity,
                        'recipient_zone' => $request->pathaozone,
                        'recipient_area' => $request->pathaoarea,
                        'delivery_type' => 48,
                        'item_type' => 2,
                        'special_instruction' => 'Special note- product must be check after delivery',
                        'item_quantity' => $order_count,
                        'item_weight' => 0.5,
                        'amount_to_collect' => round($order->amount),
                        'item_description' => 'Special note- product must be check after delivery',
                    ]);
        }
        if ($response->status() == '200') {
            $order->order_status = 5;
            $order->save();
            Toastr::success('order send to pathao successfully');
            return redirect()->back();
        } else {
            Toastr::error($response['message'], 'Courier Order Faild');
            return redirect()->back();
        }
    }

    public function invoice($id)
    {
        $order = Order::where(['id' => $id])->with('orderdetails', 'payment', 'shipping', 'customer')->firstOrFail();
        return view('backEnd.order.invoice', compact('order'));
    }

    public function slip($id)
    {
        $order = Order::where(['id' => $id])->with('orderdetails', 'payment', 'shipping')->firstOrFail();
        return view('backEnd.order.slip', compact('order'));
    }

    public function process($id)
    {
        $data = Order::where(['invoice_id' => $id])->select('id', 'invoice_id', 'order_status')->with('orderdetails')->first();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        return view('backEnd.order.process', compact('data', 'shippingcharge'));
    }

    public function order_process(Request $request)
    {
        $link = OrderStatus::find($request->status)->slug;
        $order = Order::find($request->id);
        $courier = $order->order_status;
        $order->order_status = $request->status;
        $order->admin_note = $request->admin_note;
        $order->paid = $request->status == 6 ? $order->amount : $order->paid;
        $order->due = $request->status == 6 ? 0 : $order->due;
        $order->save();

        $shipping_update = Shipping::where('order_id', $order->id)->first();
        $shippingfee = ShippingCharge::find($request->area);
        if ($shippingfee->name != $request->area) {
            if ($order->shipping_charge > $shippingfee->amount) {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            } else {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            }
        }

        $shipping_update->name = $request->name;
        $shipping_update->phone = $request->phone;
        $shipping_update->address = $request->address;
        $shipping_update->area = $shippingfee->name;
        $shipping_update->save();

        if ($request->status == 2) {
            $sms_gateway = SmsGateway::where('status', 1)->first();
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
                            "msg" => "প্রিয় $request->name \n আপনার অর্ডার #$order->invoice_id টি প্রসেস করা হয়েছে! অর্ডারটি ট্র্যাক করতে ক্লিক করুন: \n https://www.ekotatrade.com.bd/customer/order-track/result?phone=$request->phone&invoice_id=$order->invoice_id \n একতা ট্রেডের এর সাথে থাকার জন্য ধন্যবাদ!",
                            'to' => $request->phone
                        ),
                    )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            }
        }

        if ($request->status == 5) {
            $courier_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
            if ($courier_info) {
                $consignmentData = [
                    'invoice' => $order->invoice_id,
                    'recipient_name' => $order->shipping->name ?? '',
                    'recipient_phone' => $order->shipping->phone ?? '',
                    'recipient_address' => $order->shipping->address ?? '',
                    'cod_amount' => $order->amount
                ];
                $client = new Client();
                $response = $client->post('$courier_info->url', [
                    'json' => $consignmentData,
                    'headers' => [
                        'Api-Key' => '$courier_info->api_key',
                        'Secret-Key' => '$courier_info->secret_key',
                        'Accept' => 'application/json',
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);
                $order->courier_tracker = $responseData['consignment']['consignment_id'];
                ;
                $order->save();
            } else {
                return "ok";
            }
        }

        if ($request->status == 7) {
            $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty', 'product_type', 'product_size', 'product_color', 'product_id')->where('order_id', $order->id)->get();
            foreach ($orders_details as $order_details) {
                if ($order_details->product_type == 1) {
                    $product = Product::find($order_details->product_id);
                    $product->stock += $order_details->qty;
                    $product->save();
                } else {
                    $product = ProductVariable::where('product_id', $order_details->product_id);
                    if ($order_details->product_color) {
                        $product->where('color', $order_details->product_color);
                    }
                    if ($order_details->product_size) {
                        $product->where('size', $order_details->product_size);
                    }

                    $product = $product->first();
                    if ($product) {
                        $product->stock += $order_details->qty;
                        $product->save();
                    } else {
                    }
                }
            }

            $sms_gateway = SmsGateway::where('status', 1)->first();
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
                            "msg" => "প্রিয় $request->name \n আপনার অর্ডার #$order->invoice_id টি ক্যানসেল করা হয়েছে! বিস্তারিত জানতে ক্লিক করুন: https://www.ekotatrade.com.bd/customer/order-track/result?phone=$request->phone&invoice _id=$order->invoice_id \n একতা ট্রেড এর সাথে থাকার জন্য ধন্যবাদ!",
                            'to' => $request->phone
                        ),
                    )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            }

            $linkdata = OrderStatus::find($request->status)->name;
            if ($order->shipping->email) {
                $data = [
                    'email' => $order->shipping->email,
                    'order_id' => $order->id,
                ];

                try {
                    Mail::send('emails.order_cancel', $data, function ($textmsg) use ($data, $linkdata) {
                        $textmsg->to($data['email']);
                        $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                    });
                } catch (\Exception $e) {
                    Toastr::error('Email not sent', 'Failed');
                }
            }


            $data = [
                'email' => $this->contact()->hotmail,
                'order_id' => $order->id,
            ];

            try {
                Mail::send('emails.order_cancel', $data, function ($textmsg) use ($data, $linkdata) {
                    $textmsg->to($data['email']);
                    $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                });
            } catch (\Exception $e) {
                Toastr::error('Email not sent', 'Failed');
            }
        }

        if ($request->status == 6) {
            $sms_gateway = SmsGateway::where('status', 1)->first();
            if ($sms_gateway) {
                $slug = $order->orderdetail->product->slug;
                $curl = curl_init();
                curl_setopt_array(
                    $curl,
                    array(
                        CURLOPT_URL => "$sms_gateway->url",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array(
                            'api_key' => "$sms_gateway->api_key",
                            "msg" => "প্রিয় $request->name \n আপনার অ্ডার #$order->invoice_id ফল ভাবে ডেলভারি করা হযেছে! প্রোডা্ট সম্পর্কে আপনার মুল্যবা মতামত দিতে ক্লিক করুন: https://www.ekotatrade.com.bd/product/$slug#writeReview \n একা ট্রেড এর সাথে থাকার জন্য ধন্যবাদ!",
                            'to' => $request->phone
                        ),
                    )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            }

            $linkdata = OrderStatus::find($request->status)->name;
            if ($order->shipping->email) {
                $data = [
                    'email' => $order->shipping->email,
                    'order_id' => $order->id,
                ];

                try {
                    Mail::send('emails.order_place', $data, function ($textmsg) use ($data, $linkdata) {
                        $textmsg->to($data['email']);
                        $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                    });
                } catch (\Exception $e) {
                    Toastr::error('Email not sent', 'Failed');
                }
            }


            $data = [
                'email' => $this->contact()->hotmail,
                'order_id' => $order->id,
            ];

            try {
                Mail::send('emails.order_place', $data, function ($textmsg) use ($data, $linkdata) {
                    $textmsg->to($data['email']);
                    $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                });
            } catch (\Exception $e) {
                Toastr::error('Email not sent', 'Failed');
            }
        }
        Toastr::success('Success', 'Order status change successfully');
        return redirect('admin/order/' . $link);
    }

    public function destroy(Request $request)
    {
        Order::where('id', $request->id)->delete();
        OrderDetails::where('order_id', $request->id)->delete();
        Shipping::where('order_id', $request->id)->delete();
        Payment::where('order_id', $request->id)->delete();
        Toastr::success('Success', 'Order delete success successfully');
        return redirect()->back();
    }

    public function order_assign(Request $request)
    {
        $products = Order::whereIn('id', $request->input('order_ids'))->update(['user_id' => $request->user_id]);
        return response()->json(['status' => 'success', 'message' => 'Order user id assign']);
    }

    public function order_status(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))->update(['order_status' => $request->order_status]);

        if ($request->order_status == 2) {
            $orders = Order::whereIn('id', $request->input('order_ids'))->get();
            foreach ($orders as $order) {
                $sms_gateway = SmsGateway::where('status', 1)->first();
                if ($sms_gateway) {
                    $phone = $order->shipping->phone ?? '01611814504';
                    $customer = $order->shipping->name ?? 'estiak';
                    $curl = curl_init();
                    curl_setopt_array(
                        $curl,
                        array(
                            CURLOPT_URL => "$sms_gateway->url",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array(
                                'api_key' => "$sms_gateway->api_key",
                                "msg" => "প্রিয $customer \n আপনার র্ডর #$order->invoice_id টি প্রসেস করা হয়েছে! র্ডারটি ট্র্যাক করতে ক্লিক করুন:\n https://www.ekotatrade.com.bd/customer/order-track/result?phone=$phone&invoice_id=$order->invoice_id\n একতা ্রেড র সাথে াকা জন্য ধনযবদ!",
                                'to' => $phone,
                            ),
                        )
                    );
                    $response = curl_exec($curl);
                    curl_close($curl);
                }
            }
        }

        if ($request->order_status == 7) {
            $orders = Order::whereIn('id', $request->input('order_ids'))->get();
            foreach ($orders as $order) {
                $orders_details = OrderDetails::where('order_id', $order->id)->get();
                foreach ($orders_details as $order_detail) {
                    if ($order_detail->product_type == 1) {
                        $product = Product::find($order_detail->product_id);
                        $product->stock += $order_detail->qty;
                        $product->save();
                    } else {
                        $product = ProductVariable::where('product_id', $order_detail->product_id);
                        if ($order_detail->product_color) {
                            $product->where('color', $order_detail->product_color);
                        }
                        if ($order_detail->product_size) {
                            $product->where('size', $order_detail->product_size);
                        }

                        $product = $product->first();
                        if ($product) {
                            $product->stock += $order_detail->qty;
                            $product->save();
                        } else {
                        }
                    }
                }

                $sms_gateway = SmsGateway::where('status', 1)->first();
                if ($sms_gateway) {
                    $phone = $order->shipping->phone ?? '01611814504';
                    $customer = $order->shipping->name ?? 'estiak';
                    $curl = curl_init();
                    curl_setopt_array(
                        $curl,
                        array(
                            CURLOPT_URL => "$sms_gateway->url",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array(
                                'api_key' => "$sms_gateway->api_key",
                                "msg" => "প্রিয় $customer \n আপনার অ্ডার #$order->invoice_id ট ক্যান্সে রা হয়েছে!  িস্তারিত জাতে ক্লিক করন: https://www.ekotatrade.com.bd/customer/order-track/result?phone=$phone&invoice_id=$order->invoice_id \n একতা টরেড এর সাথে থাার জন্য ধন্বাদ!",
                                'to' => $phone,
                            ),
                        )
                    );
                    $response = curl_exec($curl);
                    curl_close($curl);
                }

                $linkdata = OrderStatus::find($request->order_status)->name;
                if ($order->shipping->email) {
                    $data = [
                        'email' => $order->shipping->email,
                        'order_id' => $order->id,
                    ];
                    try {
                        Mail::send('emails.order_place', $data, function ($textmsg) use ($data, $linkdata) {
                            $textmsg->to($data['email']);
                            $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                        });
                    } catch (\Exception $e) {
                        Toastr::error('Email not sent', 'Failed');
                    }
                }


                $data = [
                    'email' => $this->contact()->hotmail,
                    'order_id' => $order->id,
                ];
                try {
                    Mail::send('emails.order_place', $data, function ($textmsg) use ($data, $linkdata) {
                        $textmsg->to($data['email']);
                        $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                    });
                } catch (\Exception $e) {
                    Toastr::error('Email not sent', 'Failed');
                }
            }
        }
        if ($request->order_status == 6) {
            $orders = Order::whereIn('id', $request->input('order_ids'))->get();
            foreach ($orders as $order) {
                $sms_gateway = SmsGateway::where('status', 1)->first();
                if ($sms_gateway) {
                    $phone = $order->shipping->phone ?? '01611814504';
                    $customer = $order->shipping->name ?? 'estiak';
                    $slug = $order->orderdetail->product->slug;
                    $curl = curl_init();
                    curl_setopt_array(
                        $curl,
                        array(
                            CURLOPT_URL => "$sms_gateway->url",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array(
                                'api_key' => "$sms_gateway->api_key",
                                "msg" => "পরিয় $customer \n আনার র্ডার #$order->invoice_id সফল ভাবে ডলিভার করা হ়েছে! প্রোডক্ট সম্পর্ে আপনর মুল্বান তামত দিে কলিক করুন: https://www.ekotatrade.com.bd/product/$slug#writeReview \n এত ট্রেড এর সে থাকার জন্ ধন্যবাদ!",
                                'to' => $phone
                            ),
                        )
                    );
                    $response = curl_exec($curl);
                    curl_close($curl);
                }


                if ($order->shipping->email) {
                    $linkdata = OrderStatus::find($request->order_status)->name;
                    $data = [
                        'email' => $order->shipping->email,
                        'order_id' => $order->id,
                    ];
                    try {
                        Mail::send('emails.order_place', $data, function ($textmsg) use ($data, $linkdata) {
                            $textmsg->to($data['email']);
                            $textmsg->subject('Your order is ' . $linkdata . ' on Ekota Trade');
                        });
                    } catch (\Exception $e) {
                        Toastr::error('Email not sent', 'Failed');
                    }

                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Order status change successfully']);
    }

    public function bulk_destroy(Request $request)
    {
        $orders_id = $request->order_ids;
        foreach ($orders_id as $order_id) {
            Order::where('id', $order_id)->delete();
            OrderDetails::where('order_id', $order_id)->delete();
            Shipping::where('order_id', $order_id)->delete();
            Payment::where('order_id', $order_id)->delete();
        }
        return response()->json(['status' => 'success', 'message' => 'Order delete successfully']);
    }
    public function order_print(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('orderdetails', 'payment', 'shipping', 'customer')->get();
        $view = view('backEnd.order.print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }
    public function pos_print(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('orderdetails', 'payment', 'shipping', 'customer')->get();
        $view = view('backEnd.order.slip_print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }
    public function bulk_courier($slug, Request $request)
    {
        $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();
        if ($courier_info) {
            $orders_id = $request->order_ids;
            foreach ($orders_id as $order_id) {
                $order = Order::find($order_id);
                $courier = $order->order_status;
                if ($courier != 5) {
                    $consignmentData = [
                        'invoice' => $order->invoice_id,
                        'recipient_name' => $order->shipping->name ?? '',
                        'recipient_phone' => $order->shipping->phone ?? '',
                        'recipient_address' => $order->shipping->address ?? '',
                        'cod_amount' => $order->amount
                    ];
                    $client = new Client();
                    $response = $client->post($courier_info->url, [
                        'json' => $consignmentData,
                        'headers' => [
                            'Api-Key' => $courier_info->api_key,
                            'Secret-Key' => $courier_info->secret_key,
                            'Accept' => 'application/json',
                        ],
                    ]);
                    $responseData = json_decode($response->getBody(), true);
                    if ($responseData['status'] == 200) {
                        $message = 'Your order place to courier successfully';
                        $status = 'success';
                        $order->order_status = 5;
                        $order->courier_tracker = $responseData['consignment']['consignment_id'];
                        ;
                        $order->save();
                    } else {
                        $message = 'Your order place to courier failed';
                        $status = 'failed';
                    }
                    return response()->json(['status' => $status, 'message' => $message]);
                }
            }
        }
    }
    public function order_create()
    {
        // Cart::instance('pos_shopping')->destroy();
        Session::put('pos_shipping', 0);
        $products = Product::select('id', 'name', 'new_price', 'product_code')->where(['status' => 1])->get();
        $cartinfo = Cart::instance('pos_shopping')->content()->sortBy('rowId');
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $paymentmethods = PaymentMethod::where('status', 1)->get();
        Session::forget('cpaid');
        return view('backEnd.order.create', compact('products', 'cartinfo', 'shippingcharge', 'paymentmethods'));
    }

    public function order_store(Request $request)
    {
        if ($request->guest_customer) {
            $this->validate($request, [
                'guest_customer' => 'required',
            ]);
            $customer = Customer::find($request->guest_customer);

            $area = ShippingCharge::where('pos', 1)->first();
            $name = $customer->name ?? 'Customer';
            $phone = $customer->phone ?? '';
            $address = $area->name ?? '';
            $area = $area->id ?? '';
        } else {
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'area' => 'required',
            ]);
            $name = $request->name;
            $phone = $request->phone;
            $address = $request->address;
            $area = $request->area;
        }

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = Session::get('pos_discount') + Session::get('product_discount');

        $shippingfee = $request->area ?? 0;
        $shippingarea = 'Pos Area';

        $exits_customer = Customer::where('phone', $phone)->select('phone', 'id')->first();
        if ($exits_customer) {
            $customer_id = $exits_customer->id;
        } else {
            $password = rand(111111, 999999);
            $store = new Customer();
            $store->name = $name;
            $store->slug = $name;
            $store->phone = $phone;
            $store->password = bcrypt($password);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();
            $customer_id = $store->id;
        }

        // order data save
        $order = Order::orderBy('id', 'desc')->first();
        $lastId = $order ? $order->invoice_id + 1 : 10001;

        // order data save
        $order = new Order();
        $order->invoice_id = str_pad($lastId, 5, '0', STR_PAD_LEFT);
        $order->amount = ($subtotal + $shippingfee) - $discount;
        $order->discount = $discount ?? 0;
        $order->paid = !empty($request->amount) ? array_sum($request->amount) : 0;
        $order->due = $order->amount - $order->paid;
        $order->shipping_charge = $shippingfee;
        $order->customer_id = $customer_id;
        $order->order_status = 1;
        $order->note = $request->note;
        $order->save();

        // shipping data save
        $shipping = new Shipping();
        $shipping->order_id = $order->id;
        $shipping->customer_id = $customer_id;
        $shipping->name = $name;
        $shipping->phone = $phone;
        $shipping->address = $address;
        $shipping->area = $shippingarea;
        $shipping->save();

        if ($request->amount) {
            $amounts = array_filter($request->amount);
            if (is_array($amounts)) {
                foreach ($amounts as $key => $amount) {
                    // payment data save
                    $payment = new Payment();
                    $payment->order_id = $order->id;
                    $payment->customer_id = $customer_id;
                    $payment->payment_method = $request->payment_method[$key];
                    $payment->amount = $amount;
                    $payment->payment_status = 'paid';
                    $payment->save();
                }
            }
        }

        // order details data save
        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            $order_details = new OrderDetails();
            $order_details->order_id = $order->id;
            $order_details->product_id = $cart->id;
            $order_details->product_name = $cart->name;
            $order_details->purchase_price = $cart->options->purchase_price;
            $order_details->product_discount = $cart->options->product_discount;
            $order_details->product_size = $cart->options->product_size;
            $order_details->product_color = $cart->options->product_color;
            $order_details->product_type = $cart->options->product_type;
            $order_details->sale_price = $cart->price;
            $order_details->qty = $cart->qty;
            $order_details->save();
        }
        $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty', 'product_type', 'product_size', 'product_color', 'product_id')->where('order_id', $order->id)->get();
        foreach ($orders_details as $order_detail) {
            if ($order_detail->product_type == 1) {
                $product = Product::find($order_detail->product_id);
                if ($product->stock >= $order_detail->qty) {
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
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Session::forget('cpaid');
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }
    public function cart_add(Request $request)
    {
        $product = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'product_type', 'stock')->where(['id' => $request->id])->first();

        $var_product = ProductVariable::where('product_id', $request->id)
            ->when($request->color, function ($query, $color) {
                return $query->where('color', $color);
            })
            ->when($request->size, function ($query, $size) {
                return $query->where('size', $size);
            })
            ->first();
        if ($product->product_type == 2 && $var_product) {
            $purchase_price = $var_product->purchase_price ?? 0;
            $old_price = $var_product->old_price ?? 0;
            $new_price = $var_product->new_price ?? 0;
            $stock = $var_product->stock ?? 0;
        } else {
            $purchase_price = $product->purchase_price;
            $old_price = $product->old_price;
            $new_price = $product->new_price;
            $stock = $product->stock;
        }

        $qty = 1;
        $itemIdentifier = $product->id . '-' . ($var_product->color ?? '') . '-' . ($var_product->size ?? '');
        $cartitem = Cart::instance('pos_shopping')->content()->where('id', $itemIdentifier)->first();
        if ($cartitem) {
            $cart_qty = $cartitem->qty + $qty;

            if ($stock < $cart_qty) {
                Toastr::error('Product stock limit over', 'Failed!');
                return response()->json(['status' => 'limitover', 'message' => 'Your stock limit is over']);
            }

            // Update the quantity of the existing cart item
            Cart::instance('pos_shopping')->update($cartitem->rowId, $cart_qty);
        } else {

            if ($stock < $qty) {
                Toastr::error('Product stock limit over', 'Failed!');
                return response()->json(['status' => 'limitover', 'message' => 'Your stock limit is over']);
            }
            $cartinfo = Cart::instance('pos_shopping')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => $qty,
                'price' => $product->new_price,
                'options' => [
                    'slug' => $product->slug,
                    'image' => $product->image->image,
                    'old_price' => $product->old_price,
                    'purchase_price' => $product->purchase_price,
                    'product_discount' => 0,
                    'product_type' => $product->product_type,
                    'product_color' => $var_product->color ?? null,
                    'product_size' => $var_product->size ?? null,
                ],
            ]);
        }

        return response()->json(compact('cartinfo'));
    }
    public function cart_content()
    {
        $cartinfo = Cart::instance('pos_shopping')->content()->sortBy('rowId');
        return view('backEnd.order.cart_content', compact('cartinfo'));
    }
    public function find_customer(Request $request)
    {
        $customer = Customer::where('phone', $request->phone)->first();
        $response = [
            'status' => 'success',
            'customer' => $customer
        ];
        return response()->json($response);
    }
    public function cart_details()
    {
        $cartinfo = Cart::instance('pos_shopping')->content();
        $discount = 0;
        foreach ($cartinfo as $cart) {
            $discount += $cart->options->product_discount * $cart->qty;
        }
        Session::put('product_discount', $discount);
        return view('backEnd.order.cart_details', compact('cartinfo'));
    }
    public function cart_increment(Request $request)
    {
        $qty = $request->qty + 1;
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, $qty);
        return response()->json($cartinfo);
    }

    public function cart_decrement(Request $request)
    {
        $qty = $request->qty - 1;
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, $qty);
        return response()->json($cartinfo);
    }

    public function quantity_update(Request $request)
    {
        $qty = $request->qty;
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_remove(Request $request)
    {
        Cart::instance('pos_shopping')->remove($request->id);
        $cartinfo = Cart::instance('pos_shopping')->content();
        return response()->json($cartinfo);
    }
    public function product_price(Request $request)
    {
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'price' => $request->price,
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $cart->options->discount,
                'details_id' => $cart->options->details_id,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function product_discount(Request $request)
    {
        $discount = $request->discount;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $discount,
                'details_id' => $cart->options->details_id,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function cart_shipping(Request $request)
    {
        $shipping = $request->area ?? 0;
        Session::put('pos_shipping', $shipping);
        return response()->json($shipping);
    }

    public function cart_clear(Request $request)
    {
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        return redirect()->back();
    }
    public function order_edit($id)
    {
        Session::forget('cpaid');
        $products = Product::select('id', 'name', 'new_price', 'product_code')->where(['status' => 1])->get();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $order = Order::where('id', $id)->first();
        $cartinfo = Cart::instance('pos_shopping')->destroy();
        $shippinginfo = Shipping::where('order_id', $order->id)->first();
        $paymentmethods = PaymentMethod::where('status', 1)->get();
        $payments = Payment::where('order_id', $order->id)->get();
        $paymentAmount = $payments->sum('amount');
        Session::put('cpaid', $paymentAmount);
        Session::put('product_discount', $order->discount);
        Session::put('pos_shipping', $order->shipping_charge);
        $orderdetails = OrderDetails::where('order_id', $order->id)->get();
        foreach ($orderdetails as $ordetails) {
            $cartinfo = Cart::instance('pos_shopping')->add([
                'id' => $ordetails->product_id,
                'name' => $ordetails->product_name,
                'qty' => $ordetails->qty,
                'price' => $ordetails->sale_price,
                'options' => [
                    'image' => $ordetails->image->image,
                    'purchase_price' => $ordetails->purchase_price,
                    'product_discount' => $ordetails->product_discount,
                    'product_size' => $ordetails->product_size,
                    'product_color' => $ordetails->product_color,
                    'details_id' => $ordetails->id,
                ],
            ]);
        }
        $cartinfo = Cart::instance('pos_shopping')->content();
        return view('backEnd.order.edit', compact('products', 'cartinfo', 'shippingcharge', 'shippinginfo', 'order', 'paymentmethods', 'payments'));
    }

    public function order_update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
        ]);

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = Session::get('pos_discount') + Session::get('product_discount');

        $shippingfee = $request->area ?? 0;
        $shippingarea = 'Pos Area';

        $exits_customer = Customer::where('phone', $request->phone)->select('phone', 'id')->first();
        if ($exits_customer) {
            $customer_id = $exits_customer->id;
        } else {
            $password = rand(111111, 999999);
            $store = new Customer();
            $store->name = $request->name;
            $store->slug = $request->name;
            $store->phone = $request->phone;
            $store->password = bcrypt($password);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();
            $customer_id = $store->id;
        }

        // order data save
        $order = Order::where('id', $request->order_id)->first();
        $order->amount = ($subtotal + $shippingfee) - $discount;
        $order->discount = $discount ?? 0;
        $order->paid = !empty($request->amount) ? array_sum($request->amount) : 0;
        $order->due = $order->amount - $order->paid;
        $order->shipping_charge = $shippingfee;
        $order->customer_id = $customer_id;
        $order->order_status = 1;
        $order->note = $request->note;
        $order->save();


        // shipping data save
        $shipping = Shipping::where('order_id', $request->order_id)->first();
        $shipping->order_id = $order->id;
        $shipping->customer_id = $customer_id;
        $shipping->name = $request->name;
        $shipping->phone = $request->phone;
        $shipping->address = $request->address;
        $shipping->area = $shippingarea;
        $shipping->save();

        if ($request->up_payment) {
            $update_payments = array_filter($request->up_payment);
            $amounts = $request->amount;
            $payment_methods = $request->payment_method;

            if ($update_payments) {
                foreach ($update_payments as $key => $update_id) {
                    $uppayment = Payment::find($update_id);
                    if ($uppayment) {
                        $uppayment->customer_id = $customer_id;
                        $uppayment->payment_method = $payment_methods[$key];
                        $uppayment->amount = $amounts[$key];
                        $uppayment->save();
                    }
                }
            }
        }

        // Insert New Payments Only (Exclude Updated Ones)
        if ($request->amount) {
            $amounts = array_filter($request->amount);
            $payment_methods = $request->payment_method;

            // Exclude already updated payments
            $updated_payment_keys = array_keys($update_payments ?? []);

            if (is_array($amounts)) {
                foreach ($amounts as $key => $amount) {
                    if (!in_array($key, $updated_payment_keys)) { // Prevent duplication
                        $payment = new Payment();
                        $payment->order_id = $order->id;
                        $payment->customer_id = $customer_id;
                        $payment->payment_method = $payment_methods[$key];
                        $payment->amount = $amount;
                        $payment->payment_status = 'paid';
                        $payment->save();
                    }
                }
            }
        }

        // order details data save
        foreach ($order->orderdetails as $orderdetail) {
            $item = Cart::instance('pos_shopping')->content()->where('id', $orderdetail->product_id)->first();
            if (!$item) {
                $orderdetail->delete();
            }
        }
        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            $exits = OrderDetails::where('id', $cart->options->details_id)->first();
            if ($exits) {
                $order_details = OrderDetails::find($exits->id);
                $order_details->product_discount = $cart->options->product_discount;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->product_type = $cart->options->product_type;
                $order_details->product_size = $cart->options->product_size;
                $order_details->product_color = $cart->options->product_color;
                $order_details->save();
            } else {
                $order_details = new OrderDetails();
                $order_details->order_id = $order->id;
                $order_details->product_id = $cart->id;
                $order_details->product_name = $cart->name;
                $order_details->purchase_price = $cart->options->purchase_price;
                $order_details->product_discount = $cart->options->product_discount;
                $order_details->product_size = $cart->options->product_size;
                $order_details->product_color = $cart->options->product_color;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->product_type = $cart->options->product_type;
                $order_details->save();
            }
        }
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }

    public function order_report(Request $request)
    {
        $order_statuses = OrderStatus::with('orders')->withCount('orders')->get();
        $users = User::where('status', 1)->get();
        $orders = Order::with('shipping', 'orderdetails')->latest();
        if ($request->keyword) {
            $orders = $orders->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->status) {
            $orders = $orders->where('order_status', $request->status);
        }
        if ($request->user_id) {
            $orders = $orders->where('admin_id', $request->user_id);
        }
        if ($request->start_date && $request->end_date) {
            $orders = $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $orders = $orders->paginate(50);
        // return $orders;

        if ($request->start_date && $request->end_date) {
            $total_expense = Expense::where('status', 1)->whereBetween('created_at', [$request->start_date, $request->end_date])->sum('amount');
            $total_purchase = OrderDetails::whereHas('order', function ($query) use ($request) {
                $query->where('order_status', 6)
                    ->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->sum(DB::raw('purchase_price * qty'));

            $total_sales = OrderDetails::whereHas('order', function ($query) use ($request) {
                $query->where('order_status', 6)
                    ->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->sum(DB::raw('sale_price * qty'));
        } else {
            $total_expense = Expense::where('status', 1)->sum('amount');
            $total_purchase = OrderDetails::whereHas('order', function ($query) {
                $query->where('order_status', 6);
            })->sum(DB::raw('purchase_price * qty'));

            $total_sales = OrderDetails::whereHas('order', function ($query) {
                $query->where('order_status', 6);
            })->sum(DB::raw('sale_price * qty'));
        }
        $allorders = Order::all();
        return view('backEnd.reports.order', compact('order_statuses', 'orders', 'users', 'total_expense', 'total_sales', 'total_purchase', 'allorders'));
    }

    public function stock_report(Request $request)
    {
        $products = Product::select('id', 'name', 'new_price', 'stock', 'product_type', 'purchase_price')
            ->where('status', 1);
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $total_purchase = $products->sum(DB::raw('purchase_price * stock'));
        $total_stock = $products->sum('stock');
        $total_price = $products->sum(DB::raw('new_price * stock'));
        $products = $products->latest()->paginate(50);
        $categories = Category::where('status', 1)->get();
        return view('backEnd.reports.stock', compact('products', 'categories', 'total_purchase', 'total_stock', 'total_price'));
    }

    public function expense_report(Request $request)
    {
        $data = Expense::where('status', 1);
        if ($request->keyword) {
            $data = $data->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $data = $data->where('expense_cat_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $data->paginate(50);
        $categories = ExpenseCategories::where('status', 1)->get();
        return view('backEnd.reports.expense', compact('data', 'categories'));
    }

    public function loss_profit(Request $request)
    {
        if ($request->start_date && $request->end_date) {
            $total_expense = Expense::where('status', 1)->whereBetween('created_at', [$request->start_date, $request->end_date])->sum('amount');
            $total_purchase = OrderDetails::whereHas('order', function ($query) use ($request) {
                $query->where('order_status', 6)
                    ->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->sum(DB::raw('purchase_price * qty'));

            $total_sales = OrderDetails::whereHas('order', function ($query) use ($request) {
                $query->where('order_status', 6)
                    ->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->sum(DB::raw('sale_price * qty'));
        } else {
            $total_expense = Expense::where('status', 1)->sum('amount');
            $total_purchase = OrderDetails::whereHas('order', function ($query) {
                $query->where('order_status', 6);
            })->sum(DB::raw('purchase_price * qty'));

            $total_sales = OrderDetails::whereHas('order', function ($query) {
                $query->where('order_status', 6);
            })->sum(DB::raw('sale_price * qty'));
        }

        return view('backEnd.reports.loss_profit', compact('total_expense', 'total_purchase', 'total_sales'));
    }

    public function order_paid(Request $request)
    {
        $amount = $request->amount ?? 0;
        Session::put('cpaid', $amount);
        return response()->json($amount);
    }
    public function payment_change(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($request->status == 'paid') {
            $order->paid = $order->amount;
            $order->due = 0;
        } else {
            $order->paid = 0;
            $order->due = $order->amount;
        }
        $order->save();
        return redirect()->back();
    }
    public function payment_remove(Request $request)
    {
        $payment = Payment::find($request->id);
        $order = Order::find($payment->order_id);
        $order->paid = $order->paid - $payment->amount;
        $order->due = $order->amount - $order->paid;
        $order->save();
        $payment->delete();
        return redirect()->back();
    }
}
