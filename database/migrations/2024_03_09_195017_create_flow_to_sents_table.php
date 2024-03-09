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
        Schema::create('flow_to_sents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('flow_id')
                ->references('id')
                ->on('message_flows')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('instance_id')
                ->references('id')
                ->on('instances')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string("to");
            $table->boolean('active')->default(true);
            $table->boolean("sent")->default(false);
            $table->boolean("busy")->default(false);
            $table->dateTime("to_sent_at")->default(now());
            $table->integer('delay_in_seconds')->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flow_to_sents');
    }
};
