<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Seeds;

use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;

/**
 * Seeder for the default roles.
 */
class WelcomeGuideRoles extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $roles = $this->getRoles();

        foreach ($roles as $role) {
            // Don't save if already exist
            if (Role::where('slug', $role->slug)->first() == null) {
                $role->save();
            }
        }
    }

    /**
     * @return array Roles to seed
     */
    protected function getRoles()
    {
        return [
            new Role([
                'slug'        => 'user',
                'name'        => 'User',
                'description' => 'This role provides basic user functionality.',
            ]),
            new Role([
                'slug'        => 'site-admin',
                'name'        => 'Site Administrator',
                'description' => 'This role is meant for "site administrators", who can basically do anything except create, edit, or delete other administrators.',
            ]),
            new Role([
                'slug'        => 'group-admin',
                'name'        => 'Group Administrator',
                'description' => 'This role is meant for "group administrators", who can basically do anything with users in their own group, except other administrators of that group.',
            ]),
            new Role([
                'slug'        => 'welcome-guide-admin',
                'name'        => 'Welcome Guide Administrator',
                'description' => 'This role is meant for " welcome guide administrators", who can basically do anything with welcome guide objects.',
            ]),
            new Role([
                'slug'        => 'welcome-guide-viewer',
                'name'        => 'Welcome Guide Viewer',
                'description' => 'This role is meant for " welcome guide viewers", who can basically only view the welcome guide objects.',
            ]),
        ];
    }
}
