<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\System\Bakery\Migration;

/**
 * Logics to answers table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class AnswerLogicTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\LogicsTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\AnswersTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('answer_logic')) {
            $this->schema->create('answer_logic', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('logic_id')->unsigned();
                $table->integer('answer_id')->unsigned();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('logic_id')->references('id')->on('logics');
                $table->foreign('answer_id')->references('id')->on('answers');
                $table->foreign('creator_id')->references('id')->on('users');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('answer_logic');
    }
}
