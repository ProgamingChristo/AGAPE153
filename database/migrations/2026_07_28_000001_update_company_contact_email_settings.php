<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->upsertSetting('company_email', 'info@agape153.com');
        $this->upsertSetting('company_secondary_email', 'info.agape153@gmail.com');
    }

    public function down(): void
    {
        $this->upsertSetting('company_email', 'info.agape153@gmail.com');

        DB::table('website_settings')
            ->where('key', 'company_secondary_email')
            ->delete();
    }

    private function upsertSetting(string $key, string $value): void
    {
        $timestamp = now();

        $updated = DB::table('website_settings')
            ->where('key', $key)
            ->update([
                'value' => $value,
                'group' => 'general',
                'updated_at' => $timestamp,
            ]);

        if ($updated === 0) {
            DB::table('website_settings')->insert([
                'key' => $key,
                'value' => $value,
                'group' => 'general',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
};
