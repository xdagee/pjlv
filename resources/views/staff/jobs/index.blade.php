@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">Job Positions</h4>
                    <p class="category">Manage job titles and descriptions</p>
                </div>
                <div class="card-content">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="toolbar" style="margin-bottom: 20px;">
                        <a href="{{ url('jobs/create') }}" class="btn btn-primary">
                            <i class="material-icons">add</i> Add New Job
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobs as $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td>{{ $job->job_title }}</td>
                                        <td>{{ $job->job_description }}</td>
                                        <td class="td-actions text-right">
                                            <a href="{{ url('jobs/' . $job->id) }}" class="btn btn-info btn-simple btn-xs"
                                                title="View">
                                                <i class="material-icons">visibility</i>
                                            </a>
                                            <a href="{{ url('jobs/' . $job->id . '/edit') }}"
                                                class="btn btn-success btn-simple btn-xs" title="Edit">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <form action="{{ url('jobs/' . $job->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-simple btn-xs" title="Delete"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No jobs found.</td>
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