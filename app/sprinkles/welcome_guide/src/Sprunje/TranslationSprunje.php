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
class TranslationSprunje extends Sprunje
{
    protected $sortable = [
        "text_id",
        "translated_text",
        "creator_id"
    ];

    protected $filterable = [
        "text_id",
        "translated_text",
        "creator_id"
    ];

    protected $name = 'translations';

    /**
     * Set the initial query used by your Sprunje.
     */
    protected function baseQuery()
    {
        $query = $this->classMapper->createInstance('translation');
		
		return $query->joinCreator();
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

    /**
     * Filter LIKE the language info.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterLanguage($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                $query->orLike('languages.language_code', $value);
            }
        });
        return $this;
    }
	
    /**
     * Sort based on language code.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortLanguage($query, $direction)
    {
        $query->orderBy('languages.language_code', $direction);
        return $this;
    }

    /**
     * Filter LIKE the text info.
     *
     * @param Builder $query
     * @param mixed $value
     * @return $this
     */
    protected function filterText($query, $value)
    {
        // Split value on separator for OR queries
        $values = explode($this->orSeparator, $value);
        $query->where(function ($query) use ($values) {
            foreach ($values as $value) {
                $query->orLike('texts.technical_name', $value);
            }
        });
        return $this;
    }
	
    /**
     * Sort based on text technical name.
     *
     * @param Builder $query
     * @param string $direction
     * @return $this
     */
    protected function sortText($query, $direction)
    {
        $query->orderBy('texts.technical_name', $direction);
        return $this;
    }
	
}
