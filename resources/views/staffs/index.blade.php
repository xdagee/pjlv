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
                <h4 class="card-title"> Staffs </h4>
            </div>
            <div class="card-content table-responsive">
                <table class="table table-hover" id="staff-details-table">
                    <thead class="text-warning">
                        <th>#</th>
                        <th>Position</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Salary</th>
                        <th>Country</th>

                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
           $("button[name=add-staff]").on('click', function(){
               $.get("/staffs/create", function(data){
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