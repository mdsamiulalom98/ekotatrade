@extends('frontEnd.layouts.master')
@section('title', 'QR Code Checker')
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
                            <p class="auth-title">Check Product </p>
                            <form action="{{ url('product-checker') }}">
                                <label for="pro_number" class="pro_numbers form-label form-control ">
                                    Enter Your Product Code
                                    <input type="text" id="product_id" class="form-control mt-2"
                                        value="{{ request()->get('product_id') }}" name="product_id"
                                        placeholder="Enter Product Code">
                                </label>
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
                        @if ($product)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>This Product is found</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>

                            <div class="card mt-4">
                                <img src="{{ asset($product->image->image) }}" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title"><a
                                            href="{{ route('product', $product->slug) }}">{{ $product->name }}</a></h5>
                                    <p class="card-text">{!! $product->short_description !!}</p>
                                    <a href="{{ route('product', $product->slug) }}" class="btn btn-primary">See More</a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>This Product is Not Found !</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
