<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report - Orders Grouped by Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>Monthly Report - Orders Grouped by Category</h2>
    <h4>Year: {{ $yearQuery }}</h4>
    <table>
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

    <h5>Total Orders of Each Category in {{ $yearQuery }}</h5>
    <table>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td><strong>{{ $category }}</strong></td>
                    @php
                        $annualTotal = array_sum($structuredData[$category] ?? []);
                    @endphp
                    <td><span>{{ $annualTotal }} order(s)</span></td>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
