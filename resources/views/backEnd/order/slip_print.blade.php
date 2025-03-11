@extends('backEnd.layouts.master')
@section('title','Order Slip')
@section('content')
<style>
    .customer-invoice {
        margin: 25px 0;
    }
    .invoice_btn{
        margin-bottom: 15px;
    }
    p{
        margin:0;
    }
    td{
        font-size: 16px;
    }
   .pos__prints {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 95px;
    }
   @page { 
    margin:0px;
    }
   @media print {
    .invoice-innter{
        margin-left: -120px !important;
    }
    .invoice_btn{
        margin-bottom: 0 !important;
    }
    td{
        font-size: 18px;
    }
    p{
        margin:0;
    }
    header,footer,.no-print,.left-side-menu,.navbar-custom {
      display: none !important;
    }
  }
</style>

<section class="customer-invoice ">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <a href="" class="no-print"><strong><i class="fe-arrow-left"></i> Back To Order</strong></a>
            </div>
            <div class="col-sm-6">
                <button onclick="printFunction()"class="no-print btn btn-xs btn-success waves-effect waves-light"><i class="fa fa-print"></i></button>
            </div>
        <div class="pos__prints mt-3">
            @foreach($orders as $order)
                <div class="invoice-innter" style="width:384px;margin: 0 auto;background: #fff;overflow: hidden;padding: 30px;padding-top: 0;">
                    <table style="width:100%">
                        <colgroup>
                            <col  >
                            <col >
                          </colgroup>
                        <tr style="border-bottom: 1px dashed">
                            <td>
                                <?php
                                    echo DNS2D::getBarcodeHTML(
                                        url('/') . '/customer/order-track/result?phone=' . ($order->shipping ? $order->shipping->phone : '') . '&invoice_id=' . $order->invoice_id,
                                        'QRCODE',
                                        2,
                                        2
                                    );
                                    ?>

                            </td>
                            <td style="padding-top: 15px;">
                                <img src="{{asset($generalsetting->white_logo)}}" style="margin-top:25px !important;display: block;margin: 0 auto;margin-bottom: 15px;width: 180px" alt="">
                                <div class="invoice_form" style="text-align: center;padding-bottom: 15px">
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$generalsetting->name}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->phone}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->email}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222">{{$contact->address}}</p>
                                </div>
                            </td>
                        </tr>
                    </table style="width:100%">
                    <table style="width:100%">
                        <tr>
                            
                            <td  style="">
                                <div class="invoice_to" style="text-align: center; padding-top: 15px; padding-bottom: 15px;">
                                    
                                    <p style="font-size:16px;line-height:1.8;color:#222;">{{$order->shipping?$order->shipping->name:''}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;">{{$order->shipping?$order->shipping->phone:''}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;">{{$order->shipping?$order->shipping->address:''}}</p>
                                    <p style="font-size:16px;line-height:1.8;color:#222;">{{$order->shipping?$order->shipping->area:''}}</p>
                                   
                                </div>
                            </td>
                        </tr>
                    </table>
                    
                    <table style="width: 100%;padding-top: 15px;border-top: 1px dashed #000;">
                        <thead style="">
                            <tr >
                                <th>Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody style="border-bottom: 1px dashed">
                            @foreach($order->orderdetails as $key => $value)
                            <tr>
                                <td>{{Str::limit($value->product_name, 25)}}</td>
                                <td>{{$value->qty}}</td>
                                <td>৳{{$value->sale_price}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Subtotal</strong></td>
                                <td>৳{{$order->amount+$order->discount - $order->shipping_charge}}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Shipping(+)</strong></td>
                                <td>৳{{$order->shipping_charge}}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Discount(-)</strong></td>
                                <td>৳{{$order->discount}}</td>
                            </tr>
                            <tr >
                                <td colspan="2"><strong>Final Total</strong></td>
                                <td>৳{{$order->amount}}</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <table style="width: 100%;padding-top: 15px;">
                        </tbody>
                            <tr>
                                <td style="text-align: center;margin-top:10px">
                                    <div>
                                        <p style="border-top: 1px dashed;padding-top: 8px;padding-bottom: 5px">THANK YOU!</p>
                                    </div>
                                    <div style="width: 300px;overflow:hidden;">
                                        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($value->pro_barcode, 'C39+',2.5)}}"  />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            @endforeach
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
