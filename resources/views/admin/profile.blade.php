@extends('layouts.admin')
@section('content')

    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $admin = $user->admin;
    @endphp

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">admin_panel_settings</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Admin Profile
                        <small class="category">Update your administrator information</small>
                    </h4>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group label-floating {{ optional($admin)->name ? 'is-filled' : '' }}">
                                    <label class="control-label">Full Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', optional($admin)->name ?? '') }}" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group label-floating is-filled">
                                    <label class="control-label">Email Address</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group label-floating {{ optional($admin)->phone ? 'is-filled' : '' }}">
                                    <label class="control-label">Mobile Number</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', optional($admin)->phone ?? '') }}" />
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-rose pull-right">Update Profile</button>
                        <div class="clearfix"></div>
                    </form>

                    <hr>

                    <h4 class="card-title">Change Password</h4>
                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group label-floating">
                                    <label class="control-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group label-floating">
                                    <label class="control-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group label-floating">
                                    <label class="control-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required />
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning pull-right">Change Password</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-profile">
                <div class="card-avatar">
                    <a href="javascript:void(0)">
                        <img class="img" src="{{ asset('assets/img/default-avatar.png') }}" alt="Profile" />
                    </a>
                </div>
                <div class="card-content">
                    <h6 class="category text-gray">Super Administrator</h6>
                    <h4 class="card-title">{{ optional($admin)->name ?? 'Admin' }}</h4>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Admin ID</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{ optional($admin)->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        <span class="badge badge-rose">Super Admin</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="text-success"><i class="material-icons">verified_user</i> Active</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection