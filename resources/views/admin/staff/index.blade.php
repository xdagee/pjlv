@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <p>
                <button name="add-staff" class="btn btn-danger pull-right"> Add New Staff </button>
            </p>
        </div>
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header card-header-text" data-background-color="orange">
                    <h4 class="card-title"> Staff Summary </h4>
                </div>
                <div class="card-content table-responsive">
                    <table class="table table-hover" id="staff-details-table" width="100%">
                        <thead class="text-warning">
                            <td>id</td>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Staff Number</th>
                            <th>Role</th>
                            <th>Leave Balance</th>
                            <th>Mobile Number</th>
                            <th>Actions</th>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-center">
                                    <p>
                                        <b>
                                            Legend: Active accounts have
                                            <span class="text-success">green </span>
                                            while deactivated accounts have
                                            <span class="text-danger">red </span>
                                            backgrounds.
                                        </b>
                                    </p>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var urlPrefix = "{{ $url_prefix ?? '/staff' }}";

            var table = $("#staff-details-table").DataTable({
                "ajax": urlPrefix + "/data",
                "columns": [
                    { "data": "id" },
                    { "data": function (data) { var othername = data.othername === null ? "" : data.othername + " "; return (data.firstname + " " + othername + data.lastname); } },
                    { "data": "gender" },
                    { "data": "staff_number" },
                    { "data": "role.role_name", "defaultContent": "N/A" },
                    { "data": "total_leave_days" },
                    { "data": "mobile_number" },
                    { "data": "" }
                ],
                "columnDefs": [{
                    "targets": -1,
                    "data": "id",
                    "searchable": false,
                    "className": "td-actions text-right",
                    "defaultContent":
                        "<button type=\"button\" name=\"view-staff\" rel=\"tooltip\" title=\"View Full Details\" class=\"btn btn-info btn-simple\">\n" +
                        "        <i class=\"material-icons\">person</i>\n" +
                        "    </button>\n" +
                        "    <button type=\"button\" name=\"edit-staff\" rel=\"tooltip\" title=\"Edit Staff\" class=\"btn btn-success btn-simple\">\n" +
                        "        <i class=\"material-icons\">edit</i>\n" +
                        "    </button>"
                },
                {
                    "targets": 2,
                    "render": function (data, type, row, meta) { var gender = "Male"; if (data === 0) { gender = "Female"; } return gender; }
                },
                {
                    "targets": 0,
                    "visible": false
                }
                ],
                "order": [[1, 'asc']]

            });

            $("#staff-details-table tbody").on('click', 'button[name=edit-staff]', function () {
                var rowData = table.row($(this).parents('tr')).data();

                $.get(urlPrefix + "/" + rowData.id + "/edit", function (data) {
                    bootbox.dialog({
                        title: "<h3 class='text-center text-info'><i class='material-icons'>edit</i> Edit Staff</h3>",
                        message: data,
                        closeButton: false,
                        size: 'large',
                        buttons: {
                            confirm: {
                                label: "Save Changes",
                                className: "btn-success",
                                callback: function () {
                                    var $form = $("form[name=edit-staff-form]");

                                    $.ajax({
                                        url: urlPrefix + '/' + rowData.id,
                                        type: 'PUT',
                                        data: $form.serialize(),
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                            bootbox.alert("Staff updated successfully!", function () {
                                                table.ajax.reload();
                                            });
                                        },
                                        error: function (xhr) {
                                            var message = "An error occurred.";
                                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                                message = xhr.responseJSON.message;
                                            }
                                            bootbox.alert("Error: " + message);
                                        }
                                    });
                                }
                            },
                            cancel: {
                                label: "Cancel",
                                className: "btn-danger"
                            }
                        }
                    });
                }, "html");
            });

            $("#staff-details-table tbody").on('click', 'button[name=view-staff]', function () {
                var rowData = table.row($(this).parents('tr')).data();

                $.get(urlPrefix + "/" + rowData.id, function (staff) {
                    var gender = staff.gender == 1 ? 'Male' : 'Female';
                    var status = staff.is_active ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>';

                    var html = '<div class="table-responsive">' +
                        '<table class="table">' +
                        '<tr><th>Staff Number</th><td>' + (staff.staff_number || 'N/A') + '</td></tr>' +
                        '<tr><th>Full Name</th><td>' + staff.title + ' ' + staff.firstname + ' ' + (staff.othername || '') + ' ' + staff.lastname + '</td></tr>' +
                        '<tr><th>Gender</th><td>' + gender + '</td></tr>' +
                        '<tr><th>Mobile</th><td>' + staff.mobile_number + '</td></tr>' +
                        '<tr><th>Date of Birth</th><td>' + (staff.dob || 'N/A') + '</td></tr>' +
                        '<tr><th>Date Joined</th><td>' + (staff.date_joined || 'N/A') + '</td></tr>' +
                        '<tr><th>Leave Days</th><td>' + (staff.total_leave_days || 'N/A') + '</td></tr>' +
                        '<tr><th>Status</th><td>' + status + '</td></tr>' +
                        '</table></div>';

                    bootbox.dialog({
                        title: "<h3 class='text-center text-info'><i class='material-icons'>person</i> Staff Details</h3>",
                        message: html,
                        closeButton: true,
                        buttons: {
                            ok: {
                                label: "Close",
                                className: "btn-primary"
                            }
                        }
                    });
                }, "json");
            });


            $("button[name=add-staff]").on('click', function () {
                $.get(urlPrefix + "/create", function (data) {
                    bootbox.dialog({
                        title: "<h3 class='text-center text-primary' ><i class=\"material-icons\">contacts</i>Staff Registration</h3>",
                        message: data,
                        closeButton: false,
                        buttons: {
                            confirm: {
                                label: "Register",
                                className: "btn-primary",
                                callback: function () {
                                    var $form = $("form[name=add-staff-form]");

                                    // Basic validation
                                    var isValid = true;
                                    $form.find('[required]').each(function () {
                                        if (!$(this).val()) {
                                            isValid = false;
                                            $(this).addClass('error');
                                        } else {
                                            $(this).removeClass('error');
                                        }
                                    });

                                    if (!isValid) {
                                        bootbox.alert("Please fill in all required fields.");
                                        return false;
                                    }

                                    // Submit via AJAX
                                    $.ajax({
                                        url: urlPrefix,
                                        type: 'POST',
                                        data: $form.serialize(),
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                            bootbox.alert("Staff registered successfully!", function () {
                                                table.ajax.reload();
                                            });
                                        },
                                        error: function (xhr) {
                                            var message = "An error occurred.";
                                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                                message = xhr.responseJSON.message;
                                            }
                                            bootbox.alert("Error: " + message);
                                        }
                                    });
                                }
                            },
                            cancel: {
                                label: "Cancel",
                                className: "btn-danger"
                            }
                        }
                    });

                }, "html");
            })

        });


    </script>
@endsection