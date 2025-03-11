@extends('frontEnd.layouts.master')
@section('title', 'Warranty Checker')
@push('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/jquery-ui.css') }}">
@endpush
@section('content')
    <section>
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-sm-5">
                    <div class="qr__checker">
                        <div id="qr__check">
                            <p class="auth-title">Check Warranty </p>
                            <form action="{{ route('warranty.checker') }}">
                                <div class="form-group mb-3">
                                    <label for="pro_number">
                                        Enter Your Invoice Number
                                    </label>
                                    <input type="text" id="order_id" class="form-control mt-2"
                                        value="{{ request()->get('order_id') }}" name="order_id"
                                        placeholder="Enter Order Invoice Number">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="pro_number">
                                        Enter Your Product Code
                                    </label>
                                    <input type="text" id="product_id" class="form-control mt-2"
                                        value="{{ request()->get('product_id') }}" name="product_id"
                                        placeholder="Enter Product Code">
                                </div>
                                <button type="submit" class="btn_chcker mt-1">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- produt output -->
            @if (request()->get('product_id'))
                <div class="row justify-content-center mb-5">
                    <div class="col-sm-5">
                        @if ($status == 'success')
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @else
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($product && $order)
                            <div class="form-content">
                                <p class="auth-title">Check Warranty Result</p>
                                <div class="track_info">
                                    <ul>
                                        <li><span>Customer: </span> {{ $order->shipping->name ?? '' }}</li>
                                        <li><span>Invoice ID: </span> {{ $order->invoice_id }}</li>
                                        <li><span>Date:</span>{{ $orderDetail->created_at ?? '' }}</li>
                                        <!--<li><span>Status:</span>  </li>-->
                                    </ul>
                                </div>
                                <table class="table table-bordered tracktable">
                                    <thead>
                                        <th>Product Name</th>
                                        <th>Warranty Remaining</th>
                                        <th>Price</th>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ ($product->product_warranty ?? 0) - ($daysDiff ?? 0) }} days</td>
                                            <td style="text-align:right;">{{ $product->new_price }}</td>
                                        </tr>
                                    </tbody>
                                </table>


                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
