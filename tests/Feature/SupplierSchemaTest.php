<?php

use Illuminate\Support\Facades\Schema;

it('creates the supplier_contact_number column on the suppliers table', function () {
    expect(Schema::hasColumn('suppliers', 'supplier_contact_number'))->toBeTrue();
});
