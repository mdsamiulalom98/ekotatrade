@extends('frontEnd.layouts.master')
@section('title', $details->name)
@push('seo')
    <meta name="app-url" content="{{ route('product', $details->slug) }}" />
    <meta name="robots" content="index, follow" />
    <meta name="description" content="{{ $details->meta_description }}" />
    <meta name="keywords" content="{{ $details->slug }}" />

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{{ $details->name }}" />
    <meta name="twitter:title" content="{{ $details->name }}" />
    <meta name="twitter:description" content="{{ $details->meta_description }}" />
    <meta name="twitter:creator" content="ekotatrade.com.bd" />
    <meta property="og:url" content="{{ route('product', $details->slug) }}" />
    <meta name="twitter:image" content="{{ asset($details->image->image ?? '') }}" />

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $details->name }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('product', $details->slug) }}" />
    <meta property="og:image" content="{{ asset($details->image->image ?? '') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:description" content="{{ $details->meta_description }}" />
    <meta property="og:site_name" content="{{ $details->name }}" />
@endpush

@push('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/zoomsl.css') }}">
@endpush

@section('content')
    <div class="homeproduct main-details-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <section class="product-section">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-6 position-relative">
                                    @if (($details->old_price !== null || ($details->product_type == 2 && isset($details->variable->old_price))) && 
                                     ($details->new_price !== null || ($details->product_type == 2 && isset($details->variable->new_price))))
                                    <div class="product-details-discount-badge">
                                        <div class="sale-badge">
                                            <span class="sale-badge-text">
                                                @php
                                                    // Determine the old and new prices based on product type
                                                    $old_price = ($details->product_type == 2 && isset($details->variable->old_price))
                                                                    ? $details->variable->old_price
                                                                    : $details->old_price;
                                                    $new_price = ($details->product_type == 2 && isset($details->variable->new_price))
                                                                    ? $details->variable->new_price
                                                                    : $details->new_price;
                                
                                                    // Calculate the discount only if $old_price is not null and greater than 0
                                                    if ($old_price !== null && $old_price > 0 && $new_price !== null) {
                                                        $discount = (($old_price - $new_price) / $old_price) * 100;
                                                    } else {
                                                        $discount = 0; // Default discount to 0 if old price is null or invalid
                                                    }
                                                @endphp
                                
                                                @if ($discount > 0)
                                                    <strong>- {{ floor($discount) }}% </strong> Discount
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endif


                                    <!-- variable product image -->
                                    <div class="details_slider owl-carousel">
                                        @foreach ($sliderimages as $key => $value)
                                            <div class="dimage_item">
                                                <img src="{{ asset($value['image']) }}" class="block__pic" />
                                            </div>
                                        @endforeach
                                    </div>
                                    <div
                                        class="indicator_thumb @if ($details->images->count() + $details->variables->count() > 4) thumb_slider owl-carousel @endif">
                                        @foreach ($sliderimages as $key => $value)
                                            <div class="indicator-item" data-id="{{ $key }}">
                                                <img src="{{ asset($value['image']) }}" />
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- normal product image -->
                                </div>
                                <div class="col-sm-6">
                                    <div class="details_right">
                                        <div class="breadcrumb">
                                            <ul>
                                                <li><a href="{{ url('/') }}">Home</a></li>
                                                <li><span>/</span></li>
                                                <li><a
                                                        href="{{ url('/category/' . $details->category->slug) }}">{{ $details->category->name }}</a>
                                                </li>
                                                @if ($details->subcategory)
                                                    <li><span>/</span></li>
                                                    <li><a
                                                            href="#">{{ $details->subcategory ? $details->subcategory->subcategoryName : '' }}</a>
                                                    </li>
                                                    @endif @if ($details->childcategory)
                                                        <li><span>/</span></li>
                                                        <li><a
                                                                href="#">{{ $details->childcategory->childcategoryName }}</a>
                                                        </li>
                                                    @endif
                                            </ul>
                                        </div>

                                        <div class="product">
                                            <div class="product-cart">
                                                <p class="name">{{ $details->name }}</p>
                                                @if ($details->variable_count > 0 && $details->product_type == 2)
                                                    <div class="d-flex align-items-center">
                                                        @foreach ($details->variables as $index => $variable)
                                                            @if ($index > 0)
                                                                <span class="font-weight-bold px-2"> — </span>
                                                            @endif
                                                            <p class="details-price">
                                                                <span
                                                                    class="taka-sign before">{{ $variable->new_price }}</span>
                                                            </p>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="details-price">
                                                        @if ($details->old_price)
                                                            <del class="taka-sign before">{{ $details->old_price }}</del>
                                                        @endif
                                                        <span class="taka-sign before">{{ $details->new_price }}</span>
                                                    </p>
                                                @endif
                                                <div class="details-ratting-wrapper">
                                                    @php
                                                        $averageRating = $details->reviews->avg('ratting');
                                                        $filledStars = floor($averageRating);
                                                        $hasHalfStar = $averageRating - $filledStars >= 0.5;
                                                        $emptyStars = 5 - ($filledStars + ($hasHalfStar ? 1 : 0));
                                                    @endphp

                                                    @if ($averageRating >= 0 && $averageRating <= 5)
                                                        @for ($i = 1; $i <= $filledStars; $i++)
                                                            <i class="fas fa-star"></i>
                                                        @endfor
                                                        @if ($hasHalfStar)
                                                            <i class="fas fa-star-half-alt"></i>
                                                        @endif
                                                        @for ($i = 1; $i <= $emptyStars; $i++)
                                                            <i class="far fa-star"></i>
                                                        @endfor
                                                        <span>({{ $details->reviews->count() }})</span>
                                                    @else
                                                        <span>Invalid rating range</span>
                                                    @endif
                                                    <a class="all-reviews-button" href="#writeReview">See Reviews</a>
                                                </div>
                                                <div class="product-code">
                                                    <p><span>Product Code : </span>{{ $details->product_qr }}</p>
                                                </div>
                                                <div class="custom_list">
                                                    {!! $details->sort_description !!}
                                                </div>
                                                <form action="{{ route('cart.store') }}" method="POST" name="formName">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $details->id }}" />
                                                    @if ($productcolors->count() > 0)
                                                        <div class="pro-color" style="width: 100%;">
                                                            <div class="color_inner">
                                                                <p>Color -</p>
                                                                <div class="size-container">
                                                                    <div class="selector">
                                                                        @foreach ($sliderimages as $key => $procolor)
                                                                            <div class="selector-item color-item"
                                                                                data-id="{{ $key }}"
                                                                                @if (!isset($procolor['color'])) style="display:none" @endif>
                                                                                @if (isset($procolor['color']))
                                                                                    <input type="radio"
                                                                                        id="fc-option{{ $procolor['color'] }}"
                                                                                        value="{{ $procolor['color'] }}"
                                                                                        name="product_color"
                                                                                        class="selector-item_radio emptyalert stock_color stock_check"
                                                                                        required
                                                                                        data-color="{{ $procolor['color'] }}" />

                                                                                    <label
                                                                                        for="fc-option{{ $procolor['color'] }}"
                                                                                        class="selector-item_label"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="{{ $procolor['color'] }}"><img
                                                                                            src="{{ asset($procolor['image'] ?? '') }}">
                                                                                    </label>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($productsizes->count() > 0)
                                                        <div class="pro-size" style="width: 100%;">
                                                            <div class="size_inner">
                                                                <p>Size/Model - <span class="attibute-name"></span></p>
                                                                <div class="size-container">
                                                                    <div class="selector">
                                                                        @foreach ($sliderimages as $key => $prosize)
                                                                            <div class="selector-item size-item"
                                                                                data-id="{{ $key }}"
                                                                                @if (!isset($prosize['size'])) style="display:none" @endif>
                                                                                <input type="radio"
                                                                                    id="f-option{{ $prosize['size'] ?? '' }}"
                                                                                    value="{{ $prosize['size'] ?? '' }}"
                                                                                    name="product_size"
                                                                                    class="selector-item_radio emptyalert stock_size stock_check"
                                                                                    data-size="{{ $prosize['size'] ?? '' }}"
                                                                                    required />
                                                                                <label data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ $prosize['size'] ?? '' }}"
                                                                                    for="f-option{{ $prosize['size'] ?? '' }}"
                                                                                    class="selector-item_label"><img
                                                                                        src="{{ asset($prosize['image'] ?? '') }}"></label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($details->pro_unit)
                                                        <div class="pro_unig">
                                                            <label>Unit: {{ $details->pro_unit }}</label>
                                                            <input type="hidden" name="pro_unit"
                                                                value="{{ $details->pro_unit }}" />
                                                        </div>
                                                    @endif

                                                    @if ($details->brand_id)
                                                        <div class="pro_brand">
                                                            <p>Brand :
                                                                <a
                                                                    href="{{ route('brands', $details->brand->slug ?? '') }}">{{ $details->brand ? $details->brand->name : 'N/A' }}</a>
                                                            </p>
                                                        </div>
                                                    @endif
                                                    <div class="product-variable-prices" style="display: none;">
                                                        @if ($details->variable->old_price ?? '')
                                                            <del> <span
                                                                    class="old_price taka-sign before">{{ $details->variable->old_price ?? '' }}</span></del>
                                                        @endif <span
                                                            class="new_price taka-sign before">{{ $details->variable->new_price ?? '' }}</span>

                                                    </div>
                                                    <div class="row">
                                                        @if (!($details->variables->sum('stock') + $details->stock) < 1)
                                                            <div class="qty-cart col-sm-6">
                                                                <div class="quantity">
                                                                    <span class="minus">-</span>
                                                                    <input type="text" name="qty"
                                                                        value="1" />
                                                                    <span class="plus">+</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="col-sm-6">
                                                            <div class="pro_brand stock">
                                                                @if ($details->variables->sum('stock') + $details->stock < 1)
                                                                    <span class="text-danger font-weight-bold">Out of
                                                                        Stock</span>
                                                                @else
                                                                    @if ($details->product_type == 1)
                                                                        <p><span>Stock : </span>{{ $details->stock }}</p>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if ($details->variables->sum('stock') + $details->stock >= 1)
                                                            <div class="d-flex single_product col-sm-12">
                                                                <input type="submit" class="btn px-4 add_cart_btn"
                                                                       onclick="return sendSuccess();" name="add_cart"
                                                                       value=" Add To Cart  " />
                                                        
                                                                <input type="submit"
                                                                       class="btn px-4 order_now_btn order_now_btn_m"
                                                                       onclick="return sendSuccess();" name="order_now"
                                                                       value="Order Now " />
                                                            </div>
                                                        @endif

                                                    </div>
                                                </form>
                                                <div class="d-flex single_product col-sm-12 mt-2">
                                                    <button data-id="{{ $details->id }}"
                                                        class="hover-zoom wishlist_store" title="Wishlist"><i
                                                            class="fa-regular fa-heart"></i> Add To Wishlist</button>
                                                </div>
                                                <div class="mt-md-2 mt-2">
                                                    <h4 class="font-weight-bold">
                                                        <a class="btn btn-success w-100 call_now_btn"
                                                            href="tel: {{ $contact->hotline }}">
                                                            <i class="fa fa-phone-square"></i>
                                                            {{ $contact->hotline }}
                                                        </a>
                                                    </h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div class="description-nav-wrapper">
        <div class="container">
            <div class="row">

                <div class="col-sm-12">
                    <div class="description-nav">
                        <ul class="desc-nav-ul">
                            <li class="active">
                                <a href="#description" target="_self">Description</a>
                            </li>
                            <li>
                                <a href="#question" target="_self">Questions ({{ $question->count() }})</a>
                            </li>
                            <li>
                                <a href="#writeReview" target="_self">Reviews ({{ $reviews->count() }}) </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="pro_details_area">
        <div class="container">
            <div class="row">
                <div class="col-sm-9">
                    <div class="description tab-content details-action-box custom_list" id="description">
                        <h2>Details</h2>
                        <p>{!! $details->description !!}</p>
                    </div>
                    <section class="details-action-box tab-content" id="question">
                        <div class="section-head">
                            <div class="title">
                                <h2>Questions ({{ $question->count() }})</h2>
                                <p>Have question about this product? Get specific details about this product from expert.
                                </p>
                            </div>
                            <div class="action">
                                <div>
                                    <button type="button" class="details-action-btn question-btn btn-overlay"
                                        data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                        Ask Question
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!---->
                        @if ($question->count() > 0)
                            <div class="customer-review">
                                <div class="row">
                                    @foreach ($question as $key => $que)
                                        <div class="col-sm-12 col-12">
                                            <div class="review-card">
                                                <p class="reviewer_name"><i class="fa-solid fa-file-circle-question"></i>
                                                    {{ $que->name }}</p>
                                                <p class="review_data">{{ $que->created_at->format('d-m-Y') }}</p>

                                                <p class="review_content">{{ $que->comment }}</p>
                                            </div>
                                        </div>
                                        @if ($que->answer !== null)
                                            <div class="col-sm-1 col-1">
                                                <div class="replay_icon">
                                                    <i class="fa-solid fa-arrows-turn-right"></i>
                                                </div>
                                            </div>
                                            <div class="col-sm-11 col-11">
                                                <div class="question-card">
                                                    <p class="reviewer_name"><i class="fa-solid fa-comment-dots"></i> @
                                                        {{ $que->name }}</p>
                                                    <p class="review_data">{{ $que->created_at->format('d-m-Y') }}</p>

                                                    <p class="review_content">{{ $que->answer }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                            </div>
                        @else
                            <div class="empty-content">
                                <i class="fa fa-clipboard-list"></i>
                                <p class="empty-text">This product has no question yet. Be the first one to write a
                                    question.</p>
                            </div>
                        @endif
                        <!---->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">{{ Auth::guard('customer')->user() ? 'Your question' : 'You must Login First' }}</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="insert-review">
                                            @if (Auth::guard('customer')->user())
                                                <form action="{{ route('customer.question') }}" id="review-form"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $details->id }}">

                                                    <div class="form-group">
                                                        <label for="message-text" class="col-form-label">Question:</label>
                                                        <textarea required class="form-control radius-lg" name="comment" id="message-text"></textarea>
                                                        <span id="validation-message" style="color: red;"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="details-review-button" type="submit">Submit
                                                            Question</button>
                                                    </div>

                                                </form>
                                            @else
                                                <form action="{{ route('customer.signin') }}" method="POST" data-parsley-validate="">
                                                    @csrf
                                                    <input type="hidden" name="details" value="question">
                                                    <div class="form-group mb-3">
                                                        <label for="phone">Mobile Number</label>
                                                        <input type="text" id="phone" style="border: 1px solid;border-color: #2377c3;"
                                                            class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                            value="{{ old('phone') }}" required>
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <!-- col-end -->
                                                    <div class="form-group mb-3">
                                                        <label for="password">Password</label>
                                                        <input type="password" id="password" style="border: 1px solid;border-color: #2377c3;"
                                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                                            value="{{ old('password') }}" required>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <!-- col-end -->
                                                   
                                                    <div class="form-group mb-3">
                                                        <button class="submit-btn"> Login </button>
                                                    </div>
                                                    <!-- col-end -->
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="tab-content details-action-box" id="writeReview">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="section-head">
                                        <div class="title">
                                            <h2>Reviews ({{ $reviews->count() }})</h2>
                                            <p>Get specific details about this product from customers who own it.</p>
                                        </div>
                                        <div class="action">
                                            <div>
                                                <button type="button" class="details-action-btn question-btn btn-overlay"
                                                    data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                    Write a review
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                    @if ($reviews->count() > 0)
                                        <div class="customer-review">
                                            <div class="row">
                                                <div class="review-analytics">
                                                    <div class="inner">
                                                        <div class="left">
                                                            <div class="box">
                                                                <div class="count">
                                                                    <h1>{{ $averageRating }}</h1>
                                                                </div>
                                                                <div class="stars">
                                                                    @if ($averageRating >= 0 && $averageRating <= 5)
                                                                        @for ($i = 1; $i <= $filledStars; $i++)
                                                                            <i class="fas fa-star"></i>
                                                                        @endfor
                                                                        @if ($hasHalfStar)
                                                                            <i class="fas fa-star-half-alt"></i>
                                                                        @endif
                                                                        @for ($i = 1; $i <= $emptyStars; $i++)
                                                                            <i class="far fa-star"></i>
                                                                        @endfor
                                                                    @else
                                                                        <span>Invalid rating range</span>
                                                                    @endif
                                                                </div>
                                                                <div class="review">
                                                                    <p>Based on {{ $details->reviews->count() }} reviews
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="right">
                                                            <div class="box">
                                                                @foreach ($ratings as $rating)
                                                                    <div class="item">
                                                                        <p class="title">{{ $rating->ratting }} Star</p>
                                                                        <div class="bar-outer">
                                                                            <div class="bar-inner
                                                                            @if ($rating->ratting == 5) green
                                                                            @elseif($rating->ratting == 4) blue
                                                                            @elseif($rating->ratting == 3) skyblue
                                                                            @elseif($rating->ratting == 2) yellow
                                                                            @else red @endif;"
                                                                                style="width: {{ round($rating->percentage, 2) }}%">
                                                                            </div>
                                                                        </div>
                                                                        <p class="percentage">
                                                                            {{ round($rating->percentage, 2) }}%
                                                                        </p>
                                                                    </div>
                                                                @endforeach

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @foreach ($reviews as $key => $review)
                                                    <div class="review-item">

                                                        <div class="review-card">
                                                            <p class="reviewer_name"><i data-feather="message-square"></i>
                                                                {{ $review->name }}</p>
                                                            <p class="review_data">
                                                                {{ $review->created_at->format('d-m-Y') }}</p>
                                                            <p class="review_star">{!! str_repeat('<i class="fa-solid fa-star"></i>', $review->ratting) !!}</p>
                                                            <p class="review_content">{{ $review->review }}</p>
                                                            @if ($review->images->count() > 0)
                                                                <div class="review-image">
                                                                    @foreach ($review->images as $image)
                                                                        <div class="review-image-item">
                                                                            <img src="{{ asset($image->image) }}"
                                                                                alt="{{ $image->alt_text }}"
                                                                                class="gallery-image"
                                                                                data-full-image="{{ asset($image->image) }}"
                                                                                data-id="{{ $image->id }}"
                                                                                data-review="{{ $image->review_id }}">
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="empty-content">
                                            <i class="fa fa-clipboard-list"></i>
                                            <p class="empty-text">This product has no reviews yet. Be the first one to
                                                write a review.</p>
                                        </div>
                                    @endif
                                    <div class="modal fade" id="exampleModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{ Auth::guard('customer')->user() ? 'Your review' : 'You must Login First' }}</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="insert-review">

                                                        @if (Auth::guard('customer')->user())
                                                            @if ($is_reviewable)
                                                                <form action="{{ route('customer.review') }}"
                                                                    id="review-form" method="POST"
                                                                    enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="product_id"
                                                                        value="{{ $details->id }}">
                                                                    <div class="fz-12 mb-2">
                                                                        <div class="rating">
                                                                            <label title="Excelent">
                                                                                ☆
                                                                                <input required type="radio"
                                                                                    name="ratting" value="5" />
                                                                            </label>
                                                                            <label title="Best">
                                                                                ☆
                                                                                <input required type="radio"
                                                                                    name="ratting" value="4" />
                                                                            </label>
                                                                            <label title="Better">
                                                                                ☆
                                                                                <input required type="radio"
                                                                                    name="ratting" value="3" />
                                                                            </label>
                                                                            <label title="Very Good">
                                                                                ☆
                                                                                <input required type="radio"
                                                                                    name="ratting" value="2" />
                                                                            </label>
                                                                            <label title="Good">
                                                                                ☆
                                                                                <input required type="radio"
                                                                                    name="ratting" value="1" />
                                                                            </label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group mb-3">
                                                                        <label for="message-text"
                                                                            class="col-form-label">Message:</label>
                                                                        <textarea required class="form-control radius-lg" name="review" id="message-text"></textarea>
                                                                        <span id="validation-message"
                                                                            style="color: red;"></span>
                                                                    </div>
                                                                    <div class="variable_product">
                                                                        <!-- Existing content here -->
                                                                        <div class="form-group mb-3">
                                                                            <label for="image">Image *</label>
                                                                            <input type="file" id="image"
                                                                                class="form-control @error('image') is-invalid @enderror"
                                                                                name="image[]">

                                                                            @error('image')
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                        <button type="button"
                                                                            class="increment_btn btn btn-primary"><i
                                                                                class="fa fa-plus"></i></button>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <button class="details-review-button"
                                                                            type="submit">Submit Review</button>
                                                                    </div>

                                                                </form>
                                                            @else
                                                                <button type="button"
                                                                    class="details-action-btn question-btn btn-overlay not-eligable">
                                                                    You Don't Have the Permission to write review
                                                                </button>
                                                            @endif
                                                        @else
                                                            <form action="{{ route('customer.signin') }}" method="POST" data-parsley-validate="">
                                                    @csrf
                                                    <input type="hidden" name="details" value="review">
                                                    <div class="form-group mb-3">
                                                        <label for="phone">Mobile Number</label>
                                                        <input type="text" id="phone" style="border: 1px solid;border-color: #2377c3;"
                                                            class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                            value="{{ old('phone') }}" required>
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <!-- col-end -->
                                                    <div class="form-group mb-3">
                                                        <label for="password">Password</label>
                                                        <input type="password" id="password" style="border: 1px solid;border-color: #2377c3;"
                                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                                            value="{{ old('password') }}" required>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <!-- col-end -->
                                                   
                                                    <div class="form-group mb-3">
                                                        <button class="submit-btn"> Login </button>
                                                    </div>
                                                    <!-- col-end -->
                                                </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">

                    <div class="feature-products">
                        <p>Top Rated Product</p>
                        <div class="feature-products-wrapper">
                            <table>
                                <tbody>
                                    @foreach ($products as $key => $value)
                                        <tr>
                                            <td class="img">
                                                <a href="{{ route('product', $value->slug) }}">
                                                    <img width="50" height="50"
                                                        src="{{ asset($value->image ? $value->image->image : '') }}"
                                                        alt="" />
                                                </a>
                                            </td>
                                            <td class="title">
                                                <a href="{{ route('product', $value->slug) }}" class="text-dark">
                                                    {{ Illuminate\Support\Str::limit($value->name, 50, '...') }}
                                                </a>
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

    <section class="related-product-section">
        <div class="container">
            <div class="row">
                <div class="related-title">
                    <h5>Related Product</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="product-inner owl-carousel related_slider">
                        @foreach ($products as $key => $value)
                            <div class="product_item wist_item">
                                <div class="product_item_inner">

                                    <div class="pro_img">
                                        @if (($value->old_price !== null || ($value->product_type == 2 && isset($value->variable->old_price))) && ($value->new_price !== null || ($value->product_type == 2 && isset($value->variable->new_price))))
                                                @php
                                                    // Determine the old and new prices based on product type
                                                    $old_price = ($value->product_type == 2 && isset($value->variable->old_price))
                                                                    ? $value->variable->old_price
                                                                    : $value->old_price;
                                                    $new_price = ($value->product_type == 2 && isset($value->variable->new_price))
                                                                    ? $value->variable->new_price
                                                                    : $value->new_price;
                                            
                                                    // Calculate the discount only if $old_price is not null and greater than 0
                                                    if ($old_price !== null && $old_price > 0 && $new_price !== null) {
                                                        $discount = (($old_price - $new_price) / $old_price) * 100;
                                                    } else {
                                                        $discount = 0; // Default discount to 0 if old price is null or invalid
                                                    }
                                                @endphp
                                            
                                                @if ($discount > 0)
                                                    <div class="product-labels labels-rectangular">
                                                        <span class="onsale product-label">-{{ number_format($discount, 0) }}%</span>
                                                    </div>
                                                @endif
                                            @endif
                                        <a href="{{ route('product', $value->slug) }}">
                                            <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                                alt="{{ $value->name }}" />
                                        </a>
                                        @if ($value->variables->sum('stock') + $value->stock < 1)
                                            <div class="quick_view_btn">
                                                <a class="stock_out">Stock Out</a>
                                            </div>
                                        @else
                                            <div class="quick_view_btn">
                                                <button data-id="{{ $value->id }}" class="hover-zoom quick_view"
                                                    title="Wishlist"><i class="fa-solid fa-magnifying-glass"></i></button>

                                                <button data-id="{{ $value->id }}" class="hover-zoom wishlist_store"
                                                    title="Wishlist"><i class="fa-regular fa-heart"></i></button>

                                                <button data-id="{{ $value->id }}" class="hover-zoom compare_store"
                                                    title="Compare"><i class="fa-solid fa-retweet"></i></button>

                                                @if ($value->product_type == 2)
                                                    <button class="hover-zoom " title="Add to Cart"><a
                                                            href="{{ route('product', $value->slug) }}"><i
                                                                class="fa-solid fa-bag-shopping"></i></a></button>
                                                @else
                                                    <button data-id="{{ $value->id }}"
                                                        class="hover-zoom addcartbutton" title="Add to Cart"><i
                                                            class="fa-solid fa-bag-shopping"></i></button>
                                                @endif
                                            </div>
                                        @endif

                                    </div>
                                    <div class="pro_des">
                                        <div class="pro_name">
                                            <a
                                                href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
                                        </div>
                                        <div class="pro_ratting">
                                            @php
                                                $averageRating = $value->reviews->avg('ratting');
                                                $filledStars = floor($averageRating);
                                                $hasHalfStar = $averageRating - $filledStars >= 0.5;
                                                $emptyStars = 5 - ($filledStars + ($hasHalfStar ? 1 : 0));
                                            @endphp

                                            @if ($averageRating >= 0 && $averageRating <= 5)
                                                {{-- Display filled stars --}}
                                                @for ($i = 1; $i <= $filledStars; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor

                                                {{-- Display half star if needed --}}
                                                @if ($hasHalfStar)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @endif

                                                {{-- Display empty stars --}}
                                                @for ($i = 1; $i <= $emptyStars; $i++)
                                                    <i class="far fa-star"></i>
                                                @endfor

                                                <span>({{ $value->reviews->count() }})</span>
                                            @else
                                                <span>Invalid rating range</span>
                                            @endif
                                        </div>
                                        <div class="pro_price">
                                            @if ($value->variable_count > 0 && $value->type == 0)
                                                <p>
                                                    @if ($value->variable->old_price)
                                                        <del
                                                            class="taka-sign before">{{ $value->variable->old_price }}</del>
                                                    @endif
                                                    <span
                                                        class="taka-sign before">{{ $value->variable->new_price }}</span>
                                                </p>
                                            @else
                                                <p>
                                                    @if ($value->old_price)
                                                        <del class="taka-sign before">{{ $value->old_price }}</del>
                                                    @endif
                                                    <span class="taka-sign before">{{ $value->new_price }}</span>
                                                </p>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        {{-- <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" src="" alt="Full-size image" class="img-fluid">
                </div>
            </div>
        </div> --}}
    </div>

@endsection
@push('script')
    <script src="{{ asset('public/frontEnd/js/owl.carousel.min.js') }}"></script>

    <script src="{{ asset('public/frontEnd/js/zoomsl.min.js') }}"></script>

    @if (Session::get('details_page') == 'question')
        <script>
            var questionModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
            questionModal.show();
        </script>
        {{ Session::forget('details_page') }}
    @endif

    @if (Session::get('details_page') == 'review')
        <script>
            var reviewModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            reviewModal.show();
        </script>
        {{ Session::forget('details_page') }}
    @endif
    <script>
        $(document).ready(function() {
            $('.gallery-image').click(function() {
                var id = $(this).data("id");
                var review = $(this).data("review");
                if (review) {
                $.ajax({
                    type: "GET",
                    data: {
                        id: id,
                        review: review
                    },
                    url: "{{ route('review.images') }}",
                    success: function(data) {
                        if (data) {
                            $("#imageModal").html('');
                            $("#imageModal").html(data);
                            $('#imageModal').modal('show');
                        }
                    },
                });
            }
            });
        });
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
    </script>
    <script>
        $(document).ready(function() {
            $(".details_slider").owlCarousel({
                margin: 15,
                items: 1,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
            });
            $(".not-eligable").click(function() {
                toastr.error("You are not eligable for this option!!!");
            });

            $(".indicator-item, .color-item, .size-item").on("click", function() {
                var slideIndex = $(this).data("id");
                $(".details_slider").trigger("to.owl.carousel", slideIndex);
            });
        });
    </script>
    <!--Data Layer Start-->
    <script type="text/javascript">
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            ecommerce: null
        });
        dataLayer.push({
            event: "view_item",
            ecommerce: {
                items: [{
                    item_name: "{{ $details->name }}",
                    item_id: "{{ $details->id }}",
                    @if ($details->variable_count > 0 && $details->type == 0)
                        price: "{{ $details->variable->new_price }}",
                    @else
                        price: "{{ $details->new_price }}",
                    @endif
                    item_brand: "{{ $details->brand ? $details->brand->name : '' }}",
                    item_category: "{{ $details->category->name }}",
                    item_variant: "{{ $details->pro_unit }}",
                    currency: "BDT",
                    quantity: {{ $details->stock ?? 0 }}
                }],
                impression: [
                    @foreach ($products as $value)
                        {
                            item_name: "{{ $value->name }}",
                            item_id: "{{ $value->id }}",
                            price: "{{ $value->new_price }}",
                            item_brand: "{{ $details->brand ? $details->brand->name : '' }}",
                            item_category: "{{ $value->category ? $value->category->name : '' }}",
                            item_variant: "{{ $value->pro_unit }}",
                            currency: "BDT",
                            quantity: {{ $value->stock ?? 0 }}
                        },
                    @endforeach
                ]

            }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#add_to_cart').click(function() {
                gtag("event", "add_to_cart", {
                    currency: "BDT",
                    value: "1.5",
                    items: [
                        @foreach (Cart::instance('shopping')->content() as $cartInfo)
                            {
                                item_id: "{{ $details->id }}",
                                item_name: "{{ $details->name }}",
                                price: "{{ $details->new_price }}",
                                currency: "BDT",
                                quantity: {{ $cartInfo->qty ?? 0 }}
                            },
                        @endforeach
                    ]
                });
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#order_now').click(function() {
                gtag("event", "add_to_cart", {
                    currency: "BDT",
                    value: "1.5",
                    items: [
                        @foreach (Cart::instance('shopping')->content() as $cartInfo)
                            {
                                item_id: "{{ $details->id }}",
                                item_name: "{{ $details->name }}",
                                price: "{{ $details->new_price }}",
                                currency: "BDT",
                                quantity: {{ $cartInfo->qty ?? 0 }}
                            },
                        @endforeach
                    ]
                });
            });
        });
    </script>

    <!-- Data Layer End-->
    <script>
        $(document).ready(function() {
            $(".related_slider").owlCarousel({
                margin: 10,
                items: 6,
                loop: true,
                dots: true,
                nav: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 2,
                    },
                    600: {
                        items: 3,
                    },
                    1000: {
                        items: 6,
                        loop: true,
                    },
                },
            });
            // $('.owl-nav').remove();
        });
    </script>
    <script>
        $(document).ready(function() {
            var serialNumber = 1;

            $(".increment_btn").click(function() {
                var html = `
        <div class="increment_control">
            <div class="form-group mb-3">
                <label for="image_${serialNumber}">Image *</label>
                <input type="file" id="image_${serialNumber}"
                       class="form-control @error('image') is-invalid @enderror"
                       name="image[${serialNumber}]">

                @error('image')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="button" class="remove_btn btn btn-danger mb-3"><i class="fa fa-trash"></i></button>
        </div>
        `;

                $(".variable_product").after(html);
                serialNumber++;
            });

            $("body").on("click", ".remove_btn", function() {
                $(this).parents(".increment_control").remove();
                serialNumber--;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".minus").click(function() {
                var $input = $(this).parent().find("input");
                var count = parseInt($input.val()) - 1;
                count = count < 1 ? 1 : count;
                $input.val(count);
                $input.change();
                return false;
            });
            $(".plus").click(function() {
                var $input = $(this).parent().find("input");
                $input.val(parseInt($input.val()) + 1);
                $input.change();
                return false;
            });
        });
    </script>

    <script>
        function sendSuccess() {

            if (document.forms["formName"]["product_color"]) {
                color = document.forms["formName"]["product_color"].value;
                if (color != "") {} else {
                    toastr.error("Please select any color");
                    return false;
                }
            }
            // size validation
            if (document.forms["formName"]["product_size"]) {
                size = document.forms["formName"]["product_size"].value;
                if (size != "") {} else {
                    toastr.error("Please select any size");
                    return false;
                }
            }
        }
    </script>
    <script>
        $(".stock_check").on("click", function() {
            var color = $(".stock_color:checked").data('color');
            var size = $(".stock_size:checked").data('size');
            var id = {{ $details->id }};
            console.log(color);
            if (id) {
                $.ajax({
                    type: "GET",
                    data: {
                        id: id,
                        color: color,
                        size: size
                    },
                    url: "{{ route('stock_check') }}",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            if (response.product.stock > 0) {
                                $('.add_cart_btn').prop('disabled', false);
                                $('.order_now_btn').prop('disabled', false);
                                $(".stock").html('<p><span>Stock : </span>' + response.product.stock +
                                    '</p>');
                            } else {
                                $('.add_cart_btn').prop('disabled', true);
                                $('.order_now_btn').prop('disabled', true);
                                $(".stock").html(
                                    '<span class="text-danger font-weight-bold text-uppercase">Out of Stock</span>'
                                );
                            }
                            $(".product-variable-prices").slideDown();
                            $(".old_price").text(response.product.old_price);
                            $(".new_price").text(response.product.new_price);
                        } else {
                            toastr.error("Please select size/model");
                            $(".stock").empty();
                            // cart button disabled
                            $('.add_cart_btn').prop('disabled', true);
                            $('.order_now_btn').prop('disabled', true);
                        }
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".rating label").click(function() {
                $(".rating label").removeClass("active");
                $(this).addClass("active");
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".thumb_slider").owlCarousel({
                margin: 15,
                items: 4,
                loop: true,
                dots: false,
                nav: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
            });
        });
    </script>

    <script type="text/javascript">
        $(".block__pic").imagezoomsl({
            zoomrange: [3, 3]
        });
    </script>
@endpush