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
        "task_id",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('questions.*', 'users.last_name');

        $query = $query->leftJoin('users', 'questions.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's title, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinTitle($query)
    {
        return $query
        ->leftJoin('texts as title_text', 'questions.title', '=', 'title_text.id')
        ->leftJoin('translations as title_translation', 'title_text.id', '=', 'title_translation.text_id');
    }

    /**
     * Joins the object's title, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinSubTitle($query)
    {
        return $query
        ->leftJoin('texts as subTitle_text', 'questions.sub_title', '=', 'subTitle_text.id')
        ->leftJoin('translations as subTitle_translation', 'subTitle_text.id', '=', 'subTitle_translation.text_id');
    }

    /**
     * Joins the object's info url, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinInfoUrl($query)
    {
        return $query
        ->leftJoin('texts as infoUrl_text', 'questions.info_url', '=', 'infoUrl_text.id')
        ->leftJoin('translations as infoUrl_translation', 'infoUrl_text.id', '=', 'infoUrl_translation.text_id');
    }

    /**
     * Joins the object's info url, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinInfoDescription($query)
    {
        return $query
        ->leftJoin('texts as infoDescription_text', 'questions.info_description', '=', 'infoDescription_text.id')
        ->leftJoin('translations as infoDescription_translation', 'infoDescription_text.id', '=', 'infoDescription_translation.text_id');
    }

    /**
     * Joins the object's task, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinTask($query)
    {
        return $query
        ->leftJoin('tasks as task', 'questions.task_id', '=', 'task.id')
        ->leftJoin('texts as task_text_text', 'task.text', '=', 'task_text_text.id')
        ->leftJoin('translations as task_text_translation', 'task_text_text.id', '=', 'task_text_translation.text_id');
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

    public function answers()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping('answer'));
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
    public function subTitle()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'sub_title');
    }

    public function subTitles()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'sub_title', 'id');
    }

    /**
     * Return the text for this object
     */
    public function infoUrl()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'info_url');
    }

    public function infoUrls()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'info_url', 'id');
    }

    /**
     * Return the text for this object
     */
    public function infoDescription()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text') , 'info_description');
    }

    public function infoDescriptions()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static ::$ci->classMapper;

        return $this->hasManyThrough($classMapper->getClassMapping('translation') , $classMapper->getClassMapping('text') , 'id', 'text_id', 'info_description', 'id');
    }
}