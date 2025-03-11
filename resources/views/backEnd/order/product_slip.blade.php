@extends('backEnd.layouts.master')
@section('title','Product Slip')
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
  .pos__prints {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 5px;
    }
    .invoice-innter {
        background: white;
        display: block;
        text-align: center;
        width: 100%;
        padding: 15px;
    }
    .pro__qr {
        width: 80px;
        margin: 12px auto;
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
            <div class="col-sm-12">
        <div class="pos__prints mt-3">
            @foreach($product as $value)
                <div class="invoice-innter">
                    <p>{{$value->name}}</p>
                        <div class="pro__qr">
                             <?php
                                echo DNS2D::getBarcodeHTML(
                                    url('/') . '/product-checker/?product_id=' . ($value->product_qr),
                                    'QRCODE',
                                    2,
                                    2
                                );
                                ?>

                       </div>
                        <p>{{$value->product_qr}}</p>
                </div>
            @endforeach
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
