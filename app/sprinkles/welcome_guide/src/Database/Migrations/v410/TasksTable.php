<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Tasks table migration
 * Version 1.0.0
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Akbari (https://github.com/aminakbari)
 */
class TasksTable extends Migration
{
	/**
	 * {@inheritDoc}
	 */
	public static $dependencies = [
		'\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
		'\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\TextsTable',
		'\UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410\StepsTable'
	];

	/**
	 * {@inheritDoc}
	 */
	public function up()
	{
		if (!$this->schema->hasTable('tasks')) {
			$this->schema->create('tasks', function (Blueprint $table) {
				$table->increments('id');

				$table->integer('step_id')->unsigned();
				$table->integer('text')->unsigned()->nullable();
				$table->integer('description')->unsigned()->nullable();
				$table->text('image_1')->nullable();
				$table->text('image_2')->nullable();
				$table->string('fa_icon', 50)->nullable();

				$table->integer('creator_id')->unsigned()->nullable();
				$table->timestamps();
				$table->engine = 'InnoDB';
				$table->collation = 'utf8_unicode_ci';
				$table->charset = 'utf8';
				$table->foreign('creator_id')->references('id')->on('users');
				$table->foreign('step_id')->references('id')->on('steps');
				$table->foreign('text')->references('id')->on('texts');
				$table->foreign('description')->references('id')->on('texts');
			});
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function down()
	{
		$this->schema->drop('tasks');
	}
}
