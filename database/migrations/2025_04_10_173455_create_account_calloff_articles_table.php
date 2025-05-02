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
        Schema::create('account_calloff_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('erp_id')->unique();
            $table->string('year')->nullable();
            $table->string('name')->nullable();
            $table->string('external_id')->nullable();
            $table->string('external_name')->nullable();
            $table->integer('in_stock')->default(0);

            $table->integer('min_stock')->nullable();
            $table->timestamp('online')->nullable();
            $table->timestamp('offline')->nullable();
            $table->string('campagne_manager')->nullable();
            $table->string('external_project_manager')->nullable();

            $table->string('environment');
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
        Schema::dropIfExists('account_calloff_articles');
    }
};
