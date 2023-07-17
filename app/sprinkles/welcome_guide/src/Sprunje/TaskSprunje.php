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
class TaskSprunje extends ExtendedSprunje
{
    protected $sortable = [
        "text", 
        "description",
        "creator",
        "step",
        "created_at"
    ];

    protected $filterable = [
        "text",
        "step",
        "description", 
        "creator"
    ];

    protected $name = 'tasks';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('task');
		
        return $query->joinCreator()->joinStep()->joinText()->joinDescription()->distinct();
    }

    /**
     * Filter LIKE the step name.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterStep($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'step_name_translation.translated_text');
    }
	
    /**
     * Sort based on step name.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortStep($query, $direction)
    {
        $query->orderBy('tasks.step_id', $direction);
        return $this;
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterText($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'text_translation.translated_text');
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
