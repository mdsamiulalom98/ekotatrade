@extends('backEnd.layouts.master')
@section('title','Order Report')
@section('content')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<style>
    p{
        margin:0;
    }
   @page { 
        margin: 50px 0px 0px 0px;
    }
   @media print {
    td{
        font-size: 18px;
    }
    p{
        margin:0;
    }
    title {
        font-size: 25px;
    }
    header,footer,.no-print,.left-side-menu,.navbar-custom {
      display: none !important;
    }
  }

    .info-box {
        padding: 25px;
        color: #fff;
        border-radius: 5px;
    }
    span.info-box-text {
        font-size: 16px;
    }
    .progress-description {
        font-size: 16px;
    }
    .info-box .progress {
        height: 3px;
        margin: 6px 0;
    }
    span.info-box-icon {
        font-size: 22px;
    }
    .info-box-content span {
        font-size: 16px;
        font-weight: 600;
    }
    p{
        margin:0;
    }
   @page { 
        margin: 50px 0px 0px 0px;
    }
   @media print {
    td{
        font-size: 18px;
    }
    p{
        margin:0;
    }
    title {
        font-size: 25px;
    }
    header,footer,.no-print,.left-side-menu,.navbar-custom {
      display: none !important;
    }
    
  }
</style>
@endsection 
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Order Report</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="no-print">
                    <div class="row">   
                        <div class="col-sm-2">
                            <div class="form-group">
                               <label for="keyword" class="form-label">Keyword</label>
                                <input type="text" value="{{request()->get('keyword')}}" class="form-control" name="keyword">
                            </div>
                        </div>
                        <!--col-sm-3-->
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="user_id" class="form-label">Assign User </label>
                                <select class="form-control select2 @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" >
                                    <option value="">Select..</option>
                                    @foreach($users as $key=>$value)
                                        <option value="{{$value->id}}" @if(request()->get('user_id') == $value->id) selected @endif>{{$value->name}}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Order Status </label>
                                <select class="form-control select2 @error('status') is-invalid @enderror" name="status" value="{{ old('status') }}" >
                                    <option value="">All</option>
                                    @foreach($orderstatus as $key=>$value)
                                        <option value="{{$value->id}}" @if(request()->get('status') == $value->id) selected @endif>{{$value->name}}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-2">
                            <div class="form-group">
                               <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" value="{{request()->get('start_date')}}"  class="form-control flatdate" name="start_date">
                            </div>
                        </div>
                        <!--col-sm-3--> 
                        <div class="col-sm-2">
                            <div class="form-group">
                               <label for="end_date" class="form-label">End Date</label>
                                <input type="date" value="{{request()->get('end_date')}}" class="form-control flatdate" name="end_date">
                            </div>
                        </div>
                        <!--col-sm-3-->
                        <div class="col-sm-2">
                            <div class="form-group mb-3">
                                <button class="btn btn-primary">Submit</button>
                                <a href="{{route('admin.order_report')}}" class="btn btn-danger">Reset</a>
                            </div>
                        </div>
                        <!-- col end -->
                    </div>  
                </form>
                <div class="row mb-3">
                    <div class="col-sm-6 no-print">
                         {{$orders->links('pagination::bootstrap-4')}}
                    </div>
                    <div class="col-sm-6">
                        <div class="export-print text-end">
                            <button onclick="printFunction()"class="no-print btn btn-success"><i class="fa fa-print"></i> Print</button>
                            <button id="export-excel-button" class="no-print btn btn-info"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>
                </div>
                <div id="content-to-export">
                    <div class="table-responsive">
                        <table class="table nowrap w-100">
                            <colgroup>
                                <col style="width: 10%;">
                                <col style="width: 15%;">
                                <col style="width: 12%;">
                                <col style="width: 12%;">
                                <col style="width: 6%;">
                                <col style="width: 6%;">
                                <col style="width: 8%;">
                             </colgroup>
                        <thead>
                            <tr>
                                <th >Invoice</th>
                                <th >Customer</th>
                                <th >Phone</th>
                                <th >Order Status</th>
                                <th >Item</th>
                                <th >Qty</th>
                                <th >Total</th>
                            </tr>
                        </thead>               
                    
                        <tbody>
                            @php
                                $total_qty = 0;
                                $total_items = 0;
                                $total_amount = 0;
                            @endphp
                            @foreach($orders as $key=>$value)
                            <tr>
                                <td><a data-bs-toggle="modal" data-bs-target="#order{{$value->id}}" class="btn btn-primary" title="Order Details"><i class="fe-eye"></i></a>
                                    <span class="d-block">{{$value->invoice_id}}</span>
                                </td>
                                <td>{{$value->shipping?$value->shipping->name:''}}</td>
                                <td>{{$value->shipping?$value->shipping->phone:''}}</td>
                                <td>{{$value->status->name}}</td>
                                <td>{{$value->orderdetails->count() ?? 0}}</td>
                                <td>{{$value->orderdetails->sum('qty') ?? 0}}</td>
                                <td class="taka-sign">{{$value->amount}}</td>
                            </tr>
                            @php
                                $total_items += $value->orderdetails->count();
                                $total_qty += $value->orderdetails->sum('qty');
                                $total_amount += $value->amount - $value->shippingfee;
                            @endphp
                            @endforeach
                         </tbody>
                         <tfoot>
                             <tr>
                                 <td colspan="4" class="text-end"><strong>Total</strong></td>
                                 <td><strong class="">{{$total_items}}</strong></td>
                                 <td><strong class="">{{$total_qty}}</strong></td>
                                 <td><strong class="taka-sign">{{$total_amount}}</strong></td>
                             </tr>
                             
                         </tfoot>
                        </table>
                    </div>
                
                 <div class="card-body">
                     <div class="row">
                         
                    
                @foreach ($order_statuses as $key => $status)
                <div class="col-md-6 col-xl-3">
                    <div class="widget-rounded-circle card">
                        <div class="card-body bg-dark">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-start">
                                        <p class="text-white mb-1 text-truncate">{{ $status->name }}</p>
                                        <div class="d-flex justify-content-between">
                                            <h3 class="text-white mt-1"><span
                                                data-plugin="counterup">{{ $status->orders_count }}</span></h3>
                                           <h3 class="text-white mt-1"><span class="text-white mt-1 taka-sign" data-plugin="counterup">{{$status->orders->sum('amount') ?? 0}}</span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row-->
                        </div>
                    </div> <!-- end widget-rounded-circle-->
                </div> <!-- end col-->
                 
                @endforeach
                
                <div class="col-md-6 col-xl-3">
                    <div class="widget-rounded-circle card">
                        <div class="card-body bg-blue">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-start">
                                        <p class="text-white mb-1 text-truncate">Total Order</p>
                                        <div class="d-flex justify-content-between">
                                            <h3 class="text-white mt-1"><span
                                                data-plugin="counterup">{{ $allorders->count() }}</span></h3>
                                           <h3 class="text-white mt-1"><span class="text-white mt-1 taka-sign" data-plugin="counterup">{{$allorders->sum('amount') ?? 0}}</span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row-->
                        </div>
                    </div> <!-- end widget-rounded-circle-->
                </div> <!-- end col-->
                
            </div>
                    <div class="row">
                      <div class="col-sm-4">
                        <div class="info-box bg-blue">
                          <span class="info-box-icon"><i class="fe-shopping-cart"></i></span>
            
                          <div class="info-box-content">
                            <span class="info-box-text">Total Sales</span>
                            <span class="info-box-number">{{$total_sales}} Tk</span>
                            <div class="progress">
                              <div class="progress-bar" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                              Total sales summary
                            </span>
                          </div>
                          <!-- /.info-box-content -->
                        </div>
                      </div>
                      <!-- col end -->
                      <div class="col-sm-4">
                        <div class="info-box bg-danger">
                          <span class="info-box-icon"><i class="fe-airplay"></i></span>
            
                          <div class="info-box-content">
                            <span class="info-box-text">Total Purchase</span>
                            <span class="info-box-number">{{$total_purchase}} Tk</span>
            
                            <div class="progress">
                              <div class="progress-bar" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                              Total purchase summary
                            </span>
                          </div>
                          <!-- /.info-box-content -->
                        </div>
                      </div>
                      <!-- col end -->
                      <div class="col-sm-4">
                        <div class="info-box bg-success">
                          <span class="info-box-icon"><i class="fe-bar-chart-line"></i></span>
            
                          <div class="info-box-content">
                            <span class="info-box-text">Total Profit</span>
                            <span class="info-box-number">{{$total_sales-$total_purchase}} Tk</span>
            
                            <div class="progress">
                              <div class="progress-bar" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                              Total profit summary
                            </span>
                          </div>
                          <!-- /.info-box-content -->
                        </div>
                      </div>
                      <!-- col end -->
                    </div>
                   
                  </div>
                
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>


<!-- order details starts -->
@foreach($orders as $key=>$value)
<div class="modal fade" id="order{{$value->id}}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: 1px solid #ddd;">
        <h5 class="modal-title">Order #{{$value->invoice_id}} </h5>
        <span style="flex: 1 1 auto;text-align: right;color: green;font-size: 14px;font-weight: 600;">Order {{$value->status->name ?? ''}}</span>
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
                                            <p>{{$value->shipping->name ?? ''}}</p>
                                            <p>{{$value->shipping->address ?? ''}}</p>
                                        </ul>
                                    </div>
                                    @if($value->shipping->email)
                                    <div class="model-item">
                                        <ul class="list-unstyled mt-2">
                                            <li><strong>Email</strong></li>
                                            <li> <a href="#">{{$value->shipping->email ?? ''}}</a></li>
                                        </ul>
                                    </div>
                                    @endif
                                    <div class="model-item">
                                        <ul class="list-unstyled mt-2">
                                            <li><strong>Phone</strong></li>
                                            <li> <a href="#">{{$value->shipping->phone ?? ''}}</a></li>
                                        </ul>
                                    </div>
                                    <div class="model-item">
                                        <ul class="list-unstyled mt-2">
                                            <li><strong>Payment Via</strong></li>
                                            <li> <a href="#">{{$value->payment->payment_method ?? ''}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="sipping-details">
                                    <h5 style="font-weight: 700;">Shipping Details</h5>
                                    <div class="model-item mt-2">
                                        <ul class="list-unstyled mt-2">
                                            <p>{{$value->shipping->name ?? ''}}</p>
                                            <p>{{$value->shipping->address ?? ''}}</p>
                                        </ul>
                                    </div>
                                    <div class="model-item">
                                        <ul class="list-unstyled mt-2">
                                            <li><strong>Shipping Method</strong></li>
                                            <li>
                                                <p>{{$value->shipping->area ?? ''}}</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
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
                                @foreach($value->orderdetails as $detail)
                                <tr>
                                    <td style="width: 40%;">{{$detail->product_name}} @if($detail->product_size)<br> Size: {{ $detail->product_size }}@endif<br>@if($detail->product_color) Color: {{ $detail->product_color }}@endif</td>
                                    <td style="width: 20%;">{{$detail->qty}}</td>
                                    <td style="width: 20%;">{{$detail->sale_price * $detail->qty}}</td>
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
        <a class="btn btn-success" href="{{route('admin.orders', ['slug'=>'all'])}}" title="Edit"> All Orders </a>
      </div>
    </div>
  </div>
</div>
@endforeach
@endsection
@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        flatpickr(".flatdate", {});
    });
</script>
<script>
    function printFunction() {
        window.print();
    }
</script>
<script>
    $(document).ready(function() {
        $('#export-excel-button').on('click', function() {
            var contentToExport = $('#content-to-export').html();
            var tempElement = $('<div>');
            tempElement.html(contentToExport);
            tempElement.find('.table').table2excel({
                exclude: ".no-export",
                name: "Order Report" 
            });
        });
    });
</script>



@endsection