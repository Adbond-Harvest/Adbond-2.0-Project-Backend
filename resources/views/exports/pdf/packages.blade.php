<!-- resources/views/exports/projects-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Packages Export</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        h2 {
            color: #333;
        }
        .export-date {
            color: #666;
            font-size: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>{{ $projectName }} Packages</h2>
    <div class="export-date">Generated on: {{ now()->format('F j, Y') }}</div>
    
    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($mappedData as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>