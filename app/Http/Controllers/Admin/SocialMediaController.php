<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialMedia;
use Toastr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class SocialMediaController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:social-list|social-create|social-edit|social-delete', ['only' => ['index','store']]);
         $this->middleware('permission:social-create', ['only' => ['create','store']]);
         $this->middleware('permission:social-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:social-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $show_data = SocialMedia::orderBy('id','DESC')->get();
        return view('backEnd.socialmedia.index',compact('show_data'));
    }
    public function create()
    {
        return view('backEnd.socialmedia.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'status' => 'required',
        ]);
        
        // image with intervention 
        $image = $request->file('image');
        if ($image) {
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/socialmedia/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 210;
            $height = 210;
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
        } else {
            $imageUrl = NULL;
        }


        $input = $request->all();
        $input['image'] = $imageUrl;
        SocialMedia::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('socialmedias.index');
    }
    
    public function edit($id)
    {
        $edit_data = SocialMedia::find($id);
        return view('backEnd.socialmedia.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);
        
        $update_data = SocialMedia::find($request->id);
        $input = $request->all();
        $image = $request->file('image');
        if ($image) {
            // image with intervention 
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/socialmedia/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 210;
            $height = 210;
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
            File::delete($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('socialmedias.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = SocialMedia::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = SocialMedia::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = SocialMedia::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
