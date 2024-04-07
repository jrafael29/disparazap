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
        Schema::create('user_contact_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_group_id')
                ->references('id')
                ->on('user_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_contact_id')
                ->references('id')
                ->on('user_contacts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contact_groups');
    }
};
