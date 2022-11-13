<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Language Class
 *
 * Represents a language object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Language extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "languages";

    protected $fillable = [
        "language_code",
        "is_active",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('languages.*');

        $query = $query->leftJoin('users', 'languages.creator_id', '=', 'users.id');

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

    public function translations()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('translation'));
    }

    //observe this model being deleted and delete the relationships
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($language)
        {
            //foreach ($language->translations as $translation) {
            //    $translation->delete();
            //}
            
        });
    }
}