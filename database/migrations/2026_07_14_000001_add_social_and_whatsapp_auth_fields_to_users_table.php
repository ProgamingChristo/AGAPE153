<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('auth_provider')->default('email')->after('company_name')->index();
            $table->string('google_id')->nullable()->after('auth_provider')->unique();
            $table->string('avatar_url')->nullable()->after('google_id');
            $table->timestamp('phone_verified_at')->nullable()->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'auth_provider',
                'google_id',
                'avatar_url',
                'phone_verified_at',
            ]);
        });
    }
};
