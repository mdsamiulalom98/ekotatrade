<!doctype html>
<html lang="en">
    @php
        $order = App\Models\Order::where('id',$order_id)->with('orderdetails','payment','shipping','customer', 'status')->first();
       @endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Your Order is {{$order->status->name ?? 'Processing'}} on Ekota Trade</title>
</head>
<body class="bg-white">
<div style="background:#ddd; width:100%;text-align:center !important">
<div style="background:#fff; width:90%;margin:0 auto !important">
     
    <!-- email template -->
    <table class="body-wrap" style="background:#fff; width: 100%; margin: 0;">
        <tbody style="background:#4DBC60;">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 25px; margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                   <h3 style="color:#fff;text-align:center;padding:20px 0">Your Order Number: #{{$order->invoice_id}}</h3>
                 </td>
            </tr>
        </tbody>
    </table>
    <table class="body-wrap" style="background:#fff; width: 100%;text-align:center;">
    <tbody>
        <tr style="text-align:center">
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
               <img src="https://www.ekotatrade.com.bd/public/frontEnd/images/ekota-logo.png" style="width:180px;margin-top:15px">
             </td>
        </tr>
    </tbody>
</table>
    <table class="body-wrap" style="background:#fff; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;  font-size: 16px; width: 100%; margin: 0;padding:0 30px">
        <tbody style="background:#fff">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; padding-top: 15px;">
                    Hi Dear <strong>{{$order->shipping?$order->shipping->name:''}}</strong>
                 </td>
            </tr>
            <tr>
                <td style="padding:30px 0;border:0">Your order has been cancelled. Order ID #{{$order->invoice_id}}. You will receive a phone call soon for order confirmation, check the status of your order from <a href="https://www.ekotatrade.com.bd/customer/orders" style="font-weight:900;color:blue">here</a></td></br>
            </tr>
        </tbody>
    </table>
     <table class="body-wrap" style="background:#fff; width: 100%; margin: 0;;padding:0 30px">
        <tbody>
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 25px; margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                   <h3 style="color:#4DBC60;padding-bottom:10px">[Order # {{$order->invoice_id}}] ({{$order->created_at->format('d M Y')}})</h3>
                 </td>
            </tr>
        </tbody>
    </table>
    
    
    <!-- ./ email template -->
    

    <table class="body-wrap" style="background:#fff; width: 100%; margin: 0;text-align:center !important">
        <tbody style="background:#4DBC60;">
            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box;  margin: 0;border:0">
                <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; margin: 0;">
                   <p style="color:#fff;text-align:center;padding:20px 0;font-size:15px;letter-spacing:2px">This is an automatically generated e-mail from <br>
                   <a href="https://ekotatrade.com.bd">ekotatrade.com.bd</a>
                   <br> Please do not reply to this e-mail.</p>
                 </td>
            </tr>
        </tbody>
    </table>
    
</div>
</div>
</body>
</html>