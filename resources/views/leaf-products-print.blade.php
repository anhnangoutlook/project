<!doctype html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ទិន្នន័យស្តុកឆៅ</title>
    <style>
        body {
            margin: 20px;
            color: #000;
            font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
        }
        h1 {
            margin: 0 0 16px;
            text-align: center;
            font-size: 20px;
        }
        .print-actions {
            margin-bottom: 12px;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th,
        td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 10px;
            overflow-wrap: anywhere;
        }
        th {
            background: #e9ecef;
        }
        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }
            body {
                margin: 0;
            }
            .sidebar,
            .navbar,
            .pagination,
            .filter-inputs,
            .print-actions {
                display: none !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <h1>ទិន្នន័យស្តុកឆៅទាំងអស់</h1>
    <div class="print-actions">
        <button type="button" onclick="window.print()">បោះពុម្ព</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>ប្រភេទ</th>
                <th>ឈ្មោះទំនិញ</th>
                <th>ចាប់ពី</th>
                <th>រហូតដល់</th>
                <th>ចំ.សរុប</th>
                <th>ដក (Qty)</th>
                <th>ចំ.ស.រប្រចាំខែ</th>
                <th>សរុប (g)</th>
                <th>ដក (g)</th>
                <th>ស.វ(g)ប្រចាំខែ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leafProducts as $product)
                @php
                    $remainQty = (float) $product['remain_qty'];
                    $remainWeight = (float) $product['remain_weight'];
                @endphp
                <tr>
                    <td>{{ $product['category'] }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ $product['code_from'] }}</td>
                    <td>{{ $product['code_to'] }}</td>
                    <td>{{ $product['total_qty'] }}</td>
                    <td>{{ $product['status_1'] }}</td>
                    <td>{{ $remainQty == 0 ? 'អស់ពីស្តុក' : $product['remain_qty'] }}</td>
                    <td>{{ round((float) $product['total_weight']) }}g</td>
                    <td>{{ round((float) $product['status_2']) }}g</td>
                    <td>{{ $remainWeight == 0 ? 'អស់ពីស្តុក' : round($remainWeight) . 'g' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">មិនមានទិន្នន័យ</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
