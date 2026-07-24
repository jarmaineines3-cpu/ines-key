<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abstract of Quotation {{ $purchase->pr_no }}</title>
    <style>
        @php
            $oldPath = public_path('fonts/OldEnglish.ttf');
            $oldFontData = null;
            if (file_exists($oldPath)) {
                $oldFontData = base64_encode(file_get_contents($oldPath));
            }

            $trajanPath = public_path('fonts/TrajanPro.ttf');
            $trajanFontData = null;
            if (file_exists($trajanPath)) {
                $trajanFontData = base64_encode(file_get_contents($trajanPath));
            }
            $bookmanPath = public_path('fonts/BookmanOldStyle.ttf');
            $bookmanFontData = null;
            if (file_exists($bookmanPath)) {
                $bookmanFontData = base64_encode(file_get_contents($bookmanPath));
            }
            $bookmanBoldPath = public_path('fonts/BookmanOldStyle-Bold.ttf');
            $bookmanBoldAlt = public_path('fonts/BookmanOldStyleBold.ttf');
            $bookmanBoldData = null;
            if (file_exists($bookmanBoldPath)) {
                $bookmanBoldData = base64_encode(file_get_contents($bookmanBoldPath));
            } elseif (file_exists($bookmanBoldAlt)) {
                $bookmanBoldData = base64_encode(file_get_contents($bookmanBoldAlt));
            }
        @endphp

        @if($oldFontData)
        @font-face {
            font-family: 'OldEnglish';
            src: url('data:font/truetype;base64,{{ $oldFontData }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @else
        /* Fallback: use a serif font if OldEnglish is not available */
        @font-face {
            font-family: 'OldEnglish';
            src: local('Times New Roman');
        }
        @endif

        @if($trajanFontData)
        @font-face {
            font-family: 'TrajanPro';
            src: url('data:font/truetype;base64,{{ $trajanFontData }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @else
        @font-face {
            font-family: 'TrajanPro';
            src: local('Times New Roman');
        }
        @endif

        @if($bookmanFontData)
        @font-face {
            font-family: 'BookmanOldStyle';
            src: url('data:font/truetype;base64,{{ $bookmanFontData }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @endif

        @if($bookmanBoldData)
        @font-face {
            font-family: 'BookmanOldStyle';
            src: url('data:font/truetype;base64,{{ $bookmanBoldData }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @else
        /* if bold file not provided, try local bold face */
        @font-face {
            font-family: 'BookmanOldStyle';
            src: local('Bookman Old Style Bold');
            font-weight: bold;
        }
        @endif

        body { margin: 50px 30px 1px; font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        .container-table { border: 1px solid #888; }
        .container-table td,
        .container-table th,
        .header-table td,
        .purpose-table td,
        .sub-header-table td,
        .signature-table td {
            border: 1px solid #888;
            padding: 4px;
            vertical-align: top;
        }
        .metadata-table td {
            border: none;
            padding: 4px;
            vertical-align: top;
        }
        .header-table, .purpose-table, .metadata-table, .sub-header-table { border: none; }
        @page { margin: 40px 20px 70px 20px; }
        .page-break { display: block; page-break-before: always; page-break-after: auto; height: 0; margin: 0; padding: 0; }
        .center { text-align: center; }
        .right { text-align: right; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 0.5rem; text-align: center; }
        .subtitle { font-size: 12px; margin-bottom: 1rem; }
        .justified-paragraph { text-align: justify; text-indent: 20px; font-size: 11px; margin: 0 0 1rem 0; }
        .signature-table { margin-top: 1.25rem; }
        .signature-table td { padding: 10px 8px; }
        .signature-line { border-bottom: 1px solid #000; height: 24px; }
        .winner-column { background-color: #f4f7e1; color: #000000; font-weight: bold; }
        /* First page header/footer styles */
    </style>
</head>
<body>
    @php
        $purposeRaw = $purchase->purpose ?? '';
        $purposeClean = trim(preg_replace('/\s+/', ' ', preg_replace('/\bto purchase\b/i', '', $purposeRaw)));
    @endphp
        
    @php
        $purchaseItems = $purchase->purchaseItems;

        $supplierGroups = $purchase->supplierQuotes
            ->groupBy(fn ($quote) => $quote->supplier_id)
            ->map(function ($quotes) {
                return [
                    'supplier' => $quotes->first()->supplier,
                    'quotes' => $quotes->keyBy('purchase_item_id'),
                    'total' => $quotes->sum(fn ($quote) => $quote->item_unit_price !== null ? $quote->item_unit_price * ($quote->purchaseItem?->item_quantity ?? 0) : 0),
                ];
            })
            ->values();

        $supplierColumns = $supplierGroups;
        $minTotal = $supplierGroups->min('total');
        $winningSupplierId = collect($supplierGroups)->firstWhere('total', $minTotal)['supplier']->id ?? null;
    @endphp

    <table class="metadata-table" style="margin-bottom: 0.5rem;">
        <tr>
            <td style="width: 50%;">
                Name of Procuring Entity: <strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong>
            </td>
            <td style="width: 50%;">
                Project Reference Number: <br>
                Name of the Project: <u>{{ $purposeClean }}</u> <br>
                Location of the Project: <u>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</u>
            </td>
        </tr>
    </table>

    <br>
    <div class="title">ABSTRACT OF QUOTATION</div>
    <br>

    <table class="metadata-table" style="margin-bottom: 0.5rem; font-size: 11px;">
        <tr>
            <td colspan="2">
                <strong>Project Name:</strong> {{ $purposeClean }} <br>
                <strong>DATE OF BID OPPENING:</strong> __________________________________________ <br>
                <strong>PLACE OF BID OPPENING:</strong> {{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }} <br>
            </td>
        </tr>
    </table>

    <table class="container-table">
        <tr>
            <th style="width: 5px;" class="center" rowspan="3">No.</th>
            <th rowspan="3">Item</th>
            <th rowspan="3">Qty</th>
            <th colspan="{{ $supplierColumns->count() * 2 }}" class="center">SUPPLIER/CONTRACTORS/DEALERS</th>
        </tr>
        <tr>
            @foreach ($supplierColumns as $supplierColumn)
                <th colspan="2" class="center">
                    {{ $supplierColumn['supplier']->supplier_name }}
                </th>
            @endforeach
        </tr>
        <tr>
            @foreach ($supplierColumns as $supplierColumn)
                <th class="center">Unit Price</th>
                <th class="center">Total Price</th>
            @endforeach
        </tr>

        @foreach ($purchaseItems as $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $item->item_name }}</td>
                <td class="center">{{ $item->item_quantity }}</td>
                @foreach ($supplierColumns as $supplierColumn)
                    @php $quote = $supplierColumn['quotes'][$item->id] ?? null; @endphp
                    <td class="right">
                        {{ $quote?->item_unit_price !== null ? number_format($quote->item_unit_price, 2) : '' }}
                    </td>
                    <td class="right">
                        {{ $quote?->item_unit_price !== null ? number_format($quote->item_unit_price * $item->item_quantity, 2) : '' }}
                    </td>
                @endforeach
            </tr>
        @endforeach

        <tr>
            <td colspan="3" class="right"><strong>Total</strong></td>
            @foreach ($supplierColumns as $supplierColumn)
                <td class="right" colspan="2"><strong>{{ number_format($supplierColumn['total'], 2) }}</strong></td>
            @endforeach
        </tr>
        <tr>
            <td colspan="3" class="right"></td>
            @foreach ($supplierColumns as $supplierColumn)
                @php $awardClass = $supplierColumn['supplier']->id === $winningSupplierId ? ' winner-column' : ''; @endphp
                <td class="center{{ $awardClass }}" colspan="2">
                    @if ($supplierColumn['supplier']->id === $winningSupplierId)
                        Awardee
                    @endif
                </td>
            @endforeach
        </tr>
    </table>

    @php
        $bacMembers = collect();
        if ($purchase->schoolRelation) {
            $bacMembers = $purchase->schoolRelation->bacMembers()->with('employee')->get();
        }

        $chairperson = $bacMembers->firstWhere('role', \App\Models\BACMember::ROLE_CHAIRPERSON);
        $viceChairperson = $bacMembers->firstWhere('role', \App\Models\BACMember::ROLE_VICE_CHAIRPERSON);
        $bacMemberList = $bacMembers->where('role', \App\Models\BACMember::ROLE_MEMBER);
    @endphp

    <table style="width:100%; border-collapse: collapse; margin-bottom: 1rem; align-items: center; text-align: center;">
        <tr>
            <td style="width:33%;  padding: 8px; vertical-align: top;">
                <strong>{{ strtoupper(optional(optional($chairperson)->employee)->full_name ?? '______________________') }}</strong><br>
                <small>BAC Chairperson<br>
                    {{ $chairperson?->employee?->current_position?->position_name ?? '' }}
                </small>
            </td>
            <td style="width:33%; padding: 8px; vertical-align: top;">
                <strong>{{ strtoupper(optional(optional($viceChairperson)->employee)->full_name ?? '______________________') }}</strong><br>
                <small>BAC Vice Chairperson<br>
                    {{ $viceChairperson?->employee?->current_position?->position_name ?? '' }}
                </small>
            </td>
            <td style="width:34%; padding: 8px; vertical-align: top;">
                @forelse($bacMemberList as $member)
                    <strong>{{ strtoupper(optional($member->employee)->full_name ?? '______________________') }}</strong><br>
                    <small>{{ ucfirst($member->role ?? 'BAC Member') }}<br>
                        {{ $member->employee?->current_position?->position_name ?? '' }}
                    </small>
                    @if(! $loop->last)
                        <br><br>
                    @endif
                @empty
                    <em>No BAC members listed.</em>
                @endforelse
            </td>
        </tr>
    </table>



</body>
</html>
