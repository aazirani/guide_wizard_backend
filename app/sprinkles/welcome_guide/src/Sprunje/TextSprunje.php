<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 * @author Amin Azirani (https://github.com/aminazirani)
 */
class TextSprunje extends Sprunje
{
    protected $sortable = [
        "technical_name",
        "creator_id"
    ];

    protected $filterable = [
        "technical_name",
        "creator_id"
    ];

    protected $name = 'texts';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('text');
		
		return $query->joinCreator();
    }
	
}
