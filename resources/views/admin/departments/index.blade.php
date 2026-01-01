@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">business</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Departments Management
                        <button class="btn btn-primary btn-round pull-right" name="add-department">
                            <i class="material-icons">add</i> Add Department
                        </button>
                    </h4>
                    <div class="toolbar">
                        <!-- Extra action/buttons -->
                    </div>
                    <div class="material-datatables">
                        <table id="departments-table" class="table table-striped table-no-bordered table-hover"
                            cellspacing="0" width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th>Staff Count</th>
                                    <th class="disabled-sorting text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#departments-table').DataTable({
                "processing": true,
                "ajax": "{{ route('departments.index') }}",
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "description" },
                    { "data": "staff_count" },
                    {
                        "data": null,
                        "className": "text-right",
                        "orderable": false,
                        "render": function (data, type, row) {
                            return '<button type="button" name="edit-department" class="btn btn-success btn-simple btn-xs" rel="tooltip" title="Edit Department"><i class="material-icons">edit</i></button>' +
                                '<button type="button" name="delete-department" class="btn btn-danger btn-simple btn-xs" rel="tooltip" title="Delete Department"><i class="material-icons">close</i></button>';
                        }
                    }
                ]
            });

            // Add Department
            $("button[name=add-department]").click(function () {
                var formHtml = `
                                    <form name="add-department-form">
                                        <div class="form-group label-floating">
                                            <label class="control-label">Name <star>*</star></label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        <div class="form-group label-floating">
                                            <label class="control-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3"></textarea>
                                        </div>
                                    </form>
                                `;

                bootbox.dialog({
                    title: "Add New Department",
                    message: formHtml,
                    buttons: {
                        success: {
                            label: "Save",
                            className: "btn-rose",
                            callback: function () {
                                var $form = $("form[name=add-department-form]");
                                $.post("{{ route('departments.store') }}", $form.serializeArray().concat({ name: '_token', value: '{{ csrf_token() }}' }))
                                    .done(function (response) {
                                        table.ajax.reload();
                                        demo.showNotification('top', 'center', 'success', response.message);
                                    })
                                    .fail(function (xhr) {
                                        bootbox.alert("Error: " + (xhr.responseJSON.message || "Something went wrong"));
                                    });
                            }
                        },
                        cancel: {
                            label: "Cancel",
                            className: "btn-default"
                        }
                    }
                });
            });

            // Edit Department
            $('#departments-table tbody').on('click', 'button[name=edit-department]', function () {
                var data = table.row($(this).parents('tr')).data();

                var formHtml = `
                                    <form name="edit-department-form">
                                        <div class="form-group label-floating is-filled">
                                            <label class="control-label">Name <star>*</star></label>
                                            <input type="text" name="name" class="form-control" value="${data.name}" required>
                                        </div>
                                        <div class="form-group label-floating is-filled">
                                            <label class="control-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3">${data.description || ''}</textarea>
                                        </div>
                                    </form>
                                `;

                bootbox.dialog({
                    title: "Edit Department",
                    message: formHtml,
                    buttons: {
                        success: {
                            label: "Update",
                            className: "btn-success",
                            callback: function () {
                                var $form = $("form[name=edit-department-form]");
                                $.ajax({
                                    url: '/admin/departments/' + data.id,
                                    type: 'PUT',
                                    data: $form.serializeArray().concat({ name: '_token', value: '{{ csrf_token() }}' }),
                                    success: function (response) {
                                        table.ajax.reload();
                                        demo.showNotification('top', 'center', 'success', response.message);
                                    },
                                    error: function (xhr) {
                                        bootbox.alert("Error: " + (xhr.responseJSON.message || "Something went wrong"));
                                    }
                                });
                            }
                        },
                        cancel: {
                            label: "Cancel",
                            className: "btn-default"
                        }
                    }
                });
            });

            // Delete Department
            $('#departments-table tbody').on('click', 'button[name=delete-department]', function () {
                var data = table.row($(this).parents('tr')).data();

                bootbox.confirm({
                    message: "Are you sure you want to delete department <b>" + data.name + "</b>?",
                    buttons: {
                        confirm: {
                            label: 'Yes, delete it',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: 'No, keep it',
                            className: 'btn-default'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            $.ajax({
                                url: '/admin/departments/' + data.id,
                                type: 'DELETE',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    table.ajax.reload();
                                    demo.showNotification('top', 'center', 'success', response.message);
                                },
                                error: function (xhr) {
                                    bootbox.alert("Error: " + (xhr.responseJSON.message || "Something went wrong"));
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection