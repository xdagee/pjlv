@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title">Leave Request Details</h4>
                    <p class="category">Request #{{ $leave->id }}</p>
                </div>
                <div class="card-content">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5><strong>Applicant</strong></h5>
                            <p>{{ ($leave->staff->firstname ?? '') . ' ' . ($leave->staff->lastname ?? '') }}</p>
                            <p class="text-muted">{{ $leave->staff->user->email ?? 'No email' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><strong>Leave Type</strong></h5>
                            <p>{{ $leave->leaveType->leave_type_name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <h5><strong>Start Date</strong></h5>
                            <p>{{ \Carbon\Carbon::parse($leave->start_date)->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5><strong>End Date</strong></h5>
                            <p>{{ \Carbon\Carbon::parse($leave->end_date)->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5><strong>Duration</strong></h5>
                            <p>{{ $leave->leave_days }} day(s)</p>
                        </div>
                    </div>

                    <hr>

                    <h5><strong>Status History</strong></h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leave->leaveAction as $action)
                                <tr>
                                    <td>
                                        <span
                                            class="label label-{{ $action->leaveStatus->status_name === 'Approved' ? 'success' : ($action->leaveStatus->status_name === 'Rejected' ? 'danger' : 'warning') }}">
                                            {{ $action->leaveStatus->status_name ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>{{ ($action->staff->firstname ?? '') . ' ' . ($action->staff->lastname ?? 'System') }}
                                    </td>
                                    <td>{{ $action->created_at ? \Carbon\Carbon::parse($action->created_at)->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No actions recorded</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @php
                        $latestAction = $leave->leaveAction->last();
                        $currentStatus = $latestAction ? $latestAction->leaveStatus->status_name ?? 'Pending' : 'Pending';
                        $canApprove = in_array($staff->role_id ?? 5, [1, 2, 3, 4]) && in_array($currentStatus, ['Pending', 'Unattended', 'Recommended']);
                    @endphp

                    @if($canApprove)
                        <hr>
                        <h5><strong>Take Action</strong></h5>
                        <form method="POST" action="/leaves/{{ $leave->id }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Action</label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="action" value="approve" required> Approve
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="action" value="recommend"> Recommend
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="action" value="reject"> Reject
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" id="reason-group" style="display:none;">
                                <label for="reason">Reason for Rejection</label>
                                <textarea name="reason" id="reason" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Decision</button>
                        </form>
                    @endif

                    <div style="margin-top: 20px;">
                        <a href="/leaves" class="btn btn-default">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var actionRadios = document.querySelectorAll('input[name="action"]');
            var reasonGroup = document.getElementById('reason-group');

            actionRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    if (this.value === 'reject') {
                        reasonGroup.style.display = 'block';
                    } else {
                        reasonGroup.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <style>
        .label {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }

        .label-success {
            background-color: #4caf50;
        }

        .label-danger {
            background-color: #f44336;
        }

        .label-warning {
            background-color: #ff9800;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .radio-inline {
            margin-right: 20px;
        }
    </style>
@endsection