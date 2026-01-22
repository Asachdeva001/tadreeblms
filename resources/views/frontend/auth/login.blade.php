@extends('frontend.layouts.app' . config('theme_layout'))

@section('title', app_name().' | '.__('labels.frontend.auth.login_box_title'))

<style>

    .ftlogo {
        align-items: center !important;
        display: flex !important;
        justify-content: center !important;
    }


    .card-header {
        text-align: center;
        padding: 25px;
        background-color: transparent !important;
        border-bottom: 0 !important;
    }

    .error-block {
        margin-bottom: 16px;
        padding: 0 10px;
        font-size: 15px;
    }
    h2, h3 {
        font-weight: 500;
        margin-top: 20px;
    }

    .nws-button button {
        height: 50px !important;
        width: auto !important;
        font-size: 15px;
    }

    .form-group.nws-button {
        text-align: center;
    }

    .card {
        /* padding: 20px; */
        margin: 35px;
    }

    .breadcrumb-section {
        background-color: #c1902d4a;
        padding: 75px 0;
    }
    
</style>

@section('content')
    <div class="row justify-content-center align-items-center">
        <div class="col col-sm-8 align-self-center">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('labels.frontend.auth.login_box_title')
                    </strong>
                </div><!--card-header-->

                <div class="card-body">
                    {{ html()->form('POST', route('frontend.auth.login.post'))->open() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('validation.attributes.frontend.email'))->for('email') }}

                                {{ html()->email('email')
        ->class('form-control')
        ->placeholder(__('validation.attributes.frontend.email'))
        ->attribute('maxlength', 191)
        ->required() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('validation.attributes.frontend.password'))->for('password') }}

                                {{ html()->password('password')
        ->class('form-control')
        ->placeholder(__('validation.attributes.frontend.password'))
        ->required() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <div class="checkbox">
                                    {{ html()->label(html()->checkbox('remember', true, 1) . ' ' . __('labels.frontend.auth.remember_me'))->for('remember') }}
                                </div>
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="form-group">
                        <label>
                            Captcha:
                            <span id="captcha-question">
                                {{ session('captcha_question') }}
                            </span>

                            <button type="button" id="refresh-captcha" title="Reload captcha"
                                style="border:none;background:none;cursor:pointer;">
                                🔄
                            </button>
                        </label>

                        <input type="text" name="captcha" id="captcha-input" class="form-control" required>
                    </div>


                    <div class="row">
                        <div class="col">
                            <div class="form-group clearfix">
                                {{ form_submit(__('labels.frontend.auth.login_button')) }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col">
                            <div class="form-group text-right">
                                <a
                                    href="{{ route('frontend.auth.password.reset') }}">@lang('labels.frontend.passwords.forgot_password')</a>
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->
                    {{ html()->form()->close() }}

                    <div class="row">
                        <div class="col">
                            <div class="text-center">
                                {!! $socialiteLinks !!}
                            </div>
                        </div><!--col-->
                    </div><!--row-->
                </div><!--card body-->
            </div><!--card-->
        </div><!-- col-md-8 -->
    </div><!-- row -->
@endsection
<script>
document.getElementById('refresh-captcha').addEventListener('click', function () {
    fetch("{{ route('refresh.captcha') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('captcha-question').innerText = data.captcha_question;
            document.getElementById('captcha-input').value = '';
        });
});
</script>
