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
        "type",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('steps.*');

        $query = $query->leftJoin('users', 'steps.creator_id', '=', 'users.id');

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
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('user'), 'creator_id');
    }

    public function name()
    {
         /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
         $classMapper = static::$ci->classMapper;

        return $this->hasManyThrough(
            $classMapper->getClassMapping('translation'),
            $classMapper->getClassMapping('text'),
            'id',
            'text_id',
            'name',
            'id'
        );
    }

    public function description()
    {
         /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
         $classMapper = static::$ci->classMapper;

        return $this->hasManyThrough(
            $classMapper->getClassMapping('translation'),
            $classMapper->getClassMapping('text'),
            'id',
            'text_id',
            'description',
            'id'
        );
    }

    //observe this model being deleted and delete the relationships
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($step) {
            foreach ($step->translations as $translation) {
                $translation->delete();
            }
        });
    }
}
