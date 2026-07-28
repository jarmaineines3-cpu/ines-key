<?php

use App\Http\Controllers\PurchasePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/purchases/{purchase}/pdf', [PurchasePdfController::class, 'export'])
    ->name('purchases.pdf');

Route::get('/purchases/{purchase}/apr/pdf', [PurchasePdfController::class, 'viewAPR'])
    ->name('purchases.apr.pdf');

Route::get('/purchases/{purchase}/po/pdf', [PurchasePdfController::class, 'viewPO'])
    ->name('purchases.po.pdf');

Route::get('/purchases/{purchase}/supplier-quotes/pdf', [PurchasePdfController::class, 'viewAOQ'])
    ->name('purchases.supplier-quotes.pdf');

Route::get('/purchases/{purchase}/supplier-quotes/bac-reso', [PurchasePdfController::class, 'viewBACReso'])
    ->name('purchases.supplier-quotes.bac-reso');

Route::get('/purchases/{purchase}/supplier-quotes/mailing-list', [PurchasePdfController::class, 'exportMailingList'])
    ->name('purchases.supplier-quotes.mailing-list');

Route::get('/purchases/{purchase}/issuances/pdf', [PurchasePdfController::class, 'viewIssuances'])
    ->name('purchases.issuances.pdf');

Route::get('/purchases/{purchase}/inspection/pdf', [PurchasePdfController::class, 'viewInspection'])
    ->name('purchases.inspection.pdf');

Route::get('/purchases/{purchase}/pictures/pdf', [PurchasePdfController::class, 'viewPictures'])
    ->name('purchases.pictures.pdf');
