<?php

use App\Models\Employee;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseIssuance;
use App\Models\PurchaseItem;
use App\Models\School;
use Illuminate\Support\Carbon;

it('generates a school-scoped inventory purchase serial number in year-month-series format', function () {
    $school = School::create([
        'school_id' => 'S-002',
        'school_name' => 'Sample School Two',
        'school_address' => 'Sample Address',
    ]);

    $employee = Employee::create([
        'employee_no' => 'E-001',
        'fname' => 'Juan',
        'mname' => 'Dela',
        'lname' => 'Cruz',
        'sex' => 'M',
        'birthday' => '1990-01-01',
        'address' => 'Test Address',
    ]);

    $inventoryItem = Item::create([
        'school_id' => $school->id,
        'stock_no' => 1,
        'name' => 'Desktop Computer',
        'unit' => 'unit',
        'item_unit_price' => 25000,
        'inventory_item' => true,
    ]);

    $firstPurchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'First inventory purchase',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-003',
        'pr_no' => '2026-07-001',
        'pr_date' => Carbon::create(2026, 7, 1),
        'po_date' => Carbon::create(2026, 7, 2),
        'requested_by' => $employee->id,
        'approved_by' => $employee->id,
    ]);

    $firstPurchaseItem = PurchaseItem::create([
        'purchase_id' => $firstPurchase->id,
        'item_id' => $inventoryItem->id,
        'item_quantity' => 2,
    ]);

    $firstIssuance = PurchaseIssuance::create([
        'purchase_id' => $firstPurchase->id,
        'purchase_item_id' => $firstPurchaseItem->id,
        'employee_id' => $employee->id,
        'quantity' => 1,
        'issued_at' => Carbon::create(2026, 7, 10),
    ]);

    expect($firstIssuance->fresh()->rspi_serial_no)->toBe('2026-07-001');

    $secondPurchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'Second inventory purchase',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-003',
        'pr_no' => '2026-07-002',
        'pr_date' => Carbon::create(2026, 7, 5),
        'po_date' => Carbon::create(2026, 7, 6),
        'requested_by' => $employee->id,
        'approved_by' => $employee->id,
    ]);

    $secondPurchaseItem = PurchaseItem::create([
        'purchase_id' => $secondPurchase->id,
        'item_id' => $inventoryItem->id,
        'item_quantity' => 1,
    ]);

    $secondIssuance = PurchaseIssuance::create([
        'purchase_id' => $secondPurchase->id,
        'purchase_item_id' => $secondPurchaseItem->id,
        'employee_id' => $employee->id,
        'quantity' => 1,
        'issued_at' => Carbon::create(2026, 7, 11),
    ]);

    expect($secondIssuance->fresh()->rspi_serial_no)->toBe('2026-07-002');
});

it('generates an rsmi serial number when an issuance has no inventory number', function () {
    $school = School::create([
        'school_id' => 'S-003',
        'school_name' => 'Sample School Three',
        'school_address' => 'Sample Address',
    ]);

    $employee = Employee::create([
        'employee_no' => 'E-002',
        'fname' => 'Maria',
        'mname' => 'L.',
        'lname' => 'Dela Cruz',
        'sex' => 'F',
        'birthday' => '1992-02-02',
        'address' => 'Test Address',
    ]);

    $purchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'Non-inventory purchase',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-004',
        'pr_no' => '2026-07-003',
        'pr_date' => Carbon::create(2026, 7, 1),
        'po_date' => Carbon::create(2026, 7, 2),
        'requested_by' => $employee->id,
        'approved_by' => $employee->id,
    ]);

    $purchaseItem = PurchaseItem::create([
        'purchase_id' => $purchase->id,
        'item_id' => null,
        'item_name' => 'Ballpen',
        'item_unit' => 'box',
        'item_quantity' => 5,
    ]);

    $issuance = PurchaseIssuance::create([
        'purchase_id' => $purchase->id,
        'purchase_item_id' => $purchaseItem->id,
        'employee_id' => $employee->id,
        'quantity' => 2,
        'issued_at' => Carbon::create(2026, 7, 12),
    ]);

    expect($issuance->fresh()->rsmi_serial_no)->toBe('2026-07-001');
});

it('persists inventory details for inventory-item issuances', function () {
    $school = School::create([
        'school_id' => 'S-004',
        'school_name' => 'Sample School Four',
        'school_address' => 'Sample Address',
    ]);

    $employee = Employee::create([
        'employee_no' => 'E-003',
        'fname' => 'Ana',
        'mname' => 'M.',
        'lname' => 'Rivera',
        'sex' => 'F',
        'birthday' => '1993-03-03',
        'address' => 'Test Address',
    ]);

    $inventoryItem = Item::create([
        'school_id' => $school->id,
        'stock_no' => 2,
        'name' => 'Laptop',
        'unit' => 'unit',
        'item_unit_price' => 40000,
        'inventory_item' => true,
    ]);

    $purchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'Inventory details purchase',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-005',
        'pr_no' => '2026-07-004',
        'pr_date' => Carbon::create(2026, 7, 1),
        'po_date' => Carbon::create(2026, 7, 2),
        'requested_by' => $employee->id,
        'approved_by' => $employee->id,
    ]);

    $purchaseItem = PurchaseItem::create([
        'purchase_id' => $purchase->id,
        'item_id' => $inventoryItem->id,
        'item_quantity' => 1,
    ]);

    $issuance = PurchaseIssuance::create([
        'purchase_id' => $purchase->id,
        'purchase_item_id' => $purchaseItem->id,
        'employee_id' => $employee->id,
        'quantity' => 1,
        'issued_at' => Carbon::create(2026, 7, 13),
        'inventory_details' => 'Serial number ABC-123, room 101',
    ]);

    expect($issuance->fresh()->inventory_details)->toBe('Serial number ABC-123, room 101');
});
