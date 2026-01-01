@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">security</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Roles Management
                        <button class="btn btn-primary btn-round pull-right" name="add-role">
                            <i class="material-icons">add</i> Add Role
                        </button>
                    </h4>
                    <div class="toolbar">
                        <!--        Here you can write extra buttons/actions for the toolbar              -->
                    </div>
                    <div class="material-datatables">
                        <table id="roles-table" class="table table-striped table-no-bordered table-hover" cellspacing="0"
                            width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
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
            var table = $('#roles-table').DataTable({
                "processing": true,
                "ajax": "{{ route('roles.index') }}",
                "columns": [
                    { "data": "id" },
                    { "data": "role_name" },
                    { "data": "role_description" },
                    {
                        "data": null,
                        "className": "text-right",
                        "orderable": false,
                        "render": function (data, type, row) {
                            return '<button type="button" name="edit-role" class="btn btn-success btn-simple btn-xs" rel="tooltip" title="Edit Role"><i class="material-icons">edit</i></button>' +
                                '<button type="button" name="delete-role" class="btn btn-danger btn-simple btn-xs" rel="tooltip" title="Delete Role"><i class="material-icons">close</i></button>';
                        }
                    }
                ]
            });

            // Add Role
            $("button[name=add-role]").click(function () {
                var formHtml = `
                                <form name="add-role-form">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Role Name <star>*</star></label>
                                        <input type="text" name="role_name" class="form-control" required>
                                    </div>
                                    <div class="form-group label-floating">
                                        <label class="control-label">Description</label>
                                        <textarea name="role_description" class="form-control" rows="3"></textarea>
                                    </div>
                                </form>
                            `;

                bootbox.dialog({
                    title: "Add New Role",
                    message: formHtml,
                    buttons: {
                        success: {
                            label: "Save",
                            className: "btn-rose",
                            callback: function () {
                                var $form = $("form[name=add-role-form]");
                                $.post("{{ route('roles.store') }}", $form.serializeArray().concat({ name: '_token', value: '{{ csrf_token() }}' }))
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

            // Edit Role
            $('#roles-table tbody').on('click', 'button[name=edit-role]', function () {
                var data = table.row($(this).parents('tr')).data();

                var formHtml = `
                                <form name="edit-role-form">
                                    <div class="form-group label-floating is-filled">
                                        <label class="control-label">Role Name <star>*</star></label>
                                        <input type="text" name="role_name" class="form-control" value="${data.role_name}" required>
                                    </div>
                                    <div class="form-group label-floating is-filled">
                                        <label class="control-label">Description</label>
                                        <textarea name="role_description" class="form-control" rows="3">${data.role_description || ''}</textarea>
                                    </div>
                                </form>
                            `;

                bootbox.dialog({
                    title: "Edit Role",
                    message: formHtml,
                    buttons: {
                        success: {
                            label: "Update",
                            className: "btn-success",
                            callback: function () {
                                var $form = $("form[name=edit-role-form]");
                                $.ajax({
                                    url: '/admin/roles/' + data.id,
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

            // Delete Role
            $('#roles-table tbody').on('click', 'button[name=delete-role]', function () {
                var data = table.row($(this).parents('tr')).data();

                bootbox.confirm({
                    message: "Are you sure you want to delete the role <b>" + data.role_name + "</b>?",
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
                                url: '/admin/roles/' + data.id,
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