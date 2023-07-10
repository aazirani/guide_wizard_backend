<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 * @author Amin Akbari (https://github.com/aminakbari)
 */
class QuestionSprunje extends Sprunje
{
    protected $sortable = [
        "title",
        "sub_title",
        "type",
        "axis_count",
        "is_multiple_choice",
        "info_url",
        "info_description",
        "task_id",
        "creator_id"
    ];

    protected $filterable = [
        "title",
        "sub_title",
        "type",
        "axis_count",
        "is_multiple_choice",
        "info_url",
        "info_description",
        "task_id",
        "creator_id"
    ];

    protected $name = 'questions';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('question');
		
		return $query->joinCreator()->joinTask();
    }
	
	 /**
     * Filter LIKE the creator info.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterCreator($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                $query->orLike('users.first_name', $value)
                    ->orLike('users.last_name', $value)
                    ->orLike('users.email', $value);
            }
        });
        return $this;
    }
	
    /**
     * Sort based on creator last name.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortCreator($query, $direction)
    {
        $query->orderBy('users.last_name', $direction);
        return $this;
    }

    /**
     * Filter LIKE the task name.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterTasks($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                $query->orLike('tasks.text', $value);
            }
        });
        return $this;
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
        $query->orderBy('tasks.name', $direction);
        return $this;
    }
	
}
