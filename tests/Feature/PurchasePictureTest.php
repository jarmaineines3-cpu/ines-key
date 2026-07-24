<?php

use App\Models\Employee;
use App\Models\Purchase;
use App\Models\PurchasePicture;
use App\Models\School;
use Illuminate\Support\Carbon;

it('stores purchase picture captions and paths', function () {
    $school = School::create([
        'school_id' => 'S-005',
        'school_name' => 'Sample School Five',
        'school_address' => 'Sample Address',
    ]);

    $employee = Employee::create([
        'employee_no' => 'E-004',
        'fname' => 'Rico',
        'mname' => 'P.',
        'lname' => 'Dizon',
        'sex' => 'M',
        'birthday' => '1991-01-01',
        'address' => 'Test Address',
    ]);

    $purchase = Purchase::create([
        'school' => $school->id,
        'purpose' => 'Picture test purchase',
        'fund_cluster' => '1101101',
        'rc_code' => '08-08-006',
        'pr_no' => '2026-07-005',
        'pr_date' => Carbon::create(2026, 7, 1),
        'po_date' => Carbon::create(2026, 7, 2),
        'requested_by' => $employee->id,
        'approved_by' => $employee->id,
    ]);

    $picture = PurchasePicture::create([
        'purchase_id' => $purchase->id,
        'image_path' => 'purchases/1/pictures/image.png',
        'caption' => 'Sample caption',
    ]);

    expect($picture->purchase->id)->toBe($purchase->id)
        ->and($picture->caption)->toBe('Sample caption')
        ->and($picture->image_path)->toBe('purchases/1/pictures/image.png');
});
