<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchase extends ViewRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewPr')
                ->label('PR/RFQ/APR')
                ->url(fn (): string => route('purchases.pdf', ['purchase' => $this->getRecord()]))
                ->icon('heroicon-o-document-text')
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->getRecord()?->purchaseItems()->exists() ?? false),
            Action::make('viewPO')
                ->label('PO')
                ->url(fn (): string => route('purchases.po.pdf', ['purchase' => $this->getRecord()?->getKey()]))
                ->icon('heroicon-o-document-text')
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->getRecord()?->purchaseOrder()->exists() ?? false),
            Action::make('viewAOQ')
                ->label('AOQ')
                ->url(fn (): string => route('purchases.supplier-quotes.pdf', ['purchase' => $this->getRecord()]))
                ->icon('heroicon-o-printer')
                ->openUrlInNewTab()
                ->visible(fn (): bool => ($this->getRecord()?->supplierQuotes()->distinct('supplier_id')->count('supplier_id') ?? 0) >= 3),
            Action::make('viewBACReso')
                ->label('BAC Reso')
                ->url(fn (): string => route('purchases.supplier-quotes.bac-reso', ['purchase' => $this->getRecord()]))
                ->icon('heroicon-o-printer')
                ->openUrlInNewTab()
                ->visible(fn (): bool => ($this->getRecord()?->supplierQuotes()->distinct('supplier_id')->count('supplier_id') ?? 0) >= 3),
            Action::make('viewInspection')
                ->label('Inspection')
                ->url(fn (): string => route('purchases.inspection.pdf', ['purchase' => $this->getRecord()?->getKey()]))
                ->icon('heroicon-o-document-text')
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->getRecord()?->inspection()->exists() ?? false),
            Action::make('viewIssuances')
                ->label('Issuances')
                ->url(fn (): string => route('purchases.issuances.pdf', ['purchase' => $this->getRecord()?->getKey()]))
                ->icon('heroicon-o-document-text')
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->getRecord()?->issuances()->exists() ?? false),
            Action::make('viewPictures')
                ->label('Pictures')
                ->url(fn (): string => route('purchases.pictures.pdf', ['purchase' => $this->getRecord()?->getKey()]))
                ->icon('heroicon-o-photo')
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->getRecord()?->pictures()->exists() ?? false),
            EditAction::make(),
        ];
    }
}
