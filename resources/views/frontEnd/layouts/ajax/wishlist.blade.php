<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Cart</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $value)
            <tr>
                <td><img src="{{asset($value->options->image)}}" alt=""></td>
                <td>{{$value->name}}</td>
                <td>{{$value->qty}}</td>
                <td>{{$value->price}} ৳</td>
                <td><button class="wcart-btn"><i data-feather="shopping-cart"></i></button></td>
                <td><button class="remove-cart wishlist_remove" data-id="{{$value->rowId}}"><i data-feather="x"></i></button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="{{asset('public/frontEnd/js/jquery-3.6.3.min.js')}}"></script>
<!-- feather icon -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
<script>
  feather.replace()
</script>
<script>
    $('.wishlist_remove').on('click',function(){
    var id = $(this).data('id');   
    $("#loading").show();
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('wishlist.remove')}}",
           success:function(data){               
            if(data){
                console.log('success');
                $("#wishlist").html(data);
                $("#loading").hide();
                return wishlist_count();
            }else{
               $("#wishlist").empty();
            }
           }
        });
     }  
   });
    // wishlist_remove end
    function wishlist_count(){
        $.ajax({
           type:"GET",
           url:"{{route('wishlist.count')}}",
           success:function(data){               
            if(data){
                $(".wishlist-qty").html(data);
            }else{
               $(".wishlist-qty").empty();
            }
           }
        }); 
   };
    // wishlist_count end
</script>