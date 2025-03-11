<?php

namespace App\Http\Controllers\Frontend;

use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Product;
use App\Models\District;
use App\Models\CreatePage;
use App\Models\Campaign;
use App\Models\Banner;
use App\Models\ShippingCharge;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\Newsticker;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Review;
use App\Models\Reviewimage;
use App\Models\Question;
use App\Models\ProductVariable;
use App\Models\Productimage;
use App\Models\Feature;
use App\Models\Brand;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\CouponCode;
use App\Models\PaymentGateway;
use App\Models\CampaignReview;
use Carbon\Carbon;

class FrontendController extends Controller
{
    public function index()
    {
        $frontcategory = Category::where(['status' => 1])
            ->select('id', 'name', 'image', 'slug', 'status')
            ->get();

        $sliders = Banner::where(['status' => 1, 'category_id' => 1])
            ->select('id', 'image', 'link')
            ->get();

        $footertopads = Banner::where(['status' => 1, 'category_id' => 6])
            ->select('id', 'image', 'link')
            ->limit(5)
            ->get();

        $hotdeal_top = Product::where(['status' => 1, 'topsale' => 1])
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'product_type', 'stock')
            ->withCount('variable')
            ->with('reviews')
            ->limit(12)
            ->get();
        $homeproducts = Category::where(['front_view' => 1, 'status' => 1])
            ->orderBy('id', 'ASC')
            ->with(['products', 'products.image', 'products.variable'])
            ->get()
            ->map(function ($query) {
                $query->setRelation('products', $query->products->take(12));
                return $query;
            });
        // return $homeproducts;
        $newsticker = Newsticker::where('status', 1)->get();

        $features = Feature::where(['status' => 1])
            ->orderBy('id', 'DESC')
            ->limit(4)
            ->get();
        // deleting orphaned data;
        ProductVariable::doesntHave('product')->delete();
        Productimage::doesntHave('product')->delete();
        Campaign::doesntHave('product')->delete();
        CampaignReview::doesntHave('campaign')->delete();
        return view('frontEnd.layouts.pages.index', compact('sliders', 'frontcategory', 'hotdeal_top', 'homeproducts', 'footertopads', 'newsticker', 'features'));
    }
    
    private function setting()
    {
        return GeneralSetting::select('name')->first();
    }
    
    public function stock_check(Request $request)
    {
        $product = ProductVariable::where(['product_id' => $request->id, 'color' => $request->color, 'size' => $request->size])->first();

        $status = $product ? true : false;
        $response = [
            'status' => $status,
            'product' => $product
        ];
        return response()->json($response);
    }

    public function hotdeals()
    {
        $products = Product::where(['status' => 1, 'topsale' => 1])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'product_type', 'stock')
            ->withCount('variable')
            ->paginate(36);
        return view('frontEnd.layouts.pages.hotdeals', compact('products'));
    }
    public function product_check(Request $request)
    {

        $product = Product::where(['status' => 1, 'product_qr' => $request->product_id])->first();
        return view('frontEnd.layouts.pages.product_checker', compact('product'));
    }
    public function warranty_check(Request $request)
    {
        $product = Product::where('product_qr', $request->product_id)
            ->select('id', 'name', 'product_qr', 'product_warranty', 'new_price', 'stock')
            ->first();

        $order = Order::where(['invoice_id' => $request->order_id, 'order_status' => 6])
            ->select('id', 'invoice_id')
            ->first();
        $daysDiff = null;
        $orderDetail = null;
        if ($product && $order) {
            $orderDetail = $order->orderdetails()->where('product_id', $product->id)->first();

            if ($orderDetail) {
                $orderDate = Carbon::parse($orderDetail->updated_at);
                $today = Carbon::today();
                $daysDiff = $orderDate->diffInDays($today);
                if ($daysDiff < $product->product_warranty) {
                    $message = 'warranty is still valid';
                    $status = 'success';
                } else {
                    $message = 'warranty expired';
                    $status = 'failed';
                }
            } else {
                $message = 'product not found in order';
                $status = 'failed';
            }
        } else {
            $message = 'product or order not found';
            $status = 'failed';
        }
        $data = compact('message', 'status', 'product', 'order', 'orderDetail');
        if (isset($daysDiff)) {
            $data['daysDiff'] = $daysDiff;
        }
        return view('frontEnd.layouts.pages.warranty_checker', $data);
    }
    public function category($slug, Request $request)
    {
        $category = Category::where(['slug' => $slug, 'status' => 1])->first();
        $products = Product::where(['status' => 1, 'category_id' => $category->id])
            ->select('id', 'product_type', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'sort_description', 'stock')->withCount('variable');
        $subcategories = Subcategory::where('category_id', $category->id)->get();

        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price);
            $products = $products->where('new_price', '<=', $request->max_price);
        }

        $selectedSubcategories = $request->input('subcategory', []);
        $products = $products->when($selectedSubcategories, function ($query) use ($selectedSubcategories) {
            return $query->whereHas('subcategory', function ($subQuery) use ($selectedSubcategories) {
                $subQuery->whereIn('id', $selectedSubcategories);
            });
        });

        $products = $products->paginate(24);
        return view('frontEnd.layouts.pages.category', compact('category', 'products', 'subcategories', 'min_price', 'max_price'));
    }

    public function subcategory($slug, Request $request)
    {
        $subcategory = Subcategory::where(['slug' => $slug, 'status' => 1])->first();
        $products = Product::where(['status' => 1, 'subcategory_id' => $subcategory->id])
            ->select('id', 'name', 'slug', 'new_price', 'product_type', 'old_price', 'category_id', 'subcategory_id', 'sort_description', 'stock')->withCount('variable');
        $childcategories = Childcategory::where('subcategory_id', $subcategory->id)->get();

        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price);
            $products = $products->where('new_price', '<=', $request->max_price);
        }

        $selectedChildcategories = $request->input('childcategory', []);
        $products = $products->when($selectedChildcategories, function ($query) use ($selectedChildcategories) {
            return $query->whereHas('childcategory', function ($subQuery) use ($selectedChildcategories) {
                $subQuery->whereIn('id', $selectedChildcategories);
            });
        });

        $products = $products->paginate(24);
        // return $products;
        $impproducts = Product::where(['status' => 1, 'topsale' => 1])
            ->with('image')
            ->limit(6)
            ->select('id', 'name', 'slug', 'stock')
            ->get();

        return view('frontEnd.layouts.pages.subcategory', compact('subcategory', 'products', 'impproducts', 'childcategories', 'max_price', 'min_price'));
    }

    public function products($slug, Request $request)
    {
        $childcategory = Childcategory::where(['slug' => $slug, 'status' => 1])->first();
        $childcategories = Childcategory::where('subcategory_id', $childcategory->subcategory_id)->get();
        $products = Product::where(['status' => 1, 'childcategory_id' => $childcategory->id])->with('category')
            ->select('id', 'name', 'product_type', 'slug', 'new_price', 'old_price', 'category_id', 'subcategory_id', 'childcategory_id', 'sort_description')->withCount('variable');


        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price);
            $products = $products->where('new_price', '<=', $request->max_price);
        }

        $products = $products->paginate(24);
        // return $products;
        $impproducts = Product::where(['status' => 1, 'topsale' => 1])
            ->with('image')
            ->limit(6)
            ->select('id', 'name', 'slug', 'stock')
            ->get();

        return view('frontEnd.layouts.pages.childcategory', compact('childcategory', 'products', 'impproducts', 'min_price', 'max_price', 'childcategories'));
    }

    public function brands($slug, Request $request)
    {
        $brand = Brand::where(['slug' => $slug, 'status' => 1])->first();
        $products = Product::where(['status' => 1, 'brand_id' => $brand->id])
            ->select('id', 'product_type', 'name', 'slug', 'new_price', 'old_price', 'brand_id', 'sort_description', 'stock')->withCount('variable');

        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price);
            $products = $products->where('new_price', '<=', $request->max_price);
        }

        $products = $products->paginate(24);
        return view('frontEnd.layouts.pages.brand', compact('brand', 'products', 'min_price', 'max_price'));
    }

    public function all_products(Request $request)
    {
        $products = Product::where(['status' => 1])
            ->select('id', 'product_type', 'name', 'slug', 'new_price', 'old_price', 'brand_id', 'sort_description', 'stock')->withCount('variable');

        // return $request->sort;
        if ($request->sort == 1) {
            $products = $products->orderBy('created_at', 'desc');
        } elseif ($request->sort == 2) {
            $products = $products->orderBy('created_at', 'asc');
        } elseif ($request->sort == 3) {
            $products = $products->orderBy('new_price', 'desc');
        } elseif ($request->sort == 4) {
            $products = $products->orderBy('new_price', 'asc');
        } elseif ($request->sort == 5) {
            $products = $products->orderBy('name', 'asc');
        } elseif ($request->sort == 6) {
            $products = $products->orderBy('name', 'desc');
        } else {
            $products = $products->latest();
        }

        $min_price = $products->min('new_price');
        $max_price = $products->max('new_price');
        if ($request->min_price && $request->max_price) {
            $products = $products->where('new_price', '>=', $request->min_price);
            $products = $products->where('new_price', '<=', $request->max_price);
        }

        $selectedcategories = $request->input('category', []);
        $products = $products->when($selectedcategories, function ($query) use ($selectedcategories) {
            return $query->whereHas('category', function ($subQuery) use ($selectedcategories) {
                $subQuery->whereIn('id', $selectedcategories);
            });
        });

        // $categoriesString = $request->input('category');

        // // Extract category IDs
        // $categoryIds = explode('+', $categoriesString);

        // // Convert to integer array
        // $categoryIds = array_map('intval', $categoryIds);

        // Filter products
        // $products = $products->whereHas('category', function ($query) use ($categoryIds) {
        //     $query->whereIn('id', $categoryIds);
        // });

        // return $products;

        $categories = Category::where('status', 1)->get();
        $products = $products->paginate(24);
        return view('frontEnd.layouts.pages.products', compact('products', 'min_price', 'max_price', 'categories'));
    }

    public function details($slug)
    {
        $details = Product::where(['slug' => $slug, 'status' => 1])
            ->with('image', 'images', 'category', 'subcategory', 'childcategory')
            ->withCount('variable')
            ->firstOrFail();

        $productId = $details->id;
        $products = Product::where(['category_id' => $details->category_id, 'status' => 1])
            ->with('image')
            ->select('id', 'name', 'slug', 'status', 'category_id', 'new_price', 'old_price', 'product_type', 'stock')
            ->withCount('variable')
            ->get();

        $reviews = Review::with('images')->where('product_id', $details->id)->where('status', 'active')->orderBy('id', 'DESC')->get();
        $totalReviews = Review::where('product_id', $details->id)->where('status', 'active')->count();
        $ratings = collect(range(5, 1))->map(function ($rating) use ($totalReviews) {
            return (object)[
                'ratting' => $rating,
                'count' => 0,
                'percentage' => 0
            ];
        });
        $actualRatings = Review::selectRaw('ratting, COUNT(*) as count')
            ->where('product_id', $details->id)
            ->where('status', 'active')
            ->groupBy('ratting')
            ->orderBy('ratting', 'DESC')
            ->get();

        $ratings = $ratings->map(function ($rating) use ($actualRatings, $totalReviews) {
            $actualRating = $actualRatings->firstWhere('ratting', $rating->ratting);

            if ($actualRating) {
                $rating->count = $actualRating->count;
                $rating->percentage = ($actualRating->count / $totalReviews) * 100;
            }

            return $rating;
        });
        $question = Question::where('product_id', $details->id)->where('status', 'active')->get();

        $productcolors = ProductVariable::where('product_id', $details->id)
            ->whereNotNull('color')
            ->select('color')
            ->distinct()
            ->get();

        $productsizes = ProductVariable::where('product_id', $details->id)
            ->whereNotNull('size')
            ->select('size')
            ->distinct()
            ->get();

        $variablesArray = $details->variables->toArray();
        $imagesArray = $details->images->toArray();
        $sliderimages = array_merge($variablesArray, $imagesArray);
        $is_reviewable = false;
        $show_review_button = false;
        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->user()->id;
            $show_review_button = true;
            $is_reviewable = Order::where('order_status', 6)
                ->where('customer_id', $customerId)
                ->whereHas('orderdetails', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->exists();
        } else {
            $is_reviewable = null;
        }

        return view('frontEnd.layouts.pages.details', compact('details', 'sliderimages', 'products', 'productcolors', 'productsizes', 'reviews', 'question', 'is_reviewable', 'show_review_button', 'ratings'));
    }

    public function quickview(Request $request)
    {
        $data['data'] = Product::where(['id' => $request->id, 'status' => 1])->with('images')->withCount('reviews')->first();
        $data = view('frontEnd.layouts.ajax.quickview', $data)->render();
        if ($data != '') {
            echo $data;
        }
    }
    public function reviewimages(Request $request)
    {
        $data['data'] = Reviewimage::where(['review_id' => $request->review])->get();
        $data = view('frontEnd.layouts.ajax.reviewimages', $data)->render();
        if ($data != '') {
            echo $data;
        }
    }
    public function livesearch(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }
        $products = $products->get();

        if (empty($request->category) && empty($request->keyword)) {
            $products = [];
        }
        return view('frontEnd.layouts.ajax.search', compact('products'));
    }
    public function search(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->where('status', 1)
            ->with('image');
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }
        $products = $products->paginate(36);
        $keyword = $request->keyword;
        return view('frontEnd.layouts.pages.search', compact('products', 'keyword'));
    }

    public function shipping_charge(Request $request)
    {
        $shipping = ShippingCharge::where(['id' => $request->id])->first();
        Session::put('shipping', $shipping->amount);
        return view('frontEnd.layouts.ajax.cart');
    }
    public function shipping_charge_landing(Request $request)
    {
        $shipping = ShippingCharge::where(['id' => $request->id])->first();
        Session::put('shipping', $shipping->amount);
        return view('frontEnd.layouts.ajax.cart_bn');
    }

    public function contact(Request $request)
    {
        return view('frontEnd.layouts.pages.contact');
    }

    public function page($slug)
    {
        $page = CreatePage::where('slug', $slug)->firstOrFail();
        return view('frontEnd.layouts.pages.page', compact('page'));
    }
    public function districts(Request $request)
    {
        $areas = District::where(['district' => $request->id])->pluck('area_name', 'id');
        return response()->json($areas);
    }
    public function campaign($slug)
    {
        $campaign_data = Campaign::where('slug', $slug)->with('images')->first();
        $product = Product::where('id', $campaign_data->product_id)
            ->where('status', 1)
            ->with('image')
            ->first();
        $currentdata = date('Y-m-d');
        $couponcodes = CouponCode::where('status', 1)->where('expiry_date', '>=', $currentdata)->count();
        $productcolors = ProductVariable::where('product_id', $campaign_data->product_id)->where('stock', '>', 0)
            ->whereNotNull('color')
            ->select('color', 'image')
            ->distinct()
            ->get();

        $productsizes = ProductVariable::where('product_id', $campaign_data->product_id)->where('stock', '>', 0)
            ->whereNotNull('size')
            ->select('size', 'image')
            ->distinct()
            ->get();

        Cart::instance('shopping')->destroy();

        $var_product = ProductVariable::where(['product_id' => $campaign_data->product_id])->first();
        if ($product->product_type == 2) {
            $purchase_price = $var_product ? $var_product->purchase_price : 0;
            $old_price = $var_product ? $var_product->old_price : 0;
            $new_price = $var_product ? $var_product->new_price : 0;
            $stock = $var_product ? $var_product->stock : 0;
            $image = $var_product->image ?? '';
        } else {
            $purchase_price = $product->purchase_price;
            $old_price = $product->old_price;
            $new_price = $product->new_price;
            $stock = $product->stock;
            $image = $product->image->image ?? '';
        }
        $cart_count = Cart::instance('shopping')->count();
        if ($cart_count == 0) {
            Cart::instance('shopping')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1,
                'price' => $product->new_price,
                'options' => [
                    'slug' => $product->slug,
                    'image' => $image,
                    'old_price' => $product->old_price,
                    'purchase_price' => $product->purchase_price,
                    'product_type' => $product->product_type,
                ],
            ]);
        }
        $shippingcharge = ShippingCharge::where(['status' => 1, 'pos' => 0])->get();
        $select_charge = ShippingCharge::where(['status' => 1, 'pos' => 0])->first();
        $bkash_gateway = PaymentGateway::where(['status' => 1, 'type' => 'bkash'])->first();
        $shurjopay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        Session::put('shipping', $select_charge->amount);
        return view('frontEnd.layouts.pages.campaign.campaign', compact('campaign_data', 'product', 'productsizes', 'productcolors', 'shippingcharge', 'old_price', 'new_price', 'bkash_gateway', 'shurjopay_gateway', 'couponcodes'));
    }

    public function campaign_stock(Request $request)
    {
        $product = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'product_type', 'stock')->where(['id' => $request->id])->first();

        $variable = ProductVariable::where(['product_id' => $request->id, 'color' => $request->color, 'size' => $request->size])->first();
        $qty = 1;
        $status = $variable ? true : false;

        if ($status == true) {
            // return $variable;
            // return "wait";
            Cart::instance('shopping')->destroy();
            Cart::instance('shopping')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => $qty,
                'price' => $variable->new_price,
                'options' => [
                    'slug' => $product->slug,
                    'image' => $variable->image ?? $product->image->image,
                    'old_price' => $variable->old_price,
                    'purchase_price' => $variable->purchase_price,
                    'product_size' => $request->size,
                    'product_color' => $request->color,
                    'type' => $product->product_type
                ],
            ]);
        }
        // $data = Cart::instance('shopping')->content();
        // return response()->json($status);

        // return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
        // $response = [
        //     'status' => $status,
        //     'data' => $data
        // ];
        // return response()->json($response);

        $data = Cart::instance('shopping')->content();

        return response()->json([
            'status' => $status,
            'new_price' => $variable->new_price,
            'old_price' => $variable->old_price,
        ]);
    }

    public function payment_success(Request $request)
    {
        $order_id = $request->order_id;
        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        if ($data[0]->sp_code != 1000) {
            Toastr::error('Your payment failed, try again', 'Oops!');
            if ($data[0]->value1 == 'customer_payment') {
                return redirect()->route('home');
            } else {
                return redirect()->route('home');
            }
        }

        if ($data[0]->value1 == 'customer_payment') {

            $customer = Customer::find(Auth::guard('customer')->user()->id);

            // order data save
            $order = new Order();
            $order->invoice_id = $data[0]->id;
            $order->amount = $data[0]->amount;
            $order->customer_id = Auth::guard('customer')->user()->id;
            $order->order_status = $data[0]->bank_status;
            $order->save();

            // payment data save
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->customer_id = Auth::guard('customer')->user()->id;
            $payment->payment_method = 'shurjopay';
            $payment->amount = $order->amount;
            $payment->trx_id = $data[0]->bank_trx_id;
            $payment->sender_number = $data[0]->phone_no;
            $payment->payment_status = 'paid';
            $payment->save();
            // order details data save
            foreach (Cart::instance('shopping')->content() as $cart) {
                $order_details = new OrderDetails();
                $order_details->order_id = $order->id;
                $order_details->product_id = $cart->id;
                $order_details->product_name = $cart->name;
                $order_details->purchase_price = $cart->options->purchase_price;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->save();
            }

            Cart::instance('shopping')->destroy();
            Toastr::error('Thanks, Your payment send successfully', 'Success!');
            return redirect()->route('home');
        }

        Toastr::error('Something wrong, please try agian', 'Error!');
        return redirect()->route('home');
    }
    public function payment_cancel(Request $request)
    {
        $order_id = $request->order_id;
        $shurjopay_service = new ShurjopayController();
        $json = $shurjopay_service->verify($order_id);
        $data = json_decode($json);

        Toastr::error('Your payment cancelled', 'Cancelled!');
        if ($data[0]->sp_code != 1000) {
            if ($data[0]->value1 == 'customer_payment') {
                return redirect()->route('home');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function offers()
    {
        return view('frontEnd.layouts.pages.offers');
    }
    public function send_otp(Request $request)
    {
        $sms_gateway = SmsGateway::where(['status' => 1, 'otp_login' => 1])->first();
        if ($sms_gateway) {
            $otp_code = rand(1111, 9999);
            Session::put('otp_code', $otp_code);
            Session::put('phone_number', $request->phone);
            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => "$sms_gateway->url",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'api_key' => "$sms_gateway->api_key",
                        'msg' => "Dear User !\r\n Your OTP is $otp_code \r\nThank you for using " . $this->setting()->name,
                        'to' => $request->phone
                    ),
                )
            );

            $response = curl_exec($curl);

            curl_close($curl);
        }
        return response()->json(['status' => 'success']);
    }
    public function remove_otp()
    {
        Session::forget('otp_code');
        Session::forget('phone_number');
        return response()->json(['status' => 'success']);
    }

    public function validate_otp(Request $request)
    {
        $data = Session::get('otp_code');
        $code = $request->code;
        if ($data == $code) {
            $response = [
                'status' => 'success',
            ];
        } else {
            $response = [
                'status' => 'error',
            ];
        }
        return response()->json($response);
    }
    public function check_coupon(Request $request)
    {
        $data = CouponCode::where('coupon_code', '=', $request->code)->first();
        if ($data) {
            $response = [
                'status' => 'success',
                'data' => $data
            ];
        } else {
            $response = [
                'status' => 'error',
                'data' => $data
            ];
        }
        return response()->json($response);
    }
}