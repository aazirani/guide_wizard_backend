<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Block Types table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class BlockTypesTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('block_types')) {
            $this->schema->create('block_types', function (Blueprint $table) {
                $table->increments('id');
                $table->text('title');
                $table->text('description')->nullable();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('block_types');
    }
}
