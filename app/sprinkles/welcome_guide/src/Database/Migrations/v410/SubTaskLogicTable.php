<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Logics to Sub tasks table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class SubTaskLogicTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\LogicsTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\SubTasksTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('sub_task_logic')) {
            $this->schema->create('sub_task_logic', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('logic_id')->unsigned()->nullable();
                $table->integer('sub_task_id')->unsigned()->nullable();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
                $table->foreign('logic_id')->references('id')->on('logics');
                $table->foreign('sub_task_id')->references('id')->on('sub_tasks');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('sub_task_logic');
    }
}
