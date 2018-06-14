@extends('layouts.master') 

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <button class="btn btn-lg btn-primary" name="add-staff">
                <span>
            <i class="material-icons">add_person</i>
            Add Staff Details
                    </span>
            </button>
        </div>
    </div>

	<div class="row">
		<div class="col-md-12 col-lg-12">
			<div class="card">
                                <div class="card-header card-header-text" data-background-color="orange">
                                    <h4 class="card-title">#Employees Data</h4>
                                    <p class="category">New employees on 15th September, 2016</p>
                                </div>
                                <div class="card-content table-responsive">
                                    <table class="table table-hover">
                                        <thead class="text-warning">
                                            <th>Staff Number</th>
                                            <th>Name</th>
                                            <th>Salary</th>
                                            <th>Account Status</th>
                                            <th>Country</th>
                                            <th></th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Dakota Rice</td>
                                                <td>$36,738</td>
                                                <td>Active</td>
                                                <td>Niger</td>
                                                <td>
                                                    <button class="btn btn-sm btn-simple btn-info" name="view-fulldetails">
                                                    <i class="material-icons">library_books</i>Full Details
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Minerva Hooper</td>
                                                <td>$23,789</td>
                                                <td>Inactive</td>
                                                <td>Curaçao</td>
                                                <td>
                                                    <button class="btn btn-sm btn-simple btn-info" name="view-fulldetails">
                                                    <i class="material-icons">library_books</i>Full Details
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
		</div>
	</div>

@endsection