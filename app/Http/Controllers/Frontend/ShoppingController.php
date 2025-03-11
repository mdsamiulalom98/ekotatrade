<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\SmsGateway;

class ShoppingController extends Controller
{
    public function addTocartGet($id, Request $request)
    {
        $qty = 1;
        $productInfo = DB::table('products')->where('id', $id)->first();
        $productImage = DB::table('productimages')->where('product_id', $id)->first();
        $cartinfo = Cart::instance('shopping')->add([
            'id' => $productInfo->id,
            'name' => $productInfo->name,
            'qty' => $qty,
            'price' => $productInfo->new_price,
            'options' => [
                'image' => $productImage->image,
                'old_price' => $productInfo->old_price,
                'slug' => $productInfo->slug,
                'purchase_price' => $productInfo->purchase_price,
                'product_type' => $productInfo->product_type,
            ]
        ]);

        return response()->json($cartinfo);
    }
    public function cart_store(Request $request)
    {
        $product = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'product_type', 'stock')
            ->where(['id' => $request->id])
            ->first();

         $conditions = ['product_id' => $request->id];
    
        if ($request->filled('product_color')) {
            $conditions['color'] = $request->product_color;
        }
        if ($request->filled('product_size')) {
            $conditions['size'] = $request->product_size;
        }
    
        $var_product = ProductVariable::where($conditions)->first();
        
        if ($product->product_type == 2) {
            $purchase_price = $var_product->purchase_price ?? 0;
            $old_price = $var_product->old_price ?? 0;
            $new_price = $var_product->new_price ?? 0;
            $stock = $var_product->stock ?? 0;
        } else {
            $purchase_price = $product->purchase_price;
            $old_price = $product->old_price;
            $new_price = $product->new_price;
            $stock = $product->stock;
        }

        // Build the cart query with appropriate options
        $cartitem_query = Cart::instance('shopping')->content()->where('id', $product->id);
        if ($product->product_type == 2) {
            if ($request->filled('product_color')) {
                $cartitem_query = $cartitem_query->where('options.product_color', $request->product_color);
            }
            if ($request->filled('product_size')) {
                $cartitem_query = $cartitem_query->where('options.product_size', $request->product_size);
            }
        }
        $cartitem = $cartitem_query->first();
        
        if ($cartitem) {
            // If the product is already in the cart, show a Toastr notification and prevent adding
            Toastr::success('This product is already in your cart!!');
            return redirect()->route('customer.checkout');
        }
        // Validate stock
        $cart_qty = $request->qty;
        if ($stock < $cart_qty) {
            Toastr::error('Product stock limit over', 'Failed!');
            return back();
        }
        // return $cart_item;
        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->qty,
            'price' => $new_price,
            'options' => [
                'slug' => $product->slug,
                'image' => $product->image->image,
                'old_price' => $new_price,
                'purchase_price' => $purchase_price,
                'product_size' => $request->product_size,
                'product_color' => $request->product_color,
                'pro_unit' => $request->pro_unit,
                'product_type' => $product->product_type,

            ],
        ]);
        // return $custom;
        Toastr::success('Product successfully add to cart', 'Success!');
        $sms_gateway = SmsGateway::where(['status' => 1, 'otp_login' => 1])->first();
        if ($request->order_now) {
            if (!Auth::guard('customer')->check() && $sms_gateway) {
                return redirect()->route('customer.login');
            }
            return redirect()->route('customer.checkout');
        }
        return back();
    }
    public function cart_content(Request $request)
    {
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }
    public function cart_remove(Request $request)
    {
        $remove = Cart::instance('shopping')->update($request->id, 0);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_increment(Request $request)
    {
        $data = Cart::instance('shopping')->content();
        $item = Cart::instance('shopping')->get($request->id);
        $product = Product::find($item->id);

        if ($product->product_type == 2) {
            $productVariableQuery = ProductVariable::where('product_id', $product->id);

            if ($item->options->product_color) {
                $productVariableQuery->where('color', $item->options->product_color);
            }
            if ($item->options->product_size) {
                $productVariableQuery->where('size', $item->options->product_size);
            }

            $productVariable = $productVariableQuery->first();

            if ($productVariable) {
                $product = $productVariable;
            } else {
                Toastr::error('Product variant not found.', 'Failed!');
            }
        }

        if ($product->stock > $item->qty) {
            $qty = $item->qty + 1;
            $increment = Cart::instance('shopping')->update($request->id, $qty);
        } else {
            Toastr::error('Cannot add more items, stock is insufficient', 'Failed!');
        }

        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_decrement(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        // Ensure the quantity doesn't go below 1
        if ($item->qty > 1) {
            $qty = $item->qty - 1;
            $decrement = Cart::instance('shopping')->update($request->id, $qty);
        }
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_count(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.cart_count', compact('data'));
    }
    public function compare_count(Request $request)
    {
        $data = Cart::instance('compare')->count();
        return view('frontEnd.layouts.ajax.compare_count', compact('data'));
    }
    public function mobilecart_qty(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.mobilecart_qty', compact('data'));
    }
    public function cart_remove_bn(Request $request)
    {
        $remove = Cart::instance('shopping')->update($request->id, 0);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }
    public function cart_increment_bn(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty + 1;
        $increment = Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }
    public function cart_decrement_bn(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        if ($item->qty > 1) {
            $qty = $item->qty - 1;
            $decrement = Cart::instance('shopping')->update($request->id, $qty);
        }
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }

    // compare product functions

    public function add_compare($id)
    {
        $qty = 1;
        $productInfo = DB::table('products')
            ->where('id', $id)
            ->first();
        $productImage = DB::table('productimages')
            ->where('product_id', $id)
            ->first();
        $compareinfo = Cart::instance('compare')->add([
            'id' => $productInfo->id,
            'name' => $productInfo->name,
            'qty' => $qty,
            'price' => $productInfo->new_price,
            'options' => [
                'image' => $productImage->image,
                'description' => $productInfo->description,
                'product_type' => $productInfo->product_type,
                'slug' => $productInfo->slug,
            ],
        ]);

        return response()->json($compareinfo);
    }
    public function compare_content()
    {
        return view('frontEnd.layouts.ajax.comparecontent');
    }
    public function compare_product()
    {
        $compareproduct = Cart::instance('compare')->content();
        if ($compareproduct->count()) {
            return view('frontEnd.layouts.pages.compareproduct', compact('compareproduct'));
        } else {
            Toastr::info('You have no product in compare', 'Opps!');
            return redirect('/');
        }
    }
    public function compare_product_add($id, $rowId)
    {
        $qty = 1;
        $productInfo = DB::table('products')
            ->where('id', $id)
            ->first();
        $productImage = DB::table('productimages')
            ->where('product_id', $id)
            ->first();
        Cart::instance('shopping')->add([
            'id' => $productInfo->id,
            'name' => $productInfo->name,
            'qty' => $qty,
            'price' => $productInfo->new_price,
            'options' => [
                'image' => $productImage->image,
                'product_type' => $productInfo->product_type,
            ]
        ]);
        Toastr::success('product add to cart', 'successfully');
        Cart::instance('compare')->update($rowId, 0);
        return redirect()->back();
    }
    public function remove_compare(Request $request)
    {
        $compareproduct = Cart::instance('compare')->content();
        if ($compareproduct) {
            $rowId = $request->rowId;
            Cart::instance('compare')->update($rowId, 0);
            Toastr::success('Compare product remove successfully', 'successfully');
            return redirect()->back();
        } else {
            return redirect('/');
        }
    }

    // wishlist script
    public function wishlist_store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['message' => 'unauthorized'], 401);
        }
        $product = Product::select('id', 'name', 'slug', 'old_price', 'new_price', 'purchase_price')->where(['id' => $request->id])->first();
        Cart::instance('wishlist')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->qty,
            'price' => $product->new_price,
            'options' => [
                'slug' => $product->slug,
                'image' => $product->image->image,
                'old_price' => $product->new_price,
                'purchase_price' => $product->purchase_price,
            ],
        ]);
        $data = Cart::instance('wishlist')->content();
        return response()->json(['data' => $data]);
    }
    public function wishlist_show()
    {
        $data = Cart::instance('wishlist')->content();
        return view('frontEnd.layouts.pages.wishlist', compact('data'));
    }
    public function wishlist_remove(Request $request)
    {
        $remove = Cart::instance('wishlist')->update($request->id, 0);
        $data = Cart::instance('wishlist')->content();
        return view('frontEnd.layouts.ajax.wishlist', compact('data'));
    }
    public function wishlist_count(Request $request)
    {
        $data = Cart::instance('wishlist')->count();
        return view('frontEnd.layouts.ajax.wishlist_count', compact('data'));
    }
}