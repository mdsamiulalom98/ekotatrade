@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal=str_replace(',','',$subtotal);
    $subtotal=str_replace('.00', '',$subtotal);
    view()->share('subtotal',$subtotal);
@endphp
  <div class="main-cart-wrapper">
      <div class="main-cart-inner" id="cart-qty">
          <a href="{{route('customer.checkout')}}" class="main-cart-link">
             <i class="fa-solid fa-cart-shopping"></i>
              <span id="cart-items" class="count-badge">{{Cart::instance('shopping')->count()}}</span>
               <span>Cart</span>
          </a>
          
      </div>
  </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
<script>
  feather.replace()
</script>

<!-- cart js start -->
    <script>

      $('.cart_remove').on('click',function(){
        var id = $(this).data('id');   
        $("#loading").show();
        if(id){
          $.ajax({
             type:"GET",
             data:{'id':id},
             url:"{{route('cart.remove')}}",
             success:function(data){               
              if(data){
                $("#cartlist").html(data);
                $("#loading").hide();
                return cart_count()+cart_summary();
              }
             }
          });
         }  
       });

      function cart_count(){
          $.ajax({
             type:"GET",
             url:"{{route('cart.count')}}",
             success:function(data){               
              if(data){
                $("#cart-qty").html(data);
              }else{
                 $("#cart-qty").empty();
              }
             }
          }); 
       };
      function cart_summary(){
          $.ajax({
             type:"GET",
             url:"{{route('shipping.charge')}}",
             dataType: "html",
              success: function(response){
                  $('.cart-summary').html(response);
              }
          });
       };
    </script>
    <!-- cart js end -->