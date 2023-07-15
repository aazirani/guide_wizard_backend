<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Sprunje;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Facades\Debug;
use UserFrosting\Sprinkle\Core\Sprunje\Sprunje;
use UserFrosting\Sprinkle\Core\Facades\Translator;

/**
 * Implements an Extended Sprunje.
 */
abstract class ExtendedSprunje extends Sprunje
{
    protected function count($query)
    {
        return $query->pluck('id')->count();
    }

    /**
     * Get the unpaginated count of items (after filtering) in this query.
     *
     * @param Builder $query
     *
     * @return int
     */
    protected function countFiltered($query)
    {
        return $query->pluck('id')->count();
    }
    
    protected function filterForYesNoQuestion($query, $value, $attributeName){
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values, $attributeName) {
            foreach ($values as $value) {
                if ($value == 'yes') {
                    $query->orWhere($attributeName, 1);
                } elseif ($value == 'no') {
                    $query->orWhere($attributeName, 0);
                }
            }
        });

        return $this;
    }
    
	/**
     * Filter LIKE the creator info.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterCreator($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                $query->orLike('users.first_name', $value)
                    ->orLike('users.last_name', $value)
                    ->orLike('users.email', $value);
            }
        });
        return $this;
    }

    /**
     * Sort based on creator last name.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortCreator($query, $direction)
    {
        $query->orderBy('users.last_name', $direction);
        return $this;
    }
    
    protected function filterForTranslation($query, $value, $translatedTextObject){
        $values = explode($this->orSeparator, $value);

        return $query->where(function ($query) use ($values, $translatedTextObject) {
            foreach ($values as $value) {
                $query->orLike($translatedTextObject, "%$value%");
            }
        });
    }
    
    protected function listForYesNoQuestion(){
        return [
            [
                'value' => 'yes',
                'text'  => Translator::translate('YES'),
            ],
            [
                'value' => 'no',
                'text'  => Translator::translate('NO'),
            ],
        ];
    }
}