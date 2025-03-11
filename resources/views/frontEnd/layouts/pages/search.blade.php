@extends('frontEnd.layouts.master')
@section('title', $keyword)
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
                            <strong>{{ $keyword }}</strong>
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
                                <div class="mobile-filter-toggle">
                                    <i class="fa fa-list-ul"></i><span>filter</span>
                                </div>
                                <div class="page-sort">
                                    <form action="" class="sort-form">
                                        <select name="sort" class="form-control form-select sort">
                                            <option value="1" @if (request()->get('sort') == 1) selected @endif>
                                                Product: Latest</option>
                                            <option value="2" @if (request()->get('sort') == 2) selected @endif>
                                                Product: Oldest</option>
                                            <option value="3" @if (request()->get('sort') == 3) selected @endif>Price:
                                                High To Low</option>
                                            <option value="4" @if (request()->get('sort') == 4) selected @endif>Price:
                                                Low To High</option>
                                            <option value="5" @if (request()->get('sort') == 5) selected @endif>Name:
                                                A-Z</option>
                                            <option value="6" @if (request()->get('sort') == 6) selected @endif>Name:
                                                Z-A</option>
                                        </select>
                                        <input type="hidden" name="min_price" value="{{ request()->get('min_price') }}" />
                                        <input type="hidden" name="max_price" value="{{ request()->get('max_price') }}" />
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
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
                                                <button data-id="{{ $value->id }}" class="hover-zoom addcartbutton"
                                                    title="Add to Cart"><i class="fa-solid fa-bag-shopping"></i></button>
                                            @endif

                                        </div>

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


@endsection
@push('script')
    <script>
        $(".sort").change(function() {
            $('#loading').show();
            $(".sort-form").submit();
        })
    </script>
@endpush