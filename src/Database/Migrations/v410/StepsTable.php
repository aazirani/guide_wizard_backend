<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Steps table migration
 * Version 1.0.0
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 */
class StepsTable extends Migration
{
    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410\TextsTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('steps')) {
            $this->schema->create('steps', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('name')->unsigned()->nullable();
                $table->integer('description')->unsigned()->nullable();
                $table->integer('order')->unsigned()->nullable();
                $table->text('image')->nullable();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
                $table->foreign('name')->references('id')->on('texts');
                $table->foreign('description')->references('id')->on('texts');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('steps');
    }
}
