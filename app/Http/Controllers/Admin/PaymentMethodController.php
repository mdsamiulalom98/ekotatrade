<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:paymentmethod-list|paymentmethod-create|paymentmethod-edit|paymentmethod-delete', ['only' => ['index','show']]);
         $this->middleware('permission:paymentmethod-create', ['only' => ['create','store']]);
         $this->middleware('permission:paymentmethod-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:paymentmethod-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $show_data = PaymentMethod::orderBy('id', 'DESC')->get();
        return view('backEnd.paymentmethod.index', compact('show_data'));
    }
    public function create()
    {
        return view('backEnd.paymentmethod.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);

        // image with intervention
        $image = $request->file('logo');
        if ($image) {
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/paymentmethod/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = "";
            $height = "";
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
        } else {
            $imageUrl = null;
        }

        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['logo'] = $imageUrl;
        PaymentMethod::create($input);

        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('paymentmethods.index');
    }

    public function edit($id)
    {
        $edit_data = PaymentMethod::find($id);
        return view('backEnd.paymentmethod.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);
        // image one
        $update_data = PaymentMethod::find($request->id);
        $input = $request->all();
        $image = $request->file('logo');
        if ($image) {
            // image with intervention
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/paymentmethod/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = "";
            $height = "";
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['logo'] = $imageUrl;
            File::delete($update_data->logo);
        } else {
            $input['logo'] = $update_data->logo;
        }
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('paymentmethods.index');
    }

    public function inactive(Request $request)
    {
        $inactive = PaymentMethod::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = PaymentMethod::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = PaymentMethod::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
