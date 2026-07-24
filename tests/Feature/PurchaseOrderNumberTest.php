<?php

use App\Models\Purchase;
use App\Models\School;
use Illuminate\Support\Carbon;

it('generates a school-scoped purchase order number in the format year-month-increment', function () {
    $school = School::create([
        'school_id' => 'S-001',
        'school_name' => 'Sample School',
        'school_address' => 'Sample Address',
    ]);

    $firstPurchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'Test Purpose',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-003',
        'pr_no' => '2026-01-001',
        'pr_date' => Carbon::create(2026, 1, 10),
        'po_date' => Carbon::create(2026, 1, 15),
        'requested_by' => '1',
        'approved_by' => '1',
    ]);

    $secondPurchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'Second Test Purpose',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-003',
        'pr_no' => '2026-01-002',
        'pr_date' => Carbon::create(2026, 1, 16),
        'po_date' => Carbon::create(2026, 1, 20),
        'requested_by' => '1',
        'approved_by' => '1',
    ]);

    expect($firstPurchase->fresh()->purchaseOrder->po_no)->toBe('2026-01-001')
        ->and($secondPurchase->fresh()->purchaseOrder->po_no)->toBe('2026-01-002');
});
