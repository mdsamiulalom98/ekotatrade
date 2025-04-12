@extends('backEnd.layouts.master')
@section('title', 'Order Edit')
@section('css')
    <style>
        .increment_btn,
        .remove_btn {
            margin-top: -17px;
            margin-bottom: 10px;
        }
    </style>
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    @php
        $paid = $order->payments->isNotEmpty() ? $order->payments->sum('amount') : Session::get('cpaid', 0);
    @endphp

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <form method="post" action="{{ route('admin.order.cart_clear') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger rounded-pill delete-confirm" title="Delete"><i
                                    class="fas fa-trash-alt"></i> Cart Clear</button>
                        </form>
                    </div>
                    <h4 class="page-title">Order Edit</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="product_id" class="form-label">Products *</label>
                                <div class="pos_search">
                                    <input type="text" placeholder="Search Product or Scan Barcode ..." value=""
                                        class="search_click" name="keyword" autofocus />
                                    <button><i data-feather="search"></i></button>
                                </div>
                                <div class="search_result"></div>
                                @error('product_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col end -->
                        <form action="{{ route('admin.order.update') }}" method="POST" class="row pos_form"
                            data-parsley-validate="" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $order->id }}" name="order_id">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th style="width:10%">Image</th>
                                            <th style="width:25%">Name</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:15%">Sell Price</th>
                                            <th style="width:15%">Discount</th>
                                            <th style="width:15%">Sub Total</th>
                                            <th style="width:15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartTable">
                                        @php $product_discount = 0; @endphp
                                        @foreach ($cartinfo as $key => $value)
                                            <tr>
                                                <td><img height="30" src="{{ asset($value->options->image) }}" /></td>
                                                <td>{{ $value->name }}
                                                    @if ($value->options->product_size)
                                                        <p>Size: {{ $value->options->product_size }}</p>
                                                    @endif
                                                    @if ($value->options->product_color)
                                                        <p>Color: {{ $value->options->product_color }}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="qty-cart vcart-qty">
                                                        <div class="quantity">
                                                            <button type="button" class="minus cart_decrement"
                                                                value="{{ $value->qty }}"
                                                                data-id="{{ $value->rowId }}">-</button>
                                                            <input type="number" value="{{ $value->qty }}"
                                                                class="quantity_update" data-id="{{ $value->rowId }}" />
                                                            <button type="button" class="plus cart_increment"
                                                                value="{{ $value->qty }}"
                                                                data-id="{{ $value->rowId }}">+</button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="discount"><input type="number" class="product_price"
                                                        value="{{ $value->price }}" placeholder="0.00"
                                                        data-id="{{ $value->rowId }}" /></td>
                                                <td class="discount"><input type="number" class="product_discount"
                                                        value="{{ $value->options->product_discount }}" placeholder="0.00"
                                                        data-id="{{ $value->rowId }}" /></td>
                                                <td>{{ ($value->price - $value->options->product_discount) * $value->qty }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-xs cart_remove"
                                                        data-id="{{ $value->rowId }}"><i class="fa fa-times"></i></button>
                                                </td>
                                            </tr>
                                            @php
                                                $product_discount += $value->options->product_discount * $value->qty;
                                                Session::put('product_discount', $product_discount);
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- custome address -->
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <input type="number" id="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                placeholder="Customer Number" name="phone"
                                                value="{{ $shippinginfo->phone }}" required>
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <input type="text" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Customer Name" name="name"
                                                value="{{ $shippinginfo->name }}" required>
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
                                            <input type="address" placeholder="Address" id="address"
                                                class="form-control @error('address') is-invalid @enderror" name="address"
                                                value="{{ $shippinginfo->address }}" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label for="" class="form-label">Shipping </label>
                                            <select name="shipping_charge" id="shipping_charge" class="form-control form-select">
                                                <option value="">Select Payment Method</option>
                                                @foreach ($shippingcharge as $shipping)
                                                    <option value="{{ $shipping->id }}">
                                                        {{ $shipping->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label for="area" class="form-label">Shipping Fee*</label>
                                            <input type="number" placeholder="Shipping Fee" id="area"
                                                class="form-control @error('area') is-invalid @enderror" name="area"
                                                value="{{ $order->shipping_charge }}" />
                                            @error('area')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                </div>
                            </div>
                            <!-- cart total -->
                            <div class="col-sm-6">
                                <table class="table table-bordered">
                                    <tbody id="cart_details">
                                        @php
                                            $subtotal = Cart::instance('pos_shopping')->subtotal();
                                            $subtotal = str_replace(',', '', $subtotal);
                                            $subtotal = str_replace('.00', '', $subtotal);
                                            $shipping = Session::get('pos_shipping');
                                            $total_discount =
                                                Session::get('pos_discount') + Session::get('product_discount');
                                        @endphp
                                        <tr>
                                            <td>Sub Total</td>
                                            <td>{{ $subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Fee</td>
                                            <td>{{ $shipping }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td>{{ $total_discount }}</td>
                                        </tr>
                                        <tr>
                                            <td>Paid</td>
                                            <td>{{ $paid }}</td>
                                        </tr>
                                        <tr>
                                            <td>Due</td>
                                            <td>{{ $subtotal + $shipping - ($total_discount + $paid) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <label for="payment_method">Payment Methods</label>
                                <div class="" style="">
                                    @foreach ($payments as $key => $payment)
                                        <input type="hidden" value="{{ $payment->id }}" name="up_payment[]">
                                        <div class="control-group input-group justify-content-between">
                                            <div class="row col-11">
                                                <div class="col-sm-3">
                                                    <div class="form-group mb-3">
                                                        <select name="payment_method[]"
                                                            class="form-control form-select">
                                                            <option value="">Select Payment Method</option>
                                                            @foreach ($paymentmethods as $paymentmethod)
                                                                <option value="{{ $paymentmethod->name }}"
                                                                    {{ $paymentmethod->name == $payment->payment_method ? 'selected' : '' }}>
                                                                    {{ $paymentmethod->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- col-end -->
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="number" name="amount[]"
                                                            value="{{ $payment->amount }}"
                                                            class="form-control payment_amount"
                                                            placeholder="Amount">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="text" name="trx_id[]"
                                                            value="{{ $payment->trx_id }}"
                                                            class="form-control "
                                                            placeholder="Trx ID">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="text" name="sender_number[]"
                                                            value="{{ $payment->sender_number }}"
                                                            class="form-control"
                                                            placeholder="Sender Number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-group-btn">
                                                <a class="btn btn-danger"
                                                    href="{{ route('admin.order.payment_remove', ['id' => $payment->id]) }}">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="clone hide" style="display: none;">
                                    <div class="control-group input-group justify-content-between">
                                        <div class="row col-11">
                                            <div class="col-sm-3">
                                                <div class="form-group mb-3">
                                                    <select name="payment_method[]"
                                                        class="form-control form-select">
                                                        <option value="">Select Payment Method</option>
                                                        @foreach ($paymentmethods as $paymentmethod)
                                                            <option value="{{ $paymentmethod->name }}">
                                                                {{ $paymentmethod->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- col-end -->
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="number" name="amount[]"
                                                        class="form-control payment_amount" placeholder="Amount">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="trx_id[]"
                                                        value="{{ old('trx_id') }}"
                                                        class="form-control "
                                                        placeholder="Trx ID">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="sender_number[]"
                                                        value="{{ old('sender_number') }}"
                                                        class="form-control"
                                                        placeholder="Sender Number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group-btn">
                                            <button class="btn btn-danger btn-danger" type="button"><i
                                                    class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group control-group increment justify-content-between">
                                    <div class="row col-11">
                                        <div class="col-sm-3">
                                            <div class="form-group mb-3">
                                                <select name="payment_method[]" class="form-control form-select">
                                                    <option value="">Select Payment Method</option>
                                                    @foreach ($paymentmethods as $paymentmethod)
                                                        <option value="{{ $paymentmethod->name }}">
                                                            {{ $paymentmethod->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!-- col-end -->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input type="number" name="amount[]"
                                                    class="form-control payment_amount" placeholder="Amount">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input type="text" name="trx_id[]"
                                                    value="{{ old('trx_id') }}"
                                                    class="form-control "
                                                    placeholder="Trx ID">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input type="text" name="sender_number[]"
                                                    value="{{ old('sender_number') }}"
                                                    class="form-control"
                                                    placeholder="Sender Number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-group-btn">
                                        <button class="btn btn-success btn-increment" type="button"><i
                                                class="fa fa-plus"></i></button>
                                    </div>
                                    @error('banner')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div>
                                <input type="submit" class="btn btn-success" value="Update Order" />
                            </div>
                        </form>
                    </div>
                    <!-- end card-body-->
                </div>
                <!-- end card-->
            </div>
            <!-- end col-->
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <!-- Plugins js -->
    <script src="{{ asset('public/backEnd/') }}/assets/libs//summernote/summernote-lite.min.js"></script>
    <script>
        $(".summernote").summernote({
            placeholder: "Enter Your Text Here",
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        function cart_content() {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.order.cart_content') }}",
                dataType: "html",
                success: function(cartinfo) {
                    $('#cartTable').html(cartinfo);
                },
                error: function() {
                    alert('Error occurred while updating cart content');
                }
            });
        }

        function cart_details() {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.order.cart_details') }}",
                dataType: "html",
                success: function(cartinfo) {
                    $('#cart_details').html(cartinfo);
                },
                error: function() {
                    alert('Error occurred while updating cart details');
                }
            });
        }

        function search_clear() {
            var keyword = '';
            $.ajax({
                type: "GET",
                data: {
                    keyword: keyword
                },
                url: "{{ route('admin.livesearch') }}",
                success: function(products) {
                    if (products) {
                        $(".search_result").html(products);
                    } else {
                        $(".search_result").empty();
                    }
                },
            });
        }

        $('.cart_add').on('click', function(e) {
            var id = $(this).data('id');
            if (id) {
                $.ajax({
                    cache: 'false',
                    type: "GET",
                    data: {
                        id: id
                    },
                    url: "{{ route('admin.order.cart_add') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details() + search_clear();
                    },
                    error: function() {
                        alert('Error occurred while adding product to cart');
                    }
                });
            }
        });
        $(document).on('click', '.cart_increment', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var qty = $(this).val();
            if (id) {
                $.ajax({
                    cache: false,
                    data: {
                        id: id,
                        qty: qty
                    },
                    type: "GET",
                    url: "{{ route('admin.order.cart_increment') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    },
                    error: function() {
                        alert('Error occurred while incrementing cart item.');
                    }
                });
            }
        });
        $(document).on('click', '.cart_decrement', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var qty = $(this).val();
            if (id) {
                $.ajax({
                    cache: false,
                    type: "GET",
                    data: {
                        id: id,
                        qty: qty
                    },
                    url: "{{ route('admin.order.cart_decrement') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    },
                    error: function() {
                        alert('Error occurred while decrementing cart item.');
                    }
                });
            }
        });

        // Prevent form submission on Enter key press in quantity_update input
        $(document).on("keypress", ".quantity_update", function(e) {
            if (e.which === 13) {
                e.preventDefault();
            }
        });
        $(document).on("click", ".cart_remove", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            if (id) {
                $.ajax({
                    cache: false,
                    type: "GET",
                    data: {
                        id: id
                    },
                    url: "{{ route('admin.order.cart_remove') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    },
                    error: function() {
                        alert('Error occurred while removing cart item.');
                    }
                });
            }
        });

        // Debounce function to delay execution
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Debounced function for product price input
        const debouncedDiscountPrice = debounce(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var discount = $(this).val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    id: id,
                    discount: discount
                },
                url: "{{ route('admin.order.product_discount') }}",
                dataType: "json",
                success: function(cartinfo) {
                    cart_content();
                    cart_details();
                },
                error: function() {
                    alert('Error occurred while updating product price');
                }
            });
        }, 1500);

        $(document).on("input", ".product_discount", debouncedDiscountPrice);

        const debouncedProductPrice = debounce(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var price = $(this).val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    id: id,
                    price: price
                },
                url: "{{ route('admin.order.product_price') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                },
            });
        }, 1500);

        $(document).on("input", ".product_price", debouncedProductPrice);

        const debouncedShippingAmount = debounce(function(e) {
            e.preventDefault();
            var area = $('#area').val();
            $.ajax({
                type: "GET",
                data: {
                    area: area
                },
                url: "{{ route('admin.order.cart_shipping') }}",
                dataType: "html",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                },
                error: function() {
                    alert('Error occurred while updating shipping fee');
                }
            });
        }, 500);

        $(document).on("input", "#area", debouncedShippingAmount);

        // pshippingfee from total
        $(document).on("change", "#shipping_charge", function(e) {
            e.preventDefault();
            var shipping_charge = $('#shipping_charge').val();
            $.ajax({
                type: "GET",
                data: {
                    shipping_charge: shipping_charge
                },
                url: "{{ route('admin.order.shipping_charge') }}",
                dataType: "html",
                success: function(shipping_charge) {
                    const charge = parseInt(shipping_charge);
                    if (!isNaN(charge)) {
                        console.log(charge);
                        $('#area').val(charge);
                    }
                    return cart_content() + cart_details();
                },
                error: function() {
                    alert('Error occurred while updating shipping fee');
                }
            });
        });

        const debouncedUpdatedQuantity = debounce(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var qty = $(this).val();
            if (id) {
                $.ajax({
                    cache: false,
                    type: "GET",
                    data: {
                        id: id,
                        qty: qty
                    },
                    url: "{{ route('admin.order.quantity_update') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    },
                });
            }
        }, 1500);

        $(document).on("input", ".quantity_update", debouncedUpdatedQuantity);

        // customer details
        const debouncedCustomerPhone = debounce(function(e) {
            e.preventDefault();
            console.log('working');
            var phoneNumber = $('#phone').val();
            if (phoneNumber.length === 11) {
                $.ajax({
                    url: "{{ route('admin.order.customer') }}",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        phone: phoneNumber
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#name').val(response.customer.name);
                            $('#address').val(response.customer.address);
                        }
                    },
                    error: function() {
                        alert('Error occurred while fetching the response');
                    }
                });
                console.log('working');
            }
        },1000);

        $(document).on('input', '#phone', debouncedCustomerPhone);

        $(".cartclear").click(function(e) {
            $.ajax({
                cache: false,
                type: "GET",
                url: "{{ route('admin.order.cart_clear') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                },
                error: function() {
                    alert('Error occurred while clearing cart');
                }
            });
        });
        // pshippingfee from total

        $(document).ready(function() {
            $('.search_click').focus();
        });
        $("#paid").change(function() {
            var amount = $(this).val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'amount': amount
                },
                url: "{{ route('admin.order.paid') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".btn-increment").click(function() {
                var html = $(".clone").html();
                $(".increment").after(html);
            });
            $("body").on("click", ".btn-danger", function() {
                $(this).parents(".control-group").remove();
            });

            function calculateTotal() {
                let total = 0;
                $(".payment_amount").each(function() {
                    let value = parseFloat($(this).val()) || 0; // Convert to number, ignore empty fields
                    total += value;
                });
                $("#total").text(total.toFixed(2)); // Display total with 2 decimal places

                var amount = total;
                $.ajax({
                    cache: false,
                    type: "GET",
                    data: {
                        'amount': amount
                    },
                    url: "{{ route('admin.order.paid') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    }
                });
            }

            let timer;
            $(document).on("input", ".payment_amount", function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    calculateTotal();
                }, 1000);
            });
        });
    </script>
@endsection
