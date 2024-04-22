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
        Schema::create('check_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')
                ->references('id')
                ->on('phonenumber_checks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('group_id')
                ->references('id')
                ->on('user_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_groups');
    }
};
