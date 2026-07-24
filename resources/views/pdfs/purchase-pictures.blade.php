<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Pictures</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .image { max-width: 100%; max-height: 250px; margin-top: 8px; }
        .caption { font-weight: bold; margin-top: 4px; }
    </style>
</head>
<body style="text-align: center;">
    {{-- <h2>Purchase Pictures</h2> --}}
    <p><strong>Purchase No:</strong> {{ $purchase->pr_no }}</p>

    @foreach($purchase->pictures as $picture)
        <div class="section">
            <div class="caption">{{ $picture->caption ?: 'Untitled picture' }}</div>
            @if($picture->image_path)
                @php
                    $imagePath = ltrim($picture->image_path, '/');
                    $fileExists = Storage::disk('public')->exists($imagePath);
                @endphp
                @if($fileExists)
                    @php
                        $imageContents = Storage::disk('public')->get($imagePath);
                        $mimeType = Storage::disk('public')->mimeType($imagePath);
                        $dataUri = 'data:' . $mimeType . ';base64,' . base64_encode($imageContents);
                    @endphp
                    <img class="image" src="{{ $dataUri }}" alt="{{ $picture->caption }}">
                @else
                    <div style="color: #999;">Image not available.</div>
                @endif
            @endif
        </div>
    @endforeach
</body>
</html>
