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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('description');
            $table->string('phonenumber')->unique();
            $table->boolean('online')->default(false);
            $table->string('token')->nullable();
            $table->boolean('active')->default(true);
            $table->text('qrcode_path')->nullable();
            $table->dateTime("available_at")->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
