<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Sub Task Class
 *
 * Represents a task object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class SubTask extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "sub_tasks";

    protected $fillable = [
        "task_id", 
        "title", 
        "markdown", 
        "deadline",
        "order",  
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('tasks.*');

        $query = $query->leftJoin('users', 'sub_tasks.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's task, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinTask($query)
    {
        $query = $query->select('sub_tasks.*');

        $query = $query->leftJoin('tasks', 'sub_tasks.task_id', '=', 'tasks.id');

        return $query;
    }

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;

    /**
     * Return the user who added this object for the first time.
     */
    public function creator()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('user') , 'creator_id');
    }

    public function task()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('task') , 'task_id');
    }

    /**
     * Return the text for this object
     */
    public function title()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'title');
    }

    public function titles()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'title', 'id');
    }

    /**
     * Return the text for this object
     */
    public function markdown()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'markdown');
    }

    public function markdowns()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'markdown', 'id');
    }

    /**
     * Return the text for this object
     */
    public function deadline()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'deadline');
    }

    public function deadlines()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'deadline', 'id');
    }

    /**
    * Return the logics of this object
    */
    public function logics()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;
 
        return $this->belongsToMany($classMapper->getClassMapping('logic'));
    }
}