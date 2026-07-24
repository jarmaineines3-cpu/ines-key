<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issuances Report {{ $purchase->pr_no }}</title>
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
        .page-break { display: block; page-break-before: always; page-break-after: auto; height: 0; margin: 0; padding: 0; }
        .header-table, .purpose-table, .metadata-table, .sub-header-table { border: none; }
        .appendix { text-align: right; font-style: italic; margin-bottom: 0.5rem; }
        .center { text-align: center; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 0.5rem; text-align: center; }
        .items-table th, .items-table td { border: 1px solid #888; padding: 6px; }
        .items-table td:nth-child(4), .items-table td:nth-child(5) { text-align: right; }
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
    @php
        $inventoryIssuances = $purchase->issuances->filter(fn ($issuance) => optional($issuance->purchaseItem?->item)->inventory_item);
    @endphp

    @php
        $supplyIssuances = $purchase->issuances->filter(fn ($issuance) => ! optional($issuance->purchaseItem?->item)->inventory_item);
    @endphp

    @foreach($purchase->issuances->groupBy('employee_id') as $employeeId => $employeeIssuances)
        @php
            $employee = $employeeIssuances->first()->employee;
            $employeeInventoryIssuances = $employeeIssuances->filter(fn ($issuance) => optional($issuance->purchaseItem?->item)->inventory_item);
        @endphp
        <div class="appendix">Appendix 63</div>
        <div class="title">REQUISITION AND ISSUE SLIP</div>
        <table class="metadata-table" style="margin-bottom: 0.5rem; text-align: center;">
            <tr style="text-align: left;">
                <td>ENTITY NAME:<strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong></td>
                <td>Fund Cluster:<strong>{{ strtoupper(optional($purchase)->fund_cluster ?? '') }}</strong></td>
            </tr>
        </table>
        <table class="items-table">
            <tr>
                <td colspan="4" style="width: 25px;">
                    <strong>Division:</strong> ILOCOS SUR</u><br>
                    <strong>Office:</strong> DEPARTMENT OF EDUCATION<br>
                </td>
                <td colspan="4">
                    <strong>Responsibility Center Code:</strong> <u>{{ $purchase->rc_code ?? '' }}</u><br>
                    <strong>RIS NO:</strong> <u>{{ $employeeIssuances->first(fn ($issuance) => filled($issuance->ris_no))?->ris_no ?? $employeeIssuances->first()?->ris_no ?? '' }}</u>
                </td>
            </tr>
            <tr style="text-align: center; font-weight: bold;">
                <th>Stock No.</th>
                <th>Unit</th>
                <th width="30%">Description</th>
                <th>Quantity</th>
                <th>Yes</th>
                <th>No</th>
                <th>Quantity</th>
                <th width="20%">Remarks</th>
            </tr>
            @foreach($employeeIssuances as $issuance)
                <tr>
                    <td>{{ optional($issuance->purchaseItem?->item)->stock_no ?? '' }}</td>
                    <td>{{ $issuance->purchaseItem?->item_unit ?? '' }}</td>
                    <td>{{ $issuance->purchaseItem?->item_name ?? '' }} {!! filled($issuance->inventory_no) ? '<br>' . $issuance->inventory_details : '' !!}</td>
                    <td class="center">{{ $issuance->quantity }}</td>
                    <td class="center">/</td>
                    <td></td>
                    <td class="center">{{ $issuance->quantity }}</td>
                    <td class="center">{{ filled($issuance->inventory_no) ? 'ICS No. ' . $issuance->ics_no : '' }}</td>
                </tr>
            @endforeach
        </table>
        <table class='container-table' style="margin-top: 1rem;">
            <tr>
                <td colspan="5">
                    Purpose: <strong>{{ $purchase->purpose ?? '' }}</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <br><br>Signature:
                </td>
                <td>
                    Requested by: <br><br>
                </td>
                <td>
                    Approved by: <br><br>
                </td>
                <td>
                    Issued by: <br><br>
                </td>
                <td>
                    Received by: <br><br>
                </td>
            </tr>
            <tr>
                <td>
                    Printed Name:
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ strtoupper($employee?->full_name ?? 'N/A') }}
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ strtoupper(optional($purchase->approver)->full_name ?? 'N/A') }}
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ strtoupper(optional($purchase->requester)->full_name ?? 'N/A') }}
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ strtoupper($employee?->full_name ?? 'N/A') }} 
                </td>
            </tr>
            <tr>
                <td>
                    Designation:
                </td>
                <td style="text-align: center;">
                    {{ (optional($employee?->current_position)->position_name ?? 'N/A') }}
                </td>
                <td style="text-align: center;">
                    {{ $purchase->approver?->current_position?->position_name ?? 'N/A' }}
                </td>
                <td style="text-align: center;">
                    {{ $purchase->requester?->current_position?->position_name ?? 'N/A' }}
                </td>
                <td style="text-align: center;">
                    {{ (optional($employee?->current_position)->position_name ?? 'N/A') }}
                </td>
            </tr>
            <tr>
                <td>
                    Date:
                </td>
                <td style="text-align: center; font-weight: bold;">

                </td>
                <td style="text-align: center; font-weight: bold;">
                    
                </td>
                <td style="text-align: center; font-weight: bold;">
                    
                </td>
                <td style="text-align: center; font-weight: bold;">
                    
                </td>
            </tr>
        </table>

        @if($employeeInventoryIssuances->isNotEmpty())
            <div class="page-break"></div>
            <div class="appendix">Appendix 59</div>
            <div class="title">INVENTORY CUSTODIAN SLIP</div>
            <table class="metadata-table" style="margin-bottom: 0.5rem; text-align: center;">
                <tr style="text-align: left;">
                    <td>
                        ENTITY NAME: <strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong><br>
                        FUND CLUSTER: <strong>{{ strtoupper(optional($purchase)->fund_cluster ?? '') }}</strong>
                    </td>
                    <td>
                        <br>
                        ICS NO.: <strong>{{ $employeeIssuances->first(fn ($issuance) => filled($issuance->ics_no))?->ics_no ?? $employeeIssuances->first()?->ics_no ?? '' }}</strong>
                    </td>
                </tr>
            </table>
            <table class="items-table">
                <tr style="text-align: center; font-weight: bold;">
                    <th rowspan="2">QTY</th>
                    <th rowspan="2">Unit</th>
                    <th colspan="2">Amount</th>
                    <th rowspan="2">Description</th>
                    <th rowspan="2">Inventory Item No.</th>
                    <th rowspan="2">Estimated Useful Life</th>
                </tr>
                <tr style="text-align: center; font-weight: bold;">
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                </tr>
                @foreach($employeeInventoryIssuances as $issuance)
                    @php
                        $lowestSupplierQuotes = collect($lowestSupplier['quotes'] ?? []);
                        $unitCost = optional($lowestSupplierQuotes->first(fn ($quote) => $quote->purchase_item_id === $issuance->purchase_item_id))->item_unit_price;
                        $totalCost = $unitCost !== null ? $unitCost * $issuance->quantity : null;
                    @endphp
                    <tr>
                        <td class="center">{{ $issuance->quantity }}</td>
                        <td>{{ $issuance->purchaseItem?->item_unit ?? '' }}</td>
                        <td class="center">{{ $unitCost !== null ? number_format($unitCost, 2) : '' }}</td>
                        <td class="center">{{ $totalCost !== null ?  number_format($totalCost, 2) : '' }}</td>
                        <td style="text-align: left;">
                            {{ $issuance->purchaseItem?->item_name ?? '' }} {!! filled($issuance->inventory_no) ? '<br>' . $issuance->inventory_details : '' !!}
                        </td>
                        <td class="center">{{ ($issuance->inventory_no) }}</td>
                        <td class="center">
                            {{ $issuance->inventory_lifespan ?? '' }} {{ $issuance->inventory_lifespan === 1 ? 'Year' : 'Years' }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
        <table class="container-table">
            <tr>
                <td>
                    Received from: <br><br><br>
                    <center><strong><u>{{ strtoupper(optional($purchase->requester)->full_name ?? 'N/A') }}</u></strong></center>
                    <center>Signature over Printed Name<center>
                    <center>Property Custodian</center>
                    <center>Possition/Office</center>
                    <center>____________________</center>
                    <center>Date</center>
                </td>
                <td>
                    Received by: <br><br><br>
                    <center><strong><u>{{ strtoupper($employee?->full_name ?? 'N/A') }}</u></strong></center>
                    <center>Signature over Printed Name</center>
                    <center>Property Custodian</center>
                    <center>Possition/Office</center>
                    <center>____________________</center>
                    <center>Date</center>
                </td>
            </tr>
        </table>
        @if (! $loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    
    @if($supplyIssuances->isNotEmpty())
        <div class="page-break"></div>
        <div class="appendix">Annex 64</div>
        <div class="title">REPORT OF SUPPLIES AND MATERIALS ISSUED</div>
        <table class="metadata-table" style="margin-bottom: 0.5rem; text-align: center;">
            <tr style="text-align: left;">
                <td>ENTITY NAME:<strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong></td>
                <td>Serial No.: <strong>{{ $employeeIssuances->first(fn ($issuance) => filled($issuance->rsmi_serial_no))?->rsmi_serial_no ?? $employeeIssuances->first()?->rsmi_serial_no ?? '' }}</strong></td>
            </tr>
            <tr style="text-align: left;">
                <td>Fund Cluster:<strong>{{ strtoupper(optional($purchase)->fund_cluster ?? '') }}</strong></td>
                <td>Date: _________________</td>
            </tr>
        </table>
        <table class="items-table">
            <tr style="text-align: center; font-weight: bold;">
                <th style="width: 72px">RIS No.</th>
                <th style="width: 70px">Responsibility Center Code</th>
                <th>Stock No.</th>
                <th style="width: 155px">Item Description</th>
                <th>Unit</th>
                <th>Quantity Issued</th>
                <th>Unit Cost</th>
                <th>Amount</th>
            </tr>
            
            @foreach($supplyIssuances as $issuance)
                @php
                    $lowestSupplierQuotes = collect($lowestSupplier['quotes'] ?? []);
                    $unitCost = optional($lowestSupplierQuotes->first(fn ($quote) => $quote->purchase_item_id === $issuance->purchase_item_id))->item_unit_price;
                    $totalCost = $unitCost !== null ? $unitCost * $issuance->quantity : null;
                @endphp
                <tr>
                    <td>{{ $issuance->ris_no ?? '' }}</td>
                    <td>{{ $purchase->rc_code ?? '' }}</td>
                    <td>{{ optional($issuance->purchaseItem?->item)->stock_no ?? '' }}</td>
                    <td style="text-align: center">{{ $issuance->purchaseItem?->item_name ?? '' }}</td>
                    <td>{{ $issuance->purchaseItem?->item_unit ?? '' }}</td>
                    <td style="text-align: right">{{ $issuance->quantity }}</td>
                    <td style="text-align: right">{{ $unitCost !== null ? number_format($unitCost, 2) : '' }}</td>
                    <td style="text-align: right">{{ $totalCost !== null ?  number_format($totalCost, 2) : '' }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td colspan="4">
                    <table style="margin-bottom: 0;">
                        <tr>
                            <th colspan="2">Recapitulation:</th>
                        </tr>
                        <tr style="text-align: center; font-weight: bold;">
                            <td>Stock No.</td>
                            <td>Quantity</td>
                        </tr>
                        @php
                            $recapByStockNo = $supplyIssuances->groupBy(fn ($issuance) => optional($issuance->purchaseItem?->item)->stock_no ?? '');
                        @endphp
                        @foreach($recapByStockNo as $stockNo => $issuances)
                            @php
                                $totalIssued = $issuances->sum('quantity');
                            @endphp
                            <tr style="text-align: center;">
                                <td>{{ $stockNo }}</td>
                                <td>{{ $totalIssued }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td colspan="4" >
                    <table style="margin-bottom: 0;">
                        <tr>
                            <th colspan="3">Recapitulation:</th>
                        </tr>
                        <tr style="text-align: center; font-weight: bold;">
                            <td>Unit Cost</td>
                            <td>Total Cost</td>
                            <td>UACS Object Code </td>
                        </tr>
                        @php
                            $recapByStockNo = $supplyIssuances->groupBy(fn ($issuance) => optional($issuance->purchaseItem?->item)->stock_no ?? '');
                        @endphp
                        @foreach($recapByStockNo as $stockNo => $issuances)
                            @php
                                $firstIssuance = $issuances->first();
                                $firstItem = $firstIssuance?->purchaseItem?->item;
                                $uacsCode = $firstItem?->uacsCode?->uacs_code ?? '';
                                $lowestSupplierQuotes = collect($lowestSupplier['quotes'] ?? []);
                                $lowestQuote = $lowestSupplierQuotes->first(fn ($quote) => $quote->purchase_item_id === $firstIssuance?->purchase_item_id);
                                $unitCost = $lowestQuote?->item_unit_price;
                                $totalCost = $unitCost !== null ? $unitCost * $issuances->sum('quantity') : null;
                            @endphp
                            <tr style="text-align: center;">
                                <td>{{ $unitCost !== null ? number_format($unitCost, 2) : '' }}</td>
                                <td>{{ $totalCost !== null ? number_format($totalCost, 2) : '' }}</td>
                                <td>{{ $uacsCode }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    I hereby certify to the correctness of the above information. <br><br><br>
                    <center><strong><u>{{ strtoupper(optional($purchase->requester)->full_name ?? 'N/A') }}</u></strong></center>
                    <center>Signature over Printed Name of Supply and/or Property Custodian</center>
                </td>
                <td colspan="3" style="border-right: none;">
                    <span>
                    Posted by: <br><br><br>
                    <center><strong><u>{{ strtoupper(optional($purchase->seniorBookkeeper)->full_name ?? $purchase->senior_bookkeeper_name ?? '______________________') }}</u></strong></center>
                    <center>Signature over Printed Name of Designated Accounting Staff</center>
                    </span>
                </td>
                <td style="border-left: none; text-align: center;">
                    <br>
                    __________________<br>
                    Date
                </td> 
            </tr>
        </table>
    @endif

    @if($inventoryIssuances->isNotEmpty())
        <div class="page-break"></div>
        <div class="appendix">Annex A.7</div>
        <div class="title">REPORT OF SEMI-EXPENDABLE PROPERTY ISSUED</div>
        <table class="metadata-table" style="margin-bottom: 0.5rem; text-align: center;">
            <tr style="text-align: left;">
                <td>ENTITY NAME:<strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong></td>
                <td>Serial No.: <strong>{{ $employeeIssuances->first(fn ($issuance) => filled($issuance->rspi_serial_no))?->rspi_serial_no ?? $employeeIssuances->first()?->rspi_serial_no ?? '' }}</strong></td>
            </tr>
            <tr style="text-align: left;">
                <td>Fund Cluster:<strong>{{ strtoupper(optional($purchase)->fund_cluster ?? '') }}</strong></td>
                <td>Date: _________________</td>
            </tr>
        </table>
        <table class="items-table">
            <tr style="text-align: center; font-weight: bold;">
                <th>ICS No.</th>
                <th>Responsibility Center Code</th>
                <th>Semi-expendable Property No.</th>
                <th>Item Description</th>
                <th>Unit</th>
                <th>Quantity Issued</th>
                <th>Unit Cost</th>
                <th>Amount</th>
            </tr>
            
            @foreach($inventoryIssuances as $issuance)
                 @php
                    $lowestSupplierQuotes = collect($lowestSupplier['quotes'] ?? []);
                    $unitCost = optional($lowestSupplierQuotes->first(fn ($quote) => $quote->purchase_item_id === $issuance->purchase_item_id))->item_unit_price;
                    $totalCost = $unitCost !== null ? $unitCost * $issuance->quantity : null;
                @endphp
                <tr>
                    <td>{{ $issuance->ics_no ?? '' }}</td>
                    <td>{{ $purchase->rc_code ?? '' }}</td>
                    <td>{{ $issuance->inventory_no ?? '' }}</td>
                    <td>{{ $issuance->purchaseItem?->item_name ?? '' }}</td>
                    <td>{{ $issuance->purchaseItem?->item_unit ?? '' }}</td>
                    <td class="center">{{ $issuance->quantity }}</td>
                    <td class="center">{{ $unitCost !== null ? number_format($unitCost, 2) : '' }}</td>
                        <td class="center">{{ $totalCost !== null ?  number_format($totalCost, 2) : '' }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td colspan="4">
                    I hereby certify to the correctness of the above information. <br><br><br>
                    <center><strong><u>{{ strtoupper(optional($purchase->requester)->full_name ?? 'N/A') }}</u></strong></center>
                    <center>Signature over Printed Name of Supply and/or Property Custodian</center>
                </td>
                <td colspan="3" style="border-right: none;">
                    <span>
                    Posted by: <br><br><br>
                    <center><strong><u>{{ strtoupper(optional($purchase->seniorBookkeeper)->full_name ?? $purchase->senior_bookkeeper_name ?? '______________________') }}</u></strong></center>
                    <center>Signature over Printed Name of Designated Accounting Staff</center>
                    </span>
                </td>
                <td style="border-left: none; text-align: center;">
                    <br>
                    __________________<br>
                    Date
                </td> 
            </tr>
        </table>
    @endif
</body>
</html>
