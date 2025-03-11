@extends('frontEnd.layouts.master')
@section('title', 'Trusted & Popular Ecommerce Website in Bangladesh')
@push('seo')
    <meta name="app-url" content="" />
    <meta name="robots" content="index, follow" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />

    <!-- Open Graph data -->
    <meta property="og:title" content="" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="{{ asset($generalsetting->white_logo) }}" />
    <meta property="og:description" content="" />
@endpush
@section('content')

    <section class="slider-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="home-slider-container">
                        <div class="main_slider main_sliders owl-carousel">
                            @foreach ($sliders as $key => $value)
                                <div class="slider-item">
                                    <a href="{{ $value->link }}">
                                        <img src="{{ asset($value->image) }}" width="1176" height="400"
                                            alt="slider image {{ $key + 1 }}" />
                                    </a>
                                </div>
                                <!-- slider item -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- marque -->
    <section class="news-headline mb-2 mt-2">
        <div class="container">
            <div class="headline-inner">
                <div class="headline-wrapper">
                    @foreach ($newsticker as $key => $value)
                        <marquee direction="left" style="width: 980px;">
                            <p>{{ $value->title }}</p>
                        </marquee>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- marque -->
    <!-- category section -->
    <div class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="feature-inner owl-carousel">
                        @foreach ($features as $key => $value)
                            <div class="feature-item">
                                <a href="{{ $value->link }}">
                                    <div class="feature-img">
                                        <img src="{{ asset($value->image) }}">
                                    </div>
                                    <p>{{ $value->title }}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="all-category-section mb-2">
        <div class="container">
            <div class="section-header">
                <div class="left">
                    <h2 class="title">Featured Category</h2>
                    <p class="subtitle">Select Your Category</p>
                </div>
                <div class="right">
                    <a href="#" class="show-all-btn">Show all Category</a>
                </div>
            </div>

            <div class="all-category-inner">
                <div class="all-category-wrapper">
                    @foreach ($frontcategory as $category)
                        <div class="all-category-item">
                            <a href="{{ route('category', $category->slug) }}" class="category-link">
                                <img src="{{ asset($category->image) }}" alt="" class="category-image">
                                <div class="category-text">
                                    <span class="category-title">{{ $category->name }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="homeproduct">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="section-header">
                        <div class="left">
                            <h2 class="title">Top Selling Product</h2>
                            <p class="subtitle">Select Your Favourite Product</p>
                        </div>
                        <div class="right">
                            <a href="{{ route('hotdeals') }}" class="show-all-btn">Show all Product</a>
                        </div>

                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="product_slider owl-carousel">
                        @foreach ($hotdeal_top as $key => $value)
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
                                                if ($old_price !== null && $old_price > 0) {
                                                    $discount = (($old_price - $new_price) / $old_price) * 100;
                                                } else {
                                                    $discount = 0; // Default discount to 0 if old price is null or zero
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
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Quick View"><i
                                                        class="fa-solid fa-magnifying-glass"></i></button>

                                                <button data-id="{{ $value->id }}" class="hover-zoom wishlist_store"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Wishlist"><i
                                                        class="fa-regular fa-heart"></i></button>

                                                <button data-id="{{ $value->id }}" class="hover-zoom compare_store"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Compare"><i
                                                        class="fa-solid fa-retweet"></i></button>

                                                @if ($value->product_type == 2)
                                                    <button class="hover-zoom " title="Add to Cart"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"><a
                                                            href="{{ route('product', $value->slug) }}"><i
                                                                class="fa-solid fa-bag-shopping"></i></a></button>
                                                @else
                                                    <button data-id="{{ $value->id }}"
                                                        class="hover-zoom addcartbutton" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Add to Cart"><i
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
                                                        <del>{{ $value->variable->old_price }}Tk</del>
                                                    @endif

                                                    {{ $value->variable->new_price }}Tk

                                                </p>
                                            @else
                                                <p>
                                                    @if ($value->old_price)
                                                        <del>{{ $value->old_price }}Tk</del>
                                                    @endif

                                                    {{ $value->new_price }}Tk

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
    <!-- top sale offer -->
    <section class="footer_top_ads_area">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="section-header">
                        <div class="left">
                            <h2 class="title">Top Selling Offer</h2>
                            <p class="subtitle">Select Your Favourite Product</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="footertop_ads_inner owl-carousel">
                        @foreach ($footertopads as $key => $value)
                            <div class="footertop_ads_item">
                                <a href="{{ $value->link }}">
                                    <img src="{{ asset($value->image) }}" width="585" height="300"
                                        alt="Bottom Banner {{ $key + 1 }}" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @foreach ($homeproducts as $homecat)
        <section class="homeproduct">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="section-header">
                            <div class="left">
                                <h2 class="title">{{ $homecat->name }}</h2>
                                <p class="subtitle">Select Your Favourite Product</p>
                            </div>
                            <div class="right">
                                <a href="{{ route('category', $homecat->slug) }}" class="show-all-btn">Show all
                                    Product</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="product_sliders">
                            @foreach ($homecat->products as $key => $value)
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
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Quick View"><i
                                                            class="fa-solid fa-magnifying-glass"></i></button>

                                                    <button data-id="{{ $value->id }}"
                                                        class="hover-zoom wishlist_store" title="Wishlist"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"><i
                                                            class="fa-regular fa-heart"></i></button>

                                                    <button data-id="{{ $value->id }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" class="hover-zoom compare_store"
                                                        title="Compare"><i class="fa-solid fa-retweet"></i></button>

                                                    @if ($value->product_type == 2)
                                                        <button class="hover-zoom " title="Add to Cart"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"><a
                                                                href="{{ route('product', $value->slug) }}"><i
                                                                    class="fa-solid fa-bag-shopping"></i></a></button>
                                                    @else
                                                        <button data-id="{{ $value->id }}"
                                                            class="hover-zoom addcartbutton" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Add to Cart"><i
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
                                                @if ($value->variable && $value->product_type == 2)
                                                    <p>
                                                        @if ($value->variable->old_price)
                                                            <del>{{ $value->variable->old_price }}Tk</del>
                                                        @endif

                                                        {{ $value->variable->new_price }}Tk
                                                    </p>
                                                @else
                                                    <p>
                                                        @if ($value->old_price)
                                                            <del>{{ $value->old_price }}Tk</del>
                                                        @endif

                                                        {{ $value->new_price }}Tk

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
        </section>
    @endforeach

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ul class="product-track">
                    <li><a href="{{ route('product.checker') }}">Check Product</a></li>
                    <li><a href="{{ route('customer.order_track') }}">Track Order</a></li>
                    <li><a href="{{ route('warranty.checker') }}">Check Warranty</a></li>
                </ul>
            </div>
        </div>
    </div>


    @endsection @push('script')
    <script src="{{ asset('public/frontEnd/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/jquery.syotimer.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(".main_slider").owlCarousel({
                items: 1,
                loop: true,
                dots: 'true',
                autoplay: false,
                nav: false,
                autoplayHoverPause: false,
                margin: 0,
                mouseDrag: true,
                smartSpeed: 8000,
                autoplayTimeout: 3000,
                animateOut: "fadeOutDown",
                animateIn: "slideInDown",

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".hotdeals-slider").owlCarousel({
                margin: 15,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 3,
                        nav: true,
                    },
                    600: {
                        items: 3,
                        nav: false,
                    },
                    1000: {
                        items: 6,
                        nav: true,
                        loop: false,
                    },
                },
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".footertop_ads_inner").owlCarousel({
                margin: 15,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                        nav: true,
                    },
                    600: {
                        items: 2,
                        nav: false,
                    },
                    1000: {
                        items: 3,
                        nav: true,
                        loop: false,
                    },
                },
            });

            $(".product_slider").owlCarousel({
                margin: 15,
                items: 6,
                loop: true,
                dots: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 2,
                        nav: false,
                    },
                    600: {
                        items: 5,
                        nav: false,
                    },
                    1000: {
                        items: 6,
                        nav: false,
                    },
                },
            });
            $(".feature-inner").owlCarousel({
                margin: 15,
                items: 5,
                loop: true,
                dots: false,
                autoplay: true,
                responsive: {
                    0: {
                        items: 4,
                        nav: false,
                    },
                    600: {
                        items: 4,
                        nav: false,
                    },
                    1000: {
                        items: 5,
                        nav: false,
                    },
                },
            });
        });
    </script>

    <script>
        $("#simple_timer").syotimer({
            date: new Date(2015, 0, 1),
            layout: "hms",
            doubleNumbers: false,
            effectType: "opacity",

            periodUnit: "d",
            periodic: true,
            periodInterval: 1,
        });
    </script>
@endpush