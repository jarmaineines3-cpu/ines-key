<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

it('can refresh the db_config migration without recreating an existing table', function () {
    $result = Artisan::call('migrate:refresh', [
        '--path' => 'database/migrations/2026_04_02_085341_create_db_config_table.php',
    ]);

    expect($result)->toBe(0);
    expect(Schema::hasTable('db_config'))->toBeTrue();
});
