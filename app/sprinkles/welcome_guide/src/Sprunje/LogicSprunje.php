<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Facades\Translator;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 */
class LogicSprunje extends ExtendedSprunje
{

    protected $sortable = [
        "name",
        "expression",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "name",
        "expression",
        "creator"
    ];

    protected $name = 'logics';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('logic');
		
		return $query->joinCreator();
    }
	
}
