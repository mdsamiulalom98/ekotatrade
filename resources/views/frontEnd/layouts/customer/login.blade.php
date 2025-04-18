@extends('frontEnd.layouts.master')
@section('title', 'Customer Login')
@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-5">
                    <div class="form-content">
                        <p class="auth-title"> Customer Login </p>
                        <form action="{{ route('customer.signin') }}" method="POST" data-parsley-validate="">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="phone">Mobile Number</label>
                                <input type="text" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror" name="phone"
                                    value="{{ old('phone') }}" required>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                            <a href="{{ route('customer.forgot.password') }}" class="forget-link"><i
                                    class="fa-solid fa-unlock"></i> Forget Password?</a>
                            <div class="form-group mb-3">
                                <button class="submit-btn"> Login </button>
                            </div>
                            <!-- col-end -->
                        </form>
                        <div class="register-now no-account">
                            <p> If you don't have an account ? </p>
                            <a href="{{ route('customer.register') }}"><i data-feather="edit-3"></i> Create a New
                                Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script src="{{ asset('public/frontEnd/') }}/js/parsley.min.js"></script>
    <script src="{{ asset('public/frontEnd/') }}/js/form-validation.init.js"></script>
@endpush
