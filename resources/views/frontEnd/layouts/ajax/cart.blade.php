@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = str_replace(',', '', $subtotal);
    $subtotal = str_replace('.00', '', $subtotal);
    $shipping = Session::get('shipping') ?? 0;
    $discount = Session::get('discount') ?? 0;
    $coupon = Session::get('coupon_amount') ?? 0;
    $discountedProductId = Session::get('discounted_product_id');
@endphp
<table class="cart_table table table-bordered table-striped text-center mb-0">
    <thead>
        <tr>
            <th style="width: 20%;">Delete</th>
            <th style="width: 40%;">Product</th>
            <th style="width: 20%;">Qty</th>
            <th style="width: 20%;">Price</th>
        </tr>
    </thead>

    <tbody>
        @foreach (Cart::instance('shopping')->content() as $value)
            <tr>
                <td>
                    <a class="cart_remove" data-id="{{ $value->rowId }}"><i class="fas fa-trash text-danger"></i></a>
                </td>
                <td class="text-left">
                    <a href="{{ route('product', $value->options->slug) }}"> <img
                            src="{{ asset($value->options->image) }}" style="height: 30px; width: 30px;" />
                        {{ Str::limit($value->name, 20) }}</a>
                    @if ($value->options->product_size)
                        <p>Size: {{ $value->options->product_size }}</p>
                    @endif
                    @if ($value->options->product_color)
                        <p>Color: {{ $value->options->product_color }}</p>
                    @endif
                </td>
                <td class="cart_qty">
                    <div class="qty-cart vcart-qty">
                        <div class="quantity">
                            <button class="minus cart_decrement" data-id="{{ $value->rowId }}" >-</button>
                            <input type="text" value="{{ $value->qty }}" readonly />
                            <button class="plus cart_increment" data-id="{{ $value->rowId }}">+</button>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-end">
                        @if ($discountedProductId == $value->id)
                            <del class="text-black-50 taka-sign before">{{ $value->price * $value->qty }}</del>
                            <strong
                                class="px-1 d-inline-block taka-sign before">{{ $value->price * $value->qty - $coupon }}</strong>
                        @else
                            <strong class="taka-sign before">{{ $value->price * $value->qty }}</strong>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end px-4">Subtotal</th>
            <td class="">
                <span id="net_total" class="d-flex justify-content-end"><span>Tk
                    </span><strong>{{ $subtotal }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4">Delivery Charge (+)</th>
            <td class="">
                <span id="cart_shipping_cost" class="d-flex justify-content-end"><span>Tk
                    </span><strong>{{ $shipping }}</strong></span>
            </td>
        </tr>
        @if (Session::get('coupon_used'))
            <tr>
                <th colspan="3" class="text-end px-4">Coupon Discount (-)</th>
                <td class="">
                    <span id="cart_shipping_cost" class="d-flex justify-content-end"><span>Tk
                        </span><strong>{{ $coupon }}</strong></span>
                </td>
            </tr>
        @endif
        <tr>
            <th colspan="3" class="text-end px-4">Total</th>
            <td class="">
                <span id="grand_total" class="d-flex justify-content-end"><span>Tk
                    </span><strong>{{ $subtotal + $shipping - $coupon }}</strong></span>
            </td>
        </tr>
    </tfoot>
</table>

<script src="{{ asset('public/frontEnd/js/jquery-3.6.3.min.js') }}"></script>
<!-- cart js start -->
<script>
    $('.cart_store').on('click', function() {
        var id = $(this).data('id');
        var qty = $(this).parent().find('input').val();
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id,
                    'qty': qty ? qty : 1
                },
                url: "{{ route('cart.store') }}",
                success: function(data) {
                    if (data) {
                        return cart_count();
                    }
                }
            });
        }
    });

    $('.cart_remove').on('click', function() {
        var id = $(this).data('id');
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('cart.remove') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                        return cart_count();
                    }
                }
            });
        }
    });

    $('.cart_increment').on('click', function() {
        var id = $(this).data('id');
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('cart.increment') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                        return cart_count();
                    }
                }
            });
        }
    });

    $('.cart_decrement').on('click', function() {
        var id = $(this).data('id');
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('cart.decrement') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                        return cart_count();
                    }
                }
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
            }
        });
    };
</script>
<!-- cart js end -->