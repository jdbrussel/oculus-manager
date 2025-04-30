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
        Schema::create('account_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('address_type')->nullable();

            $table->string('external_id');
            $table->string('alternative_external_id')->nullable();

            $table->string('name');
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('house_number_additional')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('country')->nullable();

            $table->string('rayon')->nullable();
            $table->string('formula')->nullable();

            $table->string('dc_day_id')->nullable()->index();
            $table->string('dc_week_id')->nullable()->index();
            $table->string('dc_theme_id')->nullable()->index();

            $table->string('erp_id')->nullable();
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
        Schema::dropIfExists('account_addresses');
    }
};
