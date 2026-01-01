@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="purple">
                    <i class="material-icons">groups</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">All Staff</h4>
                    <div class="table-responsive">
                        <table class="table table-hover" id="staff-table" width="100%">
                            <thead class="text-primary">
                                <tr>
                                    <th>Staff Number</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Leave Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/staff/data',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    var tbody = $('#staff-table tbody');
                    tbody.empty();

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (staff) {
                            var gender = staff.gender == 1 ? 'M' : 'F';
                            var status = staff.is_active ?
                                '<span class="badge badge-success">Active</span>' :
                                '<span class="badge badge-danger">Inactive</span>';
                            var roleName = staff.role ? staff.role.role_name : 'N/A';
                            var deptName = staff.department ? staff.department.name : 'N/A';
                            var fullName = staff.title + ' ' + staff.firstname + ' ' + staff.lastname;

                            var row = '<tr>' +
                                '<td><a href="/staff/profile/' + staff.id + '" class="text-primary">' + staff.staff_number + '</a></td>' +
                                '<td>' + fullName + '</td>' +
                                '<td><span class="badge badge-info">' + roleName + '</span></td>' +
                                '<td>' + deptName + '</td>' +
                                '<td>' + staff.total_leave_days + ' days</td>' +
                                '<td>' + status + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    } else {
                        tbody.append('<tr><td colspan="6" class="text-center text-muted">No staff found.</td></tr>');
                    }
                },
                error: function (xhr) {
                    $('#staff-table tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading staff data.</td></tr>');
                }
            });
        });
    </script>
@endsection