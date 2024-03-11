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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')
                ->references('id')
                ->on('message_flows')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('type_id')
                ->references('id')
                ->on('message_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('text'); // caption or content
            // $table->string('message_type'); // text || image || video || audio etc
            $table->string('filepath')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->unsignedInteger('delay')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
