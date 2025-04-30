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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('erp_id')->nullable();
            $table->foreignId('created_by_user')->nullable()->index();
            $table->foreignId('updated_by_user')->nullable()->index();
            $table->timestamp('synched_at')->nullable();
            $table->foreignId('synched_at_user')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->boolean('pre_delete')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('deleted_at_user')->nullable()->index();
        });

        Schema::create('client_user', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('client_user');
    }
};
