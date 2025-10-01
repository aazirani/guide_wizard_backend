<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * TextSetting Class
 *
 * Represents a setting object as stored in the database.
 *
 * @package GuideWizard
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class TextSetting extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "text_settings";

    protected $fillable = [
        "title",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('text_settings.*', 'users.last_name');

        $query = $query->leftJoin('users', 'text_settings.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's title, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinTitle($query)
    {
        return $query
            ->leftJoin('texts as title_text', 'text_settings.title', '=', 'title_text.id')
            ->leftJoin('translations as title_translation', 'title_text.id', '=', 'title_translation.text_id');
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

        return $this->hasManyThrough($classMapper->getClassMapping('translation'), $classMapper->getClassMapping('text'), 'id', 'text_id', 'title', 'id');
    }

}