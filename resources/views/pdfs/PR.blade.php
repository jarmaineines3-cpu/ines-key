<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request {{ $purchase->pr_no }}</title>
    <style>
        body { margin: 0 20px; font-family: Arial, sans-serif; font-size: 12px; color: #111; }
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
    <div class="appendix">Appendix 60</div>
    <div class="title">PURCHASE REQUEST</div>
    <br>
    <table class="metadata-table" style="margin-bottom: 0.5rem;">
        <tr>
            <td><strong>Entity Name:</strong> <strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong>  </td>
            <td><strong>Fund Cluster:</strong> {{ $purchase->fund_cluster }}</td>
        </tr>
    </table>

    <table class="container-table">
        <tr>
            <td colspan="2" style="width: 25px;">
                <strong>Office/Section:</strong> {{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}
            </td>
            <td colspan="2">
                <strong>PR No.:</strong> <u>{{ $purchase->pr_no }}</u> <br>
                <strong>Responsibility Center Code:</strong> <u>{{ $purchase->rc_code }}</u>
            </td>
            <td colspan="2">
                <strong>Date:</strong> {{ $purchase->pr_date ? \Illuminate\Support\Carbon::parse($purchase->pr_date)->format('m/d/Y') : '' }}
            </td>
        </tr>
        <tr>
            <td style="width: 5px;">
                <strong>Stock/Property No.</strong>
            </td>
            <td>
                <strong>Unit</strong>
            </td>
            <td>
                <strong>Item Description</strong>
            </td>
            <td style="width: 5px;" class="center">
                <strong>Quantity</strong>
            </td>
            <td>
                <strong>Unit Cost</strong>
            </td>
            <td>
                <strong>Total Cost</strong>
            </td>
        </tr>
        @foreach($purchase->purchaseItems as $item)
            <tr>
                <td class="center">{{ optional($item->item)->stock_no ?? '' }}</td>
                <td>{{ $item->item_unit }}</td>
                <td>{{ $item->item_name }}</td>
                <td class="center">{{ $item->item_quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->item_unit_price, 2) }}</td>
                <td style="text-align: right;">{{ number_format($item->item_unit_price * $item->item_quantity, 2) }}</td>
            </tr>
        @endforeach
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: center; font-weight: bold;">***Nothing follows***</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="font-weight: bold; text-align: right;">{{ number_format($purchase->purchaseItems->sum(fn($item) => $item->item_unit_price * $item->item_quantity), 2) }}</td>
            </tr>
        </tfoot>
        <tr>
            <td colspan="6" style="padding: 30px 4px;">
                <strong>Purpose:</strong> {{ $purchase->purpose }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" class="center">Requested by:</td>
            <td colspan="3" class="center">Approved by:</td>
        </tr>
        <tr>
            <td> Signature:</td>
            <td colspan="2" class="center"></td>
            <td colspan="3" class="center"></td>
        </tr>
        <tr>
            <td> Printed Name:</td>
            <td colspan="2" class="center bold-center">{{ strtoupper(optional($purchase->requester)->full_name ?? $purchase->requested_by) }}</td>
            <td colspan="3" class="center bold-center">{{ strtoupper(optional($purchase->approver)->full_name ?? $purchase->approved_by) }}</td>
        </tr>
        <tr>
            <td> Designation:</td>
            <td colspan="2" class="center">School Property Custodian</td>
            <td colspan="3" class="center">School Head</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <br>
    <table class="metadata-table" style="margin-bottom: 0.5rem;">
        <tr>
            <td style="width: 50%;">
                Name of Procuring Entity: <strong>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</strong>
            </td>
            <td style="width: 50%;">
                Project Reference Number: <br>
                Name of the Project: <u>{{ $purchase->purpose }}</u> <br>
                Location of the Project: <u>{{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}</u>
            </td>
        </tr>
    </table>

    <br>
    <div class="title">REQUEST FOR QUOTATION</div>
    <br>

    <table class="metadata-table" style="margin-bottom: 0.5rem; font-size: 11px;">
        <tr>
            <td style="width: 70%;">
                Standard Form Number: SF-GOOD-60Revised on May 24, 2004 <br>
                Standard Form Title: Request for Quotation <br>
            </td>
            <td style="width: 30%; font-size: 13px;">
                Date: <u>{{ $purchase->pr_date ? \Illuminate\Support\Carbon::parse($purchase->pr_date)->format('m/d/Y') : '' }} </u><br>
                Quoatation No: ___________
            </td>
        </tr>
        <br>
        <tr>
            <td colspan="2">
                <strong>Supplier Name:</strong> ____________________________________ <br>
                <br>
                <strong>Address:</strong> __________________________________________ <br>
            </td>
        </tr>
    </table>
    <p class="justified-paragraph">
        Please quote your lowest price on the item/s listed below, subject to general terms and conditions. stating the shortest time of delivery and submit your quotation duly signed by your representative not later than __________ at 5:00 PM in the return envelope attached herewith.
    </p>
    <table class="metadata-table" style="margin-bottom: 0.5rem; font-size: 11px;">
        <tr>
            <td style="width: 70%;">
            </td>
            <td style="width: 30%; font-size: 12px;" class="center">
                <u><strong>{{ strtoupper(optional(optional(optional($purchase->schoolRelation)->bacMembers()->where('role', 'chairperson')->first())->employee)->full_name ?? '______________________') }}</strong></u> 
                <br>
                BAC Chairperson/Procurement Officer
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 10px;">
                Note:
            </td>
            <td style="padding: 0;">
                <ol style="margin: 0.25rem 0 0 1.25rem; padding: 0; font-size: 8px; text-align: left;">
                    <li>ALL ENTRIES MUST BE TYPEWRITTEN</li>
                    <li>DELIVERy PERIOD WITHIN ___________ CALENDAR DAYS</li>
                    <li>WARRANTY SHALL BE PROVIDED FOR A PERIOD OF SIX(6) MONTHS FOR THE SUPPLIES AND MATERIALS, ONE (1) YEAR FOR THE EQUIPMENT, FROM DATE OF ACCEPTANCE BY THE PROCURING ENTITY</li>
                    <li>PRICE VALIDITY SHALL BE FOR A PERIOD OF ___________ CALENDAR DAYS</li>
                    <li>G-EPS REGISTRATION CERTIFICATE SHALL BE ATTACHED UPON SUBMISSION OF THE QUOTATION</li>
                    <li>BIDDERS SHALL SUBMIT ORIGINAL BROCHURES SHOWING CERTIFICATIONS OF THE PRODUCT BEING OFFERED.</li>
                </ol>
            </td>
        </tr>
    </table>

    <table class="container-table">
        <tr>
            <td style="width: 5px;" class="center">
                <strong>No.</strong>
            </td>
            <td>
                <strong>Item Description</strong>
            </td>
            <td style="width: 5px;" class="center">
                <strong>Quantity</strong>
            </td>
            <td>
                <strong>Unit Price</strong>
            </td>
            <td>
                <strong>Total Cost</strong>
            </td>
        </tr>
        @foreach($purchase->purchaseItems as $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $item->item_name }}</td>
                <td class="center">{{ $item->item_quantity }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: center; font-weight: bold;">***Nothing follows***</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <p class="justified-paragraph">
        After having carefully read and accepted the general conditions on the reverse, I/we quote you on the item at prices noted above.
    </p>

    <table class="metadata-table" style="margin-bottom: 0.5rem; font-size: 11px;">
        <tr>
            <td style="width: 70%;">
            </td>
            <td style="width: 30%; font-size: 12px;" class="center">
                <br>
                ___________________________________
                <br>
                Printed name/Signature
                <br>
                <br>
                ___________________________________
                <br>
                Tel. No./Cellphone No./e-mail address
                <br>
                <br>
                ___________________________________
                <br>
                Date
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <br>
    <table class="metadata-table" style="margin-bottom: 0rem; font-size: 11px;">
        <tr style="background-color: #eed997;">
            <td style="width: 50%;">
                APR FORM revised August 2015
            </td>
            <td style="width: 50%;text-align: right;">
                FORM NO. 003 
            </td>
        </tr>
    </table>
    <table class="container-table" style="font-size: 10px; border: 2px solid #000000; ">
        <tr>
            <td rowspan="3" style="width: 20%;">
                <strong>NAME AND ADDRESS OF REQUESTING AGENCY:
            </td>
            <td rowspan="3">
                </strong> {{ strtoupper(optional($purchase->schoolRelation)->school_name ?? $purchase->school) }}
                <br>
                </strong> {{ strtoupper(optional($purchase->schoolRelation)->school_address ?? $purchase->school) }}
                <br>
                </strong> {{ strtoupper(optional($purchase->schoolRelation)->school_contact ?? $purchase->school) }}
            </td>
            <td>
                AGENCY ACCT. CODE:
            </td>
        </tr>
        <tr>
            <td>
                AGENCY CONTROL NO.:
            </td>
        </tr>
        <tr>
            <td>
                
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-size: 13px; font-weight: bold;">
                AGENCY PROCUREMENT REQUEST
            </td>
            <td >
                PS APR NO.: {{ $purchase->pr_no }}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table style="width: 100%; border-collapse: collapse; border: none; padding: 0; margin: 0;">
                    <tr>
                        <td style="border: none; padding: 0; width: 70%;">
                            To: PROCUREMENT SERVICE <br>
                            DBM Compound, RR Road <br>
                            Cristobal St., Paco, Manila
                        </td>
                        <td style="border: none; padding: 0; text-align: center;">
                            <u>{{ $purchase->pr_date ? \Illuminate\Support\Carbon::parse($purchase->pr_date)->format('m/d/Y') : '' }}</u>
                            <br>Date Prepared
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: none; padding: 0;">
                            <br>
                            <strong>PLEASE CHECK ( / ) APPROPRIATE BOX ON ACTION REQUESTED ON THE ITEM/S LISTED BELOW</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: none; padding: 0; font-size: 10px;">
                            <br>
                            [ / ] Please  issue common-use supplies/materials per Price List No. _____________________ dated ______________
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mode of delivery: [&nbsp;&nbsp;&nbsp;&nbsp;] Pick-up (fast lane) [&nbsp;&nbsp;&nbsp;&nbsp;] Pick-up (Schedule) [ / ] Delivery (door-to-door)
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;in case fund is not sufficient: [&nbsp;&nbsp;&nbsp;&nbsp;] Reduce Quantity  [&nbsp;&nbsp;&nbsp;&nbsp;] Bill Us    [&nbsp;&nbsp;&nbsp;&nbsp;] Charge to Unitilized Deposit, APR/OR No. ______ Date: ______
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;in case item is not available: [&nbsp;&nbsp;&nbsp;&nbsp;] Issue CNAS     [&nbsp;&nbsp;&nbsp;&nbsp;] Item for Replacement
                            <br>
                            <br>
                            [&nbsp;&nbsp;&nbsp;&nbsp;] purchase for our agency non-common items. Attached herewith : 
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;&nbsp;&nbsp;&nbsp;] Complete Specifications  [&nbsp;&nbsp;&nbsp;&nbsp;] Obligation Request (ObR) [&nbsp;&nbsp;&nbsp;&nbsp;] Copy of APP [&nbsp;&nbsp;&nbsp;&nbsp;] Others, pls. specify: _________________
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;&nbsp;&nbsp;&nbsp;] Certificate of Budget Allocation (CBA) [&nbsp;&nbsp;&nbsp;&nbsp;] Payment __________________
                            <br>
                            <br>
                            [&nbsp;&nbsp;&nbsp;&nbsp;] Purchase of ITEMS ON SALE (IOS) in PS Main, Depots and Sub-Depots
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 0;">
                <table class="container-table" style="padding: 0; margin: 0;">
                    <tr>
                        <td style="width: 5px;" class="center">
                            <strong>No.</strong>
                        </td>
                        <td style="width: 300px;">
                            <strong>Item Description</strong>
                        </td>
                        <td style="width: 5px;" class="center">
                            <strong>Quantity</strong>
                        </td>
                        <td>
                            <strong>Unit</strong>
                        </td>
                        <td>
                            <strong>Unit Price</strong>
                        </td>
                        <td>
                            <strong>Amount</strong>
                        </td>
                    </tr>
                    @php
                        $lineCount = max($purchase->purchaseItems->count(), 21);
                    @endphp
                    @for ($i = 0; $i < $lineCount; $i++)
                        @php $item = $purchase->purchaseItems->get($i); @endphp
                        <tr>
                            <td style="height: 20px; padding: 0; margin: 0;" class="center">{{ $item ? $i + 1 : '' }}</td>
                            <td>{{ $item?->item_name ?? '' }}</td>
                            <td class="center">{{ $item?->item_quantity ?? '' }}</td>
                            <td>{{ $item?->item_unit ?? '' }}</td>
                            <td style="text-align: right;">{{ $item ? number_format($item->item_unit_price, 2) : '' }}</td>
                            <td style="text-align: right;">{{ $item ? number_format($item->item_unit_price * $item->item_quantity, 2) : '' }}</td>
                        </tr>
                    @endfor
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: right; font-weight: bold;">Total Amount:  </td>
                            <td style="text-align: right;"> <strong>{{ number_format($purchase->purchaseItems->sum(fn($item) => $item->item_unit_price * $item->item_quantity), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                <table style="width: 100%; border-collapse: collapse; border: none; padding: 0; margin: 0;">
                    <tr>
                        <td colspan="3" class="center" style="padding: 0; font-size: 10px;">
                            <strong>NOTE: ALL SIGNATURES MUST BE OVER PRINTED NAME</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="center" style="padding: 1; font-size: 10px; width: 33%;">
                            <div    style="text-align: left;">STOCKS REQUESTED ARE CERTIFIED TO BE WITHIN APPROVED PROGRAM:</div>
                            <br>
                            <br>
                            <br>
                            <u><strong>{{ strtoupper(optional($purchase->requester)->full_name ?? $purchase->requested_by) }}</strong></u>
                            <br>AGENCY PROPERTY/SUPPLY OFFICER
                        </td>
                        <td class="center" style="padding: 0; font-size: 10px;">
                            <div style="text-align: left;">FUNDS CERTIFIED AVAILABLE:</div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <u><strong>{{ strtoupper(optional($purchase->seniorBookkeeper)->full_name ?? $purchase->senior_bookkeeper_name ?? '______________________') }}</strong></u>
                            <br>AGENY CHIEF ACCOUNTANT
                        </td>
                        <td class="center" style="padding: 0; font-size: 10px; width: 33%;">
                            <div style="text-align: left;">APPROVED:</div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <u><strong>{{ strtoupper(optional($purchase->approver)->full_name ?? $purchase->approved_by) }}</strong></u>
                            <br>AGENCY HEAD/AUTHORIZED SIGNATURE
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div style="padding: 10;">
                                [&nbsp;&nbsp; ] FUNDS DEPOSITED WITH PS _______________________ [&nbsp;&nbsp; ] CHECK No. _______________
                                <br>
                                &nbsp;&nbsp;&nbsp; IN THE AMOUNT OF ___________________________ (P _____________________) ENCLOSED
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</body>
</html>
