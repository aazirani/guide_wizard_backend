<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Question Class
 *
 * Represents a question object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Question extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "questions";

    protected $fillable = [
        "title",
        "sub_title",
        "type",
        "axis_count",
        "is_multiple_choice",
        "info_url",
        "info_description",
        "answer_required",
        "answers_selected_by_default",
        "step_id",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('questions.*');

        $query = $query->leftJoin('users', 'questions.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's step, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinStep($query)
    {
        $query = $query->select('questions.*');

        $query = $query->leftJoin('steps', 'questions.step_id', '=', 'steps.id');

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

    public function step()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('step'), 'step_id');
    }

    public function answers()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('answer'));
    }

    /**
     * Return the text for this object
     */
    public function title()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text'), 'title');
    }

    public function titles()
    {
         /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
         $classMapper = static::$ci->classMapper;

        return $this->hasManyThrough(
            $classMapper->getClassMapping('translation'),
            $classMapper->getClassMapping('text'),
            'id',
            'text_id',
            'title',
            'id'
        );
    }

    /**
     * Return the text for this object
     */
    public function subTitle()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text'), 'sub_title');
    }

    public function subTitles()
    {
         /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
         $classMapper = static::$ci->classMapper;

        return $this->hasManyThrough(
            $classMapper->getClassMapping('translation'),
            $classMapper->getClassMapping('text'),
            'id',
            'text_id',
            'sub_title',
            'id'
        );
    }

    /**
     * Return the text for this object
     */
    public function infoUrl()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text'), 'info_url');
    }

    public function infoUrls()
    {
         /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
         $classMapper = static::$ci->classMapper;

        return $this->hasManyThrough(
            $classMapper->getClassMapping('translation'),
            $classMapper->getClassMapping('text'),
            'id',
            'text_id',
            'info_url',
            'id'
        );
    }

    /**
     * Return the text for this object
     */
    public function infoDescription()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text'), 'info_description');
    }
    
    public function infoDescriptions()
    {
         /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
         $classMapper = static::$ci->classMapper;

        return $this->hasManyThrough(
            $classMapper->getClassMapping('translation'),
            $classMapper->getClassMapping('text'),
            'id',
            'text_id',
            'info_description',
            'id'
        );
    }

    //observe this model being deleted and delete the relationships
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($question) {
            foreach ($question->answers as $answer) {
                $answer->delete();
            }
        });
    }
}
