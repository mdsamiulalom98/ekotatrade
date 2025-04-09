@extends('frontEnd.layouts.master') @section('title', 'Customer Checkout') @push('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/select2.min.css') }}" />
@endpush @section('content')
    <section class="chheckout-section">
        @php
            $cartitem = Cart::instance('shopping')->content()->count();
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal = str_replace(',', '', $subtotal);
            $subtotal = str_replace('.00', '', $subtotal);
            $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
            $coupon = Session::get('coupon_amount') ?? 0;
        @endphp
        <div class="container">
            <div class="row">
                <div class="col-sm-5 cus-order-2">
                    <div class="checkout-shipping">
                        <form action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate=""
                            onsubmit="disableButton()">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h6>To confirm your order, fill in the details and click on the "Order" button.</h6>

                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="name">Write Your Name *</label>
                                                <input type="text" id="name"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    value="{{ Auth::guard('customer')->user()?->name ?? old('name') }}"
                                                    required />
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
                                                <label for="phone">Phone Number *</label>
                                                <input type="text" minlength="11" id="number" maxlength="11"
                                                    pattern="0[0-9]+"
                                                    title="please enter number only and 0 must first character"
                                                    title="Please enter an 11-digit number." id="phone"
                                                    class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                    value="{{ Auth::guard('customer')->user()?->phone ?? old('phone') }}"
                                                    required />
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
                                                <label for="email">Email</label>
                                                <input type="email" id="number"
                                                    title="please enter number only and 0 must first character" id="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    value="{{ Auth::guard('customer')->user()->email ?? old('email') }}" />
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- col-end -->
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="address">Your Address *</label>
                                                <input type="address" id="address"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    name="address"
                                                    value="{{ Auth::guard('customer')->user()->address ?? old('address') }}"
                                                    required />
                                                @error('address')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group mb-3">
                                                <label for="area">Select Delivery Area *</label>
                                                <select type="area" id="area"
                                                    class="form-control @error('area') is-invalid @enderror" name="area"
                                                    required>
                                                    @foreach ($shippingcharge as $key => $value)
                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('area')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- col-end -->
                                        <div class="col-sm-12">
                                            <div class="radio_payment">
                                                <label id="payment_method">Payment Method</label>
                                                <div class="payment_option">

                                                </div>
                                            </div>
                                            <div class="payment-methods">

                                                <div class="form-check p_cash">
                                                    <input class="form-check-input" type="radio" name="payment_method"
                                                        id="inlineRadio1" value="Cash On Delivery" checked required />
                                                    <label class="form-check-label" for="inlineRadio1">
                                                        Cash On Delivery
                                                    </label>
                                                </div>
                                                @if ($bkash_gateway)
                                                    <div class="form-check p_bkash">
                                                        <input class="form-check-input" type="radio" name="payment_method"
                                                            id="inlineRadio2" value="bkash" required />
                                                        <label class="form-check-label" for="inlineRadio2">
                                                            Bkash
                                                        </label>
                                                    </div>
                                                @endif

                                                @if ($shurjopay_gateway)
                                                    <div class="form-check p_shurjo">
                                                        <input class="form-check-input" type="radio" name="payment_method"
                                                            id="inlineRadio3" value="shurjopay" required />
                                                        <label class="form-check-label" for="inlineRadio3">
                                                            Shurjopay
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-------------------->
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button id="orderButton" class="order_place" type="submit">Order
                                                    Now</button>
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
                    <div class="cart_details table-responsive-sm">
                        <div class="card">
                            <div class="card-header">
                                <h5>Order Data</h5>
                            </div>
                            <div class="card-body cartlist">
                                @include('frontEnd.layouts.ajax.cart')
                            </div>
                        </div>
                    </div>
                    @php
                        $couponRoute = Session::get('coupon_used')
                            ? route('customer.coupon_remove')
                            : route('customer.coupon');
                        $placeholder = Session::get('coupon_used') ?? 'COUPON CODE';
                        $submit = Session::get('coupon_used') ? 'Remove' : 'Apply Coupon';
                    @endphp
                    @if ($couponcodes > 0 && $cartitem > 0)
                        <div class="coupon-section">
                            <div class="coupon-card">
                                <form action="{{ $couponRoute }}" method="POST">
                                    @csrf
                                    <div class="coupon-row">
                                        <input id="cpnCode" name="couponcode" placeholder="{{ $placeholder }}" />
                                        <button type="submit" id="cpnBtn">{{ $submit }}</button>
                                    </div>
                                </form>
                                <p id="validTill"></p>
                                <div class="circle1"></div>
                                <div class="circle2"></div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- col end -->
            </div>
        </div>
    </section>
@endsection @push('script')
    <script src="{{ asset('public/frontEnd/') }}/js/parsley.min.js"></script>
    <script src="{{ asset('public/frontEnd/') }}/js/form-validation.init.js"></script>
    <script src="{{ asset('public/frontEnd/') }}/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2").select2();
        });
        function disableButton() {
            const btn = document.getElementById('orderButton');
            btn.disabled = true;
            btn.innerText = 'Please wait...';

            setTimeout(() => {
                btn.disabled = false;
                btn.innerText = 'Order Now';
            }, 10000); // 10000ms = 10 seconds
        }

    </script>
    <script>
        $("#area").on("change", function () {
            var id = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    id: id
                },
                url: "{{ route('shipping.charge') }}",
                dataType: "html",
                success: function (response) {
                    $(".cartlist").html(response);
                },
            });
        });
    </script>
    <script>
        $("#cpnCode").on("input", function () {
            var code = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    code: code
                },
                url: "{{ route('check.coupon') }}",
                success: function (response) {
                    if (response.status) {
                        let expiryDateStr = response.data.expiry_date;
                        let expiryDate = new Date(expiryDateStr);

                        // Format the date as "10 August, 2024"
                        let formattedDate = expiryDate.toLocaleDateString('en-GB', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });

                        $("#validTill").html('Valid Till: ' + formattedDate);
                    } else {
                        $("#validTill").empty();
                    }
                },
            });
        });
    </script>
    <script type="text/javascript">
        dataLayer.push({
            ecommerce: null
        }); // Clear the previous ecommerce object.
        dataLayer.push({
            event: "view_cart",
            ecommerce: {
                items: [
                    @foreach (Cart::instance('shopping')->content() as $cartInfo)
                                {
                        item_name: "{{ $cartInfo->name }}",
                        item_id: "{{ $cartInfo->id }}",
                        price: "{{ $cartInfo->price }}",
                        item_brand: "{{ $cartInfo->options->brand }}",
                        item_category: "{{ $cartInfo->options->category }}",
                        item_size: "{{ $cartInfo->options->size }}",
                        item_color: "{{ $cartInfo->options->color }}",
                        currency: "BDT",
                        quantity: {{ $cartInfo->qty ?? 0 }}
                                },
                    @endforeach
                        ]
            }
        });
    </script>
    <script type="text/javascript">
        // Clear the previous ecommerce object.
        dataLayer.push({
            ecommerce: null
        });

        // Push the begin_checkout event to dataLayer.
        dataLayer.push({
            event: "begin_checkout",
            ecommerce: {
                items: [
                    @foreach (Cart::instance('shopping')->content() as $cartInfo)
                                {
                        item_name: "{{ $cartInfo->name }}",
                        item_id: "{{ $cartInfo->id }}",
                        price: "{{ $cartInfo->price }}",
                        item_brand: "{{ $cartInfo->options->brands }}",
                        item_category: "{{ $cartInfo->options->category }}",
                        item_size: "{{ $cartInfo->options->size }}",
                        item_color: "{{ $cartInfo->options->color }}",
                        currency: "BDT",
                        quantity: {{ $cartInfo->qty ?? 0 }}
                                },
                    @endforeach
                        ]
            }
        });
    </script>
@endpush
