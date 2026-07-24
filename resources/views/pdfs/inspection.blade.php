<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspection Report {{ $purchase->pr_no }}</title>
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
    <div class="appendix">Appendix 62</div>
    <div class="title">INSPECTION AND ACCEPTANCE REPORT</div>
    <table class="metadata-table" style="margin-bottom: 0.5rem; text-align: center;">
        <tr style="text-align: left;">
            <td>ENTITY NAME:<strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong></td>
            <td>Fund Cluster:<strong>{{ strtoupper(optional($purchase)->fund_cluster ?? '') }}</strong></td>
        </tr>
    </table>

    <table class="container-table">
        <tr>
            <td colspan="2" style="width: 25px;">
                Supplier: <strong> <u>{{ strtoupper(optional($lowestSupplier['supplier'])->supplier_name ?? '') }}</u><br> </strong>
                PO No./Date: <strong> <u>{{ optional($purchase->purchaseOrder)->po_no ?? '' }} / {{ optional($purchase->purchaseOrder)->po_date ? date('m-d-Y', strtotime(optional($purchase->purchaseOrder)->po_date)) : '' }}</u><br> </strong>
                Requisitioning Office/Dept.: ______________ <br>
                Responsibility Center Code: <strong> <u>{{ $purchase->rc_code ?? '' }}</u><br> </strong>
            </td>
            <td colspan="2">
                IAR No.: <strong><u>{{ optional($purchase->purchaseOrder)->po_no ?? '' }}</u></strong> <br>
                Inspection Date: _________________ <br>
                Invoice No.: _____________________ <br>
                Invoice Date: ____________________ <br>
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td style="width: 20px;"><strong>Stock/Property No.</strong></td>
            <td style="width: 50%;"><strong>Item Description</strong></td>
            <td class="center"><strong>Unit</strong></td>
            <td><strong>Quantity</strong></td>
        </tr>

        @php
            $purchaseItems = $purchase->purchaseItems->values();
            $minimumRowCount = 17;
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
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->item_unit }}</td>
                <td class="center">{{ $item->item_quantity }}</td>
            </tr>
        @endforeach

        <tr>
            <td></td>
            <td style="text-align: center; font-weight: bold;">***Nothing follows***</td>
            <td></td>
            <td></td>
        </tr>

        @for($i = 0; $i < $emptyRowsNeeded; $i++)
            <tr>
                <td class="center">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="center">&nbsp;</td>
            </tr>
        @endfor
        <tr style="border: none;">
            <td colspan="4" style=" width: 50%;padding: 0; margin: 0;border: none;">
                <table style="width: 100%; border: none; border-collapse: collapse;padding: 0; margin: 0;">
                    <tr style=" width: 50%;padding: 0; margin: 0;">
                        <td style=" width: 50%;padding: 2; margin: 0; text-align: center; font-weight: bold;">
                            INSPECTION
                        </td>
                        <td style=" width: 50%;padding: 2; margin: 0; text-align: center; font-weight: bold;">
                            ACCEPTANCE
                        </td>
                    </tr>
                    <tr style=" width: 50%;padding: 0; margin: 0;">
                        <td style=" width: 50%;padding: 10; margin: 0;">
                            Date Inspected: _______________________
                            <br>
                            <br>
                            <table style="width: 100%; margin-top: 5px; border-collapse: collapse;">
                                <tr>
                                    <!-- Checkbox Cell -->
                                    <td style="width: 20px; vertical-align: middle; padding-top: 2px; border: none;">
                                        <div style="width: 25px; height: 25px; border: 1px solid #000;"></div>
                                    </td>
                                    <!-- Text Cell -->
                                    <td style="vertical-align: top; text-align: justify; border: none;">
                                        Inspected, verified, and found to be in order as to quantity and specifications.
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <center><strong>{{ strtoupper($purchase->inspection->inspector?->full_name ?? 'N/A') }}</strong><center>
                            Inspection Officer/Inspection Committee
                        </td>
                        <td style=" width: 50%;padding: 10; margin: 0;">
                            Date Received: _______________________
                            <br>
                            <br>
                            <table style="width: 100%; margin-top: 0px; border-collapse: collapse;">
                                <tr>
                                    <!-- Checkbox Cell -->
                                    <td style="width: 20px; vertical-align: middle; padding-top: 2px; border: none;">
                                        <div style="width: 25px; height: 25px; border: 1px solid #000;"></div>
                                    </td>
                                    <!-- Text Cell -->
                                    <td style="vertical-align: top; text-align: justify; border: none;">
                                        Complete
                                    </td>
                                </tr>
                            </table>
                            <table style="width: 100%; margin: 0px; border-collapse: collapse;">
                                <tr>
                                    <!-- Checkbox Cell -->
                                    <td style="width: 20px; vertical-align: middle; padding-top: 2px; border: none;">
                                        <div style="width: 25px; height: 25px; border: 1px solid #000;"></div>
                                    </td>
                                    <!-- Text Cell -->
                                    <td style="vertical-align: top; text-align: justify; border: none;">
                                        Partial (please specify quantity)
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <center><strong>{{ strtoupper($purchase->requester?->full_name ?? 'N/A') }}</strong><center>
                            Supply and/or Property Custodian
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
