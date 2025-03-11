<a href="{{route('compare.product')}}" class="main-menu-link"> <span class="main-menu-span">
    <i class="fa fa-plus-square"></i>
</span>
<span class="main-menu-compare-count">{{Cart::instance('compare')->content()->count()}}</span> 
</a>