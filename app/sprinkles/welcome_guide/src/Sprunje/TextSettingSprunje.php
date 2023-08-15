<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 */
class TextSettingSprunje extends ExtendedSprunje
{

    protected $sortable = [
        "title",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "title",
        "creator"
    ];

    protected $name = 'textSettings';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('textSetting');

        return $query->joinCreator()->joinTitle()->distinct();
    }


    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterTitle($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'title_translation.translated_text');
    }

}