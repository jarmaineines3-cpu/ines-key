<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchasePdfController
{
    public function export(Purchase $purchase): Response
    {
        $purchase->load(['purchaseItems.item', 'requester', 'approver']);

        $pdf = Pdf::loadView('pdfs.PR', [
            'purchase' => $purchase,
        ]);

        return $pdf->stream(sprintf('purchase-%s.pdf', $purchase->pr_no));
    }

    public function viewAPR(Purchase $purchase): Response
    {
        $purchase->load(['purchaseItems.item', 'requester', 'approver']);

        $pdf = Pdf::loadView('pdfs.APR', [
            'purchase' => $purchase,
        ]);

        return $pdf->stream(sprintf('APR-%s.pdf', $purchase->pr_no."-".$purchase->purpose));
    }

    public function viewPO(Purchase $purchase): Response
    {
        $purchase->load([
            'purchaseItems.item',
            'requester',
            'approver',
            'seniorBookkeeper',
            'schoolRelation',
            'purchaseOrder',
            'supplierQuotes.supplier',
            'supplierQuotes.purchaseItem',
        ]);

        // Allow using the purchaseOrder's po_date when the purchase record itself
        // doesn't have `po_date` populated (some flows store it on the relation).
        $poDate = $purchase->po_date ?: ($purchase->purchaseOrder?->po_date ?? null);

        if (! filled($poDate)) {
            abort(403, 'A purchase order date is required to generate this report.');
        }

        $supplierQuoteGroups = $purchase->supplierQuotes
            ->groupBy('supplier_id')
            ->map(function ($quotes) {
                return [
                    'supplier' => $quotes->first()?->supplier,
                    'quotes' => $quotes,
                    'total' => $quotes->sum(fn ($quote) => ($quote->item_unit_price ?? 0) * ($quote->purchaseItem?->item_quantity ?? 0)),
                ];
            })
            ->sortBy('total')
            ->values();

        if ($supplierQuoteGroups->isEmpty()) {
            abort(403, 'At least one supplier quote is required to generate this report.');
        }

        $pdf = Pdf::loadView('pdfs.PO', [
            'purchase' => $purchase,
            'lowestSupplier' => $supplierQuoteGroups->first(),
        ]);

        return $pdf->stream(sprintf('purchase-%s-PO.pdf', $purchase->pr_no));
    }

    public function viewAOQ(Purchase $purchase): Response
    {
        $purchase->load(['supplierQuotes.supplier', 'supplierQuotes.purchaseItem', 'schoolRelation']);

        $supplierCount = $purchase->supplierQuotes->pluck('supplier_id')->unique()->count();

        if ($supplierCount < 3) {
            abort(403, 'At least 3 supplier quotes are required to generate this report.');
        }

        $pdf = Pdf::loadView('pdfs.AOQ', [
            'purchase' => $purchase,
        ]);

        return $pdf->stream(sprintf('purchase-%s-AOQ.pdf', $purchase->pr_no));
    }

    public function viewBACReso(Purchase $purchase): Response
    {
        $purchase->load(['supplierQuotes.supplier', 'supplierQuotes.purchaseItem', 'schoolRelation']);

        $supplierCount = $purchase->supplierQuotes->pluck('supplier_id')->unique()->count();
        if ($supplierCount < 3) {
            abort(403, 'At least 3 supplier quotes are required.');
        }

        // Pass the data to the view renderer
        $viewData = [
            'purchase' => $purchase,
            'totalPages' => 0 // Placeholder for first pass
        ];

        // 1. First Pass: Get page count
        $pdf = Pdf::loadView('pdfs.BACReso', $viewData);
        $pdf->render();
        $totalPages = $pdf->getCanvas()->get_page_count();

        // 2. Second Pass: Pass the REAL count
        $viewData['totalPages'] = $totalPages;
        
        $pdf = Pdf::loadView('pdfs.BACReso', $viewData);

        return $pdf->stream(sprintf('purchase-%s-BAC-Reso.pdf', $purchase->pr_no));
    }

    public function viewInspection(Purchase $purchase): Response
    {
        $purchase->load([
            'purchaseItems.item',
            'inspection.inspector',
            'requester',
            'approver',
            'schoolRelation',
            'purchaseOrder',
            'supplierQuotes.supplier',
            'supplierQuotes.purchaseItem',
        ]);

        if (! $purchase->inspection) {
            abort(403, 'Inspection data is required to generate this report.');
        }

        $supplierQuoteGroups = $purchase->supplierQuotes
            ->groupBy('supplier_id')
            ->map(function ($quotes) {
                return [
                    'supplier' => $quotes->first()?->supplier,
                    'quotes' => $quotes,
                    'total' => $quotes->sum(fn ($quote) => ($quote->item_unit_price ?? 0) * ($quote->purchaseItem?->item_quantity ?? 0)),
                ];
            })
            ->sortBy('total')
            ->values();

        if ($supplierQuoteGroups->isEmpty()) {
            abort(403, 'At least one supplier quote is required to generate this report.');
        }

        $pdf = Pdf::loadView('pdfs.inspection', [
            'purchase' => $purchase,
            'lowestSupplier' => $supplierQuoteGroups->first(),
        ]);

        return $pdf->stream(sprintf('purchase-%s-inspection.pdf', $purchase->pr_no));
    }

    public function viewIssuances(Purchase $purchase): Response
    {
        $purchase->load([
            'issuances.purchaseItem',
            'issuances.employee',
            'purchaseItems.item',
            'requester',
            'approver',
            'schoolRelation',
            'purchaseOrder',
            'supplierQuotes.supplier',
            'supplierQuotes.purchaseItem',
        ]);

        if (! $purchase->issuances()->exists()) {
            abort(403, 'Issuance records are required to generate this report.');
        }

        $supplierQuoteGroups = $purchase->supplierQuotes
            ->groupBy('supplier_id')
            ->map(function ($quotes) {
                return [
                    'supplier' => $quotes->first()?->supplier,
                    'quotes' => $quotes,
                    'total' => $quotes->sum(fn ($quote) => ($quote->item_unit_price ?? 0) * ($quote->purchaseItem?->item_quantity ?? 0)),
                ];
            })
            ->sortBy('total')
            ->values();

        $pdf = Pdf::loadView('pdfs.issuances', [
            'purchase' => $purchase,
            'lowestSupplier' => $supplierQuoteGroups->first(),
        ]);

        return $pdf->stream(sprintf('purchase-%s-issuances.pdf', $purchase->pr_no));
    }

    public function viewPictures(Purchase $purchase): Response
    {
        $purchase->load(['pictures']);

        if (! $purchase->pictures()->exists()) {
            abort(403, 'Picture records are required to generate this report.');
        }

        $pdf = Pdf::loadView('pdfs.purchase-pictures', [
            'purchase' => $purchase,
        ]);

        return $pdf->stream(sprintf('purchase-%s-pictures.pdf', $purchase->pr_no));
    }

    public function exportMailingList(Purchase $purchase)
    {
        $purchase->load(['supplierQuotes.supplier']);

        // Get unique suppliers for this purchase
        $suppliers = $purchase->supplierQuotes->map(fn($q) => $q->supplier)->unique('id')->values();

        $html = view('exports.mailing_list', [
            'purchase' => $purchase,
            'suppliers' => $suppliers,
        ])->render();

        $fileName = sprintf('mailing-list-%s.doc', $purchase->pr_no ?? $purchase->id);

        return response($html, 200, [
            'Content-Type' => 'application/msword; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
