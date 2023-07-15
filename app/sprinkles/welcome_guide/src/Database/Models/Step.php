<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Step Class
 *
 * Represents a step object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Step extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "steps";

    protected $fillable = [
        "name", 
        "description", 
        "order", 
        "image", 
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('steps.*', 'users.last_name');

        $query = $query->leftJoin('users', 'steps.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's name, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinName($query)
    {
        return $query
        ->leftJoin('texts as name_text', 'steps.name', '=', 'name_text.id')
        ->leftJoin('translations as name_translation', 'name_text.id', '=', 'name_translation.text_id');
    }

    /**
     * Joins the object's description, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinDescription($query)
    {
        return $query
        ->leftJoin('texts as description_text', 'steps.description', '=', 'description_text.id')
        ->leftJoin('translations as description_translation', 'description_text.id', '=', 'description_translation.text_id');
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

    /**
     * Return the text for this object
     */
    public function name()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'name');
    }

    public function names()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'name', 'id');
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

    public function tasks()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('task'));
    }

}