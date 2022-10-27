<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Logics to answers table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class BlockLogicTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\LogicsTable',
        '\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\BlocksTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('block_logic')) {
            $this->schema->create('block_logic', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('logic_id')->unsigned()->nullable();
                $table->integer('block_id')->unsigned()->nullable();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
                $table->foreign('logic_id')->references('id')->on('logics');
                $table->foreign('block_id')->references('id')->on('blocks');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('block_logic');
    }
}
