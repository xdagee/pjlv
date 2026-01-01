@extends('layouts.master')
@section('content')

    @php
        /** @var \App\Models\User $user */
        /** @var \App\Models\Staff $staff */
        // Use passed variables or fall back to Auth user
        $user = $user ?? Auth::user();
        $staff = $staff ?? $user->staff;
        $isOwnProfile = Auth::id() === ($user->id ?? null);
    @endphp

    <div class="row">
        @if($isOwnProfile)
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="blue">
                    <i class="material-icons">person</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Edit Profile
                        <small class="category">Update your information</small>
                    </h4>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
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

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group label-floating {{ optional($staff)->firstname ? 'is-filled' : '' }}">
                                    <label class="control-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control"
                                        value="{{ old('firstname', optional($staff)->firstname ?? '') }}" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group label-floating {{ optional($staff)->lastname ? 'is-filled' : '' }}">
                                    <label class="control-label">Last Name</label>
                                    <input type="text" name="lastname" class="form-control"
                                        value="{{ old('lastname', optional($staff)->lastname ?? '') }}" required />
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
                            <div class="col-md-6">
                                <div
                                    class="form-group label-floating {{ optional($staff)->mobile_number ? 'is-filled' : '' }}">
                                    <label class="control-label">Mobile Number</label>
                                    <input type="text" name="mobile_number" class="form-control"
                                        value="{{ old('mobile_number', optional($staff)->mobile_number ?? '') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group label-floating {{ optional($staff)->dob ? 'is-filled' : '' }}">
                                    <label class="control-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control"
                                        value="{{ old('dob', optional($staff)->dob ? (is_string($staff->dob) ? \Carbon\Carbon::parse($staff->dob)->format('Y-m-d') : $staff->dob->format('Y-m-d')) : '') }}" />
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">Update Profile</button>
                        <div class="clearfix"></div>
                    </form>

                    <hr>

                    <h4 class="card-title">Change Password</h4>
                    <form method="POST" action="{{ route('profile.password') }}">
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
        @endif

        <div class="col-md-{{ $isOwnProfile ? '4' : '12' }}">
            <div class="card card-profile">
                <div class="card-avatar">
                    <a href="javascript:void(0)">
                        <img class="img" src="{{ optional($staff)->picture ?? '/img/faces/nan.jpg' }}" alt="Profile" />
                    </a>
                </div>
                <div class="card-content">
                    @if(!$isOwnProfile)
                        <p class="text-muted text-center"><i class="material-icons">visibility</i> Viewing profile</p>
                    @endif
                    <h6 class="category text-gray">{{ optional($staff)->role->role_name ?? 'Staff' }}</h6>
                    <h4 class="card-title">{{ optional($staff)->title ?? '' }} {{ optional($staff)->firstname ?? '' }}
                        {{ optional($staff)->lastname ?? '' }}
                    </h4>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Staff Number</th>
                                    <td>
                                        <a href="{{ url('/staff/profile') }}" class="text-primary">
                                            {{ optional($staff)->staff_number ?? 'N/A' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ optional($staff)->role->role_name ?? 'Not Assigned' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ optional($staff)->department->name ?? 'Not Assigned' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{ optional($staff)->mobile_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Date Joined</th>
                                    <td>{{ optional($staff)->date_joined ? (is_string($staff->date_joined) ? \Carbon\Carbon::parse($staff->date_joined)->format('d M Y') : $staff->date_joined->format('d M Y')) : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Leave Balance</th>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ optional($staff)->total_leave_days ?? 0 }} days
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if(optional($staff)->is_active ?? false)
                                            <span class="text-success"><i class="material-icons">check_circle</i> Active</span>
                                        @else
                                            <span class="text-danger"><i class="material-icons">cancel</i> Inactive</span>
                                        @endif
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