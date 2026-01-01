@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="purple">
                    <i class="material-icons">settings</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">System Settings
                        <small class="pull-right text-muted">Manage all system configuration from this page</small>
                    </h4>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="material-icons">close</i>
                            </button>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form id="settings-form" method="POST" action="{{ url('/admin/settings') }}">
                        @csrf
                        @method('PUT')

                        <!-- Tabs for different setting groups -->
                        <ul class="nav nav-pills nav-pills-warning" role="tablist" style="flex-wrap: wrap;">
                            <li class="active"><a href="#leave" data-toggle="tab" role="tab">Leave</a></li>
                            <li><a href="#workflow" data-toggle="tab" role="tab">Workflow</a></li>
                            <li><a href="#display" data-toggle="tab" role="tab">Display</a></li>
                            <li><a href="#calendar" data-toggle="tab" role="tab">Calendar</a></li>
                            <li><a href="#email" data-toggle="tab" role="tab">Email</a></li>
                            <li><a href="#export" data-toggle="tab" role="tab">Export</a></li>
                            <li><a href="#analytics" data-toggle="tab" role="tab">Analytics</a></li>
                            <li><a href="#system" data-toggle="tab" role="tab">System</a></li>
                            <li><a href="#security" data-toggle="tab" role="tab">Security</a></li>
                        </ul>

                        @php $globalIndex = 0; @endphp

                        <div class="tab-content">
                            <!-- Leave Management Settings -->
                            <div class="tab-pane active" id="leave">
                                <h5 class="text-info"><i class="material-icons"
                                        style="vertical-align: middle;">event_available</i> Leave Policy Configuration</h5>
                                <p class="text-muted">Configure how leave requests and balances are managed</p>
                                <div class="row">
                                    @if(isset($settings['leave_management']))
                                        @foreach($settings['leave_management'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Workflow Settings -->
                            <div class="tab-pane" id="workflow">
                                <h5 class="text-info"><i class="material-icons"
                                        style="vertical-align: middle;">account_tree</i> Approval Workflow</h5>
                                <p class="text-muted">Configure approval chains and leave request rules</p>
                                <div class="row">
                                    @if(isset($settings['workflow']))
                                        @foreach($settings['workflow'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Display Settings -->
                            <div class="tab-pane" id="display">
                                <h5 class="text-info"><i class="material-icons" style="vertical-align: middle;">palette</i>
                                    Display & Personalization</h5>
                                <p class="text-muted">Customize how data is displayed throughout the application</p>
                                <div class="row">
                                    @if(isset($settings['display']))
                                        @foreach($settings['display'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Calendar Settings -->
                            <div class="tab-pane" id="calendar">
                                <h5 class="text-info"><i class="material-icons"
                                        style="vertical-align: middle;">calendar_today</i> Calendar & Scheduling</h5>
                                <p class="text-muted">Configure calendar display and scheduling options</p>
                                <div class="row">
                                    @if(isset($settings['calendar']))
                                        @foreach($settings['calendar'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Email Settings -->
                            <div class="tab-pane" id="email">
                                <h5 class="text-info"><i class="material-icons" style="vertical-align: middle;">email</i>
                                    Email Notification Configuration</h5>
                                <p class="text-muted">Control when and how email notifications are sent</p>
                                <div class="row">
                                    @if(isset($settings['email']))
                                        @foreach($settings['email'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Export Settings -->
                            <div class="tab-pane" id="export">
                                <h5 class="text-info"><i class="material-icons" style="vertical-align: middle;">download</i>
                                    Export & Reports</h5>
                                <p class="text-muted">Configure CSV and PDF export options</p>
                                <div class="row">
                                    @if(isset($settings['export']))
                                        @foreach($settings['export'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Analytics Settings -->
                            <div class="tab-pane" id="analytics">
                                <h5 class="text-info"><i class="material-icons"
                                        style="vertical-align: middle;">analytics</i> Dashboard & Analytics</h5>
                                <p class="text-muted">Configure analytics dashboard behavior and display</p>
                                <div class="row">
                                    @if(isset($settings['analytics']))
                                        @foreach($settings['analytics'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- System Settings -->
                            <div class="tab-pane" id="system">
                                <h5 class="text-info"><i class="material-icons" style="vertical-align: middle;">computer</i>
                                    System Configuration</h5>
                                <p class="text-muted">General system settings and preferences</p>
                                <div class="row">
                                    @if(isset($settings['system']))
                                        @foreach($settings['system'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Security Settings -->
                            <div class="tab-pane" id="security">
                                <h5 class="text-info"><i class="material-icons" style="vertical-align: middle;">security</i>
                                    Security Configuration</h5>
                                <p class="text-muted">Password policies and authentication settings</p>
                                <div class="row">
                                    @if(isset($settings['security']))
                                        @foreach($settings['security'] as $setting)
                                            @include('admin.settings._field', ['setting' => $setting, 'index' => $globalIndex++])
                                        @endforeach
                                    @else
                                        <div class="col-md-12">
                                            <p class="text-muted">No settings in this category.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-footer text-right"
                            style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <span class="text-muted pull-left" style="line-height: 36px;">
                                <i class="material-icons" style="vertical-align: middle; font-size: 18px;">info</i>
                                {{ $globalIndex }} settings available
                            </span>
                            <button type="submit" class="btn btn-rose btn-fill">
                                <i class="material-icons">save</i> Save All Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Handle checkbox unchecked state (send 0 instead of nothing)
            $('#settings-form').on('submit', function (e) {
                $(this).find('input[type="checkbox"]').each(function () {
                    if (!this.checked) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: $(this).attr('name'),
                            value: '0'
                        }).appendTo($(this).closest('form'));
                    }
                });
            });

            // Show notification on save
            @if(session('success'))
                demo.showNotification('top', 'center', 'success', '{{ session("success") }}');
            @endif
        });
    </script>
@endsection