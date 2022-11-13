<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Answer Class
 *
 * Represents a answer object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Answer extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "answers";

    protected $fillable = [
        "question_id",
        "title",
        "order",
        "image",
        "is_enabled",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('answers.*');

        $query = $query->leftJoin('users', 'answers.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's question, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinQuestion($query)
    {
        $query = $query->select('answers.*');

        $query = $query->leftJoin('questions', 'answers.question_id', '=', 'questions.id');

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

    public function question()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('question') , 'question_id');
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