<?php
namespace UserFrosting\Sprinkle\GuideWizard\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Logic Class
 *
 * Represents a logic object as stored in the database.
 *
 * @package GuideWizard
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Logic extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "logics";

    protected $fillable = [
        "name",
        "expression",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('logics.*', 'users.last_name');

        $query = $query->leftJoin('users', 'logics.creator_id', '=', 'users.id');

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

    /**
     * Return the answers of this object
     */
    public function answers()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsToMany($classMapper->getClassMapping('answer'));
    }

    /**
     * Return the answers of this object
     */
    public function subTasks()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsToMany($classMapper->getClassMapping('subTask'), 'sub_task_logic');
    }

    //observe this model being deleted and delete the relationships
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($answer)
        {
            //foreach ($answer->translations as $translation) {
            //    $translation->delete();
            //}
            
        });
    }
}