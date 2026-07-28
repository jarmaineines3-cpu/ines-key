<?php

use Illuminate\Support\Facades\Schema;

test('positions table has teaching column', function () {
    expect(Schema::hasColumn('positions', 'teaching'))->toBeTrue();
});
