<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Productimage;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductVariable;
use File;

class ProductController extends Controller
{
    public function getSubcategory(Request $request)
    {
        $subcategory = DB::table("subcategories")
            ->where("category_id", $request->category_id)
            ->pluck('subcategoryName', 'id');
        return response()->json($subcategory);
    }
    public function getChildcategory(Request $request)
    {
        $childcategory = DB::table("childcategories")
            ->where("subcategory_id", $request->subcategory_id)
            ->pluck('childcategoryName', 'id');
        return response()->json($childcategory);
    }

    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Product::orderBy('id', 'DESC')->with('image', 'category')->select('id', 'slug', 'name', 'new_price', 'stock', 'category_id', 'status', 'topsale', 'feature_product', 'product_type');

        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        $data = $query->paginate(50);

        return view('backEnd.product.index', compact('data'));
    }

    public function create()
    {
        $categories = Category::where('parent_id', '=', '0')->where('status', 1)->select('id', 'name', 'status')->with('childrenCategories')->get();
        $brands = Brand::where('status', '1')->select('id', 'name', 'status')->get();
        $colors = Color::where('status', '1')->get();
        $sizes = Size::where('status', '1')->get();
        return view('backEnd.product.create', compact('categories', 'brands', 'colors', 'sizes'));
    }

    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);

        $input = $request->except(['image', 'files', 'sizes', 'colors', 'purchase_prices', 'old_prices', 'new_prices', 'stocks', 'images']);

        $last_id = Product::orderBy('id', 'desc')->select('id')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;
        if (empty($request->slug)) {
            $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name . '-' . $last_id));
        }

        $input['status'] = $request->status ? 1 : 0;
        $input['topsale'] = $request->topsale ? 1 : 0;
        $input['product_code'] = 'P' . str_pad($last_id, 4, '0', STR_PAD_LEFT);

        if ($request->new_prices) {
            $veriable_new = 0;
            $new_prices = array_filter($request->new_prices);
            if (is_array($new_prices)) {
                foreach ($new_prices as $key => $price) {
                    if ($key == 0) {
                        $veriable_new += $price;
                    }
                }
            }
            // return $veriable_new;
            $input['new_price'] = $veriable_new;
        }


        $save_data = Product::create($input);

        $pro_image = $request->file('image');
        if ($pro_image) {
            foreach ($pro_image as $key => $image) {
                $name =  time() . '-' . $image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $uploadPath = 'public/uploads/product/';
                $image->move($uploadPath, $name);
                $imageUrl = $uploadPath . $name;

                $pimage             = new Productimage();
                $pimage->product_id = $save_data->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
        }
        if ($request->stocks) {
            $size       = $request->sizes;
            $color      = $request->colors;
            $stocks     = array_filter($request->stocks);
            $purchase   = $request->purchase_prices;
            $old_price  = $request->old_prices;
            $new_price  = $request->new_prices;
            $images     = $request->file('images');
            if (is_array($stocks)) {
                foreach ($stocks as $key => $stock) {

                    if ($images[$key]) {
                        $image = $images[$key];
                        $name =  time() . '-' . $image->getClientOriginalName();
                        $name = strtolower(preg_replace('/\s+/', '-', $name));
                        $uploadPath = 'public/uploads/product/';
                        $image->move($uploadPath, $name);
                        $imageUrl = $uploadPath . $name;
                    } else {
                        $imageUrl = NULL;
                    }
                    $variable = new ProductVariable();
                    $variable->product_id       = $save_data->id;
                    $variable->size             = $size ? $size[$key] : NULL;
                    $variable->color            = $color ? $color[$key] : NULL;
                    $variable->purchase_price   = $purchase[$key];
                    $variable->old_price        = $old_price ? $old_price[$key] : NULL;
                    $variable->new_price        = $new_price[$key];
                    $variable->stock            = $stock;
                    $variable->image            = $imageUrl;
                    $variable->save();
                }
            }
        }

        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('products.index');
    }

    public function edit($id)
    {
        $edit_data = Product::with('images')->find($id);
        $categories = Category::where('parent_id', '=', '0')->where('status', 1)->select('id', 'name', 'status')->get();
        $categoryId = Product::find($id)->category_id;
        $subcategoryId = Product::find($id)->subcategory_id;
        $subcategory = Subcategory::where('category_id', '=', $categoryId)->select('id', 'subcategoryName', 'status')->get();
        $childcategory = Childcategory::where('subcategory_id', '=', $subcategoryId)->select('id', 'childcategoryName', 'status')->get();
        $brands = Brand::where('status', '1')->select('id', 'name', 'status')->get();
        $sizes = Size::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();
        $variables = ProductVariable::where('product_id', $id)->get();
        // return $edit_data;
        return view('backEnd.product.edit', compact('edit_data', 'categories', 'subcategory', 'childcategory', 'brands', 'colors', 'sizes', 'variables'));
    }
    public function price_edit()
    {
        $products = DB::table('products')->select('id', 'name', 'status', 'old_price', 'new_price', 'stock')->where('status', 1)->get();;
        return view('backEnd.product.price_edit', compact('products'));
    }
    public function price_update(Request $request)
    {
        $ids = $request->ids;
        $oldPrices = $request->old_price;
        $newPrices = $request->new_price;
        $stocks = $request->stock;
        foreach ($ids as $key => $id) {
            $product = Product::select('id', 'name', 'status', 'old_price', 'new_price', 'stock')->find($id);

            if ($product) {
                $product->update([
                    'old_price' => $oldPrices[$key],
                    'new_price' => $newPrices[$key],
                    'stock' => $stocks[$key],
                ]);
            }
        }
        Toastr::success('Success', 'Price update successfully');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);

        $update_data = Product::find($request->id);
        $last_id = Product::orderBy('id', 'desc')->select('id')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;
        if (empty($request->slug)) {
            $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name . '-' . $last_id));
        }

        $input = $request->except(['image', 'files', 'up_id', 'up_sizes', 'up_colors', 'up_purchase_prices', 'sizes', 'colors', 'purchase_prices', 'old_prices', 'new_prices', 'stocks', 'images', 'up_old_prices', 'up_new_prices', 'up_stocks', 'up_images']);

        $input['status'] = $request->status ? 1 : 0;
        $input['topsale'] = $request->topsale ? 1 : 0;


        $update_data->update($input);
        if ($request->new_prices) {
            $veriable_new = 0;
            $new_prices = array_filter($request->new_prices);
            if (is_array($new_prices)) {
                foreach ($new_prices as $key => $price) {
                    if ($key == 1) {
                        $veriable_new += $price;
                    }
                }
            }
            // return $veriable_new;
            $input['new_price'] = $veriable_new;
        }

        // image dynamic
        $images = $request->file('image');
        if ($images) {
            foreach ($images as $key => $image) {
                $name =  time() . '-' . $image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $uploadPath = 'public/uploads/product/';
                $image->move($uploadPath, $name);
                $imageUrl = $uploadPath . $name;

                $pimage             = new Productimage();
                $pimage->product_id = $update_data->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
        }

        if ($request->up_id) {
            $update_ids = array_filter($request->up_id);
            $up_color   = $request->up_colors;
            $up_size    = $request->up_sizes;
            $up_size    = $request->up_sizes;
            $up_stock   = $request->up_stocks;
            $up_purchase   = $request->up_purchase_prices;
            $up_old_price  = $request->up_old_prices;
            $up_new_price  = $request->up_new_prices;
            $images     = $request->file('up_images');
            if ($update_ids) {
                foreach ($update_ids as $key => $update_id) {
                    $upvariable =  ProductVariable::find($update_id);
                    if (isset($images[$key])) {
                        $image = $images[$key];
                        $name =  time() . '-' . $image->getClientOriginalName();
                        $name = strtolower(preg_replace('/\s+/', '-', $name));
                        $uploadPath = 'public/uploads/product/';
                        $image->move($uploadPath, $name);
                        $imageUrl = $uploadPath . $name;
                        File::delete($upvariable->image);
                    } else {
                        $imageUrl = $upvariable->image;
                    }


                    $upvariable->product_id       = $update_data->id;
                    $upvariable->size             = $up_size ? $up_size[$key] : NULL;
                    $upvariable->color            = $up_color ? $up_color[$key] : NULL;
                    $upvariable->purchase_price   = $up_purchase[$key];
                    $upvariable->old_price        = $up_old_price ? $up_old_price[$key] : NULL;
                    $upvariable->new_price        = $up_new_price[$key];
                    $upvariable->stock            = $up_stock[$key];
                    $upvariable->image            = $imageUrl;
                    $upvariable->save();
                }
            }
        }


        if ($request->stocks) {
            $size       = $request->sizes;
            $color      = $request->colors;
            $stocks     = array_filter($request->stocks);
            $purchase   = $request->purchase_prices;
            $old_price  = $request->old_prices;
            $new_price  = $request->new_prices;
            $images     = $request->file('images');
            if (is_array($stocks)) {
                foreach ($stocks as $key => $stock) {

                    if (isset($images[$key])) {
                        $image = $images[$key];
                        $name =  time() . '-' . $image->getClientOriginalName();
                        $name = strtolower(preg_replace('/\s+/', '-', $name));
                        $uploadPath = 'public/uploads/product/';
                        $image->move($uploadPath, $name);
                        $imageUrl = $uploadPath . $name;
                    } else {
                        $imageUrl = NULL;
                    }

                    $variable = new ProductVariable();
                    $variable->product_id       = $update_data->id;
                    $variable->size             = $size ? $size[$key] : NULL;
                    $variable->color            = $color ? $color[$key] : NULL;
                    $variable->purchase_price   = $purchase[$key];
                    $variable->old_price        = $old_price ? $old_price[$key] : NULL;
                    $variable->new_price        = $new_price[$key];
                    $variable->stock            = $stock;
                    $variable->image            = $imageUrl;
                    $variable->save();
                }
            }
        }

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('products.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Product::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Product::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Product::find($request->hidden_id);
        if ($delete_data->images) {
            foreach ($delete_data->images as $image) {
                $filePath = $image->image; // Assuming this is '/public/uploads/product/1723615615-a9-pro-anc-4-e1722539762427.jpg'

                // Check if the file exists
                if (file_exists(base_path($filePath))) {
                    // Delete the file
                    File::delete($filePath);
                    Toastr::success('Success', 'Image found and deleted');
                } else {
                    Toastr::error('Error', "Image not found at path: $filePath");
                }

                $image->delete();
            }
        }
        if ($delete_data->variables) {
            foreach ($delete_data->variables as $variable) {
                $filePath = $variable->image;
                if (file_exists(base_path($filePath))) {
                    File::delete($filePath);
                    Toastr::success('Success', 'Image found and deleted');
                } else {
                    Toastr::error('Error', "Image not found at path: $filePath");
                }

                $variable->delete();
            }
        }
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
    public function imgdestroy(Request $request)
    {
        $delete_data = Productimage::find($request->id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
    public function pricedestroy(Request $request)
    {
        $delete_data = ProductVariable::find($request->id);
        $delete_data->delete();
        Toastr::success('Success', 'Product price delete successfully');
        return redirect()->back();
    }
    public function update_deals(Request $request)
    {
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['topsale' => $request->status]);
        return response()->json(['status' => 'success', 'message' => 'Hot deals product status change']);
    }
    public function update_feature(Request $request)
    {
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['feature_product' => $request->status]);
        return response()->json(['status' => 'success', 'message' => 'Feature product status change']);
    }
    public function update_status(Request $request)
    {
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['status' => $request->status]);
        return response()->json(['status' => 'success', 'message' => 'Product status change successfully']);
    }
}