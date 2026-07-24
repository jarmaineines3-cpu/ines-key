<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $purchase->pr_no }}</title>
    <style>
        body { margin: 0 20px; font-family: "Times New Roman", Times, serif; font-size: 12px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        .container-table { border: 1px solid #888; }
        .container-table td,
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
        .page-break { display: block; page-break-before: always; page-break-after: auto; height: 0; margin: 0; padding: 0; }
        .appendix { text-align: right; font-style: italic; margin-bottom: 0.5rem; }
        .center { text-align: center; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 0.5rem; text-align: center; }
        .items-table th, .items-table td { border: 1px solid #888; padding: 6px; }
        .items-table td:nth-child(5), .items-table td:nth-child(6) { text-align: right; }
        .items-table th { background-color: #f0f0f0; }
        .sub-header-table { width: 100%; border-collapse: collapse; }
        .justified-paragraph { text-align: justify; text-indent: 20px; font-size: 11px; margin: 0 0 1rem 0; }
        .sub-header-table td { border: none; padding: 0; }
        .purpose-table td { padding-top: 12px; }
        .signature-table { margin-top: 1.25rem; }
        .signature-table td { padding: 10px 8px; }
        .signature-line { border-bottom: 1px solid #000; height: 24px; }
        .bold-center { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <br>
    <div class="appendix">Appendix 61</div>
    <div class="title">PURCHASE ORDER</div>
    <table class="metadata-table" style="margin-bottom: 0.5rem; text-align: center;">
        <tr>
            <td style="color: red; font-size: 14px"> <strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong></td>
        </tr>
    </table>

    <table class="container-table">
        <tr>
            <td colspan="3" style="width: 25px;">
                <strong>Supplier:</strong> <u>{{ strtoupper(optional($lowestSupplier['supplier'])->supplier_name ?? '') }}</u><br>
                <strong>Address:</strong> <u>{{ strtoupper(optional($lowestSupplier['supplier'])->supplier_address ?? '') }}</u><br>
                <strong>TIN:</strong> <u>{{ strtoupper(optional($lowestSupplier['supplier'])->supplier_tin ?? '') }}</u>
            </td>
            <td colspan="3">
                <strong>PO No.:</strong> <u>{{ optional($purchase->purchaseOrder)->po_no ?? '' }}</u> <br>
                <strong>Date:</strong> <u>{{ optional($purchase->purchaseOrder)->po_date ? \Illuminate\Support\Carbon::parse(optional($purchase->purchaseOrder)->po_date)->format('m-d-Y') : '' }}</u> <br>
                <strong>Mode of Procurement:</strong> <u>Shopping</u>
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td style="width: 5px;">
                <strong>Stock/Property No.</strong>
            </td>
            <td>
                <strong>Unit</strong>
            </td>
            <td style="width: 40%;">
                <strong>Item Description</strong>
            </td>
            <td style="width: 5px;" class="center">
                <strong>Quantity</strong>
            </td>
            <td>
                <strong>Unit Cost</strong>
            </td>
            <td>
                <strong>Amount</strong>
            </td>
        </tr>
       {{-- ... previous table headers ... --}}

        @php
            $purchaseItems = $purchase->purchaseItems->values();
            $minimumRowCount = 17;
            // Separate items from empty rows
            $displayItems = $purchaseItems; 
            $count = $displayItems->count();
            $emptyRowsNeeded = max($minimumRowCount - $count, 0);
        @endphp

        @foreach($displayItems as $item)
            @php
                $supplierQuote = $lowestSupplier['quotes']->firstWhere('purchase_item_id', $item->id);
                $quotedUnitPrice = $supplierQuote?->item_unit_price ?? $item->item_unit_price;
                $quotedTotalPrice = $quotedUnitPrice * $item->item_quantity;
            @endphp
            <tr>
                <td class="center">{{ optional($item->item)->stock_no ?? '' }}</td>
                <td>{{ $item->item_unit }}</td>
                <td>{{ $item->item_name }}</td>
                <td class="center">{{ $item->item_quantity }}</td>
                <td style="text-align: right;">{{ number_format($quotedUnitPrice, 2) }}</td>
                <td style="text-align: right;">{{ number_format($quotedTotalPrice, 2) }}</td>
            </tr>
        @endforeach

        {{-- Add the Nothing Follows row immediately after the items --}}
        <tr>
            <td></td>
            <td></td>
            <td style="text-align: center; font-weight: bold;">***Nothing follows***</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        {{-- Add remaining empty rows if needed --}}
        @for($i = 0; $i < $emptyRowsNeeded; $i++)
            <tr>
                <td class="center">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td style="text-align: right;">&nbsp;</td>
                <td style="text-align: right;">&nbsp;</td>
            </tr>
        @endfor

        <tfoot style="border-top: 2px solid #000;">
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="font-weight: bold; text-align: right;">{{ number_format($purchase->purchaseItems->sum(fn($item) => ($lowestSupplier['quotes']->firstWhere('purchase_item_id', $item->id)?->item_unit_price ?? $item->item_unit_price) * $item->item_quantity), 2) }}</td>
            </tr>
        </tfoot>
        <tr>
            <td colspan="6">
                <p>
                    In case of failure to make full delivery within time specified above, a penalty of one-tenth (1/10) of one percent for every day of delay thall be imposed on the undelivered item/s
                </p>
                <table>
                    <tr>
                        <td style="border: none; width: 50%;">
                            Conforme: <br>
                        </td>
                        <td style="border: none;">
                            Very truly yours, <br>
                        </td>
                    </tr>
                    <br>
                    <tr>
                        <td style="border: none; text-align: center;">
                            <strong><u>{{ strtoupper(optional($lowestSupplier['supplier'])->supplier_name ?? '') }}</u></strong><br>
                            Signature over Printed Name of Supplier <br><br>
                            _________________________________<br>
                            Date
                        </td>
                        <td style="border: none; text-align: center;">
                            <strong><u>{{ strtoupper(optional($purchase->approver)->full_name ?? $purchase->approved_by) }}</u></strong> <br>
                            Signature over Printed Name of Authorized Official <br><br>
                            {{ ($purchase->approver?->current_position?->position_name ?? $purchase->approved_by) }} <br>
                            Designation
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="padding: 0;">
            <td colspan="6" style="padding: 0;">
                <table style="margin: 0; padding: 0;">
                    <tr>
                        <td style="border: 1px solid #000000; width: 50%; border-collapse: collapse; margin: 0;">
                            Fund Cluster:___________________________ <br>
                            Funds Available:_______________________ <br>
                            <br>
                            <b><u><center>{{ strtoupper(optional($purchase->seniorBookkeeper)->full_name ?? $purchase->senior_bookkeeper_name ?? '______________________') }}</center></u></b>
                            <center>Senior Bookkeeper <br></center><br>
                        </td>
                        <td style="border: 1px solid #000000; border-collapse: collapse; margin: 0;">
                            ORS/BURS No.:___________________________ <br>
                            Date of the ORS/BURS:___________________________ <br>
                            <br>
                            Amount:___________________________ <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
