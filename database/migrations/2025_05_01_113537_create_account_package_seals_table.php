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
        Schema::create('account_package_seals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_package_id')->constrained();
            $table->foreignId('erp_id')->nullable();
            $table->string('name');
            $table->string('external_id')->nullable();
            $table->string('external_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_package_seals');

    }
};
