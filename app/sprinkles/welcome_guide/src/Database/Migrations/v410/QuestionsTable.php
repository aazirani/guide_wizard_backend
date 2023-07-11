<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Questions table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class QuestionsTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\TextsTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\TasksTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('questions')) {
            $this->schema->create('questions', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('title')->unsigned()->nullable();
                $table->integer('sub_title')->unsigned()->nullable();
                $table->string('type', 10);
                $table->integer('axis_count')->unsigned();
                $table->boolean('is_multiple_choice')->default(false);
                $table->integer('info_url')->unsigned()->nullable();
                $table->integer('info_description')->unsigned()->nullable();
                $table->integer('task_id')->unsigned();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
                $table->foreign('title')->references('id')->on('texts');
                $table->foreign('sub_title')->references('id')->on('texts');
                $table->foreign('info_url')->references('id')->on('texts');
                $table->foreign('info_description')->references('id')->on('texts');
                $table->foreign('task_id')->references('id')->on('tasks');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('questions');
    }
}
