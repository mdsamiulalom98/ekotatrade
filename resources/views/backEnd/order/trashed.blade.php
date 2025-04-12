@extends('backEnd.layouts.master')
@section('title', 'Trashed Order')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row ">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.order.create') }}" class="btn btn-danger rounded-pill"><i
                                class="fe-shopping-cart"></i> Add New</a>
                    </div>
                    <h4 class="page-title">Trashed Order ({{ $orders->count() }})</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row order_page">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <ul class="action2-btn">
                                    <li><a data-bs-toggle="modal" data-bs-target="#asignUser"
                                            class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a>
                                    </li>
                                    <li><a data-bs-toggle="modal" data-bs-target="#changeStatus"
                                            class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a>
                                    </li>
                                    <li><a href="{{ route('admin.order.order_print') }}"
                                            class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i>
                                            Print</a></li>
                                    <li><a href="{{ route('admin.order.pos_print') }}"
                                            class="btn rounded-pill btn-info multi_pos_print"><i class="fe-printer"></i>
                                            Print Pos</a></li>

                                    @can('order-delete')
                                        <li>
                                            <a href="{{ route('admin.order.bulk_trashed') }}"
                                                class="btn rounded-pill btn-danger order_delete">
                                                <i class="fe-plus"></i>
                                                Delete All
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                            <div class="col-sm-4">
                                <form class="custom_form">
                                    <div class="form-group">
                                        <input type="text" name="keyword" placeholder="Search">
                                        <button class="btn  rounded-pill btn-info">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive " style="overflow-y: scroll; height: 100vh;">
                            <table id="datatable-buttons" class="table table-striped   w-100">
                                <thead>
                                    <tr>
                                        <th style="width:2%">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input checkall" value="">
                                                </label>
                                            </div>
                                        </th>
                                        <th style="width:2%">SL</th>
                                        <th style="width:8%">Action</th>
                                        <th style="width:8%">Invoice</th>
                                        <th style="width:10%">Date</th>
                                        <th style="width:10%">Name</th>
                                        <th style="width:10%">Phone</th>
                                        <th style="width:10%">Courier ID</th>
                                        <th style="width: 10%">Order Note</th>
                                        <th style="width:10%">Amount</th>
                                        <th style="width:10%">Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($show_data as $key => $value)
                                        <tr>
                                            <td><input type="checkbox" class="checkbox" value="{{ $value->id }}"></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="button-list custom-btn-list">
                                                    @can('order-view')
                                                        <a href="{{ route('admin.order.invoice', ['id' => $value->id]) }}"
                                                            title="Invoice"><i class="fe-download"></i></a>
                                                    @endcan
                                                    <a data-bs-toggle="modal" data-bs-target="#order{{ $value->id }}"
                                                        title="Order Details"><i class="fe-eye"></i></a>
                                                    <a href="{{ route('admin.order.process', ['id' => $value->invoice_id]) }}"
                                                        title="Process"><i class="fe-settings"></i></a>
                                                    <a href="{{ route('admin.order.edit', ['id' => $value->id]) }}"
                                                        title="Edit"><i class="fe-edit"></i></a>
                                                    @can('order-delete')
                                                        <form method="post" action="{{ route('admin.order.destroy') }}"
                                                            class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="id">
                                                            <button type="submit" title="Delete" class="delete-confirm">
                                                                <i class="fe-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    <a data-bs-toggle="modal" data-bs-target="#orderNote" title="Note"
                                                        class="order_note" data-id="{{ $value->id }}">
                                                        <i class="fe-file-text"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td><a
                                                    href="{{ route('admin.order.slip', ['id' => $value->id]) }}">
                                                    <u>{{ $value->invoice_id }}</u>
                                                </a>
                                                <br> {{ $value->customer_ip }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($value->updated_at)) }}<br>
                                                {{ date('h:i:s a', strtotime($value->updated_at)) }}
                                            </td>
                                            <td>
                                                <strong>{{ $value->shipping ? $value->shipping->name : '' }}</strong>
                                                <p>{{ $value->shipping ? $value->shipping->address : '' }}</p>
                                            </td>
                                            <td>{{ $value->shipping ? $value->shipping->phone : '' }}</td>
                                            <td>{{ $value->courier_tracker }}</td>
                                            <td>
                                                @if ($value->ordernote)
                                                    <p>{{ $value->ordernote->note }}</p>
                                                    <a data-bs-toggle="modal" data-bs-target="#orderNote{{ $value->id }}"
                                                        title="Order Note"><i class="fe-file-text"></i></a>
                                                @else
                                                    <p></p>
                                                @endif
                                            </td>
                                            <td>৳{{ $value->amount }}</td>
                                            <td>{{ $value->status ? $value->status->name : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="custom-paginate">
                            {{ $show_data->links('pagination::bootstrap-4') }}
                        </div>
                    </div> <!-- end card body-->

                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
    </div>
    <!-- Assign User End -->
    <div class="modal fade" id="asignUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.assign') }}" id="order_assign">
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">Select..</option>
                                @foreach ($users as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success asign_user_button">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign User End-->

    <!-- Assign User End -->
    <div class="modal fade" id="changeStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Status Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.status') }}" id="order_status_form">
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="order_status" id="order_status" class="form-control">
                                <option value="">Select..</option>
                                @foreach ($orderstatus as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success change_status_button">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign User End-->

    <!-- pathao coureir start -->
    @foreach ($show_data as $key => $value)
        <div class="modal fade" id="pathao{{ $value->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pathao Courier - {{ $value->invoice_id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.order.pathao') }}" id="order_sendto_pathao">

                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="id" value="{{ $value->id }}">
                                <label for="pathaostore" class="form-label">Store</label>
                                <select name="pathaostore" id="pathaostore" class="pathaostore form-control">
                                    <option value="">Select Store...</option>
                                    @if (isset($pathaostore['data']['data']))
                                        @foreach ($pathaostore['data']['data'] as $key => $store)
                                            <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                                        @endforeach
                                    @else
                                    @endif
                                </select>
                                @if ($errors->has('pathaostore'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pathaostore') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!-- form group end -->
                            <div class="form-group mt-3">
                                <label for="pathaocity" class="form-label">City</label>
                                <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control"
                                    style="width:100%">
                                    <option value="">Select City...</option>
                                    @if (isset($pathaocities['data']['data']))
                                        @foreach ($pathaocities['data']['data'] as $key => $city)
                                            <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                        @endforeach
                                    @else
                                    @endif
                                </select>
                                @if ($errors->has('pathaocity'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pathaocity') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!-- form group end -->
                            <div class="form-group mt-3">
                                <label for="" class="form-label">Zone</label>
                                <select name="pathaozone" id="pathaozone"
                                    class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}"
                                    value="{{ old('pathaozone') }}" style="width:100%">
                                </select>
                                @if ($errors->has('pathaozone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pathaozone') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!-- form group end -->
                            <div class="form-group mt-3">
                                <label for="" class="form-label">Area</label>
                                <select name="pathaoarea" id="pathaoarea"
                                    class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}"
                                    value="{{ old('pathaoarea') }}" style="width:100%">
                                </select>
                                @if ($errors->has('pathaoarea'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pathaoarea') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!-- form group end -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- order details starts -->
    @foreach ($show_data as $key => $value)
        <div class="modal fade" id="order{{ $value->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: 1px solid #ddd;">
                        <h5 class="modal-title">Order #{{ $value->invoice_id }} </h5>
                        <span style="flex: 1 1 auto;text-align: right;color: green;font-size: 14px;font-weight: 600;">Order
                            {{ $value->status->name ?? '' }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <section class="seection-padding">
                            <div class="cart_details table-responsive-sm">
                                <div class="card">
                                    <!--<div class="card-header">-->
                                    <!--    <h5>Order Information</h5>-->
                                    <!--</div>-->
                                    <div class="card-body cartlist">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="billings-details">
                                                    <h5 style="font-weight: 700;">Billings Details</h5>
                                                    <div class="model-item mt-2">
                                                        <ul class="list-unstyled mt-2">
                                                            <p>{{ $value->shipping->name ?? '' }}</p>
                                                            <p>{{ $value->shipping->address ?? '' }}</p>
                                                        </ul>
                                                    </div>
                                                    @if ($value->shipping->email)
                                                        <div class="model-item">
                                                            <ul class="list-unstyled mt-2">
                                                                <li><strong>Email</strong></li>
                                                                <li> <a href="#">{{ $value->shipping->email ?? '' }}</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <div class="model-item">
                                                        <ul class="list-unstyled mt-2">
                                                            <li><strong>Phone</strong></li>
                                                            <li> <a href="#">{{ $value->shipping->phone ?? '' }}</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="sipping-details">
                                                    <h5 style="font-weight: 700;">Shipping Details</h5>
                                                    <div class="model-item mt-2">
                                                        <ul class="list-unstyled mt-2">
                                                            <p>{{ $value->shipping->name ?? '' }}</p>
                                                            <p>{{ $value->shipping->address ?? '' }}</p>
                                                        </ul>
                                                    </div>
                                                    <div class="model-item">
                                                        <ul class="list-unstyled mt-2">
                                                            <li><strong>Shipping Method</strong></li>
                                                            <li>
                                                                <p>{{ $value->shipping->area ?? '' }}</p>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <ul class="list-unstyled mt-2">
                                                    <li><strong>Payment Via</strong></li>
                                                    <li> <a>{{ $value->payment->payment_method ?? '' }}</a></li>
                                                </ul>
                                            </div>
                                            <div class="col-sm-4">
                                                @if ($value->payment)
                                                    <ul class="list-unstyled mt-2">
                                                        <li><strong>TRX ID</strong></li>
                                                        <li> <a>{{ $value->payment->trx_id ?? '' }}</a></li>
                                                    </ul>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                @if ($value->payment)
                                                    <ul class="list-unstyled mt-2">
                                                        <li><strong>Sender Number</strong></li>
                                                        <li> <a>{{ $value->payment->sender_number ?? '' }}</a></li>
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>


                                        <table class="cart_table table table-bordered table-striped text-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%;">Product</th>
                                                    <th style="width: 20%;">Quantity</th>
                                                    <th style="width: 20%;">Total</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($value->orderdetails as $detail)
                                                    <tr>
                                                        <td style="width: 40%;">{{ $detail->product_name }}
                                                            @if ($detail->product_size)
                                                                <br> Size: {{ $detail->product_size }}
                                                            @endif
                                                            <br>
                                                            @if ($detail->product_color)
                                                                Color: {{ $detail->product_color }}
                                                            @endif
                                                        </td>
                                                        <td style="width: 20%;">{{ $detail->qty }}</td>
                                                        <td style="width: 20%;">{{ $detail->sale_price * $detail->qty }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </section>
                        <!-- form group end -->
                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
                        <!--<button type="submit" class="btn btn-success">Submit</button>-->
                        <a class="btn btn-success" href="{{ route('admin.order.edit', ['id' => $value->id]) }}" title="Edit">
                            Edit </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- order details ends -->
    <!-- order details starts -->
    @foreach ($show_data as $key => $value)
        <div class="modal fade" id="orderNote{{ $value->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: 1px solid #ddd;">
                        <h5 class="modal-title">Order #{{ $value->invoice_id }} </h5>
                        <span style="flex: 1 1 auto;text-align: right;color: green;font-size: 14px;font-weight: 600;">Order
                            {{ $value->status->name ?? '' }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <section class="seection-padding">
                            <div class="cart_details table-responsive-sm">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Order Notes</h5>
                                    </div>
                                    <div class="card-body cartlist">
                                        <table class="cart_table table table-bordered table-striped text-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 60%;">Date</th>
                                                    <th style="width: 40%;">Note</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($value->ordernotes as $note)
                                                    <tr>
                                                        <td style="width: 60%;">
                                                            {{ date('d-m-Y', strtotime($note->created_at)) }}<br>
                                                            {{ date('h:i:s a', strtotime($note->created_at)) }}
                                                        </td>
                                                        <td style="width: 40%;">{{ $note->note }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <!-- form group end -->
                    </div>

                </div>
            </div>
        </div>
    @endforeach
    <!-- order details ends -->
    <!-- Assign User End -->
    <div class="modal fade" id="orderNote" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.order.note_create') }}" id="order_note_form" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="order_note" class="form-label">Order Note</label>
                            <textarea name="order_note" id="order_note" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success ">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign User End-->
    <!-- pathao courier  End-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".checkall").on('change', function () {
                $(".checkbox").prop('checked', $(this).is(":checked"));
            });

            // order assign
            $(document).on('submit', 'form#order_assign', function (e) {
                $(".asign_user_button").prop('disabled', true);
                e.preventDefault();
                var url = $(this).attr('action');
                var method = $(this).attr('method');
                let user_id = $(document).find('select#user_id').val();

                var order = $('input.checkbox:checked').map(function () {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        user_id,
                        order_ids
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });

            });

            $(document).on('change', 'form#order_assign', function (e) {
                $(".asign_user_button").prop('disabled', false);
            });

            $(document).on('click', '.order_note', function (e) {
                var id = $(this).data('id');
                $('#order_id').val(id);
            });

            $(document).on('submit', 'form#order_status_form', function (e) {
                $(".change_status_button").prop('disabled', true);
                e.preventDefault();
                var url = $(this).attr('action');
                var method = $(this).attr('method');
                let order_status = $(document).find('select#order_status').val();

                var order = $('input.checkbox:checked').map(function () {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                $(".change_status_button").text('Processing...');

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        order_status,
                        order_ids
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error('Failed something wrong');
                            $(".change_status_button").text('Submit');
                        }
                    }
                });
            });

            // order status change
            $(document).on('change', 'form#order_status_form', function (e) {
                $(".change_status_button").prop('disabled', false);
            });

            // order delete
            $(document).on('click', '.order_delete', function (e) {
                $(".order_delete").prop('disabled', true);
                e.preventDefault();
                var url = $(this).attr('href');
                var order = $('input.checkbox:checked').map(function () {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                alert('Are you sure?');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        order_ids
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });

            });

            // multiple print
            $(document).on('click', '.multi_order_print', function (e) {
                $(".multi_order_print").prop('disabled', true);
                e.preventDefault();
                var url = $(this).attr('href');
                var order = $('input.checkbox:checked').map(function () {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select Atleast One Order!');
                    return;
                }
                $.ajax({
                    type: 'GET',
                    url,
                    data: {
                        order_ids
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            console.log(res.items, res.info);
                            var myWindow = window.open("", "_blank");
                            myWindow.document.write(res.view);
                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });
            });
            // multiple print
            $(document).on('click', '.multi_pos_print', function (e) {
                $(".multi_pos_print").prop('disabled', true);
                e.preventDefault();
                var url = $(this).attr('href');
                var order = $('input.checkbox:checked').map(function () {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select Atleast One Order!');
                    return;
                }
                $.ajax({
                    type: 'GET',
                    url,
                    data: {
                        order_ids
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            console.log(res.items, res.info);
                            var myWindow = window.open("", "_blank");
                            myWindow.document.write(res.view);
                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });
            });
            // multiple courier
            $(document).on('click', '.multi_order_courier', function (e) {
                $(".multi_order_courier").prop('disabled', true);
                e.preventDefault();
                var url = $(this).attr('href');
                var order = $('input.checkbox:checked').map(function () {
                    return $(this).val();
                });
                var order_ids = order.get();

                if (order_ids.length == 0) {
                    toastr.error('Please Select An Order First !');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        order_ids
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            window.location.reload();

                        } else {
                            toastr.error('Failed something wrong');
                        }
                    }
                });

            });
        })
    </script>
@endsection
