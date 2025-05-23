@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    @php
        $roleTitle = implode(', ', $userRole); // Shows assigned roles, comma-separated
    @endphp

    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit {{ $user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('users.update')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" required class="form-control" id="name" value="{{ old('name', $user->name) }}" placeholder="Enter user name">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input name="email" type="email" required class="form-control" id="email" value="{{ old('email', $user->email) }}" placeholder="Enter email">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6 position-relative">
                                    <label for="password">Password (Leave blank to keep current)</label>
                                    <div class="input-group">
                                        <input name="password" type="password" class="form-control" id="password" placeholder="Enter new password">
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
                                        <input name="confirm-password" type="password" class="form-control" id="confirm-password" placeholder="Confirm new password">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                                <i class="fas fa-eye" id="eyeConfirmIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group d-none">
                                <label for="roles">Assign Role</label>
                                <select name="roles" class="form-control select2" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @if(in_array($role->name, $userRole)) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Client Fields --}}
                            <div id="client-fields" style="display: none;">
                                <h5 class="mt-4 mb-3">Client Information</h5>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Date of Birth</label>
                                        <input type="date" name="client_dob" class="form-control" value="{{ old('client_dob', $client?->dob) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Photo</label>
                                        <input type="file" name="client_photo" class="form-control-file border border-1 p-1">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Address</label>
                                        <textarea name="client_address" class="form-control" rows="1">{{ old('client_address', $client?->address) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Lawyer Fields --}}
                            <div id="lawyer-fields" style="display: none;">
                                <h5 class="mt-4 mb-3">Lawyer Information</h5>
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label>Bar Council ID</label>
                                        <input type="text" name="lawyer_bar_id" class="form-control" value="{{ old('lawyer_bar_id', $lawyer?->bar_id) }}">
                                    </div>
                                    <div class="form-group col-md-2 mb-3">
                                        <label for="lawyer_practice_area" class="form-label">Practice Area</label>
                                        <select name="lawyer_practice_area" id="lawyer_practice_area" class="form-control select2" style="width: 100%;">
                                            <option value="civil" {{ (isset($lawyer) && $lawyer->practice_area == 'civil') ? 'selected' : '' }}>Civil</option>
                                            <option value="criminal" {{ (isset($lawyer) && $lawyer->practice_area == 'criminal') ? 'selected' : '' }}>Criminal</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="practice_court">Practice Court Name</label>
                                        <input type="text" name="lawyer_practice_court" id="lawyer_practice_court" class="form-control"
                                               value="{{ old('practice_court', $lawyer?->practice_court) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Photo</label>
                                        <input type="file" name="lawyer_photo" class="form-control-file border border-1 p-1">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Chamber Name</label>
                                        <input type="text" name="lawyer_chamber_name" class="form-control" value="{{ old('lawyer_chamber_name', $lawyer?->chamber_name) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Chamber Address</label>
                                        <textarea name="lawyer_chamber_address" class="form-control" rows="1">{{ old('lawyer_chamber_address', $lawyer?->chamber_address) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @can('users.update')
                                <button class="btn btn-success" type="submit">Update</button>
                            @endcan

                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('footer')
@stop

@section('css')
@stop

@section('js')
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

            // Roles array from backend
            const roles = @json($roles->pluck('name'));

            function toggleExtraFields() {
                const selectedRole = $('.select2').val(); // Get selected role

                // Hide both fields initially
                $('#client-fields, #lawyer-fields').hide();

                // Check selected role and show relevant fields
                if (selectedRole.includes('client')) {
                    $('#client-fields').show();
                }
                if (selectedRole.includes('lawyer')) {
                    $('#lawyer-fields').show();
                }
            }

            // Trigger on page load to show the selected role's fields
            toggleExtraFields();

            // Recheck fields when role is changed
            $('.select2').on('change', function() {
                toggleExtraFields();
            });
        });
    </script>
@stop
