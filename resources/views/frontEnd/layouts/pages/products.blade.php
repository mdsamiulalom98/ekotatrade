@extends('frontEnd.layouts.master')
@section('title', 'Shop')
@push('css')
    <link rel="stylesheet" href="{{ asset('public/frontEnd/css/jquery-ui.css') }}" />
@endpush

@section('content')
    <section class="product-section">
        <div class="container">
            <div class="sorting-section">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="category-breadcrumb d-flex align-items-center">
                            <a href="{{ route('home') }}">Home</a>
                            <span>/</span>
                            <strong>Shop</strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="showing-data">
                                    <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of
                                        {{ $products->total() }} Results</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="filter_sort">
                                    <div class="filter_btn">
                                        <i class="fa fa-list-ul"></i>
                                    </div>
                                    <div class="page-sort">
                                        <form action="" class="sort-form">
                                            <select name="sort" class="form-control form-select sort">
                                                <option value="1" @if (request()->get('sort') == 1) selected @endif>
                                                    Product: Latest</option>
                                                <option value="2" @if (request()->get('sort') == 2) selected @endif>
                                                    Product: Oldest</option>
                                                <option value="3" @if (request()->get('sort') == 3) selected @endif>
                                                    Price: High To Low</option>
                                                <option value="4" @if (request()->get('sort') == 4) selected @endif>
                                                    Price: Low To High</option>
                                                <option value="5" @if (request()->get('sort') == 5) selected @endif>
                                                    Name: A-Z</option>
                                                <option value="6" @if (request()->get('sort') == 6) selected @endif>
                                                    Name: Z-A</option>
                                            </select>
                                            <input type="hidden" name="min_price"
                                                value="{{ request()->get('min_price') }}" />
                                            <input type="hidden" name="max_price"
                                                value="{{ request()->get('max_price') }}" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-3 filter_sidebar">
                    <div class="filter_close"><i class="fa fa-long-arrow-left"></i> Filter</div>
                    <form action="" class="attribute-submit">
                        <div class="sidebar_item wraper__item">
                            <div class="accordion" id="price_sidebar">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapsePrice" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            Price
                                        </button>
                                    </h2>
                                    <div id="collapsePrice" class="accordion-collapse collapse show"
                                        data-bs-parent="#price_sidebar">
                                        <div class="accordion-body cust_according_body">
                                            <div class="category-filter-box category__wraper" id="categoryFilterBox">
                                                <div class="category-filter-item">
                                                    <div class="filter-body">
                                                        <div class="slider-box">
                                                            <div class="filter-price-inputs">
                                                                <p class="min-price">Tk<input type="text"
                                                                        name="min_price" id="min_price" readonly="" />
                                                                </p>
                                                                <p class="max-price">Tk<input type="text"
                                                                        name="max_price" id="max_price" readonly="" />
                                                                </p>
                                                            </div>
                                                            <div id="price-range" class="slider form-attribute"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--sidebar item end-->


                        <div class="sidebar_item wraper__item">
                            <div class="accordion" id="filter_sidebar">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseFilter" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            Filter
                                        </button>
                                    </h2>
                                    <div id="collapseFilter" class="accordion-collapse collapse show"
                                        data-bs-parent="#filter_sidebar">
                                        <div class="accordion-body cust_according_body">
                                            <div class="filter-body">
                                                <ul class="space-y-3">
                                                    @foreach ($categories as $category)
                                                        <li class="subcategory-filter-list">
                                                            <label for="{{ $category->slug . '-' . $category->id }}"
                                                                class="subcategory-filter-label">
                                                                <input class="form-checkbox form-attribute"
                                                                    id="{{ $category->slug . '-' . $category->id }}"
                                                                    name="category[]" value="{{ $category->id }}"
                                                                    type="checkbox"
                                                                    @if (is_array(request()->get('category')) && in_array($category->id, request()->get('category'))) checked @endif />
                                                                <p class="subcategory-filter-name">
                                                                    {{ $category->name }}</p>
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--sidebar item end-->
                    </form>
                </div>
                <div class="col-sm-9">
                    <div class="category-product main_product_inner">
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
            <div class="row">
                <div class="col-sm-12">
                    <div class="custom_paginate">
                        {{ $products->links('pagination::bootstrap-4') }}

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="homeproduct">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="meta_des">
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script>
        $("#price-range").click(function() {
            $(".price-submit").submit();
        })
        $(".form-attribute").on('change click', function() {
            $(".attribute-submit").submit();
        })
        $(".sort").change(function() {
            $(".sort-form").submit();
        })
        $(".form-checkbox").change(function() {
            $(".subcategory-submit").submit();
        })
    </script>
    <script>
        $(function() {
            $("#price-range").slider({
                step: 5,
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->get('min_price') ? request()->get('min_price') : $min_price }},
                    {{ request()->get('max_price') ? request()->get('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val({{ request()->get('min_price') ? request()->get('min_price') : $min_price }});
            $("#max_price").val({{ request()->get('max_price') ? request()->get('max_price') : $max_price }});
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

            $("#mobile-price-range").slider({
                step: 5,
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->get('min_price') ? request()->get('min_price') : $min_price }},
                    {{ request()->get('max_price') ? request()->get('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val({{ request()->get('min_price') ? request()->get('min_price') : $min_price }});
            $("#max_price").val({{ request()->get('max_price') ? request()->get('max_price') : $max_price }});
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

        });
    </script>
@endpush