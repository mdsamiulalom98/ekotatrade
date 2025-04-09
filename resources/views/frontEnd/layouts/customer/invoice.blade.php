@extends('frontEnd.layouts.master')
@section('title', 'Customer Invoice')
@section('content')
    <style>
        .customer-invoice {
            margin: 25px 0;
        }

        .invoice_btn {
            margin-bottom: 15px;
        }

        td {
            font-size: 16px;
        }

        @page {
            size: a4;
            margin: 0mm;
            background: #F9F9F9
        }

        @media print {
            td {
                font-size: 18px;
            }

            header,
            footer,
            .no-print {
                display: none !important;
            }
        }
    </style>
    <section class="customer-invoice ">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 no-print">
                    <a href="{{route('customer.orders')}}"><strong><i class="fa-solid fa-arrow-left"></i> Back To
                            Order</strong></a>
                </div>
                <div class="col-sm-6">
                    <button onclick="printFunction()" class="no-print invoice_btn"><i class="fa fa-print"></i></button>
                </div>
                <div class="col-sm-12">
                    <div class="invoice-innter"
                        style="width: 900px;margin: 0 auto;background: #f9f9f9;overflow: hidden;padding: 30px;padding-top: 0;">
                        <table style="width:100%">
                            <tr>
                                <td style="width: 40%; float: left; padding-top: 15px;">
                                    <img src="{{asset($generalsetting->white_logo)}}"
                                        style="margin-top:25px !important;width:150px" alt="">

                                    <div class="invoice_form">
                                        <p style="font-size:16px;line-height:1.8;color:#222"><strong>Invoice From:</strong>
                                        </p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{$generalsetting->name}}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->phone}}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->email}}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->address}}</p>
                                    </div>
                                </td>
                                <td style="width:60%;float: left;">
                                    <div class="invoice-bar"
                                        style=" background: #00aef0; transform: skew(38deg); width: 100%; margin-left: 65px; padding: 0px 60px; ">
                                        <p
                                            style="font-size: 30px; color: #fff; transform: skew(-38deg); text-transform: uppercase; text-align: right; font-weight: bold;">
                                            Invoice</p>
                                    </div>
                                    <div class="invoice-bar"
                                        style="background:#fff;  width: 80%; margin-left: 135px; padding: 12px 32px; margin-top: 6px;text-align:right">
                                        <p style="display:inline-block">Invoice ID :
                                            <strong>#{{ $order->invoice_id }}</strong></p>
                                        <br>
                                        <p style="display:inline-block">Invoice Date:
                                            <strong>{{$order->created_at->format('d-m-y')}}</strong>
                                        </p>

                                    </div>
                                    <div class="invoice_to" style="padding-top: 0px;">
                                        <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">
                                            <strong>Invoice To:</strong>
                                        </p>
                                        <p
                                            style="font-size:16px;line-height:1.8;color:#222;text-align: right;font-weight:normal">
                                            {{$order->shipping ? $order->shipping->name : ''}}
                                        </p>
                                        <p
                                            style="font-size:16px;line-height:1.8;color:#222;text-align: right;font-weight:normal">
                                            {{$order->shipping ? $order->shipping->phone : ''}}
                                        </p>
                                        <p
                                            style="font-size:16px;line-height:1.8;color:#222;text-align: right;font-weight:normal">
                                            {{$order->shipping ? $order->shipping->address : ''}}
                                        </p>
                                        <p
                                            style="font-size:16px;line-height:1.8;color:#222;text-align: right;font-weight:normal">
                                            {{$order->shipping ? $order->shipping->area : ''}}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table class="table" style="margin-top: 5px">
                            <thead style="background: #00aef0; color: #fff;">
                                <tr>
                                    <th>SL</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            @php
                                $subtotal = 0;
                            @endphp
                            <tbody>
                                @foreach($order->orderdetails as $key => $value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$value->product_name}} <br> @if($value->product_size) <small>Size/Model:
                                        {{$value->product_size}}</small> @endif @if($value->product_color) <small>Color:
                                        {{$value->product_color}}</small> @endif</td>
                                        <td>Tk{{$value->sale_price}}</td>
                                        <td>{{$value->qty}}</td>
                                        <td>Tk{{$value->sale_price * $value->qty}}</td>
                                    </tr>
                                    @php
                                        $subtotal += $value->sale_price * $value->qty;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>

                        <div class="invoice-bottom">
                            <table class="table" style="margin-bottom: 0px;">
                                <colgroup>
                                    <col span="2">
                                    <col>
                                    <col>
                                </colgroup>
                                <tbody style="background:#f1f9f8 ">
                                    <tr>
                                        <td rowspan="6">
                                            <?php echo DNS2D::getBarcodeHTML(url('/') . '/customer/order-track/result?phone=' . ($order->shipping ? $order->shipping->phone : '') . '&invoice_id=' . $order->invoice_id, 'QRCODE', 5, 5); ?>
                                        </td>
                                        <td><strong>SubTotal</strong></td>
                                        <td><strong>Tk{{$order->orderdetails->sum('sale_price')}}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Discount(-)</strong></td>
                                        <td><strong>৳{{ $order->discount }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Delivery Cost(+)</strong></td>
                                        <td><strong>৳{{ $order->shipping_charge }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Final Total</strong></td>
                                        <td><strong>৳{{ $subtotal + $order->shipping_charge }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Advance(-)</strong></td>
                                        <td><strong>৳{{ $order->paid }}</strong></td>
                                    </tr>
                                    @if ($order->is_paid == 0)
                                        <tr style="background:#00aef0;color:#fff">
                                            <td><strong>Due(-)</strong></td>
                                            <td><strong>৳{{ $order->due }}</strong></td>
                                        </tr>
                                    @endif
                                    @if ($order->is_paid)
                                        <img style="position: absolute;top: 15%;opacity: .3;height: 130px;left: 29%;z-index: 0;"
                                            src="{{ asset('public/frontEnd/images/paid.jpg') }}" alt="">
                                    @endif
                                </tbody>
                            </table>

                            <div style="font-size: 14px; color: #222; margin: 10px 0 4px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Sender Number</th>
                                            <th>Transaction</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->payments as $key => $history)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $history->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $history->payment_method }}</td>
                                                <td>{{ $history->sender_number }}</td>
                                                <td>{{ $history->trx_id }}</td>
                                                <td>৳{{ $history->amount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="terms-condition"
                                style="overflow: hidden; width: 100%; text-align: center; padding: 20px 0; border-top: 1px solid #ddd;">
                                <h5 style="font-style: italic;"><a
                                        href="{{route('page', ['slug' => 'terms-condition'])}}">Terms & Conditions</a></h5>
                                <p style="text-align: center; font-style: italic; font-size: 15px; margin-top: 10px;">* This
                                    is a computer generated invoice, does not require any signature.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function printFunction() {
            window.print();
        }
    </script>
@endsection
