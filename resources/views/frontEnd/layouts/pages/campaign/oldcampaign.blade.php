<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $generalsetting->name }}</title>
        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" type="image/x-icon" />
        <!-- fot awesome -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/all.css" />
        <link rel="stylesheet"rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
        <!-- core css -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/bootstrap.min.css" />
        <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets/css/toastr.min.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.theme.default.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.carousel.min.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/style.css?v=1.0.2" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/responsive.css?v=1.0.1" />
        @foreach($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
          !function(f,b,e,v,n,t,s)
          {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
          n.callMethod.apply(n,arguments):n.queue.push(arguments)};
          if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
          n.queue=[];t=b.createElement(e);t.async=!0;
          t.src=v;s=b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t,s)}(window, document,'script',
          'https://connect.facebook.net/en_US/fbevents.js');
          fbq('init', '{{{$pixel->code}}}');
          fbq('track', 'PageView');
        </script>
        <noscript>
          <img height="1" width="1" style="display:none" 
               src="https://www.facebook.com/tr?id={{{$pixel->code}}}&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
        @endforeach
        
        <meta name="app-url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta name="robots" content="index, follow" />
        <meta name="description" content="{{$campaign_data->short_description}}" />
        <meta name="keywords" content="{{ $campaign_data->slug }}" />
        
        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product" />
        <meta name="twitter:site" content="{{$campaign_data->name}}" />
        <meta name="twitter:title" content="{{$campaign_data->name}}" />
        <meta name="twitter:description" content="{{ $campaign_data->short_description}}" />
        <meta name="twitter:creator" content="" />
        <meta property="og:url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta name="twitter:image" content="{{asset($campaign_data->banner)}}" />
        
        <!-- Open Graph data -->
        <meta property="og:title" content="{{$campaign_data->name}}" />
        <meta property="og:type" content="product" />
        <meta property="og:url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta property="og:image" content="{{asset($campaign_data->banner)}}" />
        <meta property="og:description" content="{{ $campaign_data->short_description}}" />
        <meta property="og:site_name" content="{{$campaign_data->name}}" />
    </head>

    <body>
         @php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal=str_replace(',','',$subtotal);
            $subtotal=str_replace('.00', '',$subtotal);
            $shipping = Session::get('shipping')?Session::get('shipping'):0;
        @endphp

        <section class="banner-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        <div class="campaign_banner">
                            <div class="banner_title">
                                <h2>{{$campaign_data->main_title}}</h2>
                            </div>
                            <div class="banner-img">
                                <img src="{{asset($campaign_data->banner)}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- banner section end -->

        <!-- short-desctiption section start -->
        <section class="short-des">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-8">
                        <div class="short-des-title">
                            {!! $campaign_data->banner_title !!}
                        </div>
                        <div class="ord_btn">
                            <a href="#order_form" class="order_place"> অর্ডার করুন <i class="fa-solid fa-arrow-down"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- short-desctiption section end -->

        <!-- desctiption section start -->
        <section class="description-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="description-inner">
                            <div class="description-title">
                                <h2>{{$campaign_data->description_title1}}</h2>
                            </div>
                            <div class="main-description">
                                {!! $campaign_data->description1 !!}
                            </div>
                        </div>
                        <div class="ord_btn mt-5">
                            <a href="#order_form" class="order_place"> অর্ডার করুন <i class="fa-solid fa-arrow-down"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- desctiption section end -->

        <!-- desctiption section start -->
        <section class="whychoose-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="whychoose-inner">
                            <div class="whychoose-title">
                                <h2>{!! $campaign_data->description_title2 !!}</h2>
                            </div>
                            <div class="main-whychoose">
                                {!! $campaign_data->description2 !!}
                            </div>
                        </div>
                        <div class="ord_btn my-5">
                            <a href="#order_form" class="order_place"> অর্ডার করুন <i class="fa-solid fa-arrow-down"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- desctiption section end -->

         <!-- review section start -->
         @if($campaign_data->images)
         <section class="review-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="rev_inn">
                            <div class="rev_title">
                                <h2>{{$campaign_data->review_title}}</h2>
                            </div>
                            <div class="review_slider owl-carousel">
                            @foreach($campaign_data->images as $key=>$value)
                            <div class="review_item">
                                <img src="{{asset($value->image)}}" alt="">
                            </div>
                            @endforeach
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        <!-- review section end -->

        <!-- offer price form end -->
        <section class="price-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="offer_price">
                            <div class="offer_title">
                                <h2>অফারটি সীমিত সময়ের জন্য, তাই অফার শেষ হওয়ার আগেই অর্ডার করুন</h2>
                            </div>
                            <div class="product-price">
                                <h2>
                                    @if($product->old_price)
                                    <p class="old_price"> আগের দাম : <del> {{$product->old_price}}</del> /=</p>
                                    @endif
                                    <p>বর্তমান দাম {{$product->new_price}}/=</p>
                                </h2>
                                
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
                        <div class="row order_by">
                            <div class="col-sm-5">
                                <div class="checkout-shipping" id="order_form">
                                    <form action="{{route('customer.ordersave')}}" method="POST" data-parsley-validate="">
                                    @csrf
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="potro_font">👇 অর্ডারটি কনফার্ম করতে আপনার ইনফরমেশন দিন  </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="name">আপনার নাম লিখুন * </label>
                                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" required>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!-- col-end -->
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="phone">আপনার মোবাইল লিখুন *</label>
                                                        <input type="number" minlength="11" id="number" maxlength="11" pattern="0[0-9]+" title="please enter number only and 0 must first character" title="Please enter an 11-digit number." id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{old('phone')}}" required>
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!-- col-end -->
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="address">আপনার ঠিকানা লিখুন   *</label>
                                                        <input type="address" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{old('address')}}"  required>
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="area">আপনার এরিয়া সিলেক্ট করুন  *</label>
                                                        <select type="area" id="area" class="form-control @error('area') is-invalid @enderror" name="area"   required>
                                                            @foreach($shippingcharge as $key=>$value)
                                                            <option value="{{$value->id}}">{{$value->name}}</option>
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
                                                
                                                <div class="col-sm-12">
                                                    <div class="form-group text-center">
                                                        <button class="order_place confirm_order" type="submit">অর্ডার কন্ফার্ম করুন </button>
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
                                <div class="cart_details">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="potro_font">পণ্যের বিবরণ </h5>
                                        </div>
                                        <div class="card-body cartlist  table-responsive">
                                            <table class="cart_table table table-bordered table-striped text-center mb-0">
                                                <thead>
                                                   <tr>
                                                     {{-- <th style="width: 20%;">ডিলিট</th>--}}
                                                      <th style="width: 50%;">প্রোডাক্ট</th>
                                                      <th style="width: 25%;">পরিমাণ</th>
                                                      <th style="width: 25%;">মূল্য</th>
                                                     </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach(Cart::instance('shopping')->content() as $value)
                                                    <tr>
                                                       {{-- <td>
                                                            <a class="cart_remove" data-id="{{$value->rowId}}"><i class="fas fa-trash text-danger"></i></a>
                                                        </td>--}}
                                                        <td class="text-left">
                                                             <a style="font-size: 14px;" href="{{route('product',$value->options->slug)}}"><img src="{{asset($value->options->image)}}" height="50" width="50"> {{Str::limit($value->name,20)}}</a>
                                                        </td>
                                                        <td width="15%" class="cart_qty">
                                                            <div class="qty-cart vcart-qty">
                                                                <div class="quantity">
                                                                    <button class="minus cart_decrement"  data-id="{{$value->rowId}}">-</button>
                                                                    <input type="text" value="{{$value->qty}}" readonly />
                                                                    <button class="plus  cart_increment" data-id="{{$value->rowId}}">+</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>৳{{$value->price*$value->qty}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                     <tr>
                                                      <th colspan="2" class="text-end px-4">মোট</th>
                                                      <td>
                                                       <span id="net_total"><span class="alinur">৳ </span><strong>{{$subtotal}}</strong></span>
                                                      </td>
                                                     </tr>
                                                     <tr>
                                                      <th colspan="2" class="text-end px-4">ডেলিভারি চার্জ</th>
                                                      <td>
                                                       <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong>{{$shipping}}</strong></span>
                                                      </td>
                                                     </tr>
                                                     <tr>
                                                      <th colspan="2" class="text-end px-4">সর্বমোট</th>
                                                      <td>
                                                       <span id="grand_total"><span class="alinur">৳ </span><strong>{{$subtotal+$shipping}}</strong></span>
                                                      </td>
                                                     </tr>
                                                    </tfoot>
                                            </table>

                                        </div>
                                    </div>
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
    <!--===-->
       <div class="social__icons">
            <a class="message_i"  data-bs-toggle="tooltip" data-bs-placement="left"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="Contact Us" ><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background-color:new -496 507.7 54 54;" xml:space="preserve"><style type="text/css">.chaty-sts4-0{fill: #ffffff;}.chaty-st0{fill: #808080;}</style><g><circle cx="-469" cy="534.7" r="27" fill="#0071dc"></circle></g><path class="chaty-sts4-0" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"></path><path class="chaty-st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z"></path><path class="chaty-st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z"></path></svg></a>
            <a class="cros_i"  data-bs-toggle="tooltip" data-bs-placement="left"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="Hide"  style="display:none;" title="Hide"><i class="fa-solid fa-xmark" ></i></a>
        </div>
    
        <div class="social__icons_list">
             <li><a href="tel:{{$contact->hotline}}" class="call_bg"><i class="fa-solid fa-phone"></i></a></li>
             <li><a href="{{$contact->whatsapp}}" class="whatsapp_bg" target="_blank"><i class="fa-brands fa-whatsapp whatsapp_menu"></i></a></li>
             <li><a href="{{$contact->messanger}}" target="_blank"><i class="fa-brands fa-facebook-messenger"></i></a></li>
        </div>
    <!--=======-->
        <!--<script src="{{ asset('public/frontEnd/campaign/js') }}/jquery-2.1.4.min.js"></script>-->
        
        <script src="{{ asset('public/frontEnd/campaign/js') }}/jquery-3.6.3.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/all.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/popper.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/bootstrap.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/owl.carousel.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/select2.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/script.js"></script>
        <script src="{{asset('public/backEnd/')}}/assets/js/toastr.min.js"></script>
        {!! Toastr::message() !!} 
         <script>
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            
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
      
        <!-- bootstrap js -->
        <script>
            $(document).ready(function () {
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
             $("#area").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "{{route('shipping.landing')}}",
                    dataType: "html",
                    success: function(response){
                        $('.cartlist').html(response);
                    }
                });
            });
        </script>
           <script>
            $(".cart_remove").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.remove_bn')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        },
                    });
                }
            });
            $(".cart_increment").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.increment_bn')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        },
                    });
                }
            });

            $(".cart_decrement").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.decrement_bn')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        },
                    });
                }
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
      
    </body>
</html>
