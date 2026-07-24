<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mailing List - {{ $purchase->pr_no ?? $purchase->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Mailing List for {{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Supplier</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>TIN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $i => $supplier)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $supplier->supplier_name ?? '' }}</td>
                    <td>{{ $supplier->supplier_address ?? '' }}</td>
                    <td>{{ $supplier->supplier_contact_numbera ?? $supplier->supplier_contact_number ?? '' }}</td>
                    <td>{{ $supplier->supplier_tin ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
