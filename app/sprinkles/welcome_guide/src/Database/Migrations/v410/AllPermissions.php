<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Migrations\v410;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use UserFrosting\Sprinkle\Account\Database\Models\Permission;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Database\Migration;

class AllPermissions extends Migration
{
	public static $dependencies = [
		'\UserFrosting\Sprinkle\Account\Database\Migrations\v400\PermissionsTable',
		'\UserFrosting\Sprinkle\Account\Database\Migrations\v400\RolesTable'
	];

	public function up()
	{
	}


	public function down()
	{
	}

	public function seed()
	{
		// Add default permissions
		$permissions = [

			//answers
			'view_answers' => new Permission([
				'slug' => 'view_answers',
				'name' => 'View view_answers',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all answers.'
			]),
			'create_answer' => new Permission([
				'slug' => 'create_answer',
				'name' => 'Create create_answer',
				'conditions' => 'always()',
				'description' => 'Create a new answer.'
			]),
			'update_answer_field' => new Permission([
				'slug' => 'update_answer_field',
				'name' => 'Update update_answer_field',
				'conditions' => 'always()',
				'description' => 'Edit subjects.'
			]),
			'delete_answer' => new Permission([
				'slug' => 'delete_answer',
				'name' => 'Delete delete_answer',
				'conditions' => 'always()',
				'description' => 'Delete answer.'
			]),

			//questions
			'view_questions' => new Permission([
				'slug' => 'view_questions',
				'name' => 'View view_questions',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all questions.'
			]),
			'create_question' => new Permission([
				'slug' => 'create_question',
				'name' => 'Create create_question',
				'conditions' => 'always()',
				'description' => 'Create a new question.'
			]),
			'update_question_field' => new Permission([
				'slug' => 'update_question_field',
				'name' => 'Update update_question_field',
				'conditions' => 'always()',
				'description' => 'Edit question.'
			]),
			'delete_question' => new Permission([
				'slug' => 'delete_question',
				'name' => 'Delete delete_question',
				'conditions' => 'always()',
				'description' => 'Delete question.'
			]),

			//languages
			'view_languages' => new Permission([
				'slug' => 'view_languages',
				'name' => 'View view_languages',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all languages.'
			]),
			'create_language' => new Permission([
				'slug' => 'create_language',
				'name' => 'Create create_language',
				'conditions' => 'always()',
				'description' => 'Create a new language.'
			]),
			'update_language_field' => new Permission([
				'slug' => 'update_language_field',
				'name' => 'Update update_language_field',
				'conditions' => 'always()',
				'description' => 'Edit language.'
			]),
			'delete_language' => new Permission([
				'slug' => 'delete_language',
				'name' => 'Delete delete_language',
				'conditions' => 'always()',
				'description' => 'Delete language.'
			]),

			//logics
			'view_logics' => new Permission([
				'slug' => 'view_logics',
				'name' => 'View view_logics',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all logics.'
			]),
			'create_logic' => new Permission([
				'slug' => 'create_logic',
				'name' => 'Create create_logic',
				'conditions' => 'always()',
				'description' => 'Create a new logic.'
			]),
			'update_logic_field' => new Permission([
				'slug' => 'update_logic_field',
				'name' => 'Update update_logic_field',
				'conditions' => 'always()',
				'description' => 'Edit logic.'
			]),
			'delete_logic' => new Permission([
				'slug' => 'delete_logic',
				'name' => 'Delete delete_logic',
				'conditions' => 'always()',
				'description' => 'Delete logic.'
			]),

			//steps
			'view_steps' => new Permission([
				'slug' => 'view_steps',
				'name' => 'View view_steps',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all steps.'
			]),
			'create_step' => new Permission([
				'slug' => 'create_step',
				'name' => 'Create create_step',
				'conditions' => 'always()',
				'description' => 'Create a new step.'
			]),
			'update_step_field' => new Permission([
				'slug' => 'update_step_field',
				'name' => 'Update update_step_field',
				'conditions' => 'always()',
				'description' => 'Edit step.'
			]),
			'delete_step' => new Permission([
				'slug' => 'delete_step',
				'name' => 'Delete delete_step',
				'conditions' => 'always()',
				'description' => 'Delete step.'
			]),

			//blocks
			'view_blocks' => new Permission([
				'slug' => 'view_blocks',
				'name' => 'View view_blocks',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all blocks.'
			]),
			'create_block' => new Permission([
				'slug' => 'create_block',
				'name' => 'Create create_block',
				'conditions' => 'always()',
				'description' => 'Create a new block.'
			]),
			'update_block_field' => new Permission([
				'slug' => 'update_block_field',
				'name' => 'Update update_block_field',
				'conditions' => 'always()',
				'description' => 'Edit block.'
			]),
			'delete_block' => new Permission([
				'slug' => 'delete_block',
				'name' => 'Delete delete_block',
				'conditions' => 'always()',
				'description' => 'Delete block.'
			]),

			//texts
			'view_texts' => new Permission([
				'slug' => 'view_texts',
				'name' => 'View view_texts',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all texts.'
			]),
			'create_text' => new Permission([
				'slug' => 'create_text',
				'name' => 'Create create_text',
				'conditions' => 'always()',
				'description' => 'Create a new text.'
			]),
			'update_text_field' => new Permission([
				'slug' => 'update_text_field',
				'name' => 'Update update_text_field',
				'conditions' => 'always()',
				'description' => 'Edit text.'
			]),
			'delete_text' => new Permission([
				'slug' => 'delete_text',
				'name' => 'Delete delete_text',
				'conditions' => 'always()',
				'description' => 'Delete text.'
			]),

			//translations
			'view_translations' => new Permission([
				'slug' => 'view_translations',
				'name' => 'View view_translations',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all translations.'
			]),
			'create_translation' => new Permission([
				'slug' => 'create_translation',
				'name' => 'Create create_translation',
				'conditions' => 'always()',
				'description' => 'Create a new translation.'
			]),
			'update_translation_field' => new Permission([
				'slug' => 'update_translation_field',
				'name' => 'Update update_translation_field',
				'conditions' => 'always()',
				'description' => 'Edit translation.'
			]),
			'delete_translation' => new Permission([
				'slug' => 'delete_translation',
				'name' => 'Delete delete_translation',
				'conditions' => 'always()',
				'description' => 'Delete translation.'
			]),

			//block_types
			'view_block_types' => new Permission([
				'slug' => 'view_block_types',
				'name' => 'View view_block_types',
				'conditions' => 'always()',
				'description' => 'View a page containing a list of all block_types.'
			]),
			'create_block_type' => new Permission([
				'slug' => 'create_block_type',
				'name' => 'Create create_block_type',
				'conditions' => 'always()',
				'description' => 'Create a new block_type.'
			]),
			'update_block_type_field' => new Permission([
				'slug' => 'update_block_type_field',
				'name' => 'Update update_block_type_field',
				'conditions' => 'always()',
				'description' => 'Edit block_type.'
			]),
			'delete_block_type' => new Permission([
				'slug' => 'delete_block_type',
				'name' => 'Delete delete_block_type',
				'conditions' => 'always()',
				'description' => 'Delete block_type.'
			]),
			
		];

		foreach ($permissions as $id => $permission) {
			$slug = $permission->slug;
			$conditions = $permission->conditions;
			// Skip if a permission with the same slug and conditions has already been added
			if (!Permission::where('slug', $slug)->where('conditions', $conditions)->first()) {
				$permission->save();
			}
		}

		// Automatically add permissions to particular roles
		$roleAdmin = Role::where('slug', 'site-admin')->first();
		if ($roleAdmin) {
			$roleAdmin->permissions()->sync([

				$permissions['view_answers']->id,
				$permissions['create_answer']->id,
				$permissions['update_answer_field']->id,
				$permissions['delete_answer']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_questions']->id,
				$permissions['create_question']->id,
				$permissions['update_question_field']->id,
				$permissions['delete_question']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_languages']->id,
				$permissions['create_language']->id,
				$permissions['update_language_field']->id,
				$permissions['delete_language']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_logics']->id,
				$permissions['create_logic']->id,
				$permissions['update_logic_field']->id,
				$permissions['delete_logic']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_steps']->id,
				$permissions['create_step']->id,
				$permissions['update_step_field']->id,
				$permissions['delete_step']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_blocks']->id,
				$permissions['create_block']->id,
				$permissions['update_block_field']->id,
				$permissions['delete_block']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_texts']->id,
				$permissions['create_text']->id,
				$permissions['update_text_field']->id,
				$permissions['delete_text']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_translations']->id,
				$permissions['create_translation']->id,
				$permissions['update_translation_field']->id,
				$permissions['delete_translation']->id
			]);

			$roleAdmin->permissions()->sync([
				$permissions['view_block_types']->id,
				$permissions['create_block_type']->id,
				$permissions['update_block_type_field']->id,
				$permissions['delete_block_type']->id
			]);
		}
	}
}
