<div class="card">
    <div class="card-header" data-background-color="green">
        <h4 class="title">Leave Usage Breakdown</h4>
        <p class="category">Days used by type</p>
    </div>
    <div class="card-content table-responsive">
        <table class="table table-hover">
            <thead class="text-success">
                <th>Leave Type</th>
                <th class="text-right">Used Days</th>
            </thead>
            <tbody>
                @forelse($breakdown['by_type'] as $type => $data)
                    <tr>
                        <td>{{ $type }}</td>
                        <td class="text-right">{{ $data['used'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No leave taken yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>