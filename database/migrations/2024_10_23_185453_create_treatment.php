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
        Schema::create('SacTratativa', function (Blueprint $table) {
            $table->int("CdTratativa");
            $table->int("CdEvento");
            $table->string("CdRemetente");
            $table->int("NrFun");
            $table->timestamp('DtTratativa')->useCurrent();
            $table->int("InStatus");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SacTratativa');
    }
};
