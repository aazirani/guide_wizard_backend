<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Controller\UtilityClasses;

use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Translation;

class TranslationsUtilities{
    public static function addTranslations($params, $arrayOfKeys, $arrayOfObjectToReceiveWithKeyAsKey, $classMapper, $currentUserId, $objectName, $object): array
    {
        foreach ($arrayOfKeys as $key){

            $text = $classMapper->staticMethod('text', 'where', 'id', $arrayOfObjectToReceiveWithKeyAsKey[$key])->with('creator')->first();
            if(!$text){
                $text = $classMapper->createInstance('text');
            }
            $text->technical_name = $objectName."_".$object->id."_".$key;
            $text->creator_id = $currentUserId;
            $text->save();

            $valuesFromKeyThatStartsWithString = TranslationsUtilities::getValuesFromKeyThatStartsWithString($params, $key);

            foreach ($valuesFromKeyThatStartsWithString as $languageId => $value) {
                if (empty(trim($value))) {
                    continue;
                }
                $matchThese = ['text_id' => $text->id, 'language_id' => $languageId];
                $translation = Translation::where($matchThese)->with('creator')->first();
                if(!$translation){
                    $translation = $classMapper->createInstance('translation');
                }
                $translation->language_id = $languageId;
                $translation->translated_text = $value;
                $translation->text_id = $text->id;
                if(!$translation->creator_id){
                    $translation->creator_id = $currentUserId;
                }
                $translation->save();
            }

            $arrayOfObjectToReceiveWithKeyAsKey[$key] = $text->id;

        }

        return $arrayOfObjectToReceiveWithKeyAsKey;
    }
    

    protected static function getValuesFromKeyThatStartsWithString($arrayOfKey, $keyStartsWith): array
    {
        $valuesWithLanguageIdAsKey = array();
        foreach ($arrayOfKey as $key => $value) {
            if (str_starts_with($key, $keyStartsWith."_")) {
                $explode = explode('_', $key);
                $valuesWithLanguageIdAsKey[end($explode)] = $value;
            }
        }
        return $valuesWithLanguageIdAsKey;
    }
}