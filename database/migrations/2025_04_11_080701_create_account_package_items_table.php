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
        Schema::create('account_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_package_id')->constrained()->cascadeOnDelete();
            $table->string('erp_id')->unique();
            $table->string('year')->nullable();
            $table->string('name');
            $table->string('external_id')->nullable();
            $table->string('external_name')->nullable();

            $table->integer('type')->default(1);
            $table->integer('num_versions')->default(1);
            $table->integer('num_per_version')->nullable();

            $table->integer('quantity')->default(0)->nullable();
            $table->integer('quantity_reserved')->default(0)->nullable();
            $table->integer('quantity_stock')->default(0)->nullable();
            $table->jsonb('allocation')->nullable();
            $table->enum('environment', ['production', 'development'])->nullable();
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
        Schema::dropIfExists('account_package_items');
    }
};
