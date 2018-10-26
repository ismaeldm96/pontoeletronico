<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TRIGGER `tr_horarios_ai` AFTER INSERT ON `horarios` FOR EACH ROW 
                INSERT INTO horarios_historico (horarios_id, atualizado_em, user_id, acao) 
                VALUES (NEW.id, NEW.criado_em, NEW.user_id, 'Criacao do registro');
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER 'tr_horarios_ai'");
    }
}
