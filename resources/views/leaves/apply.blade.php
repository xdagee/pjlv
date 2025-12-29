@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title">Apply for Leave</h4>
                    <p class="category">Submit a new leave request</p>
                </div>
                <div class="card-content">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($balance)
                        <div class="alert alert-info">
                            <strong>Your Leave Balance:</strong> {{ $balance['remaining'] }} days remaining
                            ({{ $balance['total_used'] }} used of {{ $balance['total_allowance'] }})
                        </div>
                    @endif

                    <form method="POST" action="/leaves">
                        @csrf

                        <div class="form-group">
                            <label for="leave_type_id">Leave Type *</label>
                            <select name="leave_type_id" id="leave_type_id" class="form-control" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->leave_type_name }}
                                        @if($type->leave_duration > 0)
                                            (Max: {{ $type->leave_duration }} days)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date *</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date *</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="leave_days">Number of Days *</label>
                            <input type="number" name="leave_days" id="leave_days" class="form-control"
                                value="{{ old('leave_days', 1) }}" min="1" required readonly>
                            <small class="text-muted">Calculated automatically based on dates</small>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-success">
                                <i class="material-icons">send</i> Submit Request
                            </button>
                            <a href="/leaves" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var startDate = document.getElementById('start_date');
            var endDate = document.getElementById('end_date');
            var leaveDays = document.getElementById('leave_days');

            function calculateDays() {
                if (startDate.value && endDate.value) {
                    var start = new Date(startDate.value);
                    var end = new Date(endDate.value);
                    var diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                    if (diff > 0) {
                        leaveDays.value = diff;
                    } else {
                        leaveDays.value = 0;
                    }
                }
            }

            startDate.addEventListener('change', function () {
                if (endDate.value && new Date(endDate.value) < new Date(startDate.value)) {
                    endDate.value = startDate.value;
                }
                endDate.min = startDate.value;
                calculateDays();
            });

            endDate.addEventListener('change', calculateDays);
        });
    </script>

    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }

        .alert-info {
            background-color: #d9edf7;
            color: #31708f;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
@endsection