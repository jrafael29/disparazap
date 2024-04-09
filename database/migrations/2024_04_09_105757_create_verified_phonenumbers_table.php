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
        Schema::create('verified_phonenumbers', function (Blueprint $table) {
            $table->id();
            $table->string("phonenumber")->unique();
            $table->boolean('verified')->default(false);
            $table->boolean('isOnWhatsapp')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verified_phonenumbers');
    }
};
