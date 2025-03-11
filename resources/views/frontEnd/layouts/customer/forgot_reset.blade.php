@extends('frontEnd.layouts.master')
@section('title', 'Forgot Password Reset')
@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-5">
                    <div class="form-content">
                        <p class="auth-title">Forgot Password Verify</p>
                        <form action="{{ route('customer.forgot.store') }}" method="POST" data-parsley-validate="">
                            @csrf
                            <div class="d-flex align-items-end gap-2">
                                <div class="form-group mb-4 col">
                                    <label for="otp">OTP</label>
                                    <input type="text" id="otp"
                                        class="form-control @error('otp') is-invalid @enderror" name="otp"
                                        value="{{ old('otp') }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-4 mb-4">
                                    <button data-phone="{{ Session::get('verify_phone') }}"
                                        class="btn btn-theme text-white w-100 send_otp" type="button">Resend
                                        OTP</button>
                                    <p id="resendText"></p>
                                </div>
                            </div>
                            <!-- col-end -->

                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    value="{{ old('password') }}" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- col-end -->
                            <div class="form-group mb-3">
                                <button class="submit-btn">submit</button>
                            </div>
                            <!-- col-end -->
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script src="{{ asset('public/frontEnd/') }}/js/parsley.min.js"></script>
    <script src="{{ asset('public/frontEnd/') }}/js/form-validation.init.js"></script>
    <script>
        // send_otp
        $(".send_otp").on("click", function() {
            $(".send_otp").prop('disabled', true);
            var phone = $(this).data('phone');
            if (phone) {
                $.ajax({
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        phone: phone
                    },
                    url: "{{ route('customer.forgot.resendotp') }}",
                    success: function(data) {
                        if (data) {
                            toastr.success('Success', 'OTP Sent successfully');
                            $(".send_otp").html('Sent');
                            $(".send_otp").addClass('button-clicked');
                            // Start the countdown timer
                            var countdown = 60; // Adjust the countdown time as needed
                            var timer = setInterval(function() {
                                countdown--;
                                $("#resendText").html("Resend OTP in (" + countdown +
                                    ")");

                                if (countdown <= 0) {
                                    clearInterval(timer);
                                    $(".send_otp").html("Send");
                                    $(".send_otp").removeClass('button-clicked');
                                    $(".send_otp").prop('disabled', false);
                                    remove_otp();
                                }
                            }, 1000);
                        }
                    },
                });
            }
        });

        function remove_otp() {
            toastr.success('Success', 'Send OTP Again');
            $("#resendText").html("");
        }
    </script>
@endpush