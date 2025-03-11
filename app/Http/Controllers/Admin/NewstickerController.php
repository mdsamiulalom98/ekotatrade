<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Newsticker;
use Toastr;
use Image;
use File;
use Str;

class NewstickerController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:newsticker-list|newsticker-create|newsticker-edit|newsticker-delete', ['only' => ['index','store']]);
         $this->middleware('permission:newsticker-create', ['only' => ['create','store']]);
         $this->middleware('permission:newsticker-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:newsticker-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Newsticker::orderBy('id','DESC')->get();
        // return $data;
        return view('backEnd.newsticker.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.newsticker.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        Newsticker::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('newsticker.index');
    }
    
    public function edit($id)
    {
        $edit_data  = Newsticker::find($id);
        return view('backEnd.newsticker.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        $update_data = Newsticker::find($request->id);
        $input = $request->all();
        
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('newsticker.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Newsticker::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Newsticker::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Newsticker::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
