@extends('dashboard.core')

@section('dashboard-heading')
    @if(count($rows) > 0)
        <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-default">Export</button></a>
    @endif
@endsection

@section('dashboard-body')
    <div class="view-table-container">
        <table class="table">
            <thead>
                <tr class="heading-row">
                    @foreach($columns as $column)
                        <th>{{ $column[0] }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($columns as $column)
                            <td><strong class="mobile-heading">{{ $column[0] }}: </strong>{{ $row[$column[1]] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
