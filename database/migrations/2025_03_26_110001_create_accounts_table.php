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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('erp_id')->nullable();
            $table->enum('environment', ['production', 'development'])->nullable();
            $table->string('erp_status');

            $table->jsonb('config')->nullable();

            $table->timestamp('synched_at')->nullable();
            $table->foreignId('synched_at_user')->nullable()->index();
            $table->foreignId('created_by_user')->nullable()->index();
            $table->foreignId('updated_by_user')->nullable()->index();
            $table->timestamps();
            $table->boolean('pre_delete')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('deleted_at_user')->nullable()->index();
        });

        Schema::create('account_user', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('account_module', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('account_user');
        Schema::dropIfExists('account_module');
    }
};
