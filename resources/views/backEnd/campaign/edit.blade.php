@extends('backEnd.layouts.master')
@section('title', 'Landing Page Edit')
@section('css')
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('campaign.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                    </div>
                    <h4 class="page-title">Landing Page Edit</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('campaign.update') }}" method="POST" class=row data-parsley-validate=""
                            enctype="multipart/form-data" name="editForm">
                            @csrf
                            <input type="hidden" value="{{ $edit_data->id }}" name="hidden_id">

                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="product_id" class="form-label">Products *</label>
                                    <select class="select2 form-control  @error('product_id') is-invalid @enderror"
                                        value="{{ old('product_id') }}" name="product_id" data-placeholder="Choose ...">
                                        <option value="">Select..</option>
                                        @foreach ($products as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="banner_title" class="form-label">Banner Title</label>
                                    <input type="text" class="form-control @error('banner_title') is-invalid @enderror"
                                        name="banner_title" value="{{ $edit_data->banner_title }}" id="banner_title">
                                    @error('banner_title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->

                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="banner" class="form-label">Banner Image (1350 x 670)</label>
                                    <input type="file" class="form-control @error('banner') is-invalid @enderror"
                                        name="banner" value="{{ $edit_data->banner }}" id="banner">
                                    <img src="{{ asset($edit_data->banner) }}" alt="" class="edit-image">
                                    @error('banner')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->

                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Landing Page Title </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ $edit_data->name }}" id="name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->

                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="pro_title" class="form-label">Product Title </label>
                                    <input type="text" class="form-control @error('pro_title') is-invalid @enderror"
                                        name="pro_title" value="{{ $edit_data->pro_title }}" id="pro_title">
                                    @error('pro_title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->

                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="image_one" class="form-label">Image One (500 x 500)</label>
                                    <input type="file" class="form-control @error('image_one') is-invalid @enderror"
                                        name="image_one" value="{{ $edit_data->image_one }}" id="image_one">
                                    <img src="{{ asset($edit_data->image_one) }}" alt="" class="edit-image">
                                    @error('image_one')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->

                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="image_two" class="form-label">Image Two (500 x 500)</label>
                                    <input type="file" class="form-control @error('image_two') is-invalid @enderror "
                                        name="image_two" value="{{ $edit_data->image_two }}" id="image_two">
                                    <img src="{{ asset($edit_data->image_two) }}" alt="" class="edit-image">
                                    @error('image_two')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->

                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="image_three" class="form-label">Image Three (500 x 500)</label>
                                    <input type="file" class="form-control @error('image_three') is-invalid @enderror "
                                        name="image_three" value="{{ $edit_data->image_three }}" id="image_three">
                                    <img src="{{ asset($edit_data->image_three) }}" alt="" class="edit-image">
                                    @error('image_three')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="review" class="form-label">Review </label>
                                    <input type="text" class="form-control @error('review') is-invalid @enderror"
                                        name="review" value="{{ $edit_data->review }}" id="review">
                                    @error('review')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-6 mb-3">
                                <label for="image">Review Image (500 x 500)</label>
                                <div class="input-group control-group increment">
                                    <input type="file" name="image[]"
                                        class="form-control @error('image') is-invalid @enderror" />
                                    <div class="input-group-btn">
                                        <button class="btn btn-success btn-increment" type="button"><i
                                                class="fa fa-plus"></i></button>
                                    </div>
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="clone hide" style="display: none;">
                                    <div class="control-group input-group">
                                        <input type="file" name="image[]" class="form-control" />
                                        <div class="input-group-btn">
                                            <button class="btn btn-danger" type="button"><i
                                                    class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="product_img">
                                    @foreach ($edit_data->images as $image)
                                        <img src="{{ asset($image->image) }}" class="edit-image border"
                                            alt="" />
                                        <a href="{{ route('campaign.image.destroy', ['id' => $image->id]) }}"
                                            class="btn btn-xs btn-danger waves-effect waves-light"><i
                                                class="mdi mdi-close"></i></a>
                                    @endforeach
                                </div>
                            </div>
                            <!-- col end -->

                            <!------------------->
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="necessity_title" class="form-label">Necessity Description Title </label>
                                    <input type="text"
                                        class="form-control @error('necessity_title') is-invalid @enderror"
                                        name="necessity_title" value="{{ $edit_data->necessity_title }}"
                                        id="necessity_title">
                                    @error('necessity_title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="short_description" class="form-label">Necessity Description</label>
                                    <textarea name="short_description" rows="6"
                                        class="summernote form-control @error('short_description') is-invalid @enderror">{{ $edit_data->short_description }}</textarea>
                                    @error('short_description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="rules" class="form-label">Rules Title </label>
                                    <input type="text" class="form-control @error('rules') is-invalid @enderror"
                                        name="rules" value="{{ $edit_data->rules }}" id="rules">
                                    @error('rules')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12 my-3">
                                <div class="form-group">
                                    <label for="rules_description" class="form-label">Rules Description</label>
                                    <textarea name="rules_description" rows="6"
                                        class="summernote form-control @error('rules_description') is-invalid @enderror">{{ $edit_data->rules_description }}</textarea>
                                    @error('rules_description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->

                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="description" class="form-label">Why Choose Us</label>
                                    <textarea name="description" rows="6"
                                        class="summernote form-control @error('description') is-invalid @enderror">{{ $edit_data->description }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->

                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="whychoose_img" class="form-label">Why Choose Image (500 x 500)</label>
                                    <input type="file"
                                        class="form-control @error('whychoose_img') is-invalid @enderror "
                                        name="whychoose_img" value="{{ $edit_data->whychoose_img }}" id="whychoose_img">
                                    <img src="{{ asset($edit_data->whychoose_img) }}" alt="" class="edit-image">
                                    @error('whychoose_img')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                             <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="meta_description" class="form-label">Meta Description (For Social Sharing)</label>
                                    <textarea name="meta_description" rows="6"
                                        class="form-control @error('meta_description') is-invalid @enderror">{{ $edit_data->meta_description }}</textarea>
                                    @error('meta_description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->

                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label for="status" class="d-block">Status</label>
                                    <label class="switch">
                                        <input type="checkbox" value="1" name="status"
                                            @if ($edit_data->status == 1) checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end --> 
                            <div class="col-sm-3 mb-3">
                                <div class="form-group">
                                    <label for="coupon_code" class="d-block">Coupon</label>
                                    <label class="switch">
                                        <input type="checkbox" value="1" name="coupon_code"
                                            @if ($edit_data->coupon_code == 1) checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                    @error('coupon_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div>
                                <input type="submit" class="btn btn-success" value="Submit">
                            </div>

                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
    </div>
@endsection



@section('script')
    <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-pickers.init.js"></script>
    <!-- Plugins js -->
    <script src="{{ asset('public/backEnd/') }}/assets/libs//summernote/summernote-lite.min.js"></script>
    <script>
        $(".summernote").summernote({
            placeholder: "Enter Your Text Here",
        });
    </script>
    <script type="text/javascript">
        document.forms['editForm'].elements['product_id'].value = "{{ $edit_data->product_id }}"
        $('.select2').select2();
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".btn-increment").click(function() {
                var html = $(".clone").html();
                $(".increment").after(html);
            });
            $("body").on("click", ".btn-danger", function() {
                $(this).parents(".control-group").remove();
            });
            $('.select2').select2();
        });
    </script>
@endsection