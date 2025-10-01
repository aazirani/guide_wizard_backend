<?php
namespace UserFrosting\Sprinkle\GuideWizard\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;
use UserFrosting\Sprinkle\Core\Facades\Translator;

/**
 * Implements Sprunje for the module API.
 *
 */
class LanguageSprunje extends ExtendedSprunje
{

    protected $listable = [
        'is_active',
        'is_main_language'
    ];

    protected $sortable = [
        "language_code",
        "language_name",
        "is_active",
        "is_main_language",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "language_code",
        "language_name",
        "is_active",
        "is_main_language",
        "creator"
    ];

    protected $name = 'languages';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('language');
		
		return $query->joinCreator();
    }

    /**
     * Return a list of possible options.
     *
     * @return array
     */
    protected function listIsActive()
    {
        return $this->listForYesNoQuestion();
    }

    /**
     * Filter by option.
     *
     * @param Builder $query
     * @param mixed   $value
     *
     * @return self
     */
    protected function filterIsActive($query, $value)
    {
        return $this->filterForYesNoQuestion($query, $value, 'is_active');
    }

    /**
     * Return a list of possible options.
     *
     * @return array
     */
    protected function listIsMainLanguage()
    {
        return $this->listForYesNoQuestion();
    }

    /**
     * Filter by option.
     *
     * @param Builder $query
     * @param mixed   $value
     *
     * @return self
     */
    protected function filterIsMainLanguage($query, $value)
    {
        return $this->filterForYesNoQuestion($query, $value, 'is_main_language');
    }
	
}
