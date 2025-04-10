<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\Courierapi;

class ApiIntegrationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:paymentgateway-list', ['only' => ['pay_manage', 'pay_update']]);
        $this->middleware('permission:smsgateway-list', ['only' => ['sms_manage', 'sms_update']]);
        $this->middleware('permission:courierapi-list', ['only' => ['courier_manage', 'courier_update']]);}
    public function pay_manage()
    {
        $bkash = PaymentGateway::where('type', '=', 'bkash')->first();
        $shurjopay = PaymentGateway::where('type', '=', 'shurjopay')->first();
        return view('backEnd.apiintegration.pay_manage', compact('bkash', 'shurjopay'));
    }

    public function pay_update(Request $request)
    {
        $update_data = PaymentGateway::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);
        Toastr::success('Success', 'Data update successfully');
        return redirect()->back();
    }

    public function sms_manage()
    {
        $sms = SmsGateway::first();
        return view('backEnd.apiintegration.sms_manage', compact('sms'));
    }

    public function sms_update(Request $request)
    {
        $update_data = SmsGateway::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['order'] = $request->order ? 1 : 0;
        $input['forget_pass'] = $request->forget_pass ? 1 : 0;
        $input['password_g'] = $request->password_g ? 1 : 0;
        $input['otp_login'] = $request->otp_login ? 1 : 0;
        $input['landing_otp'] = $request->landing_otp ? 1 : 0;
        $update_data->update($input);
        Toastr::success('Success', 'Data update successfully');
        return redirect()->back();
    }

    public function courier_manage()
    {
        $steadfast = Courierapi::where('type', '=', 'steadfast')->first();
        $pathao = Courierapi::where('type', '=', 'pathao')->first();
        return view('backEnd.apiintegration.courier_manage', compact('steadfast', 'pathao'));
    }

    public function courier_update(Request $request)
    {
        $update_data = Courierapi::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);
        Toastr::success('Success', 'Data update successfully');
        return redirect()->back();
    }
}
