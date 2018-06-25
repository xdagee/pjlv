@extends('layouts.master')
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
                    <table class="table table-hover"  id="staff-details-table" width="100%">
                        <thead class="text-warning">
                        <td>id</td>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Staff Number</th>
                        <th>Supervisor Name</th>
                        <th>Mobile Number</th>
                        <th>Actions</th>
                        </thead>
                        <tfoot>
                        <tr>
                            <th colspan="7" class="text-center">
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

       var table = $("#staff-details-table").DataTable({
           "ajax": "staff/data",
            "columns": [
                {"data" : "id"},
                {"data": function(data) { var othername= data.othername===null?"" : data.othername+" " ;  return (data.firstname + " "+ othername + data.lastname);}},
                {"data": "gender"},
                {"data": "staff_number"},
                {"data": "total_leave_days"},
                {"data": "mobile_number"},
                {"data" : ""}
            ],
            "columnDefs" : [{
               "targets" :-1 ,
                "data" : "id",
                "searchable" : false,
                "className" : "td-actions text-right",
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
                    "render": function(data,type,row,meta){var gender = "Male";if(data===0){gender = "Female";}return gender;}
                },
                {
                    "targets": 0,
                    "visible" : false
                }
            ],
           "order" : [[1,'asc']]

        });

            $("#staff-details-table tbody").on('click','button[name=edit-staff]', function () {
                var data = table.row( $(this).parents('tr') ).data();
                    console.log(data);
                        bootbox.alert("edit details. staff id is  "+ data.id);
            });

            $("#staff-details-table tbody").on('click','button[name=view-staff]', function () {
                var data = table.row( $(this).parents('tr') ).data();
                


                bootbox.alert("view details. staff id is  "+ data.id);
            });


            $("button[name=add-staff]").on('click', function(){
                $.get("/staff/create", function(data){
                    bootbox.dialog({
                        title:"<h3 class='text-center text-primary' ><i class=\"material-icons\">contacts</i>Staff Registration</h3>",
                        message:data,
                        closeButton:false,
                        buttons:{
                            confirm:{
                                label:"Register",
                                className:"btn-primary",
                                callback:function(){

                                    var $form = $("form[name=add-staff-form]");
                                    $form.validate();
                                    if($form.valid())
                                    {
                                        var data = JSON.stringify($form.serializeArray());
                                        setTimeout(function () {
                                            bootbox.alert("Application successful: "+data);
                                        },1000);
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                            },
                            cancel:{
                                label:"Cancel",
                                className:"btn-danger"
                            }
                        }
                    });

                }, "html");
            })

        });


    </script>
@endsection