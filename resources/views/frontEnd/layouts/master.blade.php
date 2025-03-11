<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title') - {{ $generalsetting->name }}</title>
    <!-- App favicon -->

    <link rel="shortcut icon" href="{{ asset($generalsetting->favicon) }}" alt="Super Ecommerce Favicon" />
    <meta name="author" content="Super Ecommerce" />
    <link rel="canonical" href="" />
    @stack('seo')
    @stack('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/owl.theme.default.min.css') }}" />
    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('public/backEnd/') }}/assets/css/toastr.min.css" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/wsit-menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/style.css?v=1.2.9') }}" />
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/responsive.css?v=1.0.14') }}" />

    <meta name="facebook-domain-verification" content="38f1w8335btoklo88dyfl63ba3st2e" />

    @foreach ($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
            !(function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = "2.0";
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");
            fbq("init", "{{ $pixel->code }}");
            fbq("track", "PageView");
        </script>
        <noscript>
            <img height="1" width="1" style="display: none;"
                src="https://www.facebook.com/tr?id={{ $pixel->code }}&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
    @endforeach

    @foreach ($gtm_code as $gtm)
        <!-- Google tag (gtag.js) -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })
            (window, document, 'script', 'dataLayer', 'GTM-{{ $gtm->code }}');
        </script>
        <!-- End Google Tag Manager -->
    @endforeach
</head>

<body class="gotop">
    @php $subtotal = Cart::instance('shopping')->subtotal(); @endphp
    <div class="mobile-menu no-print">
        <div class="mobile-menu-logo">
            <div class="logo-image">
                <img src="{{ asset($generalsetting->white_logo) }}" alt="" />
            </div>
            <div class="mobile-menu-close">
                <i class="fa fa-times"></i>
            </div>
        </div>
        <ul class="first-nav">
            @foreach ($menucategories as $scategory)
                <li class="parent-category">
                    <a href="{{ url('category/' . $scategory->slug) }}" class="menu-category-name">
                        <img src="{{ asset($scategory->image) }}" alt="" class="side_cat_img" />
                        {{ $scategory->name }}
                    </a>
                    @if ($scategory->subcategories->count() > 0)
                        <span class="menu-category-toggle">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                    @endif
                    <ul class="second-nav" style="display: none;">
                        @foreach ($scategory->subcategories as $subcategory)
                            <li class="parent-subcategory">
                                <a href="{{ url('subcategory/' . $subcategory->slug) }}"
                                    class="menu-subcategory-name">{{ $subcategory->subcategoryName }}</a>
                                @if ($subcategory->childcategories->count() > 0)
                                    <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                                @endif
                                <ul class="third-nav" style="display: none;">
                                    @foreach ($subcategory->childcategories as $childcat)
                                        <li class="childcategory"><a href="{{ url('products/' . $childcat->slug) }}"
                                                class="menu-childcategory-name">{{ $childcat->childcategoryName }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
    <header id="navbar_top" class="no-print">
        <div class="mobile-header">
            <div class="mobile-logo">
                <div class="menu-bar">
                    <a class="toggle">
                        <i class="fa-solid fa-bars"></i>
                    </a>
                </div>
                <div class="menu-logo">
                    <a href="{{ route('home') }}"><img src="{{ asset($generalsetting->white_logo) }}"
                            alt="" /></a>
                </div>
                <div class="menu-bag">
                    <a href="{{ route('customer.checkout') }}" class="margin-shopping">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="mobilecart-qty">{{ Cart::instance('shopping')->count() }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mobile-search">
            <form action="{{ route('search') }}">
                <input type="text" placeholder="Search Product ... " value=""
                    class="msearch_keyword msearch_click" name="keyword" />
                <button><i data-feather="search"></i></button>
            </form>
            <div class="search_result"></div>
        </div>

        <div class="main-header ">
            <!-- header to end -->
            <div class="logo-area">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="logo-header">
                                <div class="main-logo">
                                    <a href="{{ route('home') }}"><img src="{{ asset($generalsetting->white_logo) }}"
                                            alt="" /></a>
                                </div>
                                <div class="main-search">
                                    <form action="{{ route('search') }}">
                                        <input type="text" placeholder="Search Product..."
                                            class="search_keyword search_click" name="keyword" />
                                        <button>
                                            <i data-feather="search"></i>
                                        </button>
                                    </form>
                                    <div class="search_result"></div>
                                </div>
                                <div class="main-menu-link-wrapper">
                                    <ul class="main-menu-link-ul">
                                        <li class="main-menu-li wishlist-qty">
                                            <a href="{{ route('all.product') }}" class="main-menu-link">
                                                <span class="main-menu-span">
                                                    <img src="{{ asset('public/frontEnd/images/shop-icon.png') }}"
                                                        class="shop-icon-image" />
                                                </span>
                                                <span>Shop</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>

                                <div class="main-menu-link-wrapper">
                                    <ul class="main-menu-link-ul">
                                        <li class="main-menu-li" id="compareContent">
                                            <a href="{{ route('hotdeals') }}" class="main-menu-link">
                                                <span class="main-menu-span">
                                                    <i class="fa fa-gifts"></i>
                                                </span>
                                                <span>Offer</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>


                                <div class="main-cart-wrapper">
                                    <div class="main-cart-inner" id="cart-qty">
                                        <a href="{{ route('customer.checkout') }}" class="main-cart-link">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                            <span id="cart-items"
                                                class="count-badge">{{ Cart::instance('shopping')->count() }}</span>

                                            <span>Cart</span>
                                        </a>

                                    </div>
                                </div>


                                <div class="menu-link-box">
                                    <div class="menu-link-inner">
                                        <ul class="menu-link-ul">
                                            <li class="menu-link-list">
                                                <a href="tel:{{ $contact->phone }}" class="menu-item-link">
                                                    <i class="fa-solid fa-phone"></i> <span
                                                        class="menu-link-title">{{ $contact->phone }} <s>Call us
                                                            anytime</s></span>
                                                </a>
                                            </li>
                                            @if (Auth::guard('customer')->user())
                                                <li class="menu-link-list menu-login-item">
                                                    <a class="menu-item-link log_account">
                                                        <i class="fa-regular fa-user"></i><span
                                                            class="menu-link-title">Account<s>Dashboard </s></span>
                                                    </a>
                                                    <div class="log-in-dropdown">
                                                        <ul class="login-dropdown-ul">
                                                            <li class="login-dropdown-li">
                                                                <a href="{{ route('customer.account') }}"
                                                                    class="login-dropdown-link sign-in">
                                                                    <span class="login-dropdown-title">Dashboard</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            @else
                                                <li class="menu-link-list menu-login-item">
                                                    <a class="menu-item-link log_account">
                                                        <i class="fa-regular fa-user"></i>
                                                        <span class="menu-link-title">Account <s>Log in/Register
                                                            </s></span>
                                                    </a>
                                                    <div class="log-in-dropdown">
                                                        <ul class="login-dropdown-ul">
                                                            <li class="login-dropdown-li">
                                                                <a href="{{ route('customer.login') }}"
                                                                    class="login-dropdown-link sign-in">
                                                                    <i class="fa-regular fa-user"></i>
                                                                    <span class="login-dropdown-title">Log in</span>
                                                                </a>
                                                            </li>
                                                            <li class="login-dropdown-li">
                                                                <a class="login-dropdown-link sign-up"
                                                                    href="{{ route('customer.register') }}">
                                                                    <i class="fa-regular fa-user"></i>
                                                                    <span class="login-dropdown-title">Register</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endif

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="menu-area sticky">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="catagory_menu">
                            <ul>
                                @foreach ($menucategories as $scategory)
                                    <li class="cat_bar ">
                                        <a href="{{ url('category/' . $scategory->slug) }}">
                                            <span class="cat_head">{{ $scategory->name }}</span>
                                            @if ($scategory->subcategories->count() > 0)
                                                <i class="fa-solid fa-angle-down cat_down"></i>
                                            @endif
                                        </a>
                                        @if ($scategory->subcategories->count() > 0)
                                            <ul class="Cat_menu">
                                                @foreach ($scategory->subcategories as $subcat)
                                                    <li class="Cat_list cat_list_hover">
                                                        <a href="{{ url('subcategory/' . $subcat->slug) }}">
                                                            <span>{{ Str::limit($subcat->subcategoryName, 25) }}</span>
                                                            @if ($subcat->childcategories->count() > 0)
                                                                <i class="fa-solid fa-chevron-right cat_down"></i>
                                                            @endif
                                                        </a>
                                                        @if ($subcat->childcategories->count() > 0)
                                                            <ul class="child_menu">
                                                                @foreach ($subcat->childcategories as $childcat)
                                                                    <li class="child_main">
                                                                        <a
                                                                            href="{{ url('products/' . $childcat->slug) }}">{{ $childcat->childcategoryName }}</a>

                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- main-header end -->
    </header>
    <div id="content">
        @yield('content')
    </div>
    <!-- content end -->
    <footer>

        <div class="footer-top">
            <div class="container">

                {{-- <div class="row">
                    <div class="footer_supp_number">

                        <div class="widget_item">
                            <div class="widget_icon">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <div class="widget_numb">
                                <h6>Online Orders</h6>
                                <p>{{$contact->hotline}}</p>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="widget_item">
                            <div class="widget_icon">
                                <i class="fa-regular fa-handshake"></i>
                            </div>
                            <div class="widget_numb">
                                <h6>Corporate Deals</h6>
                                <p>{{$contact->hotline}}</p>
                            </div>
                        </div>
                        <div class="widget_item">
                            <div class="widget_icon">
                                <i class="fa-brands fa-slack"></i>
                            </div>
                            <div class="widget_numb">
                                <h6>RMA</h6>
                                <p>{{$contact->hotline}}</p>
                            </div>
                        </div>
                        <div class="widget_item">
                            <div class="widget_icon">
                                <i class="fa-solid fa-wrench"></i>
                            </div>
                            <div class="widget_numb">
                                <h6>Service</h6>
                                <p>{{$contact->hotline}}</p>
                            </div>
                        </div>

                    </div>
                </div> --}}

                <div class="row">
                    <div class="col-sm-12 p-0">
                        <div class="main_footer_inner">

                            <div class="main_footer_widget">
                                <div class="widget_left">
                                    <div class="widget_left_item">
                                        <div class="widget_left_item_i">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="widget_left_item_des">
                                            <h6>Contact Us</h6>
                                            <p><a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></p>
                                        </div>
                                    </div>
                                    <div class="widget_left_item">
                                        <div class="widget_left_item_i">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="widget_left_item_des">
                                            <h6>Email</h6>
                                            <p><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
                                        </div>
                                    </div>
                                    <div class="widget_left_item">
                                        <div class="widget_left_item_i">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                        <div class="widget_left_item_des">
                                            <h6>Branch Time Schedule</h6>
                                            <p>We are open 10: 00 am - 08:00 pm</p>
                                        </div>
                                    </div>
                                    <div class="widget_left_item">
                                        <div class="widget_left_item_i">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="widget_left_item_des">
                                            <h6>Address & Directions</h6>
                                            <p>{{ $contact->address }}</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="main_footer_widget">
                                <div class="widget_middle">
                                    <h4>Important Links</h4>
                                    <ul>
                                        @foreach ($pages as $page)
                                            <li><a
                                                    href="{{ route('page', ['slug' => $page->slug]) }}">{{ $page->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="social_media">
                                        <h4>Follow Us</h4>
                                        <ul>
                                            @foreach ($socialicons as $value)
                                                <li class="mobile-social-list">
                                                    <a class="mobile-social-link" href="{{ $value->link }}">
                                                        <img src="{{ asset($value->image) }}" class="backend-image"
                                                            alt="">
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>
                            </div>
                            <div class="main_footer_widget">
                                <div class="widget_right">
                                    <h4>Payment Partner</h4>
                                    <div class="pay_img">
                                        <img src="{{ asset('public/frontEnd') }}/images/sslcommerz_logo.webp"
                                            alt="">
                                    </div>
                                    <h4>Delivery Partner</h4>
                                    <div class="pay_img">
                                        <img src="{{ asset('public/frontEnd') }}/images/courier-images.png"
                                            alt="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="copyright">
                            <p>Copyright © {{ date('Y') }} {{ $generalsetting->name }}. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="footer_nav">
        <ul>
            <li>
                <a href="{{ route('all.product') }}" class="">
                    <span>
                        <img src="{{ asset('public/frontEnd/images/shop-icon.png') }}" class="shop-icon-image" />
                    </span>
                    <span>Shop</span>
                </a>
            </li>

            <li>
                <a href="{{ route('hotdeals') }}">
                    <span>
                        <i class="fa-solid fa-gifts"></i>
                    </span>
                    <span>Offers</span>
                </a>
            </li>

            <li class="mobile_home">
                <a href="{{ route('home') }}">
                    <span><i class="fa-solid fa-home"></i></span> <span>Home</span>
                </a>
            </li>

            <li>
                <a href="{{ route('customer.checkout') }}">
                    <span>
                        <i class="fa-solid fa-cart-shopping"></i>
                    </span>
                    <span>Cart (<b class="mobilecart-qty">{{ Cart::instance('shopping')->count() }}</b>)</span>
                </a>
            </li>
            @if (Auth::guard('customer')->user())
                <li>
                    <a href="{{ route('customer.account') }}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Account</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('customer.login') }}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Login</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <div class="scrolltop no-print" style="">
        <div class="scroll">
            <i class="fa fa-angle-up"></i>
        </div>
    </div>
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

    <!-- /. fixed sidebar -->

    <div id="custom-modal"></div>
    <div id="page-overlay"></div>
    <div id="loading">
        <div class="custom-loader"></div>
    </div>

    <script src="{{ asset('public/frontEnd/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/mobile-menu.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/wsit-menu.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/mobile-menu-init.js') }}"></script>
    <script src="{{ asset('public/frontEnd/js/wow.min.js') }}"></script>
    <script>
        new WOW().init();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- feather icon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <script>
        feather.replace();
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        document.addEventListener("DOMContentLoaded", function() {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    $('.sticky').addClass('fixed-top');
                    navbar_height = document.querySelector('.navbar').offsetHeight;
                    document.body.style.paddingTop = navbar_height + 'px';
                } else {
                    $('.sticky').removeClass('fixed-top');
                    document.body.style.paddingTop = '0';
                }
            });
        });
    </script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/toastr.min.js"></script>
    {!! Toastr::message() !!} @stack('script')
    <script>
        $(".quick_view").on("click", function() {
            var id = $(this).data("id");
            $("#loading").show();
            if (id) {
                $.ajax({
                    type: "GET",
                    data: {
                        id: id
                    },
                    url: "{{ route('quickview') }}",
                    success: function(data) {
                        if (data) {
                            $("#custom-modal").html(data);
                            $("#custom-modal").show();
                            $("#loading").hide();
                            $("#page-overlay").show();
                        }
                    },
                });
            }
        });
    </script>
    <!-- quick view end -->
    <!-- cart js start -->
    <script>
        $(".addcartbutton").on("click", function() {
            var id = $(this).data("id");
            var qty = 1;
            if (id) {
                $.ajax({
                    cache: "false",
                    type: "GET",
                    url: "{{ url('add-to-cart') }}/" + id + "/" + qty,
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            toastr.success('Success', 'Product add to cart successfully');
                            return cart_count() + mobile_cart();
                        }
                    },
                });
            }
        });
        $(".compare_store").on("click", function() {
            var id = $(this).data("id");
            if (id) {
                $.ajax({
                    cache: "false",
                    type: "GET",
                    url: "{{ url('add-to-compare') }}/" + id,
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            toastr.success('Product add in compare', '');
                            return compare_count();
                        }
                    },
                });
            }
        });

        $(".cart_store").on("click", function() {
            var id = $(this).data("id");
            var qty = $(this).parent().find("input").val();
            if (id) {
                $.ajax({
                    type: "GET",
                    data: {
                        id: id,
                        qty: qty ? qty : 1
                    },
                    url: "{{ route('cart.store') }}",
                    success: function(data) {
                        if (data) {
                            toastr.success('Success', 'Product add to cart succfully');
                            return cart_count() + mobile_cart();
                        }
                    },
                });
            }
        });

        function cart_count() {
            $.ajax({
                type: "GET",
                url: "{{ route('cart.count') }}",
                success: function(data) {
                    if (data) {
                        $("#cart-qty").html(data);
                    } else {
                        $("#cart-qty").empty();
                    }
                },
            });
        }

        function compare_count() {
            $.ajax({
                type: "GET",
                url: "{{ route('compare.count') }}",
                success: function(data) {
                    if (data) {
                        $('#compareContent').html(data);

                    } else {
                        $('#compareContent').empty();

                    }
                },
            });
        }


        $.ajax({
            type: "GET",
            url: "{{ url('compare/content') }}",
            dataType: "html",
            success: function(compareinfo) {

            }
        });


        function mobile_cart() {
            $.ajax({
                type: "GET",
                url: "{{ route('mobile.cart.count') }}",
                success: function(data) {
                    if (data) {
                        $(".mobilecart-qty").html(data);
                    } else {
                        $(".mobilecart-qty").empty();
                    }
                },
            });
        }

        function cart_summary() {
            $.ajax({
                type: "GET",
                url: "{{ route('shipping.charge') }}",
                dataType: "html",
                success: function(response) {
                    $(".cart-summary").html(response);
                },
            });
        }
    </script>
    <!-- cart js end -->
    <script>
        $(".search_click").on("keyup change", function() {
            var keyword = $(".search_keyword").val();
            $.ajax({
                type: "GET",
                data: {
                    keyword: keyword
                },
                url: "{{ route('livesearch') }}",
                success: function(products) {
                    if (products) {
                        $(".search_result").html(products);
                    } else {
                        $(".search_result").empty();
                    }
                },
            });
        });
        $(".msearch_click").on("keyup change", function() {
            var keyword = $(".msearch_keyword").val();
            $.ajax({
                type: "GET",
                data: {
                    keyword: keyword
                },
                url: "{{ route('livesearch') }}",
                success: function(products) {
                    if (products) {
                        $("#loading").hide();
                        $(".search_result").html(products);
                    } else {
                        $(".search_result").empty();
                    }
                },
            });
        });
        // compare
        // get type
        // $('.comparecartbutton').click(function(){
        //     var id = $(this).data('id');
        //     if(id){
        //         $.ajax({
        //             cache: 'false',
        //             type:"GET",
        //             url:"{{ url('add-to-compare') }}/"+id,
        //             dataType: "json",
        //             success: function(compareinfo){
        //                 return compare_content();
        //             }
        //         });
        //     }
        // });
    </script>
    <script>
        $('.wishlist_store').on('click', function() {
            var id = $(this).data('id');
            var qty = 1;
            $("#loading").show();
            if (id) {
                $.ajax({
                    type: "GET",
                    data: {
                        'id': id,
                        'qty': qty ? qty : 1
                    },
                    url: "{{ route('wishlist.store') }}",
                    success: function(response) {
                        if (response) {
                            $("#loading").hide();
                            toastr.success('success', 'Product added in wishlist');
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 401) {
                            // Handle unauthorized response
                            toastr.error('sorry', 'You have to login first');
                            window.location.href = '{{ route('customer.login') }}';
                        } else {
                            // Handle other errors
                        }
                    }
                });
            }
        });

        $('.wishlist_remove').on('click', function() {
            var id = $(this).data('id');
            $("#loading").show();
            if (id) {
                $.ajax({
                    type: "GET",
                    data: {
                        'id': id
                    },
                    url: "{{ route('wishlist.remove') }}",
                    success: function(data) {
                        if (data) {
                            $("#wishlist").html(data);
                            $("#loading").hide();
                            return wishlist_count();
                        }
                    }
                });
            }
        });

        function wishlist_count() {
            $.ajax({
                type: "GET",
                url: "{{ route('wishlist.count') }}",
                success: function(data) {
                    if (data) {
                        $(".wishlist-qty").html(data);
                    } else {
                        $(".wishlist-qty").empty();
                    }
                }
            });
        };
    </script>


    <script>
        $(".district").on("change", function() {
            var id = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    id: id
                },
                url: "{{ route('districts') }}",
                success: function(res) {
                    if (res) {
                        $(".area").empty();
                        $(".area").append('<option value="">Select..</option>');
                        $.each(res, function(key, value) {
                            $(".area").append('<option value="' + key + '" >' + value +
                                "</option>");
                        });
                    } else {
                        $(".area").empty();
                    }
                },
            });
        });
    </script>
    <script>
        $(".toggle").on("click", function() {
            $("#page-overlay").show();
            $(".mobile-menu").addClass("active");
        });

        $("#page-overlay").on("click", function() {
            $("#page-overlay").hide();
            $(".mobile-menu").removeClass("active");
            $(".feature-products").removeClass("active");
        });

        $(".mobile-menu-close").on("click", function() {
            $("#page-overlay").hide();
            $(".mobile-menu").removeClass("active");
        });

        $(".mobile-filter-toggle").on("click", function() {
            $("#page-overlay").show();
            $(".feature-products").addClass("active");
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".parent-category").each(function() {
                const menuCatToggle = $(this).find(".menu-category-toggle");
                const secondNav = $(this).find(".second-nav");

                menuCatToggle.on("click", function() {
                    menuCatToggle.toggleClass("active");
                    secondNav.slideToggle("fast");
                    $(this).closest(".parent-category").toggleClass("active");
                });
            });
            $(".parent-subcategory").each(function() {
                const menuSubcatToggle = $(this).find(".menu-subcategory-toggle");
                const thirdNav = $(this).find(".third-nav");

                menuSubcatToggle.on("click", function() {
                    menuSubcatToggle.toggleClass("active");
                    thirdNav.slideToggle("fast");
                    $(this).closest(".parent-subcategory").toggleClass("active");
                });
            });
        });
    </script>

    <script>
        var menu = new MmenuLight(document.querySelector("#menu"), "all");

        var navigator = menu.navigation({
            selectedClass: "Selected",
            slidingSubmenus: true,
            // theme: 'dark',
            title: "ক্যাটাগরি",
        });

        var drawer = menu.offcanvas({
            // position: 'left'
        });

        //  Open the menu.
        document.querySelector('a[href="#menu"]').addEventListener("click", (evnt) => {
            evnt.preventDefault();
            drawer.open();
        });
    </script>

    <script>
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $(".scrolltop:hidden").stop(true, true).fadeIn();
            } else {
                $(".scrolltop").stop(true, true).fadeOut();
            }
        });
        $(function() {
            $(".scroll").click(function() {
                $("html,body").animate({
                    scrollTop: $(".gotop").offset().top
                }, "1000");
                return false;
            });
        });
    </script>
    <script>
        $(".filter_btn").click(function() {
            $(".filter_sidebar").addClass('active');
            $("body").css("overflow-y", "hidden");
        })
        $(".filter_close").click(function() {
            $(".filter_sidebar").removeClass('active');
            $("body").css("overflow-y", "auto");
        })
    </script>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K29C9BKJ" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

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