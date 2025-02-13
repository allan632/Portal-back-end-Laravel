<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profile_user', function (Blueprint $table) {
            $table->id(); // ID padrão para a tabela de junção
            $table->unsignedBigInteger('user_fk'); // Chave estrangeira para a tabela users
            $table->unsignedBigInteger('profile_fk'); // Chave estrangeira para a tabela profiles
            $table->timestamps();
            
            // Definindo as chaves estrangeiras e seus relacionamentos
            $table->foreign('user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profile_fk')->references('id')->on('profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_user');
    }
};
