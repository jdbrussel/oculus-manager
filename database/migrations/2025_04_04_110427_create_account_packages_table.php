<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')->constrained()->cascadeOnDelete();

            $table->string('erp_id')->unique();
            $table->enum('environment', ['production', 'development'])->nullable();
            $table->integer('year')->nullable();

            $table->string('external_id')->nullable();
            $table->string('external_name')->nullable();

            $table->string('edition')->nullable();
            $table->string('type')->nullable();

            $table->jsonb('config')->nullable();

            $table->timestamp('order_datetime_from')->nullable();
            $table->timestamp('order_datetime_until')->nullable();

            $table->timestamp('order_in_production_datetime_from')->nullable();
            $table->timestamp('order_in_production_datetime_until')->nullable();

            $table->timestamp('order_production_ready_datetime')->nullable();
            $table->timestamp('scheduled_fulfilment_datetime')->nullable();
            $table->timestamp('scheduled_delivery_datetime')->nullable();

            $table->timestamp('run_time_datetime_from')->nullable();
            $table->timestamp('run_time_datetime_until')->nullable();

            $table->string('handling_location_id')->nullable();

            $table->timestamp('synched_at')->nullable();
            $table->foreignId('synched_at_user')->nullable()->index();

            $table->foreignId('created_by_user')->nullable()->index();
            $table->foreignId('updated_by_user')->nullable()->index();

            $table->timestamps();

            $table->boolean('pre_delete')->nullable();

            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('deleted_at_user')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_packages');
    }
};
