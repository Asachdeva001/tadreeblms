@extends('backend.layouts.app')
@section('title', 'Employee'.' | '.app_name())
@push('after-styles')
<link rel="stylesheet" href="{{asset('assets/css/colors/switch.css')}}">
<style>
       .switch.switch-3d.switch-lg {
    width: 40px;
    height: 20px;
}
.switch.switch-3d.switch-lg .switch-handle {
    width: 20px;
    height: 20px;
}
</style>
@endpush

@section('content')

<form name="edit-employee"
      action="{{ route('admin.employee.update', $teacher->id) }}"
      method="POST"
      enctype="multipart/form-data">
@csrf

<div class="pb-3 d-flex justify-content-between align-items-center">
    <h4>Edit Trainee</h4>
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-primary">View Trainee</a>
</div>

<div class="card">
<div class="card-body">
<div class="row">

{{-- First Name --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label for="first_name" class="form-control-label">
        {{ __('labels.backend.teachers.fields.first_name') }}
    </label>
    <input class="form-control" type="text" name="first_name" id="first_name"
           value="{{ $teacher->first_name }}" required>
</div>

{{-- Last Name --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label for="last_name" class="form-control-label">
        {{ __('labels.backend.teachers.fields.last_name') }}
    </label>
    <input class="form-control" type="text" name="last_name" id="last_name"
           value="{{ $teacher->last_name }}" required>
</div>

{{-- Email --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label for="email" class="form-control-label">
        {{ __('labels.backend.teachers.fields.email') }}
    </label>
    <input class="form-control" type="email" id="email"
           value="{{ $teacher->email }}" readonly>
</div>

{{-- Password --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label for="password-field" class="form-control-label">
        {{ __('labels.backend.teachers.fields.password') }}
    </label>
    <div class="position-relative">
        <input type="password"
               name="password"
               id="password-field"
               class="form-control"
               placeholder="{{ __('labels.backend.teachers.fields.password') }}">

        <span onclick="togglePassword()"
              style="position:absolute;top:50%;right:10px;transform:translateY(-50%);cursor:pointer;">
            <i class="fa fa-eye" id="toggle-icon"></i>
        </span>
    </div>
</div>

{{-- Image --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Image</label>

    <div class="custom-file-upload-wrapper">
        <input type="file" name="image" id="customFileInput" class="custom-file-input">
        <label for="customFileInput" class="custom-file-label">
            <i class="fa fa-upload mr-1"></i> Choose a file
        </label>
    </div>

    <div class="mt-4">
        <label class="form-control-label">Uploaded Image</label>
        <img src="{{ asset('public/uploads/employee/'.$teacher->avatar_location) }}"
             style="width:100%;">
    </div>
</div>

{{-- ID Number --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Id Number</label>
    <input type="text" name="id_number" class="form-control"
           value="{{ $teacher->id_number }}">
</div>

{{-- Classification Number --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Classification Number</label>
    <input type="text" name="class_number" class="form-control"
           value="{{ $teacher->classfi_number }}">
</div>

{{-- Nationality --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Nationality</label>
    <select name="nationality" class="form-control">
        <option value="">Select Country</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}"
                {{ $teacher->nationality == $country->id ? 'selected' : '' }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- DOB --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Date of Birth</label>
    <input type="date" name="dob" class="form-control" value="{{ $teacher->dob }}">
</div>

{{-- Mobile --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Mobile Phone</label>
    <input type="text" name="mobile_number" class="form-control"
           value="{{ $teacher->phone }}">
</div>

{{-- Gender --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">
        {{ __('labels.backend.general_settings.user_registration_settings.fields.gender') }}
    </label><br>

    <label class="mr-3">
        <input type="radio" name="gender" value="male"
               {{ $teacher->gender == 'male' ? 'checked' : '' }}>
        {{ __('validation.attributes.frontend.male') }}
    </label>

    <label>
        <input type="radio" name="gender" value="female"
               {{ $teacher->gender == 'female' ? 'checked' : '' }}>
        {{ __('validation.attributes.frontend.female') }}
    </label>
</div>

{{-- Status --}}
<div class="col-lg-6 col-sm-12 mt-3">
    <label class="form-control-label">Status</label><br>
    <label class="custom-control custom-switch">
        <input type="checkbox"
               name="active"
               class="custom-control-input"
               id="status_switch"
               value="1"
               {{ $teacher->active ? 'checked' : '' }}>
        <span class="custom-control-label"></span>
    </label>
</div>

{{-- Buttons --}}
<div class="col-12 mt-4 d-flex justify-content-between">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
        Cancel
    </a>
    <button type="submit" class="btn btn-primary">
        Update
    </button>
</div>

</div>
</div>
</div>
</form>
@endsection

@push('after-scripts')
    <script>
        $(document).on('change', '#payment_method', function(){
            if($(this).val() === 'bank'){
                $('.paypal_details').hide();
                $('.bank_details').show();
            }else{
                $('.paypal_details').show();
                $('.bank_details').hide();
            }
        });
    </script>
     <script>
    function togglePassword() {
        var passwordField = document.getElementById("password-field");
        var icon = document.getElementById("toggle-icon");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
<script>
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            const label = input.nextElementSibling;
            const fileName = e.target.files.length > 0 ? e.target.files[0].name : 'Choose a file';
            label.innerHTML = '<i class="fa fa-upload mr-1"></i> ' + fileName;
        });
    });
</script>
@endpush