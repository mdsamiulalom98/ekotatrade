@extends('frontEnd.layouts.master')
@section('title', 'Customer Register')
@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-5">
                    <div class="form-content">
                        <p class="auth-title"> Customer Registration </p>
                        <form action="{{ route('customer.store') }}" method="POST" data-parsley-validate="">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="name">Name <span style="color: red;">*</span></label>
                                <input type="text" id="name"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" placeholder="Your Name" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- col-end -->
                            <div class="d-flex align-items-end gap-2">
                                <div class="form-group mb-4 col">
                                    <label for="phone"> Phone Number <span style="color: red;">*</span></label>
                                    <input type="text" id="phone"
                                        class="form-control @error('phone') is-invalid @enderror" name="phone"
                                        value="{{ old('phone') }}" placeholder="Phone Number" required>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @if ($smsgatewayinfo->otp_login == 1)
                                    <div class="form-group col-4 mb-4">
                                        <button class="btn btn-theme text-white w-100 send_otp" type="button"
                                            disabled>Send OTP</button>
                                        <p id="resendText"></p>
                                    </div>
                                @endif
                            </div>
                            <!-- col-end -->
                            
                            @if ($smsgatewayinfo->otp_login == 1)
                                <div class="form-group mb-4">
                                    <label for="otp"> OTP <span style="color: red;">*</span> </label>
                                    <input type="otp" id="otp" minlength="4"
                                        class="form-control @error('otp') is-invalid @enderror" placeholder="otp "
                                        name="otp" value="{{ old('otp') }}" required>
                                    @error('otp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- col-end -->
                            @else
                                <div class="form-group mb-4">
                                    <label for="password"> Password <span style="color: red;">*</span> </label>
                                    <input type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror" placeholder="Password "
                                        name="password" value="{{ old('password') }}" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- col-end -->
                            @endif
                            <div class="form-group mb-4">
                                <label for="email">Email (Optional)</label>
                                <input type="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" placeholder="Your Email" >
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- col-end -->
                            <button class="submit-btn">Registration</button>
                            <div class="register-now no-account">
                                <p><i class="fa-solid fa-user"></i> If Registered ?</p>
                                <a href="{{ route('customer.login') }}"><i data-feather="edit-3"></i> Login</a>
                            </div>
                    </div>
                    <!-- col-end -->
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
            var phone = $('#phone').val();
            if (phone.length > 10) {
                $.ajax({
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        phone: phone,
                    },
                    url: "{{ route('customer.send_otp') }}",
                    success: function(data) {
                        if (data) {
                            toastr.success('Success', 'The OTP has been Sent successfully');
                            $(".send_otp").html('Sent OTP');
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
            $.ajax({
                type: "GET",
                url: "{{ route('customer.remove_otp') }}",
                success: function(response) {
                    if (response) {
                        toastr.success('Success', 'OTP Destroyed Successfully');
                        $("#resendText").html("");
                    } else {

                    }
                },
            });
        }
    </script>
    <script>
        $("#phone").on("input", function() {
            var code = $(this).val();
            code = code.replace(/\D/g, '');
            $(this).val(code);
            var isValid = false;
            // Check if the input is a number and has exactly 11 digits
            if (/^\d{11}$/.test(code)) {
                // Check if it starts with one of the allowed prefixes
                if (code.startsWith("013") || code.startsWith("014") ||
                    code.startsWith("015") || code.startsWith("016") ||
                    code.startsWith("017") || code.startsWith("018") ||
                    code.startsWith("019")) {
                    isValid = true;
                }
            }
            console.log('test: ' + isValid);
            if (isValid) {
                $("#phone").addClass('border-success');
                $("#phone").removeClass('border-danger');
                $(".send_otp").prop('disabled', false);
            } else {
                $("#phone").addClass('border-danger');
                $("#phone").removeClass('border-success');
                $(".send_otp").prop('disabled', true);
            }
        });
    </script>
@endpush