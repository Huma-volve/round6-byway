<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformSettingFactory> */
    use HasFactory;

      protected $fillable = [
        'commission_percentage',
        'withdrawal_limit',
        'notifications_enabled',
    ];
     protected $casts = [
        'commission_percentage' => 'decimal:2',
        'withdrawal_limit' => 'decimal:2',
        'notifications_enabled' => 'boolean',
    ];
}
