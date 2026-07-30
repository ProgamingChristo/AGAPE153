<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_visits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->char('visitor_hash', 64);
            $table->char('session_hash', 64)->nullable();
            $table->string('path', 191);
            $table->string('route_name', 120)->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('source_host')->nullable();
            $table->string('device', 20)->default('desktop');
            $table->string('browser', 40)->nullable();
            $table->timestamps();

            $table->index('visitor_hash');
            $table->index('route_name');
            $table->index('created_at');
        });

        if (Schema::hasTable('product_views') && Schema::hasTable('products')) {
            DB::table('product_views as pv')
                ->leftJoin('products as p', 'p.id', '=', 'pv.product_id')
                ->select([
                    'pv.id as visit_id',
                    'pv.user_id',
                    'pv.ip_address',
                    'pv.device',
                    'pv.browser',
                    'pv.created_at',
                    'pv.updated_at',
                    'p.slug as product_slug',
                ])
                ->chunkById(500, function ($rows): void {
                    $visits = $rows->map(function ($row): array {
                        $browser = Str::lower((string) $row->browser);
                        $browserName = match (true) {
                            Str::contains($browser, ['edg/', 'edge/']) => 'Edge',
                            Str::contains($browser, ['opr/', 'opera']) => 'Opera',
                            Str::contains($browser, ['chrome/', 'crios/']) => 'Chrome',
                            Str::contains($browser, ['firefox/', 'fxios/']) => 'Firefox',
                            Str::contains($browser, ['safari/']) => 'Safari',
                            default => 'Other',
                        };
                        $visitorSeed = ($row->ip_address ?: 'legacy-'.$row->visit_id).'|'.($row->browser ?: 'unknown');

                        return [
                            'user_id' => $row->user_id,
                            'visitor_hash' => hash_hmac('sha256', $visitorSeed, (string) config('app.key', 'agape153')),
                            'session_hash' => null,
                            'path' => $row->product_slug ? '/products/'.$row->product_slug : '/products',
                            'route_name' => 'products.show',
                            'locale' => config('app.locale', 'id'),
                            'source_host' => null,
                            'device' => in_array($row->device, ['mobile', 'tablet', 'desktop'], true) ? $row->device : 'desktop',
                            'browser' => $browserName,
                            'created_at' => $row->created_at ?: now(),
                            'updated_at' => $row->updated_at ?: $row->created_at ?: now(),
                        ];
                    })->all();

                    if ($visits !== []) {
                        DB::table('website_visits')->insert($visits);
                    }
                }, 'pv.id', 'visit_id');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visits');
    }
};
