<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\System\Bakery\Migration;

/**
 * Blocks table migration
 * Version 1.0.0
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Akbari (https://github.com/aminakbari)
 */
class BlocksTable extends Migration
{
	/**
	 * {@inheritDoc}
	 */
	public $dependencies = [
		'\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
		'\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\TextsTable',
		'\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\StepsTable',
		'\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\BlockTypeTable'
	];

	/**
	 * {@inheritDoc}
	 */
	public function up()
	{
		if (!$this->schema->hasTable('blocks')) {
			$this->schema->create('blocks', function (Blueprint $table) {
				$table->increments('id');

				$table->integer('step_id')->unsigned();
				$table->integer('text')->unsigned();
				$table->integer('block_type_id')->unsigned();
				$table->integer('description')->unsigned()->nullable();
				$table->text('image_1')->nullable();
				$table->text('image_2')->nullable();
				$table->string('fa_icon', 50)->nullable();

				$table->timestamps();
				$table->engine = 'InnoDB';
				$table->collation = 'utf8_unicode_ci';
				$table->charset = 'utf8';
				$table->foreign('creator_id')->references('id')->on('users');
				$table->foreign('step_id')->references('id')->on('steps');
				$table->foreign('text')->references('id')->on('texts');
				$table->foreign('description')->references('id')->on('texts');
				$table->foreign('block_type_id')->references('id')->on('block_types');
			});
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function down()
	{
		$this->schema->drop('blocks');
	}
}
