<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsPersonalAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Alterando as colunas para VARCHAR(255)
            $table->string('updated_at', 255)->nullable()->change();
            $table->string('created_at', 255)->nullable()->change();
            $table->string('expires_at', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Revertendo as colunas para o tipo original (datetime)
            $table->dateTime('updated_at')->nullable()->change();
            $table->dateTime('created_at')->nullable()->change();
            $table->dateTime('expires_at')->nullable()->change();
        });
    }
}

