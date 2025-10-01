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
class QuestionSprunje extends ExtendedSprunje
{
    protected $listable = [
        'is_multiple_choice',
        'type',
        'axis_count'
    ];
    protected $sortable = [
        "title",
        "sub_title",
        "type",
        "axis_count",
        "is_multiple_choice",
        "info_url",
        "info_description",
        "step",
        "creator",
        "created_at"
    ];

    protected $filterable = [
        "title",
        "sub_title",
        "type",
        "axis_count",
        "is_multiple_choice",
        "info_url",
        "info_description",
        "step",
        "creator"
    ];

    protected $name = 'questions';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('question');
		
		return $query->joinCreator()->joinStep()->joinTitle()->joinSubTitle()->joinInfoUrl()->joinInfoDescription()->distinct();
    }

    /**
     * Return a list of possible options.
     *
     * @return array
     */
    protected function listType()
    {
        return [
            [
                'value' => 'text',
                'text'  => Translator::translate('TEXT'),
            ],
            [
                'value' => 'image',
                'text'  => Translator::translate('IMAGE'),
            ],
        ];
    }

    /**
     * Return a list of possible options.
     *
     * @return array
     */
    protected function listAxisCount()
    {
        return [
            [
                'value' => '1',
                'text'  => '1',
            ],
            [
                'value' => '2',
                'text'  => '2',
            ],
            [
                'value' => '3',
                'text'  => '3',
            ],
            [
                'value' => '4',
                'text'  => '4',
            ],
        ];
    }

    /**
     * Return a list of possible options.
     *
     * @return array
     */
    protected function listIsMultipleChoice()
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
    protected function filterIsMultipleChoice($query, $value)
    {
        return $this->filterForYesNoQuestion($query, $value, 'is_multiple_choice');
    }

    /**
     * Filter by option.
     *
     * @param Builder $query
     * @param mixed   $value
     *
     * @return self
     */
    protected function filterType($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                if ($value == 'image') {
                    $query->orWhere('type', 'IMAGE');
                } elseif ($value == 'text') {
                    $query->orWhere('type', 'TEXT');
                }
            }
        });

        return $this;
    }

    /**
     * Filter by option.
     *
     * @param Builder $query
     * @param mixed   $value
     *
     * @return self
     */
    protected function filterAxisCount($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                if ($value == '1') {
                    $query->orWhere('axis_count', '1');
                } elseif ($value == '2') {
                    $query->orWhere('axis_count', '2');
                } elseif ($value == '3') {
                    $query->orWhere('axis_count', '3');
                } elseif ($value == '4') {
                    $query->orWhere('axis_count', '4');
                }
            }
        });

        return $this;
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
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterSubTitle($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'subTitle_translation.translated_text');
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterInfoUrl($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'infoUrl_translation.translated_text');
    }

    /**
     * Filter LIKE the object translations.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterInfoDescription($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'infoDescription_translation.translated_text');
    }

    /**
     * Filter LIKE the step name.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterStep($query, $value)
    {
        return $this->filterForTranslation($query, $value, 'step_name_translation.translated_text');
    }

    /**
     * Sort based on step text.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortStep($query, $direction)
    {
        $query->orderBy('questions.step_id', $direction);
        return $this;
    }
	
}
