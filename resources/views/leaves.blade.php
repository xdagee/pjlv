@extends('layouts.master') 

@section('content')
<div class="col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-header card-header-tabs" data-background-color="blue">
                                    <div class="nav-tabs-navigation">
                                        <div class="nav-tabs-wrapper">
                                            <span class="nav-tabs-title"><p>Leaves :</p></span>
                                            <ul class="nav nav-tabs">
                                                <li>
                                                    <a href="#pending" >
                                                    <p>Pending
                                                    <span class="notification">5</span>
                                                        <i class="material-icons">hourglass_empty </i>
                                                    </p>                                                                                            
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>

                                                <li class="">
                                                    <a href="#recommended" >
                                                     Recommended
                                                        <i class="material-icons">thumb_up</i>
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>

                                                <li class="">
                                                    <a href="#approved" >
                                                    Approved
                                                        <i class="material-icons">done_outline</i> 
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>
                                                
                                                <li class="">
                                                    <a href="#declined" >
                                                     Declined
                                                        <i class="material-icons">thumb_down</i>
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>

                                                <li class="">
                                                    <a href="#rejected" >
                                                     Rejected
                                                        <i class="material-icons">clear</i>
                                                        <div class="ripple-container"></div>
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="pending">
                                           <table class="table table-hover table-responsive" id="tab-table">
                                        <thead class="text-warning">
                                            <th>Staff Number</th>
                                            <th>Name</th>
                                            <th>Request Date</th>
                                            <th>Leave Type</th>
                                            <th>Return Date(Number of days)</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Dakota Rice</td>
                                                <td>20-June-2018</td>
                                                <td>Annual Leave</td>
                                                <td>15-May-2016 (10 days)</td>
                                                <td>
                                                 <button type="button" rel="tooltip" title="Recommend Leave" 
                                                 class="btn btn-success btn-simple btn-xs" data-leave-id="" name="recommend-leave">
                                                                <i class="material-icons">check_circle</i>
                                                            </button>

                                                <button type="button" rel="tooltip" title="Decline Leave" 
                                                class="btn btn-danger btn-simple btn-xs" data-leave-id="" name="decline-leave">
                                                                <i class="material-icons">remove_circle</i>
                                                            </button>
                                                 </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                        </div>
                                        <div class="col-md-offset-10">
                                        <button type="button" rel="tooltip" title="Load more" class="btn btn-info btn-simple btn-xs" >More Data
                                        <i class="material-icons">more_horiz</i></button>
                                        </div>
                                       
                                 </div>

 </div>
  </div>
 </div>

<!-- <script type="text/javascript">
	$(document).ready(function(){

		var loading = "<tr><i class='fa fa-fw spinner spin'></i>Loading Data....Please Wait</tr>";

		$(".nav-tabs>li>a").on('click', function(){
			var name = $(this).href();
			console.log(name);
			$("#tab-table").empty().append(loading);
		});

	});


</script>    -->                                 


@endsection