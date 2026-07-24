<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePicture extends Model
{
    protected $fillable = [
        'purchase_id',
        'image_path',
        'caption',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $picture): void {
            if (blank($picture->image_path)) {
                return;
            }

            $path = storage_path('app/public/' . ltrim($picture->image_path, '/'));

            if (file_exists($path)) {
                @unlink($path);
            }
        });
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
