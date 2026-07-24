<?php

use Illuminate\Support\Facades\Schema;

it('creates the school logo and contact metadata columns on the schools table', function () {
    expect(Schema::hasColumn('schools', 'school_logo'))->toBeTrue();
    expect(Schema::hasColumn('schools', 'school_email'))->toBeTrue();
    expect(Schema::hasColumn('schools', 'school_social'))->toBeTrue();
});
