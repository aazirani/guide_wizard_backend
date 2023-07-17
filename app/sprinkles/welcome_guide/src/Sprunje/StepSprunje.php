<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 */
class StepSprunje extends ExtendedSprunje
{
    protected $sortable = [
        "name",
        "description",
        "order",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "name",
        "description",
        "order",
        "creator"
    ];

    protected $name = 'steps';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('step');
		
        return $query->joinCreator()->joinName()->joinDescription()->distinct();

    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterName($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'name_translation.translated_text');
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterDescription($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'description_translation.translated_text');
    }

}
