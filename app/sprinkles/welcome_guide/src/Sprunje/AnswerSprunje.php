<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;

/**
 * Implements Sprunje for the module API.
 *
 * @author Amin Akbari (https://github.com/aminakbari)
 */
class AnswerSprunje extends ExtendedSprunje
{

    protected $listable = [
        'is_enabled'
    ];

    protected $sortable = [
        "title",
        "order",
        "image",
        "is_enabled",
        "question",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "title",
        "order",
        "image",
        "is_enabled",
        "question",
        "creator"
    ];

    protected $name = 'answers';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('answer');

        return $query->joinCreator()->joinQuestion()->joinTitle()->distinct();
    }

    /**
     * Return a list of possible options.
     *
     * @return array
     */
    protected function listIsEnabled()
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
    protected function filterIsEnabled($query, $value)
    {
        return $this->filterForYesNoQuestion($query, $value, 'is_enabled');
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

    /**
     * Filter LIKE the question title.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterQuestion($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'question_title_translation.translated_text');
    }
	
    /**
     * Sort based on question title.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortQuestion($query, $direction)
    {
        $query->orderBy('answers.question_id', $direction);
        return $this;
    }

}