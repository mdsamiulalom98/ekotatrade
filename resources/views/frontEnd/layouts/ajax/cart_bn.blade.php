@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal = str_replace(',', '', $subtotal);
    $subtotal = str_replace('.00', '', $subtotal);
    $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
    $discount = Session::get('discount') ? Session::get('discount') : 0;
@endphp
<style>
    
</style>
<table class="cart_table table table-bordered table-striped text-center mb-0">
    <colgroup>
        <col class="product_col">
        <col style="min-width: 80px;">
        <col class="price_col">
    </colgroup>

    <thead>
        <tr>
            <th style="">প্রোডাক্ট</th>
            <th style="">পরিমাণ</th>
            <th style="">মূল্য</th>
        </tr>
    </thead>

    <tbody>
        @foreach (Cart::instance('shopping')->content() as $value)
            <tr>
                <td>
                   <img src="{{ asset($value->options->image) }}" style="height:30px;width:30px" />
               
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
                            <button class="minus cart_decrement" data-id="{{ $value->rowId }}"><i
                                    class="fe-minus"></i></button>
                            <input type="text" value="{{ $value->qty }}" readonly />
                            <button class="plus cart_increment" data-id="{{ $value->rowId }}"><i
                                    class="fe-plus"></i></button>
                        </div>
                    </div>
                </td>

                <td>৳{{ $value->price * $value->qty }}</td>

            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-end px-4">Total/মোট</th>
            <td>
                <span id="net_total"><span class="alinur">৳ </span><strong>{{ $subtotal }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-end px-4">ডেলিভারি চার্জ</th>
            <td>
                <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong>{{ $shipping }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-end px-4">সর্বমোট</th>
            <td>
                <span id="grand_total"><span class="alinur">৳
                    </span><strong>{{ $subtotal + $shipping }}</strong></span>
            </td>
        </tr>
    </tfoot>
</table>

<script src="{{ asset('public/frontEnd/js/jquery-3.6.3.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js"></script>
<!-- cart js start -->
<script>
    $('.cart_remove').on('click', function() {
        var id = $(this).data('id');
        $("#loading").show();
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('cart.remove_bn') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                        $("#loading").hide();
                    }
                }
            });
        }
    });

    $('.cart_increment').on('click', function() {
        var id = $(this).data('id');
        $("#loading").show();
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('cart.increment_bn') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                        $("#loading").hide();
                    }
                }
            });
        }
    });

    $('.cart_decrement').on('click', function() {
        var id = $(this).data('id');
        $("#loading").show();
        if (id) {
            $.ajax({
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('cart.decrement_bn') }}",
                success: function(data) {
                    if (data) {
                        $(".cartlist").html(data);
                        $("#loading").hide();
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
                    $(".cart_header").html(data);
                } else {
                    $(".cart_header").empty();
                }
            }
        });
    };
</script>
<!-- cart js end -->