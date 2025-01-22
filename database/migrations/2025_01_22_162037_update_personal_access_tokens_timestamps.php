<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;



class UpdatePersonalAccessTokensTimestamps extends Migration
{
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Define valores padrão para as colunas created_at e updated_at
            $table->timestamp('created_at')->default(DB::raw('GETDATE()'))->change();
            $table->timestamp('updated_at')->default(DB::raw('GETDATE()'))->change();
        });
    }

    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Remove os valores padrão
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
        });
    }
}
