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
            ['key' => 'site_description', 'value' => 'Chuyên cung cấp các thiết bị điện tử chính hãng với mức giá cạnh tranh nhất thị trường. Hệ thống 64 cửa hàng toàn quốc.'],
            ['key' => 'support_email', 'value' => 'support@ptsmart.vn'],
            ['key' => 'hotline', 'value' => '1900 8888'],
            ['key' => 'working_hours', 'value' => '08:00 - 21:00 (Hàng ngày)'],
            ['key' => 'default_shipping_fee', 'value' => '30000'],
            ['key' => 'site_address', 'value' => '123 Nguyễn Huệ, Quận 1, TP.HCM'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/ptsmart'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/ptsmart'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@ptsmart'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
