<!DOCTYPE html>
@if (config('app.display_type') == 'rtl' || (session()->has('display_type') && session('display_type') == 'rtl'))
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl"> --}}
{{-- @else --}}
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
{{-- @endlangrtl --}}

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', app_name())</title>
    <meta name="description" content="@yield('meta_description', 'Laravel 5 Boilerplate')">
    <meta name="author" content="@yield('meta_author', 'Anthony Rappa')">
    @if (config('favicon_image') != '')
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/logos/' . config('favicon_image')) }}" />
    @endif
    @yield('meta')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-all.css') }}">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet"
        media="screen" />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.0/css/select.dataTables.min.css" />
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    {{-- <link rel="stylesheet" --}}
    {{-- href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.standalone.min.css"/> --}}
    {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}




    @stack('before-styles')

    <!-- Check if the language is set to RTL, so apply the RTL layouts -->
    <!-- Otherwise apply the normal LTR layouts -->
    {{ style(mix('css/backend.css')) }}


    @stack('after-styles')

    @if (config('app.display_type') == 'rtl' || session('display_type') == 'rtl')
        <style>
            .float-left {
                float: right !important;
            }

            .float-right {
                float: left !important;
            }
        </style>
    @endif
    <style>
        .sidebar .nav-dropdown-items {
            padding: 0 0 0 40px !important;
        }
    </style>

</head>

<body class="{{ config('backend.body_classes') }}">
    <!-- @include('backend.includes.loader') -->
    @include('backend.includes.header')

    <div class="app-body">
        @include('backend.includes.sidebar')

        <main class="main">
            @include('includes.partials.logged-in-as')
            {{-- {!! Breadcrumbs::render() !!} --}}

            <div class="container-fluid" style="padding-top: 30px">
                <div class="animated fadeIn dashboardbox">
                    <div class="content-header">
                        @yield('page-header')
                    </div>
                    <!--content-header-->

                    @include('includes.partials.messages')
                    @yield('content')
                </div>
                <!--animated-->
            </div>
            <!--container-fluid-->
        </main>
        <!--main-->

        {{-- @include('backend.includes.aside') --}}
    </div>
    <!-- Modal Container -->
    <div id="modalContainer"></div>
    <!--app-body-->

    @include('backend.includes.footer')

    <!-- Scripts -->
    @stack('before-scripts')
    {!! script(mix('js/manifest.js')) !!}
    {!! script(mix('js/vendor.js')) !!}
    {!! script(mix('js/backend.js')) !!}
    <script>
        //Route for message notification
        var messageNotificationRoute = "{{ route('admin.messages.unread') }}"
    </script>
    <script src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"
        integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/main.js') }}" type="text/javascript"></script>
    <script>
        window._token = '{{ csrf_token() }}';
    </script>

    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
    CKEDITOR.config.versionCheck = false;
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof CKEDITOR !== 'undefined') {
            document.querySelectorAll('.editor').forEach(el => {
                CKEDITOR.replace(el);
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {

        if (typeof CKEDITOR === 'undefined') {
            console.error('CKEditor not loaded');
            return;
        }

        $('.editor').each(function () {
            let id = $(this).attr('id');

            // CKEditor REQUIRES an ID
            if (!id) {
                id = 'editor_' + Math.random().toString(36).substr(2, 9);
                $(this).attr('id', id);
            }

            CKEDITOR.replace(id, {
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
                filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{ csrf_token() }}',
                extraPlugins: 'smiley,lineutils,widget,codesnippet,prism,colorbutton,colordialog'
            });
        });

    });

    </script>

    @stack('after-scripts')

</body>

</html>