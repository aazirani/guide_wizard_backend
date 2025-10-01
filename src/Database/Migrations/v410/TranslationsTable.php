<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Core\Database\Migration;

/**
 * Translations table migration
 * Version 0.1
 *
 * See https://laravel.com/docs/5.4/migrations#tables
 * @extends Migration
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class TranslationsTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\UsersTable',
        '\UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410\LanguagesTable',
        '\UserFrosting\Sprinkle\GuideWizard\Database\Migrations\v410\TextsTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('translations')) {
            $this->schema->create('translations', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('text_id')->unsigned();
                $table->longText('translated_text');
                $table->integer('language_id')->unsigned();

                $table->integer('creator_id')->unsigned()->nullable();
                $table->timestamps();
                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
                $table->foreign('creator_id')->references('id')->on('users');
                $table->foreign('language_id')->references('id')->on('languages');
                $table->foreign('text_id')->references('id')->on('texts');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('translations');
    }
}
