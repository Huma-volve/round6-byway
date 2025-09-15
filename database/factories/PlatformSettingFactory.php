<?php

namespace Database\Factories;

use App\Models\PlatformSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlatformSetting>
 */
class PlatformSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

        protected $model = PlatformSetting::class;

    public function definition(): array
    {
        return [
            //
            'commission_percentage' => 15.00,
        'withdrawal_limit' => 50.00,
        'notifications_enabled' => true,
        ];
    }
}
