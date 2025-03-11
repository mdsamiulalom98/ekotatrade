<div class="product_item_inner">

    <div class="pro_img">
        @if (
            ($value->old_price !== null || ($value->product_type == 2 && isset($value->variable->old_price))) &&
                ($value->new_price !== null || ($value->product_type == 2 && isset($value->variable->new_price))))
            @php
                // Determine the old and new prices based on product type
                $old_price =
                    $value->product_type == 2 && isset($value->variable->old_price)
                        ? $value->variable->old_price
                        : $value->old_price;
                $new_price =
                    $value->product_type == 2 && isset($value->variable->new_price)
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
            <img src="{{ asset($value->image ? $value->image->image : '') }}" alt="{{ $value->name }}" />
        </a>
        @if ($value->variables->sum('stock') + $value->stock < 1)
            <div class="quick_view_btn">
                <a class="stock_out">Stock Out</a>
            </div>
        @else
            <div class="quick_view_btn">
                <button data-id="{{ $value->id }}" class="hover-zoom quick_view" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Quick View"><i class="fa-solid fa-magnifying-glass"></i></button>

                <button data-id="{{ $value->id }}" class="hover-zoom wishlist_store" title="Wishlist"
                    data-bs-toggle="tooltip" data-bs-placement="top"><i class="fa-regular fa-heart"></i></button>

                <button data-id="{{ $value->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                    class="hover-zoom compare_store" title="Compare"><i class="fa-solid fa-retweet"></i></button>

                @if ($value->product_type == 2)
                    <button class="hover-zoom " title="Add to Cart" data-bs-toggle="tooltip" data-bs-placement="top"><a
                            href="{{ route('product', $value->slug) }}"><i
                                class="fa-solid fa-bag-shopping"></i></a></button>
                @else
                    <button data-id="{{ $value->id }}" class="hover-zoom addcartbutton" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Add to Cart"><i class="fa-solid fa-bag-shopping"></i></button>
                @endif
            </div>
        @endif

    </div>
    <div class="pro_des">
        <div class="pro_name">
            <a href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
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

        <div class="pro_btn">
            @if ($value->variable_count > 0 && $value->type == 0)
                <div class="cart_btn">
                    <a href="{{ route('product', $value->slug) }}" data-id="{{ $value->id }}"
                        class="addcartbutton"><i class="fa-solid fa-shopping-cart"></i> Order Now </a>
                </div>
            @else
                @php
                    $cartItems = Cart::instance('shopping')->content();
                    $productInCart = $cartItems->firstWhere('id', $value->id);
                @endphp
                @if ($productInCart)
                    <div class="qty-cart vcart-qty">
                        <div class="quantity">
                            <button class="minus cart_decrement" data-id="{{ $value->rowId }}">-</button>
                            <input type="text" value="{{ $value->qty }}" readonly />
                            <button class="plus cart_increment" data-id="{{ $value->rowId }}">+</button>
                        </div>
                    </div>
                @else
                    <div class="cart_btn">

                        <button type="submit"><i class="fa-solid fa-shopping-cart"></i> Order Now
                        </button>

                    </div>
                @endif
            @endif
        </div>

    </div>
</div>


<script src="{{ asset('public/frontEnd/js/jquery-3.6.3.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('.wishlistBtn').click(function(e) {
                e.preventDefault();
                let button = $(this);
                let token = $('meta[name="csrf-token"]').attr('content');
                let download = button.data('download');
                let member_id = $(this).data('id');
                let data = {
                    _token: token,
                    member_id: member_id,
                    download: download
                };
                // Add download to data if it's true
                $.ajax({
                    url: '{{ route('cart.store') }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            navigationReload();
                            wishlistReload();
                            $('.wishlistBtn').addClass('red-color');
                        } else if (response.status === 'error') {
                            toastr.error(response.message);
                        } else if (response.status === 'redirect') {
                            toastr.info(response.message);
                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 2000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            });
        });
    </script>
