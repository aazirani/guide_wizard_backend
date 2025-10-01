<?php
namespace UserFrosting\Sprinkle\GuideWizard\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 */
class SubTaskSprunje extends ExtendedSprunje
{
    protected $sortable = [
        "title", 
        "markdown", 
        "deadline",
        "order",
        "task",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "title", 
        "markdown", 
        "deadline",
        "order",
        "task",
        "creator"
    ];

    protected $name = 'subTasks';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('subTask');
		
		return $query->joinCreator()->joinTask()->joinTitle()->joinMarkdown()->joinDeadline()->distinct();
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterTitle($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'title_translation.translated_text');
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterMarkdown($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'markdown_translation.translated_text');
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterDeadline($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'deadline_translation.translated_text');
    }

    /**
     * Filter LIKE the task text.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterTask($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'task_text_translation.translated_text');
    }
	
    /**
     * Sort based on task name.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortTask($query, $direction)
    {
        $query->orderBy('sub_tasks.task_id', $direction);
        return $this;
    }
	
}
