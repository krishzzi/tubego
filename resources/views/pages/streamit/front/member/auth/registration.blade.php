@extends('layouts.streamit.front.master')
@section('content')




    <!-- MainContent -->
    <section class="sign-in-page" style="background-image: {{$system->register_bg}}">
        <div class="container">
            <div class="row justify-content-center align-items-center height-self-center">
                <div class="col-lg-5 col-md-12 align-self-center">
                    <div class="sign-user_card ">
                        <div class="sign-in-page-data">
                            <div class="sign-in-from w-100 m-auto">
                                <h3 class="mb-3 text-center">Sign Up</h3>

                                @if(!empty($error))
                                    Message : <i class="text-right text-warning">{{$error}}</i>
                                @endif

                                <form class="mt-4" action="{{ route('register.user') }}" method="post" > @CSRF
                                    <div class="form-group">
                                        <input type="text"  name="name" class="form-control mb-0" id="exampleInputEmail2" placeholder="Enter Full Name" autocomplete="off" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control mb-0" id="exampleInputEmail3" placeholder="Enter email" autocomplete="off" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control mb-0" id="exampleInputPassword2" placeholder="Password" required>
                                    </div>

                                    <div class="form-group">
                                        <input type="password" name="c_password" class="form-control mb-0" id="exampleInputPassword2" placeholder="Confirm Password" required>
                                    </div>

                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="tc_check">
                                        <label class="custom-control-label" for="customCheck">I accept <a href="#" class="text-primary"> Terms and Conditions</a></label>
                                    </div>

                                    <div class=" mb-3">
                                        <button type="submit" class="btn btn-success btn-block">Register</button>
                                    </div>

                                </form>


{{--                                Register With Providers--}}

                            <!-- Split dropup button -->
                                <div class="btn-group dropup w-100 m-auto">
                                    <button type="button" class="btn btn-sm btn-hover btn-block ">
                                        Register With
                                    </button>
                                    <button type="button" class="btn btn-sm  btn-hover dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Choose Provider</span>
                                    </button>
                                    <div class="dropdown-menu bg-transparent">
                                        <!-- Dropdown menu links -->
                                        <a href="{{ route('login.user') }}/github" class="btn btn-dark btn-sm btn-block"> Github</a>
                                        <a href="{{ route('login.user') }}/google" class="btn btn-hover btn-sm btn-block">Google</a>
                                        <a href="{{ route('login.user') }}/facebook" class="btn btn-primary btn-sm btn-block">Facebook</a>
                                    </div>
                                </div>







                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-center links">
                                Already have an account? <a href="{{ route('login.user') }}" class="text-primary ml-2">Sign In</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- MainContent End-->




@endsection
