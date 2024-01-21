<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Seeds;

use UserFrosting\Sprinkle\Account\Database\Models\Permission;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

/**
 * Seeder for the default permissions.
 */
class WelcomeGuidePermissions extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {

        // We require the default welcome guide roles
        Seeder::execute('WelcomeGuideRoles');

        // Get and save permissions
        $permissions = $this->getPermissions();
        $this->savePermissions($permissions);

        // Add default mappings to permissions
        $this->syncPermissionsRole($permissions);
    }

    /**
     * @return array Permissions to seed
     */
    protected function getPermissions()
    {
        $defaultRoleIds = [
            'user' => Role::where('slug', 'user')->first()->id,
            'group-admin' => Role::where('slug', 'group-admin')->first()->id,
            'site-admin' => Role::where('slug', 'site-admin')->first()->id,
        ];

        return [
            'create_group' => new Permission([
                'slug' => 'create_group',
                'name' => 'Create group',
                'conditions' => 'always()',
                'description' => 'Create a new group.',
            ]),
            'create_user' => new Permission([
                'slug' => 'create_user',
                'name' => 'Create user',
                'conditions' => 'always()',
                'description' => 'Create a new user in your own group and assign default roles.',
            ]),
            'create_user_field' => new Permission([
                'slug' => 'create_user_field',
                'name' => 'Set new user group',
                'conditions' => "subset(fields,['group'])",
                'description' => 'Set the group when creating a new user.',
            ]),
            'delete_group' => new Permission([
                'slug' => 'delete_group',
                'name' => 'Delete group',
                'conditions' => 'always()',
                'description' => 'Delete a group.',
            ]),
            'delete_user' => new Permission([
                'slug' => 'delete_user',
                'name' => 'Delete user',
                'conditions' => "!has_role(user.id,{$defaultRoleIds['site-admin']}) && !is_master(user.id)",
                'description' => 'Delete users who are not Site Administrators.',
            ]),
            'update_account_settings' => new Permission([
                'slug' => 'update_account_settings',
                'name' => 'Edit user',
                'conditions' => 'always()',
                'description' => 'Edit your own account settings.',
            ]),
            'update_group_field' => new Permission([
                'slug' => 'update_group_field',
                'name' => 'Edit group',
                'conditions' => 'always()',
                'description' => 'Edit basic properties of any group.',
            ]),
            'update_user_field' => new Permission([
                'slug' => 'update_user_field',
                'name' => 'Edit user',
                'conditions' => "!has_role(user.id,{$defaultRoleIds['site-admin']}) && subset(fields,['name','email','locale','group','flag_enabled','flag_verified','password'])",
                'description' => 'Edit users who are not Site Administrators.',
            ]),
            'update_user_field_group' => new Permission([
                'slug' => 'update_user_field',
                'name' => 'Edit group user',
                'conditions' => "equals_num(self.group_id,user.group_id) && !is_master(user.id) && !has_role(user.id,{$defaultRoleIds['site-admin']}) && (!has_role(user.id,{$defaultRoleIds['group-admin']}) || equals_num(self.id,user.id)) && subset(fields,['name','email','locale','flag_enabled','flag_verified','password'])",
                'description' => 'Edit users in your own group who are not Site or Group Administrators, except yourself.',
            ]),
            'uri_account_settings' => new Permission([
                'slug' => 'uri_account_settings',
                'name' => 'Account settings page',
                'conditions' => 'always()',
                'description' => 'View the account settings page.',
            ]),
            'uri_activities' => new Permission([
                'slug' => 'uri_activities',
                'name' => 'Activity monitor',
                'conditions' => 'always()',
                'description' => 'View a list of all activities for all users.',
            ]),
            'uri_dashboard' => new Permission([
                'slug' => 'uri_dashboard',
                'name' => 'Admin dashboard',
                'conditions' => 'always()',
                'description' => 'View the administrative dashboard.',
            ]),
            'uri_group' => new Permission([
                'slug' => 'uri_group',
                'name' => 'View group',
                'conditions' => 'always()',
                'description' => 'View the group page of any group.',
            ]),
            'uri_group_own' => new Permission([
                'slug' => 'uri_group',
                'name' => 'View own group',
                'conditions' => 'equals_num(self.group_id,group.id)',
                'description' => 'View the group page of your own group.',
            ]),
            'uri_groups' => new Permission([
                'slug' => 'uri_groups',
                'name' => 'Group management page',
                'conditions' => 'always()',
                'description' => 'View a page containing a list of groups.',
            ]),
            'uri_user' => new Permission([
                'slug' => 'uri_user',
                'name' => 'View user',
                'conditions' => 'always()',
                'description' => 'View the user page of any user.',
            ]),
            'uri_user_in_group' => new Permission([
                'slug' => 'uri_user',
                'name' => 'View user',
                'conditions' => "equals_num(self.group_id,user.group_id) && !is_master(user.id) && !has_role(user.id,{$defaultRoleIds['site-admin']}) && (!has_role(user.id,{$defaultRoleIds['group-admin']}) || equals_num(self.id,user.id))",
                'description' => 'View the user page of any user in your group, except the master user and Site and Group Administrators (except yourself).',
            ]),
            'uri_users' => new Permission([
                'slug' => 'uri_users',
                'name' => 'User management page',
                'conditions' => 'always()',
                'description' => 'View a page containing a table of users.',
            ]),
            'view_group_field' => new Permission([
                'slug' => 'view_group_field',
                'name' => 'View group',
                'conditions' => "in(property,['name','icon','slug','description','users'])",
                'description' => 'View certain properties of any group.',
            ]),
            'view_group_field_own' => new Permission([
                'slug' => 'view_group_field',
                'name' => 'View group',
                'conditions' => "equals_num(self.group_id,group.id) && in(property,['name','icon','slug','description','users'])",
                'description' => 'View certain properties of your own group.',
            ]),
            'view_user_field' => new Permission([
                'slug' => 'view_user_field',
                'name' => 'View user',
                'conditions' => "in(property,['user_name','name','email','locale','theme','roles','group','activities'])",
                'description' => 'View certain properties of any user.',
            ]),
            'view_user_field_group' => new Permission([
                'slug' => 'view_user_field',
                'name' => 'View user',
                'conditions' => "equals_num(self.group_id,user.group_id) && !is_master(user.id) && !has_role(user.id,{$defaultRoleIds['site-admin']}) && (!has_role(user.id,{$defaultRoleIds['group-admin']}) || equals_num(self.id,user.id)) && in(property,['user_name','name','email','locale','roles','group','activities'])",
                'description' => 'View certain properties of any user in your own group, except the master user and Site and Group Administrators (except yourself).',
            ]),

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

            //subTasks
            'view_subTasks' => new Permission([
                'slug' => 'view_subTasks',
                'name' => 'View view_subTasks',
                'conditions' => 'always()',
                'description' => 'View a page containing a list of all subTasks.'
            ]),
            'create_subTask' => new Permission([
                'slug' => 'create_subTask',
                'name' => 'Create create_subTask',
                'conditions' => 'always()',
                'description' => 'Create a new subTask.'
            ]),
            'update_subTask_field' => new Permission([
                'slug' => 'update_subTask_field',
                'name' => 'Update update_subTask_field',
                'conditions' => 'always()',
                'description' => 'Edit subTask.'
            ]),
            'delete_subTask' => new Permission([
                'slug' => 'delete_subTask',
                'name' => 'Delete delete_subTask',
                'conditions' => 'always()',
                'description' => 'Delete subTask.'
            ]),

            //tasks
            'view_tasks' => new Permission([
                'slug' => 'view_tasks',
                'name' => 'View view_tasks',
                'conditions' => 'always()',
                'description' => 'View a page containing a list of all tasks.'
            ]),
            'create_task' => new Permission([
                'slug' => 'create_task',
                'name' => 'Create create_task',
                'conditions' => 'always()',
                'description' => 'Create a new task.'
            ]),
            'update_task_field' => new Permission([
                'slug' => 'update_task_field',
                'name' => 'Update update_task_field',
                'conditions' => 'always()',
                'description' => 'Edit task.'
            ]),
            'delete_task' => new Permission([
                'slug' => 'delete_task',
                'name' => 'Delete delete_task',
                'conditions' => 'always()',
                'description' => 'Delete task.'
            ]),

            //text_settings
            'view_textSettings' => new Permission([
                'slug' => 'view_textSettings',
                'name' => 'View view_textSettings',
                'conditions' => 'always()',
                'description' => 'View a page containing a list of all textSettings.'
            ]),
            'update_textSetting_field' => new Permission([
                'slug' => 'update_textSetting_field',
                'name' => 'Update update_textSetting_field',
                'conditions' => 'always()',
                'description' => 'Edit textSetting.'
            ]),
        ];
    }

    /**
     * Save permissions.
     *
     * @param array $permissions
     */
    protected function savePermissions(array &$permissions)
    {
        foreach ($permissions as $slug => $permission) {

            // Trying to find if the permission already exist
            $existingPermission = Permission::where(['slug' => $permission->slug, 'conditions' => $permission->conditions])->first();

            // Don't save if already exist, use existing permission reference
            // otherwise to re-sync permissions and roles
            if ($existingPermission == null) {
                $permission->save();
            } else {
                $permissions[$slug] = $existingPermission;
            }
        }
    }

    /**
     * Sync permissions with default roles.
     *
     * @param array $permissions
     */
    protected function syncPermissionsRole(array $permissions)
    {
        $roleWelcomeGuideViewer = Role::where('slug', 'welcome-guide-viewer')->first();
        if ($roleWelcomeGuideViewer) {
            $roleWelcomeGuideViewer->permissions()->sync([
                $permissions['view_answers']->id,
                $permissions['view_languages']->id,
                $permissions['view_logics']->id,
                $permissions['view_questions']->id,
                $permissions['view_steps']->id,
                $permissions['view_subTasks']->id,
                $permissions['view_tasks']->id,
                $permissions['view_textSettings']->id
            ]);
        }

        $roleWelcomeGuideAdmin = Role::where('slug', 'welcome-guide-admin')->first();
        if ($roleWelcomeGuideAdmin) {
            $roleWelcomeGuideAdmin->permissions()->sync([
                $permissions['create_answer']->id,
                $permissions['update_answer_field']->id,
                $permissions['delete_answer']->id,

                $permissions['create_language']->id,
                $permissions['update_language_field']->id,
                $permissions['delete_language']->id,

                $permissions['create_logic']->id,
                $permissions['update_logic_field']->id,
                $permissions['delete_logic']->id,

                $permissions['create_question']->id,
                $permissions['update_question_field']->id,
                $permissions['delete_question']->id,

                $permissions['create_step']->id,
                $permissions['update_step_field']->id,
                $permissions['delete_step']->id,

                $permissions['create_subTask']->id,
                $permissions['update_subTask_field']->id,
                $permissions['delete_subTask']->id,

                $permissions['create_task']->id,
                $permissions['update_task_field']->id,
                $permissions['delete_task']->id,

                $permissions['update_textSetting_field']->id
            ]);
        }
    }
}
