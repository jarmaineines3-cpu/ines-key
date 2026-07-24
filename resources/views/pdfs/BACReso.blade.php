<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAC Resolution NO. {{ $purchase->pr_no }}</title>
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
            $bookmanBoldPath = public_path('fonts/BookmanOldStyle-bold.ttf');
            $bookmanBoldAlt = public_path('fonts/BookmanOldStyle-Bold.ttf');
            $bookmanBoldAlt2 = public_path('fonts/BookmanOldStyleBold.ttf');
            $bookmanBoldData = null;
            if (file_exists($bookmanBoldPath)) {
                $bookmanBoldData = base64_encode(file_get_contents($bookmanBoldPath));
            } elseif (file_exists($bookmanBoldAlt)) {
                $bookmanBoldData = base64_encode(file_get_contents($bookmanBoldAlt));
            } elseif (file_exists($bookmanBoldAlt2)) {
                $bookmanBoldData = base64_encode(file_get_contents($bookmanBoldAlt2));
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

        body { margin: 125px 70px 1px; font-family: Arial, sans-serif; font-size: 12px; color: #111; }
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
        @page {
            margin: 40px 20px 70px 20px;
        }
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
        .page-header {
            position: fixed;
            top: -20px;
            left: 20px;
            right: 20px;
            width: calc(100% - 40px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-top: 0;
            margin: 0;
            z-index: 1000;
        }
        .page-footer {
            position: fixed;
            bottom: -65px;
            left: 20px;
            right: 20px;
            width: calc(100% - 40px);
            border-top: 1px solid #888;
            font-size: 10px;
            color: #333;
            text-align: center;
            padding: 8px 0;
            margin: 0;
            z-index: 1000;
        }
        .page-number:before {
        content: counter(page);
    }
        .header-logo img { max-height: 50px; }
        .republic { font-family: 'OldEnglish', serif; font-size: 18px; margin: 0; line-height: 1; }
        .school-name { font-family: 'TrajanPro', 'Times New Roman', serif; font-size: 28px; margin: 0; line-height: 1; text-transform: uppercase; }
        .school-content { font-family: 'BookmanOldStyle', 'Times New Roman', serif; font-size: 14px; margin: 0; line-height: 1; }
        .first-page-spacer { height: 10px; }
    </style>
</head>
<body>
    @php
        $purposeRaw = $purchase->purpose ?? '';
        $purposeClean = trim(preg_replace('/\s+/', ' ', preg_replace('/\bto purchase\b/i', '', $purposeRaw)));
        $schoolLogoPath = blank($purchase->schoolRelation?->school_logo)
            ? public_path('images/deped-logo.png')
            : storage_path('app/public/' . ltrim($purchase->schoolRelation?->school_logo, '/'));

        if (! file_exists($schoolLogoPath)) {
            $schoolLogoPath = public_path('images/deped-logo.png');
        }
    @endphp
    @php
        // Total amount of purchase items (sum of unit_price * quantity)
        $itemsTotal = $purchase->purchaseItems->sum(function ($it) {
            return ($it->item_unit_price ?? 0) * ($it->item_quantity ?? 0);
        });

        try {
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $integerPart = (int) floor($itemsTotal);
            $cents = (int) round(($itemsTotal - $integerPart) * 100);
            $words = $formatter->format($integerPart);
            $amountInWords = ucwords($words) . ' Pesos' . ($cents ? ' AND ' . sprintf('%02d', $cents) . '/100' : '') . ' (PHP ' . number_format($itemsTotal, 2) . ')';
        } catch (\Throwable $e) {
            $amountInWords = 'PHP ' . number_format($itemsTotal, 2);
        }
    @endphp
    <!-- First page header with DepEd logo, 'Republic of the Philippines' in Old English, and school name in Trajan Pro -->
    <div class="page-header">
        <div class="header-logo">
            <img src="{{ public_path('images/deped-logo.png') }}" alt="DepEd Logo">
        </div>
        <div class="republic">Republic of the Philippines</div>
        <div class="republic" style="font-size: 25px;">Department of Education</div>
        <div class="school-name" style="font-size: 12px; font-weight: bold;">Region I</div>
        <div class="school-name" style="font-size: 12px; font-weight: bold;">Schools Division of Ilocos Sur</div>
        <div class="school-name" style="font-size: 12px; font-weight: bold;">{{ strtoupper($purchase->schoolRelation?->school_name ?? $purchase->school) }}</div>
        <hr>
    </div>
    <div class="page-footer">
        <span>
            <table style="padding: 0; margin: 0; width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="text-align: left; font-size: 11px;">
                        <img src="{{ public_path('images/deped.webp') }}" alt="DepEd Logo" style="height: 45px;">
                        <img src="{{ public_path('images/bp.png') }}" alt="DepEd Logo" style="height: 45px;">
                        <img src="{{ public_path('images/school-is-cool.png') }}" alt="DepEd Logo" style="height: 45px;">
                        <img src="{{ $schoolLogoPath }}" alt="School Logo" style="height: 45px;">
                    </td>
                    <td style="font-size: 10px;">
                        <table style="padding: 0; margin: 0; width: 100%; border-collapse: collapse;">
                            <tr>
                                <td>
                                    <img src="{{ public_path('images/maps.png') }}" alt="Icon" style="height: 10px;"> {{ ($purchase->schoolRelation?->school_address ?? $purchase->school) }}
                                    <br>
                                    <img src="{{ public_path('images/phone.png') }}" alt="Icon" style="height: 10px;"> {{ ($purchase->schoolRelation?->school_contact ?? $purchase->school) }}
                                    <br>
                                    <img src="{{ public_path('images/email.png') }}" alt="Icon" style="height: 10px;"> {{ ($purchase->schoolRelation?->school_email ?? $purchase->school) }}
                                    <br>
                                    <img src="{{ public_path('images/social.png') }}" alt="Icon" style="height: 10px;"> {{ ($purchase->schoolRelation?->school_social ?? $purchase->school) }}
                                <td>
                                <td>
                                    <table style="text-align: center;">
                                        <tr>
                                            <td style="border: 1px solid black;"> Doc. Ref. Code: </td>
                                            <td style="border: 1px solid black; width: 60px;"> </td>
                                            <td style="border: 1px solid black; width: 20px;"> Rev </td>
                                            <td style="border: 1px solid black; width: 30px;">  </td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;"> Effectivity</td>
                                            <td style="border: 1px solid black;"> </td>
                                            <td style="border: 1px solid black;"> Page </td>
                                            <td style="border: 1px solid black;"><span class="page-number"></span> of {{ $totalPages }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table> 
        </span>
    </div>
    <div class="content-wrapper">
        <div class="school-content">
        <center><strong>BIDS AND AWARDS COMMITTEE</strong></center>
        <center><strong>
            @php
                $prNo = (string) ($purchase->pr_no ?? $purchase->id ?? '');
                $yearSegment = strlen($prNo) >= 4 ? substr($prNo, 0, 4) : date('Y');
            @endphp
            <div class="resolution">RESOLUTION NO. {{ $prNo }} S. {{ $yearSegment }}</div>
        </strong></center>
        <br>
        <center><strong>A RESOLUTION DECLARING LOWEST CALCULATED RESPONSIVE QUOTATION AND RECOMMENDING APPROVAL</strong></center>
        <br>
        <br>
        Title of the Project: <strong>Procurement of {{ $purposeClean }}</strong>
        <p style="text-align: justify;">
            <b>WHEREAS,</b> there is an approved Purchase Request for the {{ $purposeClean }} amounting to {{ $amountInWords }};
        </p>
        <p style="text-align: justify;">
            <b>WHEREAS,</b> the Department of Education, {{ $purchase->schoolRelation?->school_name ?? $purchase->school }}
                invited  suppliers to submit price quotations for the procurement of the {{ $purposeClean }} through Negotiated Procurement (Small Value Procurement) in accordance with Section 34 of the IRR of Republic Act No. 12009, and posted the request on the agency's official website and bulletin board from {{ $purchase->pr_date ? \Illuminate\Support\Carbon::parse($purchase->pr_date)->format('F j, Y') : 'N/A' }} to {{ $purchase->purchaseOrder?->po_date ? \Illuminate\Support\Carbon::parse($purchase->purchaseOrder?->po_date)->format('F j,Y') : '________' }};
        </p>
        <p style="text-align: justify;">
            <b>WHEREAS,</b> on {{ $purchase->purchaseOrder?->po_date ? \Illuminate\Support\Carbon::parse($purchase->purchaseOrder?->po_date)->format('F j, Y') : '________' }}, the Bids and Awards Committee convened at {{ ($purchase->schoolRelation?->school_name ?? $purchase->school) }} to evaluate the submitted price quotations and required documents, including the Mayor's Permit, BIR, DTI, and PhilGEPS Registration, and noted the following observations:
        </p>

    @php
        $abcTotal = $purchase->purchaseItems->sum(fn ($item) => ($item->item_unit_price ?? 0) * ($item->item_quantity ?? 0));
        $supplierRows = $purchase->supplierQuotes
            ->groupBy(fn ($quote) => $quote->supplier_id)
            ->map(function ($quotes) {
                return [
                    'supplier' => $quotes->first()->supplier,
                    'total' => $quotes->sum(fn ($quote) => $quote->item_unit_price !== null ? $quote->item_unit_price * ($quote->purchaseItem?->item_quantity ?? 0) : 0),
                ];
            })
            ->values()
            ->sortBy('total')
            ->values();

        $supplierRows = $supplierRows->map(function ($row, $index) use ($abcTotal) {
            $variance = $abcTotal > 0 ? ( $abcTotal - $row['total']) / $abcTotal * 100 : 0;
            $row['variance'] = $variance;
            $row['remark'] = match ($index) {
                0 => 'Lowest Calculated and Responsive Quotation',
                1 => 'Higher Calculated and Responsive Quotation',
                2 => 'Highest Calculated and Responsive Quotation',
                default => 'Higher Quotation',
            };
            return $row;
        });
    @endphp

    <table class="container-table" style="margin-bottom: 1rem;">
        <thead>
            <tr>
                <th rowspan="2">Name of Bidder</th>
                <th colspan="4">Observations</th>
            </tr>
            <tr>
                <th>Required Documents</th>
                <th>Price Quotations in (PHP)</th>
                <th>% of Variance from ABC ({{ number_format($abcTotal, 2) }})</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplierRows as $row)
                <tr>
                    <td>{{ $row['supplier']->supplier_name }}</td>
                    <td>Mayor's Permit, BIR, DTI, PhilGEPS Registration</td>
                    <td class="right">{{ number_format($row['total'], 2) }}</td>
                    <td class="right">{{ number_format($row['variance'], 2) }}%</td>
                    <td>{{ $row['remark'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: justify;">
        <b>WHEREAS,</b> the Bids and Awards declared the quotation of {{ $supplierRows->first()['supplier']->supplier_name }} as the supplier with the Lowest Calculated and Responsive Quotation, having complied with all required documents;
    </p>
    <p style="text-align: justify;">
        <b>NOW THEREFORE,</b> the Members of the Bids and Awards Committee hereby RESOLVE: <br>
        <ol>
            <li>To declare the quotation of {{ $supplierRows->first()['supplier']->supplier_name }} located at {{ $supplierRows->first()['supplier']->supplier_address ?? 'N/A' }} as the the supplier with the Lowest Calculated and Responsive Quotation for the procurement of {{ $purposeClean }}; and <br></li>
            <li>To recommend the approval of the said quotation to the Head of Procuring Entity of {{ $purchase->schoolRelation?->school_name ?? $purchase->school }} for the approval of these findings and the award for the contract
            for the procurement of {{ $purposeClean }} to {{ $supplierRows->first()['supplier']->supplier_name }}.</li>
        </ol>
    </p>
    <p style="text-align: justify;">
        <b>RESOLVED,</b> at the Department of Education, {{ $purchase->schoolRelation?->school_name ?? $purchase->school }}, {{ $purchase->schoolRelation?->school_address ?? $purchase->school_address }}  this {{ $purchase->purchaseOrder?->po_date ? \Illuminate\Support\Carbon::parse($purchase->purchaseOrder?->po_date)->format('jS') : '____' }} day of {{ $purchase->purchaseOrder?->po_date ? \Illuminate\Support\Carbon::parse($purchase->purchaseOrder?->po_date)->format('F Y') : '________' }}.
    </p>

    @php
        $bacMembers = collect();
        if ($purchase->schoolRelation) {
            $bacMembers = $purchase->schoolRelation->bacMembers()->with('employee')->get();
        }

        $chairperson = $bacMembers->firstWhere('role', \App\Models\BACMember::ROLE_CHAIRPERSON);
        $viceChairperson = $bacMembers->firstWhere('role', \App\Models\BACMember::ROLE_VICE_CHAIRPERSON);
        $bacMemberList = $bacMembers->where('role', \App\Models\BACMember::ROLE_MEMBER);
    @endphp

    <br>
    <br>
    <table style="width:100%; border-collapse: collapse; margin-bottom: 1rem; align-items: center; text-align: center;">
        <tr>
            <td style="width:33%;  padding: 8px; vertical-align: top;">
                <strong>{{ strtoupper($chairperson?->employee?->full_name ?? '______________________') }}</strong><br>
                <small>BAC Chairperson<br>
                    {{ $chairperson?->employee?->current_position?->position_name ?? '' }}
                </small>
            </td>
            <td style="width:33%; padding: 8px; vertical-align: top;">
                <strong>{{ strtoupper($viceChairperson?->employee?->full_name ?? '______________________') }}</strong><br>
                <small>BAC Vice Chairperson<br>
                    {{ $viceChairperson?->employee?->current_position?->position_name ?? '' }}
                </small>
            </td>
        </tr>
        <br>

        @forelse($bacMemberList->chunk(2) as $memberRow)
            <tr>
                @foreach($memberRow as $member)
                    <td style="width:33%; padding: 8px; vertical-align: top;">
                        <strong>{{ strtoupper($member->employee?->full_name ?? '______________________') }}</strong><br>
                        <small>BAC Member<br>
                            {{ $member->employee?->current_position?->position_name ?? '' }}
                        </small>
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="2" style="padding: 8px; vertical-align: top; text-align: center;">
                    <em>No BAC members listed.</em>
                </td>
            </tr>
        @endforelse
    </table>
    <br>
    <p style="text-align: justify;">
        <b>APPROVED:</b> 
        <br><br>
        <center>
            <strong>{{ strtoupper(optional($purchase->approver)->full_name ?? $purchase->approved_by) }}</strong><br>
            <small>{{ ($purchase->approver?->current_position?->position_name ?? $purchase->approved_by) }}</small><br>
            Head of Procuring Entity <br>
            Date Approved: {{ $purchase->purchaseOrder?->po_date ? \Illuminate\Support\Carbon::parse($purchase->purchaseOrder?->po_date)->format('F j,Y') : '________' }}
        </center>
    </p>
    <br>
    <br>
    <p style="text-align: justify;">
        <b>BIDS AND AWARDS COMMITTEE <br>RESOLUTION NO: <u>{{ $purchase->pr_no ?? '______________________' }}, S. {{ str($purchase->pr_no)->substr(0, 4) ?? '______________________' }}</u></b> 
        <br>
    </p>
    <script type="text/php">
    if (isset($pdf)) {
        // Use the built-in font metrics to find the width of the text
        $font = $fontMetrics->get_font("Arial", "normal");
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $size = 10;
        
        // Calculate the center position
        $width = $fontMetrics->get_text_width($text, $font, $size);
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 50; // Adjust this value to set the distance from the bottom
        
        // Render the text
        $pdf->page_text($x, $y, $text, $font, $size, [0, 0, 0]);
    }
</script>
</body>
</html>
