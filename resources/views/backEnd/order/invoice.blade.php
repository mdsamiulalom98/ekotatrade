@extends('backEnd.layouts.master')
@section('title', 'Order Invoice')
@section('content')
    <style>
        .customer-invoice {
            margin: 25px 0;
        }

        .invoice_btn {
            margin-bottom: 15px;
        }

        p {
            margin: 0;
        }

        td {
            font-size: 16px;
        }

        .payment-success {
            background-image: url(http://localhost/ekotatrade/public/frontEnd/images/paid.jpg);
            background-repeat: no-repeat;
            background-size: 170px 160px;
            background-position: bottom center;
        }

        @page {
            margin: 0px;
        }

        @media print {
            .invoice-innter {
                margin-left: -120px !important;
            }

            .invoice_btn {
                margin-bottom: 0 !important;
            }

            td {
                font-size: 18px;
            }

            p {
                margin: 0;
            }

            header,
            footer,
            .no-print,
            .left-side-menu,
            .navbar-custom {
                display: none !important;
            }
        }
    </style>
    <section class="customer-invoice ">
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <a href="{{ url('admin/order', $order->status->slug ?? '') }}" class="no-print"><strong><i
                                class="fe-arrow-left"></i> Back To Order</strong></a>
                </div>
                <div class="col-sm-4 text-center">
                    <button onclick="printFunction()"class="no-print btn btn-xs btn-success waves-effect waves-light"><i
                            class="fa fa-print"></i></button>
                </div>
                <div class="col-sm-4 d-flex justify-content-center">
                    <!-- Button trigger modal -->
                    <button type="button" class="no-print btn btn-xs btn-primary waves-effect waves-light"
                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Payment Change
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Payment Change</h5>
                                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('order.payment.change') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="exampleSelect">Payment Status</label>
                                            <select name="status" class="form-control form-select" id="exampleSelect">
                                                <option value="paid">
                                                    Paid
                                                </option>
                                                <option value="unpaid">
                                                    Unpaid
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 mt-3">
                    <div class="invoice-innter {{ $order->due > 0 ? 'payment-due' : 'payment-success' }}"
                        style="width:760px;margin: 0 auto;background-color: #fff;overflow: hidden;padding: 30px;padding-top: 0;position: relative;">
                        <table style="width:100%">
                            <tr>
                                <td style="width: 40%; float: left; padding-top: 15px;">
                                    <img src="{{ asset($generalsetting->white_logo) }}" width="190px"
                                        style="margin-top:25px !important" alt="">
                                    <div style="font-size: 14px; color: #222; margin: 10px 0 4px;">
                                        <p><strong>Payment Method:</strong> <span
                                                style="text-transform: uppercase;">{{ $order->payment->payment_method ?? '' }}</span>
                                        </p>
                                        @if ($order->payment)
                                            <p>Trx ID : {{ $order->payment->trx_id ?? '' }}</p>
                                        @endif
                                        @if ($order->payment)
                                            <p>Sender Number : {{ $order->payment->sender_number ?? '' }}</p>
                                        @endif
                                    </div>
                                    <div class="invoice_form">
                                        <p style="font-size:16px;line-height:1.8;color:#222"><strong>Invoice From:</strong>
                                        </p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{ $generalsetting->name }}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{ $contact->phone }}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{ $contact->email }}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222">{{ $contact->address }}</p>
                                    </div>
                                </td>
                                <td style="width:60%;float: left;">
                                    <div class="invoice-bar"
                                        style=" background: #4DBC60; transform: skew(38deg); width: 100%; margin-left: 65px; padding: 20px 60px; ">
                                        <p
                                            style="font-size: 30px; color: #fff; transform: skew(-38deg); text-transform: uppercase; text-align: right; font-weight: bold;">
                                            Invoice</p>
                                    </div>
                                    <div class="invoice-bar"
                                        style="background: #fff; transform: skew(36deg); width: 72%; margin-left: 182px; padding: 12px 32px; margin-top: 6px;">
                                        <p
                                            style="font-size: 15px; color: #222;font-weight:bold; transform: skew(-36deg); text-align: right; padding-right: 18px">
                                            Invoice ID : <strong>#{{ $order->invoice_id }}</strong></p>
                                        <p
                                            style="font-size: 15px; color: #222;font-weight:bold; transform: skew(-36deg); text-align: right; padding-right: 32px">
                                            Invoice Date: <strong>{{ $order->created_at->format('d-m-y') }}</strong></span>
                                        </p>
                                    </div>
                                    <div class="invoice_to" style="padding-top: 0px;">
                                        <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">
                                            <strong>Invoice To:</strong>
                                        </p>
                                        <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">
                                            {{ $order->shipping ? $order->shipping->name : '' }}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">
                                            {{ $order->shipping ? $order->shipping->phone : '' }}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">
                                            {{ $order->shipping ? $order->shipping->address : '' }}</p>
                                        <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">
                                            {{ $order->shipping ? $order->shipping->area : '' }}</p>
                                        @if ($order->note)
                                            <p style="font-size:16px;line-height:1.8;color:#222;text-align: right;">Note :
                                                {{ $order->note }}</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table class="table" style="margin-top: 5px;margin-bottom: 0;">
                            <thead style="background: #4DBC60; color: #fff;">
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
                                @foreach ($order->orderdetails as $key => $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $value->product_name }} <br>
                                            @if ($value->product_size)
                                                <small>Size/Model: {{ $value->product_size }}</small>
                                                @endif @if ($value->product_color)
                                                    <small>Color: {{ $value->product_color }}</small>
                                                @endif
                                        </td>
                                        <td>৳{{ $value->sale_price }}</td>
                                        <td>{{ $value->qty }}</td>
                                        <td>৳{{ $value->sale_price * $value->qty }}</td>
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
                                <tbody style="background:transparent;">
                                    <tr>
                                        <td rowspan="6"><?php
                                        echo DNS2D::getBarcodeHTML(url('/') . '/customer/order-track/result?phone=' . ($order->shipping ? $order->shipping->phone : '') . '&invoice_id=' . $order->invoice_id, 'QRCODE', 5, 5);
                                        ?></td>
                                        <td><strong>Bill of Goods</strong></td>
                                        {{-- <td><strong>৳{{ $order->amount + $order->discount - $order->shipping_charge }}</strong> --}}
                                        <td><strong>৳{{ $subtotal }}</strong>
                                        </td>
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
                                    <tr style="background:#4DBC60;color:#fff">
                                        <td><strong>Due(-)</strong></td>
                                        <td><strong>৳{{ $order->due }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="terms-condition"
                                style="overflow: hidden; width: 100%; text-align: center; padding: 10px 0 0; border-top: 1px solid #ddd;">
                            </div>

                            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                                <div style="height: 200px;border: 2px solid #ddd; width: 50%; padding: 10px;">
                                    <strong style="margin-top: 145px;display: block; text-align: center;">Customer /
                                        Receiver</strong>
                                </div>
                                <div style="height: 200px;border: 2px solid #ddd; width: 50%; padding: 10px;">
                                    <strong style="margin-top: 145px;display: block; text-align: center;">Authorized
                                        Signature and Seal</strong>
                                </div>
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
