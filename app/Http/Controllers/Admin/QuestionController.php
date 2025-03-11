<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Product;
use App\Models\Question;
use App\Models\Customer;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $show_data = Question::orderBy('id', 'DESC')->get();
        return view('backEnd.question.index', compact('show_data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'comment' => 'required',
        ]);
        $customer = Customer::where('id', $request->customer_id)->first();
        $input = $request->all();
        $input['status'] = $request->status == 1 ? 'active' : 'pending';
        Question::create($input);

        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('question.index');
    }

    public function edit($id)
    {
        $edit_data = Question::find($id);
        $products = Product::where(['status' => 1])->select('id', 'name')->get();
        return view('backEnd.question.edit', compact('edit_data', 'products'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'email' => 'required',
            'product_id' => 'required',
        ]);
        $input = $request->except('hidden_id');
        $input['status'] = $request->status == 1 ? 'active' : 'pending';
        $update_data = Question::find($request->hidden_id);
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('question.index');
    }

    public function pending()
    {
        $data = Question::where('status', 'pending')->get();
        return view('backEnd.question.pending', compact('data'));
    }
    public function inactive(Request $request)
    {
        $inactive = Question::find($request->hidden_id);
        $inactive->status = 'pending';
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Question::find($request->hidden_id);
        $active->status = 'active';
        $active->save();

        $product = Product::select('id', 'comment')->find($active->product_id);
        $product->comment += 1;
        $product->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Question::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
