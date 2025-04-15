@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Create User</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input name="name" type="text" required class="form-control" id="name" placeholder="Enter user name">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input name="email" type="email" required class="form-control" id="email" placeholder="Enter email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 position-relative">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input name="password" type="password" required class="form-control" id="password" placeholder="Enter password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6 position-relative">
                                <label for="confirm-password">Confirm Password</label>
                                <div class="input-group">
                                    <input name="confirm-password" type="password" required class="form-control" id="confirm-password" placeholder="Confirm password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                            <i class="fas fa-eye" id="eyeConfirmIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="roles">Assign Role</label>
                            <select name="roles[]" class="form-control select2" multiple="multiple" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Client Fields --}}
                        <div id="client-fields" style="display: none;">
                            <h5 class="mt-4 mb-3">Client Information</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Date of Birth</label>
                                    <input type="date" name="client_dob" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Photo</label>
                                    <input type="file" name="client_photo" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Address</label>
                                    <textarea name="client_address" class="form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Lawyer Fields --}}
                        <div id="lawyer-fields" style="display: none;">
                            <h5 class="mt-4 mb-3">Lawyer Information</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Bar Council ID</label>
                                    <input type="text" name="lawyer_bar_id" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Practice Area</label>
                                    <input type="text" name="lawyer_practice_area" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Photo</label>
                                    <input type="file" name="lawyer_photo" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Chamber Name</label>
                                    <input type="text" name="lawyer_chamber_name" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Chamber Address</label>
                                    <textarea name="lawyer_chamber_address" class="form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success mt-3" type="submit">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@stop

@section('footer')
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <!-- Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2 on the roles select
            $('.select2').select2({
                placeholder: "Select a role",
                allowClear: true
            });

            // Password show/hide functionality
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const icon = $('#eyeIcon');
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fas fa-eye').addClass('fas fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fas fa-eye-slash').addClass('fas fa-eye');
                }
            });

            // Confirm password show/hide functionality
            $('#toggleConfirmPassword').on('click', function() {
                const confirmPasswordField = $('#confirm-password');
                const icon = $('#eyeConfirmIcon');
                if (confirmPasswordField.attr('type') === 'password') {
                    confirmPasswordField.attr('type', 'text');
                    icon.removeClass('fas fa-eye').addClass('fas fa-eye-slash');
                } else {
                    confirmPasswordField.attr('type', 'password');
                    icon.removeClass('fas fa-eye-slash').addClass('fas fa-eye');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function toggleExtraFields() {
                const selectedRoles = $('.select2').val();
                $('#client-fields, #lawyer-fields').hide();

                if (selectedRoles.includes('Client')) {
                    $('#client-fields').show();
                }
                if (selectedRoles.includes('Lawyer')) {
                    $('#lawyer-fields').show();
                }
            }

            $('.select2').on('change', toggleExtraFields);
            toggleExtraFields(); // on load (for edit form too)
        });
    </script>
@stop
