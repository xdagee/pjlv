@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="green">
                    <i class="material-icons">celebration</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Public Holidays</h4>
                    <p class="text-muted">These are the official company holidays for the year.</p>
                    <div class="material-datatables">
                        <table class="table table-striped table-hover" cellspacing="0" width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Holiday Name</th>
                                    <th>Date</th>
                                    <th>Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holidays as $holiday)
                                    <tr>
                                        <td>{{ $holiday->name }}</td>
                                        <td>{{ $holiday->date->format('F j, Y') }}</td>
                                        <td>{{ $holiday->date->format('l') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No holidays have been defined yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection