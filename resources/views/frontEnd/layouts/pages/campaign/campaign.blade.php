<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $generalsetting->name }}</title>
    <link rel="shortcut icon" href="{{ asset($generalsetting->favicon) }}" type="image/x-icon" />
    <!-- fot awesome -->
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/all.css" />
    <!-- core css -->
    <link href="{{ asset('public/backEnd/') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/animate.css" />

    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/assets/css/toastr.min.css" />
    <!-- owl carousel -->
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.theme.default.css" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.carousel.min.css" />
    <!-- owl carousel -->
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/select2.min.css" />
    <!-- common css -->
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/style.css?v=1.0.6" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/responsive.css?v=1.0.7" />
    @foreach ($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $pixel->code }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $pixel->code }}&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
    @endforeach

    <meta name="app-url" content="{{ route('campaign', $campaign_data->slug) }}" />
    <meta name="robots" content="index, follow" />
    <meta name="description" content="{{ $campaign_data->meta_description }}" />
    <meta name="keywords" content="{{ $campaign_data->slug }}" />

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product" />
    <meta name="twitter:site" content="{{ $campaign_data->name }}" />
    <meta name="twitter:title" content="{{ $campaign_data->name }}" />
    <meta name="twitter:description" content="{{ $campaign_data->meta_description }}" />
    <meta name="twitter:creator" content="ekotatrade.com" />
    <meta property="og:url" content="{{ route('campaign', $campaign_data->slug) }}" />
    <meta name="twitter:image" content="{{ asset($campaign_data->image_one) }}" />

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $campaign_data->name }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('campaign', $campaign_data->slug) }}" />
    <meta property="og:image" content="{{ asset($campaign_data->banner) }}" />
    <meta property="og:description" content="{{ $campaign_data->meta_description }}" />
    <meta property="og:site_name" content="{{ $campaign_data->name }}" />
</head>

<body>
    @php
        $cartitem = Cart::instance('shopping')->content()->count();
        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
    @endphp

    <section
        style="background: url('{{ asset($campaign_data->banner) }}'); background-repeat: no-repeat; background-size:contain; background-position: center;">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="campaign_image">
                        <div class="campaign_item">
                            <div class="banner_t">
                                @if ($campaign_data->banner_title)
                                    <h2>{{ $campaign_data->banner_title }}</h2>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="text-center mt-3">
        <a href="#order_form" class="cam_order_now"><i class="fa-solid fa-cart-shopping"></i>
            অর্ডার করুন </a>
    </div>
    

    <section class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="cont_inner">
                        <div class="cont_num">
                            <h2>বিস্তারিত জানতে কল করুন</h2>
                            <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                        </div>
                        <div class="discount_inn">
                            <h2>
                                @if ($product->old_price)
                                    <del>{{ $campaign_data->name }} এর আগের দাম {{ $product->old_price }}/=</del>
                                @endif
                                <p>{{ $campaign_data->name }} এর বর্তমান দাম {{ $product->new_price }}/=</p>
                            </h2>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rules_sec">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="rules_inner">

                        <div class="rules_item">
                            <div class="rules_head">
                                <h2>{{ $campaign_data->necessity_title }}</h2>
                                <div class="rules_des">
                                    {!! $campaign_data->short_description !!}
                                </div>
                            </div>
                        </div>
                        <div class="rules_item">
                            <div class="rules_head">
                                <h2>{{ $campaign_data->rules }}</h2>
                                <div class="rules_des">
                                    {!! $campaign_data->rules_description !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="campro_inn">
                        @if($campaign_data->pro_title)
                        <div class="campro_head">
                            <h2>{{ $campaign_data->pro_title }}</h2>
                        </div>
                        @endif
                        @if($campaign_data->image_one || $campaign_data->image_two || $campaign_data->image_three)
                        <div class="campro_img_slider owl-carousel">
                            <div class="campro_img_item">
                                <img src="{{ asset($campaign_data->image_one) }}" alt="">
                            </div>
                            <div class="campro_img_item">
                                <img src="{{ asset($campaign_data->image_two) }}" alt="">
                            </div>
                            <div class="campro_img_item">
                                <img src="{{ asset($campaign_data->image_three) }}" alt="">
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-12">
                            <div class="ord_btn">
                                <a href="#order_form" class="cam_order_now"> অর্ডার করতে ক্লিক
                                    করুন </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="why_choose_sec">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="why_choose_inn">
                        <div class="why_choose">
                            <div class="why_choose_head">
                                <h2>আমাদের উপর কেন আস্থা রাখবেন ?</h2>
                            </div>
                            <div class="why_choose_midd">
                                <div class="why_choose_widget">
                                    {!! $campaign_data->description !!}
                                </div>
                                <div class="why_choose_widget">
                                    <div class="why_img">
                                        <img src="{{ asset($campaign_data->whychoose_img) }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="rev_inn">
                        <div class="border_line review-title">
                            <h3>{{ $campaign_data->review }}</h3>
                        </div>
                        <div class="review_slider owl-carousel">
                            @foreach ($campaign_data->images as $key => $value)
                                <div class="review_item">
                                    <img src="{{ asset($value->image) }}" alt="">
                                </div>
                            @endforeach
                        </div>
                        <div class="col-sm-12">
                            <div class="ord_btn">
                                <a href="#order_form" class="cam_order_now"> অর্ডার করতে ক্লিক
                                    করুন </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="form_sec">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form_inn">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2 class="campaign_offer">অফারটি সীমিত সময়ের জন্য, তাই অফার শেষ হওয়ার আগেই অর্ডার করুন</h2>
                                </div>
                            </div>
                            <div class="row order_by">

                                <div class="col-sm-5 cus-order-2">
                                    <div class="checkout-shipping" >
                                        <form action="{{ route('customer.ordersave') }}" method="POST"
                                            data-parsley-validate="">
                                            @csrf
                                            <input type="hidden" value="campaign" name="order_type">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="potro_font">অর্ডারটি কনফার্ম করতে আপনার ইনফরমেশন দিন
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group mb-4">
                                                                <label for="name">আপনার নাম লিখুন * </label>
                                                                <input type="text" id="name"
                                                                    class="form-control @error('name') is-invalid @enderror"
                                                                    name="name" value="{{ old('name') }}"
                                                                    required>
                                                                @error('name')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <!-- col-end -->
                                                        <div class="col-sm-12 {{ $smsgatewayinfo->landing_otp == 1 ? 'd-flex gap-2' : '' }} align-items-end ">
                                                            <div class="form-group mb-4">
                                                                <label for="phone">মোবাইল নাম্বার লিখুন
                                                                    *</label>
                                                                <input type="text" minlength="11" id="number"
                                                                    maxlength="11" pattern="0[0-9]+"
                                                                    title="please enter number only and 0 must first character"
                                                                    title="Please enter an 11-digit number."
                                                                    id="phone"
                                                                    class="form-control @error('phone') is-invalid @enderror"
                                                                    name="phone" value="{{ old('phone') }}"
                                                                    required>
                                                                @error('phone')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                            @if ($smsgatewayinfo->landing_otp == 1)
                                                                <div class="form-group col-4 mb-4">
                                                                    <button
                                                                        class="btn btn-theme text-white w-100 send_otp"
                                                                        type="button" disabled>ভেরিফাই</button>
                                                                    <p id="resendText"></p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <!-- col-end -->
                                                        @if ($smsgatewayinfo->landing_otp == 1)
                                                            <div class="col-sm-12">

                                                                <div class="form-group mb-4">
                                                                    <label for="otp"> ভেরিফিকেশন কোড </label>
                                                                    <input type="otp" id="otp"
                                                                        minlength="4"
                                                                        class="form-control @error('otp') is-invalid @enderror"
                                                                        placeholder="ভেরিফিকেশন কোড" name="otp"
                                                                        value="{{ old('otp') }}" required>
                                                                    @error('otp')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <!-- col-end -->
                                                        @endif
                                                        <!-- col-end -->
                                                        <div class="col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="address">আপনার ঠিকানা লিখুন *</label>
                                                                <input type="address" id="address"
                                                                    class="form-control @error('address') is-invalid @enderror"
                                                                    name="address" value="{{ old('address') }}"
                                                                    required>
                                                                @error('email')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>


                                                        <div class="col-sm-12">
                                                            <div class="form-group mb-3">
                                                                <label for="area">আপনার এরিয়া সিলেক্ট করুন
                                                                    *</label>
                                                                <select type="area" id="area"
                                                                    class="form-control @error('area') is-invalid @enderror"
                                                                    name="area" required>
                                                                    @foreach ($shippingcharge as $key => $value)
                                                                        <option value="{{ $value->id }}">
                                                                            {{ $value->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('email')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <!-- col-end -->
                                                        @if ($productcolors->count() > 0)
                                                            <div class="pro-color d-none d-sm-block"
                                                                style="width: 100%;">
                                                                <p>Select Color -</p>
                                                                <div class="color_inner">
                                                                    
                                                                    <div class="size-container">
                                                                        <div class="selector">
                                                                            @foreach ($productcolors as $key => $procolor)
                                                                                <div class="selector-item color-item"
                                                                                    data-id="{{ $key }}">
                                                                                    <input type="radio"
                                                                                        id="fc-option{{ $procolor->color }}"
                                                                                        value="{{ $procolor->color }}"
                                                                                        name="product_color"
                                                                                        class="selector-item_radio emptyalert stock_color stock_check"
                                                                                        required
                                                                                        data-color="{{ $procolor->color }}" />
                                                                                    <label data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="{{ $procolor->color ?? '' }}"
                                                                                        for="fc-option{{ $procolor->color }}"
                                                                                        class="selector-item_label">
                                                                                        <img
                                                                                            src="{{ asset($procolor->image ?? '') }}">
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($productsizes->count() > 0)
                                                            <div class="pro-size d-none d-sm-block"
                                                                style="width: 100%;">
                                                                    <p>Select Size - <span class="attibute-name"></span>
                                                                    </p>
                                                                <div class="size_inner">
                                                                    <div class="size-container">
                                                                        <div class="selector">
                                                                            @foreach ($productsizes as $index => $prosize)
                                                                                <div class="selector-item" data-id="{{$index}}">
                                                                                    <input type="radio"
                                                                                        id="f-option{{ $prosize->size }}"
                                                                                        value="{{ $prosize->size }}"
                                                                                        name="product_size"
                                                                                        class="selector-item_radio emptyalert stock_size stock_check"
                                                                                        data-size="{{ $prosize->size }}"
                                                                                        required />
                                                                                    <label
                                                                                        for="f-option{{ $prosize->size }}"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="{{ $prosize->size ?? '' }}"
                                                                                        class="selector-item_label">
                                                                                        <img
                                                                                            src="{{ asset($prosize->image ?? '') }}">
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="product-variable-prices" style="display: none;">
                                                            <span
                                                                class="new_price taka-sign before">{{ $subtotal + $shipping }}</span>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="radio_payment">
                                                                <label id="payment_method">Payment Method</label>
                                                                <div class="payment_option">

                                                                </div>
                                                            </div>
                                                            <div class="payment-methods">

                                                                <div class="form-check p_cash">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="payment_method" id="inlineRadio1"
                                                                        value="Cash On Delivery" checked required />
                                                                    <label class="form-check-label"
                                                                        for="inlineRadio1">
                                                                        Cash On Delivery
                                                                    </label>
                                                                </div>
                                                                @if ($bkash_gateway)
                                                                    <div class="form-check p_bkash">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="payment_method" id="inlineRadio2"
                                                                            value="bkash" required />
                                                                        <label class="form-check-label"
                                                                            for="inlineRadio2">
                                                                            Bkash
                                                                        </label>
                                                                    </div>
                                                                @endif

                                                                @if ($shurjopay_gateway)
                                                                    <div class="form-check p_shurjo">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="payment_method" id="inlineRadio3"
                                                                            value="shurjopay" required />
                                                                        <label class="form-check-label"
                                                                            for="inlineRadio3">
                                                                            Shurjopay
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-------------------->
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <button class="order_place confirm_order"
                                                                    type="submit">অর্ডার কনফার্ম করুন </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- card end -->
                                        </form>
                                    </div>
                                </div>
                                <!-- col end -->
                                <div class="col-sm-7 cust-order-1">
                                    <div class="cart_details" id="order_form">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="potro_font">পণ্যের বিবরণ </h5>
                                            </div>
                                            <div class="card-body cartlist ">
                                                @include('frontEnd.layouts.ajax.cart_bn')
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            @php
                                                $couponRoute = Session::get('coupon_used')
                                                    ? route('customer.coupon_remove')
                                                    : route('customer.coupon');
                                                $placeholder = Session::get('coupon_used') ?? 'COUPON CODE';
                                                $submit = Session::get('coupon_used') ? 'Remove' : 'Apply Coupon';
                                            @endphp
                                            @if ($couponcodes > 0 && $cartitem > 0 && $campaign_data->coupon_code == 1)
                                                <div class="coupon-section">
                                                    <div class="coupon-card">
                                                        <form action="{{ $couponRoute }}" method="POST">
                                                            @csrf
                                                            <di class="coupon-row">
                                                                <input id="cpnCode" name="couponcode" placeholder="{{ $placeholder }}" />
                                                                <button type="submit" id="cpnBtn">{{ $submit }}</button>
                                                            </di>
                                                        </form>
                                                        <p id="validTill"></p>
                                                        <div class="circle1"></div>
                                                        <div class="circle2"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        @if ($productcolors->count() > 0)
                                            <div class="pro-color d-block d-sm-none" style="width: 100%;">
                                                    <p>Select Color -</p>
                                                <div class="color_inner">
                                                    <div class="size-container">
                                                        <div class="selector">
                                                            @foreach ($productcolors as $key => $procolor)
                                                                <div class="selector-item color-item"
                                                                    data-id="{{ $key }}">
                                                                    <input type="radio"
                                                                        id="fc-option{{ $procolor->color }}-mobile"
                                                                        value="{{ $procolor->color }}"
                                                                        name="product_color"
                                                                        class="selector-item_radio emptyalert stock_color stock_check"
                                                                        required
                                                                        data-color="{{ $procolor->color }}" />
                                                                    <label data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="{{ $procolor->color ?? '' }}"
                                                                        for="fc-option{{ $procolor->color }}-mobile"
                                                                        class="selector-item_label">
                                                                        <img
                                                                            src="{{ asset($procolor->image ?? '') }}">
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($productsizes->count() > 0)
                                            <div class="pro-size d-block d-sm-none" style="width: 100%;">
                                                    <p>Select Size - <span class="attibute-name"></span>
                                                    </p>
                                                <div class="size_inner">
                                                    <div class="size-container">
                                                        <div class="selector">
                                                            @foreach ($productsizes as $key => $prosize)
                                                                <div class="selector-item" data-id="{{ $key }}">
                                                                    <input type="radio"
                                                                        id="f-option{{ $prosize->size }}-mobile"
                                                                        value="{{ $prosize->size }}"
                                                                        name="product_size"
                                                                        class="selector-item_radio emptyalert stock_size stock_check"
                                                                        data-size="{{ $prosize->size }}" required />
                                                                    <label for="f-option{{ $prosize->size }}-mobile"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="{{ $prosize->size ?? '' }}"
                                                                        class="selector-item_label">
                                                                        <img src="{{ asset($prosize->image ?? '') }}">
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- col end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="social__icons no-print">
        <a class="message_i" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="custom-tooltip"
            data-bs-title="Contact Us"><svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54"
                style="enable-background-color:new -496 507.7 54 54;" xml:space="preserve">
                <style type="text/css">
                    .chaty-sts4-0 {
                        fill: #ffffff;
                    }

                    .chaty-st0 {
                        fill: #808080;
                    }
                </style>
                <g>
                    <circle cx="-469" cy="534.7" r="27" fill="#0071dc"></circle>
                </g>
                <path class="chaty-sts4-0"
                    d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z">
                </path>
                <path class="chaty-st0"
                    d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z">
                </path>
                <path class="chaty-st0"
                    d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z">
                </path>
            </svg></a>
        <a class="cros_i" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="custom-tooltip"
            data-bs-title="Hide" style="display:none;" title="Hide"><i class="fa-solid fa-xmark"></i></a>
    </div>

    <div class="social__icons_list">
        <li><a href="tel:{{ $contact->hotline }}" class="call_bg"><i class="fa-solid fa-phone"></i></a></li>
        <li><a href="{{ $contact->whatsapp }}" class="whatsapp_bg" target="_blank"><i
                    class="fa-brands fa-whatsapp whatsapp_menu"></i></a></li>
        <li><a href="{{ $contact->messanger }}" target="_blank"><i class="fa-brands fa-facebook-messenger"></i></a>
        </li>
    </div>

    <script src="{{ asset('public/frontEnd/campaign/js') }}/jquery-2.1.4.min.js"></script>
    <script src="{{ asset('public/frontEnd/campaign/js') }}/all.js"></script>
    <script src="{{ asset('public/frontEnd/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/frontEnd/campaign/js') }}/bootstrap.min.js"></script>
    <script src="{{ asset('public/frontEnd/campaign/js') }}/owl.carousel.min.js"></script>
    <script src="{{ asset('public/frontEnd/campaign/js') }}/select2.min.js"></script>
    <script src="{{ asset('public/frontEnd/campaign/js') }}/script.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/toastr.min.js"></script>
    {!! Toastr::message() !!}
    <!-- bootstrap js -->
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>

    <script>
    $(document).ready(function() {
        $('.selector-item').on('click', function() {
            // Remove 'active' class and opacity from all selector items
            $('.selector-item').removeClass('active').css('opacity', '1');
            
            // Add 'active' class to the clicked item and reduce opacity for others
            $(this).addClass('active').css('opacity', '0.6');
        });
    });
    

        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                margin: 15,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                items: 1,
            });
            $('.owl-nav').remove();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        $("#area").on("change", function() {
            var id = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    id: id
                },
                url: "{{ route('shipping.charge') }}",
                dataType: "html",
                success: function(response) {
                    $('.cartlist').html(response);
                }
            });
        });
    </script>

    <script>
        $('.review_slider').owlCarousel({
            dots: false,
            arrow: false,
            autoplay: true,
            loop: true,
            margin: 10,
            smartSpeed: 1000,
            mouseDrag: true,
            touchDrag: true,
            items: 6,
            responsiveClass: true,
            responsive: {
                300: {
                    items: 1,
                },
                480: {
                    items: 2,
                },
                768: {
                    items: 5,
                },
                1170: {
                    items: 5,
                },
            }
        });
    </script>

    <script>
        $('.campro_img_slider').owlCarousel({
            dots: false,
            arrow: false,
            autoplay: true,
            loop: true,
            margin: 10,
            smartSpeed: 1000,
            mouseDrag: true,
            touchDrag: true,
            items: 3,
            responsiveClass: true,
            responsive: {
                300: {
                    items: 1,
                },
                480: {
                    items: 2,
                },
                768: {
                    items: 3,
                },
                1170: {
                    items: 3,
                },
            }
        });
    </script>

    <script>
        function cart_content() {
            $.ajax({
                type: "GET",
                url: "{{ route('cart.content') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                    } else {
                        $(".cartlist").html(data);
                    }
                },
            });
        }
        $(".stock_check").on("click", function() {
            var color = $(".stock_color:checked").data('color');
            var size = $(".stock_size:checked").data('size');
            var id = {{ $campaign_data->product_id }};
            $('.selector-item').removeClass('active').css('opacity', '1');
            $(this).addClass('active').css('opacity', '0.6');
            if (id) {
                $.ajax({
                    type: "GET",
                    data: {
                        id: id,
                        color: color,
                        size: size
                    },
                    url: "{{ route('campaign.stock_check') }}",
                    dataType: "json",
                    success: function(response) {
                        if (response.status == true) {
                            $('.confirm_order').prop('disabled', false);
                            $(".product-variable-prices").slideDown();
                            var shipping = {{ $shipping }};
                            var new_price = response.new_price;
                            var total = shipping + new_price;
                            $('.new_price').text(total);
                        } else {
                            $('.confirm_order').prop('disabled', true);
                            toastr.error('Stock Out', "Please select another color or size");
                        }
                        cart_content();
                        // return cart_content();
                    }
                });
            }
        });
    </script>
    <script>
        $("#number").on("input", function() {
            var code = $(this).val();
            code = code.replace(/\D/g, '');
            $(this).val(code);

            var isValid = false;
            // Check if the input is a number and has exactly 11 digits
            if (/^\d{11}$/.test(code)) {
                // Check if it starts with one of the allowed prefixes
                if (code.startsWith("013") || code.startsWith("014") ||
                    code.startsWith("015") || code.startsWith("016") ||
                    code.startsWith("017") || code.startsWith("018") ||
                    code.startsWith("019")) {
                    isValid = true;
                }
            }
            console.log('test: ' + isValid);
            if (isValid) {
                $("#number").addClass('border-success');
                $("#number").removeClass('border-danger');
                $(".send_otp").prop('disabled', false);
            } else {
                $("#number").addClass('border-danger');
                $("#number").removeClass('border-success');
                $(".send_otp").prop('disabled', true);
            }
        });

        // send_otp
        $(".send_otp").on("click", function() {
            $(".send_otp").prop('disabled', true);
            var phone = $('#number').val();
            if (phone.length > 10) {
                $.ajax({
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        phone: phone
                    },
                    url: "{{ route('customer.send_otp') }}",
                    success: function(data) {
                        if (data) {
                            toastr.success('Success', 'OTP Sent successfully');
                            $(".send_otp").html('Sent');
                            $(".send_otp").addClass('button-clicked');
                            // Start the countdown timer
                            var countdown = 60; // Adjust the countdown time as needed
                            var timer = setInterval(function() {
                                countdown--;
                                $("#resendText").html("Resend OTP in (" + countdown +
                                    ")");

                                if (countdown <= 0) {
                                    clearInterval(timer);
                                    $(".send_otp").html("Send");
                                    $(".send_otp").removeClass('button-clicked');
                                    $(".send_otp").prop('disabled', false);
                                    remove_otp();
                                }
                            }, 1000);
                        }
                    },
                });
            }
        });

        function remove_otp() {
            $.ajax({
                type: "GET",
                url: "{{ route('customer.remove_otp') }}",
                success: function(response) {
                    if (response) {
                        toastr.success('Success', 'OTP Destroyed Successfully');
                        $("#resendText").html("");
                        $("#otp").val("");
                        $(".confirm_order").prop('disabled', true);
                    } else {

                    }
                },
            });
        }
    </script>
    <script>
        $("#otp").on("input", function() {
            var code = $(this).val();
            if (code.length > 3) {
                $("#otp").addClass('border-success');
                $("#otp").removeClass('border-danger');
                $.ajax({
                    type: "GET",
                    data: {
                        code: code
                    },
                    url: "{{ route('customer.validate_otp') }}",
                    success: function(response) {
                        if (response.status == 'success') {
                            $(".confirm_order").prop('disabled', false);
                            toastr.success('Success', 'OTP Matched successfully');
                        } else {
                            $(".confirm_order").prop('disabled', true);
                        }
                    },
                });
            } else {
                $("#otp").addClass('border-danger');
                $("#otp").removeClass('border-success');
            }
        });
    </script>
    <script>
        $(".message_i").on("click", function() {
            $(".message_i i").addClass("rotate");
            $(this).hide();
            $(".cros_i").show();
            $(".cros_i i").addClass("rotate");
        });

        $(".cros_i").on("click", function() {
            $(".cros_i i").addClass("rotate");
            $(this).hide();
            $(".message_i").show();
            $(".message_i i").addClass("rotate");
        });

        $(".social__icons").on("click", function() {
            $(".social__icons_list").toggleClass("social_menu");
        });
    </script>

</body>

</html>