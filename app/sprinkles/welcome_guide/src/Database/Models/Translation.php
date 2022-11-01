<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

/**
 * Translation Class
 *
 * Represents a translation object as stored in the database.
 *
 * @package WelcomeGuide
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 */
class Translation extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "translations";

    protected $fillable = [
        "text_id",
        "translated_text",
        "language_id",
        "creator_id"
    ];

    /**
     * Joins the object's creator, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinCreator($query)
    {
        $query = $query->select('translations.*');

        $query = $query->leftJoin('users', 'translations.creator_id', '=', 'users.id');

        return $query;
    }

    /**
     * Joins the object's text, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinText($query)
    {
        $query = $query->select('translations.*');

        $query = $query->leftJoin('texts', 'translations.text_id', '=', 'texts.id');

        return $query;
    }

    /**
     * Joins the object's language, so we can do things like sort, search, paginate, etc.
     */
    public function scopeJoinLanguage($query)
    {
        $query = $query->select('translations.*');

        $query = $query->leftJoin('languages', 'translations.language_id', '=', 'languages.id');

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

    /**
     * Return the text of this object.
     */
    public function text()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('text'), 'text_id');
    }

    /**
     * Return the language of this object.
     */
    public function language()
    {
        /** @var UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping('language'), 'language_id');
    }

}
