@extends('frontEnd.layouts.master')
@section('title','Customer Account')
@section('content')
<section class="customer-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="customer-sidebar">
                    @include('frontEnd.layouts.customer.sidebar')
                </div>
            </div>
            <div class="col-sm-9">
                <div class="customer-content">
                    <h5 class="account-title">My Order</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <colgroup>
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Warranty </th>
                                    <th>Status</th>
                                    <th style="white-space: nowrap;">View Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $key=>$value)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td style="white-space: nowrap;">{{$value->created_at->format('d-m-y')}}</td>
                                    <td style="white-space: nowrap;">Tk{{$value->amount}}</td>
                                    <td style="white-space: nowrap;"><a class="text-primary text-decoration-underline" href="{{ route('warranty.checker') }}?order_id={{$value->invoice_id}}&product_id={{$value->orderdetail->product->product_qr ?? ''}}">{{$value->invoice_id}}</a></td>
                                    <td style="white-space: nowrap;">{{$value->status?$value->status->name:''}}</td>
                                    <td style="white-space: nowrap;">
                                        <a href="{{route('customer.invoice',['id'=>$value->id])}}" class="invoice_btn"><i class="fa-solid fa-eye"></i></a>
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection