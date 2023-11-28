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
        Schema::create('date_range_assigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->date('day_1')->nullable();
            $table->date('day_2')->nullable();
            $table->date('day_3')->nullable();
            $table->date('day_4')->nullable();
            $table->date('day_5')->nullable();
            $table->date('day_6')->nullable();
            $table->date('day_7')->nullable();
            $table->date('day_8')->nullable();
            $table->date('day_9')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('date_range_assigns');
    }
};
