<a href="{{route('customer.wishlist')}}" class="main-menu-link"> 
    <span class="main-menu-span">
        <i class="fa-solid fa-heart"></i>
    </span>
    <span class="main-menu-compare-count">{{Cart::instance('wishlist')->content()->count()}}</span> 
</a>
<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
<script>
  feather.replace()
</script>


    