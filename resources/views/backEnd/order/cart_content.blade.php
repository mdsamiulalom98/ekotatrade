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
                    <button type="button" class="minus cart_decrement" value="{{ $value->qty }}"
                        data-id="{{ $value->rowId }}">-</button>
                    <input type="number" value="{{ $value->qty }}" class="quantity_update"
                        data-id="{{ $value->rowId }}" />
                    <button type="button" class="plus cart_increment" value="{{ $value->qty }}"
                        data-id="{{ $value->rowId }}">+</button>
                </div>
            </div>
        </td>
        <td class="discount">
            <input type="number" class="product_price" value="{{ $value->price }}" placeholder="0.00"
                data-id="{{ $value->rowId }}" />
        </td>
        <td class="discount">
            <input type="number" class="product_discount" value="{{ $value->options->product_discount }}"
                placeholder="0.00" data-id="{{ $value->rowId }}" />
        </td>
        <td>{{ ($value->price - $value->options->product_discount) * $value->qty }}
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{ $value->rowId }}"><i
                    class="fa fa-times"></i></button>
        </td>
    </tr>
    @php
        $product_discount += $value->options->product_discount * $value->qty;
        Session::put('product_discount', $product_discount);
    @endphp
@endforeach
