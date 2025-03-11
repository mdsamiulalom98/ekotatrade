<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="review-image-block">
                <div class="review-image-slider owl-carousel">
                    @foreach ($data as $image)
                        <div class="review-slider-item">
                            <img src="{{ asset($image->image) }}" alt="">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('public/frontEnd/js/owl.carousel.min.js') }}"></script>
<script>
    $(".review-image-slider").owlCarousel({
        margin: 15,
        loop: true,
        dots: false,
        autoplay: true,
        nav: true,
        items: 1,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,  
        navText: ["<i class='fa-solid fa-angle-left'></i>",
                "<i class='fa-solid fa-angle-right'></i>"
            ],
    });
</script>