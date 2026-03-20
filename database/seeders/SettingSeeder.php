<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'PTSmart'],
            ['key' => 'support_email', 'value' => 'support@ptsmart.vn'],
            ['key' => 'hotline', 'value' => '1900 6789'],
            ['key' => 'working_hours', 'value' => '08:00 - 21:00 (Hàng ngày)'],
            ['key' => 'default_shipping_fee', 'value' => '30000'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
