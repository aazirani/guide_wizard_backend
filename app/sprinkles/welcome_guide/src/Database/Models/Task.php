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
        "fa_icon", 
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('tasks.*');

        $query = $query->leftJoin('users', 'tasks.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's step, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinStep($query)
    {
        $query = $query->select('tasks.*');

        $query = $query->leftJoin('steps', 'tasks.step_id', '=', 'steps.id');

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

    public function questions()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('question'));
    }

    public function subTasks()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('subTask'));
    }

    //observe this model being deleted and delete the relationships
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($question)
        {
            //foreach ($question->answers as $answer) {
            //    $answer->delete();
            //}
            
        });
    }
}