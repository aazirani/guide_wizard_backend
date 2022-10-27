<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Answers table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class AnswersTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\QuestionsTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\TextsTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('answers')) {
            $this->schema->create('answers', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('question_id')->unsigned();
                $table->integer('title')->unsigned();
                $table->integer('order')->unsigned();
                $table->string('image', 300)->nullable();
                $table->boolean('is_enabled')->default(true);


                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
                $table->foreign('question_id')->references('id')->on('questions');
                $table->foreign('title')->references('id')->on('texts');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('answers');
    }
}
