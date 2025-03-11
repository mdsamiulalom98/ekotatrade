<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\CouponCode;
use App\Models\Product;

class CouponCodeController extends Controller
{
    public function index(Request $request)
    {
        $data = CouponCode::orderBy('id', 'DESC')->get();
        return view('backEnd.couponcode.index', compact('data'));
    }
    public function create()
    {
        $products = Product::where(['status' => 1])->select('id', 'name', 'status')->get();
        return view('backEnd.couponcode.create', compact('products'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'coupon_code' => 'required',
            'expiry_date' => 'required',
            'offer_type' => 'required',
            'amount' => 'required',
            'buy_amount' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        CouponCode::create($input);
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('couponcodes.index');
    }

    public function edit($id)
    {
        $edit_data = CouponCode::find($id);
        $products = Product::where(['status' => 1])->select('id', 'name', 'status')->get();
        return view('backEnd.couponcode.edit', compact('edit_data', 'products'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'coupon_code' => 'required',
            'expiry_date' => 'required',
            'offer_type' => 'required',
            'amount' => 'required',
            'buy_amount' => 'required',
        ]);
        $update_data = CouponCode::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('couponcodes.index');
    }

    public function inactive(Request $request)
    {
        $inactive = CouponCode::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = CouponCode::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = CouponCode::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
