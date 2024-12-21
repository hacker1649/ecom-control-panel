@extends('admin.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y p-0">
        <div class="row">
            <div class="col mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="mb-4">Monthly Report - Orders Grouped by Category</h3>
                                    <!-- Print Button -->
                                    <div class="mb-3 d-flex justify-content-end">
                                        <a href="{{ route('generate.report.pdf') }}?year={{ $yearQuery }}"
                                            class="btn btn-primary me-2" target="_blank">Print Report</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <!-- Year Dropdown -->
                                    <form method="GET" action="{{ route('report.index') }}">
                                        <div class="d-flex justify-content-start align-items-center gap-3">
                                            <label for="year" class="fw-bold fs-6 form-label m-0">Select Year</label>
                                            <!-- Added margin-right (me-2) for space -->
                                            <select id="year" name="year" class="form-select" style="width: 150px;"
                                                onchange="this.form.submit()">
                                                @foreach (range(now()->year - 4, now()->year) as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $year == $yearQuery ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mar mb-5">
                                    <!-- Report Table -->
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                @for ($month = 1; $month <= 12; $month++)
                                                    <th>{{ \Carbon\Carbon::create()->month($month)->format('M') }}</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                                <tr>
                                                    <td>{{ $category }}</td>
                                                    @for ($month = 1; $month <= 12; $month++)
                                                        <td>{{ $structuredData[$category][$month] ?? 0 }}</td>
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-responsive mar pt-5 mb-5">
                                    <h5 class="mb-4">Total Orders of Each Category in {{ $yearQuery }}</h5>
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            @foreach ($categories as $category)
                                                <tr>
                                                    <td>
                                                        <strong class="float-start">{{ $category }}</strong>
                                                        @php
                                                            $annualTotal = array_sum($structuredData[$category] ?? []);
                                                        @endphp
                                                        <span class="float-end">{{ $annualTotal }} order(s)</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
