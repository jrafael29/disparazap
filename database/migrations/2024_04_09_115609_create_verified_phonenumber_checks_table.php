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
        Schema::create('verified_phonenumber_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verify_id')
                ->references('id')
                ->on('verified_phonenumbers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('check_id')
                ->references('id')
                ->on('phonenumber_checks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('done')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verified_phonenumber_checks');
    }
};
