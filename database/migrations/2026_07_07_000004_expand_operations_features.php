<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('payment_gateway')->nullable()->after('payment_method')->index();
            $table->string('payment_reference')->nullable()->after('payment_gateway')->index();
            $table->string('midtrans_order_id')->nullable()->after('payment_reference')->index();
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id')->index();
            $table->text('midtrans_snap_token')->nullable()->after('midtrans_transaction_id');
            $table->text('midtrans_redirect_url')->nullable()->after('midtrans_snap_token');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->json('payment_payload')->nullable()->after('paid_at');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('shipping_cost');
            $table->string('shipping_provider')->nullable()->after('tracking_code');
            $table->string('shipping_status')->nullable()->after('shipping_provider');
            $table->text('tracking_url')->nullable()->after('shipping_status');
            $table->string('approval_status')->default('standard')->after('accepted_by')->index();
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->string('quotation_status')->default('draft')->after('approved_by')->index();
            $table->text('quotation_notes')->nullable()->after('quotation_status');
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('delta');
            $table->string('type')->default('adjustment')->index();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('channel')->index();
            $table->string('event')->index();
            $table->string('recipient')->nullable();
            $table->string('subject')->nullable();
            $table->longText('message')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('content_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('page')->default('home')->index();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('body')->nullable();
            $table->string('image_url')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cart_events', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event')->index();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('source')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('content_translations', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('group')->default('site')->index();
            $table->string('key');
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->unique(['locale', 'group', 'key']);
        });

        if (Schema::hasColumn('users', 'role') && DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'member'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role') && DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','member') NOT NULL DEFAULT 'member'");
        }

        Schema::dropIfExists('content_translations');
        Schema::dropIfExists('cart_events');
        Schema::dropIfExists('content_sections');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('stock_movements');

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'payment_gateway',
                'payment_reference',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_snap_token',
                'midtrans_redirect_url',
                'paid_at',
                'payment_payload',
                'discount_amount',
                'shipping_provider',
                'shipping_status',
                'tracking_url',
                'approval_status',
                'approved_at',
                'quotation_status',
                'quotation_notes',
            ]);
        });
    }
};
