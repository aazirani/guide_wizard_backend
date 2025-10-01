<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Sub tasks table migration
 * Version 1.0.0
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 */
class SubTasksTable extends Migration
{
	/**
	 * {@inheritDoc}
	 */
	public static $dependencies = [
		'\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
		'\UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410\TextsTable',
		'\UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410\TasksTable'
	];

	/**
	 * {@inheritDoc}
	 */
	public function up()
	{
		if (!$this->schema->hasTable('sub_tasks')) {
			$this->schema->create('sub_tasks', function (Blueprint $table) {
				$table->increments('id');

				$table->integer('task_id')->unsigned();
				$table->integer('title')->unsigned()->nullable();
				$table->integer('markdown')->unsigned()->nullable();
				$table->integer('deadline')->unsigned()->nullable();
				$table->integer('order')->unsigned();

				$table->integer('creator_id')->unsigned()->nullable();
				$table->timestamps();
				$table->engine = 'InnoDB';
				$table->collation = 'utf8_unicode_ci';
				$table->charset = 'utf8';
				$table->foreign('creator_id')->references('id')->on('users');
				$table->foreign('task_id')->references('id')->on('tasks');
				$table->foreign('title')->references('id')->on('texts');
				$table->foreign('markdown')->references('id')->on('texts');
				$table->foreign('deadline')->references('id')->on('texts');
			});
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function down()
	{
		$this->schema->drop('sub_tasks');
	}
}
