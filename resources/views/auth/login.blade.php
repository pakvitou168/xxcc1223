@extends('layouts.app')
@section('content')

    <body class="login">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="container sm:px-10">
                <div class="block xl:grid grid-cols-2 gap-4">
                    <!-- BEGIN: Login Info -->
                    <div class="hidden xl:flex flex-col min-h-screen">
                        <a href="" class="-intro-x flex items-center pt-5">
                            <img alt="Midone Tailwind HTML Admin Template" class="w-6"
                                src="{{ asset('/images/logo.svg') }}">
                            <span class="text-white text-lg ml-3">Phillip General Insurance</span>
                        </a>
                        <div class="my-auto">
                            <img alt="Midone Tailwind HTML Admin Template" class="-intro-x w-1/2 -mt-16"
                                src="{{ asset('/images/illustration.svg') }}">
                            <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">
                                A few more clicks to
                                <br>
                                sign in to your account.
                            </div>
                        </div>
                    </div>
                    <!-- END: Login Info -->
                    <!-- BEGIN: Login Form -->
                    <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                        <div
                            class="my-auto mx-auto xl:ml-20 bg-white dark:bg-dark-1 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                            <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">
                                Sign In
                            </h2>
                            <div class="intro-x mt-2 text-gray-500 xl:hidden text-center">A few more clicks to sign in to
                                your account. Manage all your e-commerce accounts in one place</div>
                            <div class="intro-x mt-8">
                                <input type="text"
                                    class="intro-x login__input form-control py-3 px-4 border-gray-300 block @error('password') border-red-500 @enderror"
                                    value="{{ old('username') }}" placeholder="Username" name="username">
                                @error('username')
                                    <span class="text-red-700 mx-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="relative">
                                    <div class="relative z-0">
                                        <input type="password"
                                            class="intro-x login__input form-control py-3 px-4 border-gray-300 block mt-4 @error('password') border-red-500 @enderror"
                                            placeholder="Password" name="password" id="password" />
                                    </div>
                                    <div onclick="hidePassword()" class="absolute right-2 top-3 z-10 hidden cursor-pointer"
                                        id="hide-pass">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="feather feather-eye-off">
                                            <path
                                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                            </path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>
                                    </div>
                                    <div onclick="showPassword()" class="absolute right-2 top-3 z-10 cursor-pointer"
                                        id="show-pass">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="feather feather-eye">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </div>
                                </div>
                                @error('password')
                                    <span class="text-red-700 mx-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="intro-x flex text-gray-700 dark:text-gray-600 text-xs sm:text-sm mt-4">
                                <div class="flex items-center mr-auto">
                                    <input id="remember-me" type="checkbox" class="form-check-input border mr-2">
                                    <label class="cursor-pointer select-none" for="remember-me" name="remember">Remember
                                        me</label>
                                </div>
                                <a href="">Forgot Password?</a>
                            </div>
                            <div class="intro-x mt-5 xl:mt-8 text-center xl:text-left bg-red-100">
                                <button class="btn btn-primary py-3 px-4 w-full xl:w-full xl:mr-3 align-top">Login</button>
                                {{-- <button class="btn btn-outline-secondary py-3 px-4 w-full xl:w-32 mt-3 xl:mt-0 align-top">Sign up</button> --}}
                            </div>
                            <div class="intro-x mt-10 xl:mt-24 text-gray-700 dark:text-gray-600 text-center xl:text-left">
                                By signin up, you agree to our
                                <br>
                                <a class="text-theme-1 dark:text-theme-10" href="">Terms and Conditions</a> &amp; <a
                                    class="text-theme-1 dark:text-theme-10" href="">Privacy Policy</a>
                            </div>
                        </div>
                    </div>
                    <!-- END: Login Form -->
                </div>
            </div>
        </form>
        <!-- BEGIN: JS Assets-->
        <!-- END: JS Assets-->
    </body>
@endsection
<script>
    function showPassword() {
        document.getElementById('show-pass').style.display = 'none';
        document.getElementById('hide-pass').style.display = 'block';
        document.getElementById('password').type = 'text';
    }

    function hidePassword() {
        document.getElementById('show-pass').style.display = 'block';
        document.getElementById('hide-pass').style.display = 'none';
        document.getElementById('password').type = 'password';
    }
</script>
