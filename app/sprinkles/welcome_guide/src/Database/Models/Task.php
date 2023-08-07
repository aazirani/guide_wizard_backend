<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Task Class
 *
 * Represents a task object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Task extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "tasks";

    protected $fillable = [
        "step_id", 
        "text", 
        "description", 
        "image_1",
        "image_2", 
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('tasks.*', 'users.last_name');

        $query = $query->leftJoin('users', 'tasks.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's step, so we can do things like sort, search, paginate, etc.

    public function scopeJoinStep($query)
    {
        $query = $query->select('tasks.*');

        $query = $query->leftJoin('steps', 'tasks.step_id', '=', 'steps.id');

        return $query;
    }
    */

    /**
     * Joins the object's name, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinText($query)
    {
        return $query
        ->leftJoin('texts as text_text', 'tasks.text', '=', 'text_text.id')
        ->leftJoin('translations as text_translation', 'text_text.id', '=', 'text_translation.text_id');
    }

    /**
     * Joins the object's description, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinDescription($query)
    {
        return $query
        ->leftJoin('texts as description_text', 'tasks.description', '=', 'description_text.id')
        ->leftJoin('translations as description_translation', 'description_text.id', '=', 'description_translation.text_id');
    }

    /**
     * Joins the object's step, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinStep($query)
    {
        return $query
        ->leftJoin('steps as step', 'tasks.step_id', '=', 'step.id')
        ->leftJoin('texts as step_name_text', 'step.name', '=', 'step_name_text.id')
        ->leftJoin('translations as step_name_translation', 'step_name_text.id', '=', 'step_name_translation.text_id');
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

    public function step()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('step') , 'step_id');
    }

    /**
     * Return the text for this object
     */
    public function text()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'text');
    }

    public function texts()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'text', 'id');
    }

    /**
     * Return the text for this object
     */
    public function description()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'description');
    }

    public function descriptions()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'description', 'id');
    }

    public function subTasks()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('subTask'));
    }
}