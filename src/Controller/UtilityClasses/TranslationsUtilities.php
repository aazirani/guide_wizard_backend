<?php
namespace UserFrosting\Sprinkle\WelcomeGuide\Controller\UtilityClasses;

use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Language;
use UserFrosting\Sprinkle\WelcomeGuide\Database\Models\Translation;

class TranslationsUtilities{
    public static function addTranslations($params, $arrayOfObjectToReceiveWithKeyAsKey, $classMapper, $currentUserId, $objectName, $object, $userActivityLogger, $currentUser, $updateTechnicalName): array
    {
        foreach ($arrayOfObjectToReceiveWithKeyAsKey as $key => $valueOfKey){
            $translationsWereUpdatedFlag = false;

            $text = $classMapper->staticMethod('text', 'where', 'id', $arrayOfObjectToReceiveWithKeyAsKey[$key])->with('creator')->first();
            if(!$text){
                $text = $classMapper->createInstance('text');
            }
            
            if($updateTechnicalName || !isset($text->technical_name)){
                $text->technical_name = $objectName."_".$object->id."_".$key;
            }
            
            $text->creator_id = $currentUserId;

            $text->save();

            $valuesFromKeyThatStartsWithString = TranslationsUtilities::getValuesFromKeyThatStartsWithString($params, $key);

            foreach ($valuesFromKeyThatStartsWithString as $languageId => $value) {
                $matchThese = ['text_id' => $text->id, 'language_id' => $languageId];
                $translation = Translation::where($matchThese)->with('creator')->first();
                if (empty(trim($value))) {
                    if($translation){
                        $translation->delete();
                        $translationsWereUpdatedFlag = true;
                    }
                    continue;
                }
                if(!$translation){
                    $translation = $classMapper->createInstance('translation');
                    $translationsWereUpdatedFlag = true;
                }
                $translation->language_id = $languageId;
                $translation->translated_text = $value;
                $translation->text_id = $text->id;
                if(!$translation->creator_id){
                    $translation->creator_id = $currentUserId;
                }
                
                if ($translation->isDirty()) { // Only write in activities if something has changed
                    $translationsWereUpdatedFlag = true;
                }
                
                $translation->save();
            }

            if($translationsWereUpdatedFlag){
                $userActivityLogger->info("User {$currentUser->user_name} updated basic data for text with the technical name {$text->technical_name}.", ['type' => 'text_updated', 'user_id' => $currentUser->id]);
            }
            $arrayOfObjectToReceiveWithKeyAsKey[$key] = $text->id;

        }

        return $arrayOfObjectToReceiveWithKeyAsKey;
    }
    

    protected static function getValuesFromKeyThatStartsWithString($arrayOfKeys, $keyStartsWith): array
    {
        $valuesWithLanguageIdAsKey = array();
        foreach ($arrayOfKeys as $key => $value) {
            if (str_starts_with($key, $keyStartsWith."_")) {
                $explode = explode('_', $key);
                $valuesWithLanguageIdAsKey[end($explode)] = $value;
            }
        }
        return $valuesWithLanguageIdAsKey;
    }

    public static function setFormValues($form, $classMapper, $arrayOfKeys){
        //Set all the languages for the modal dialog
        $languages = LANGUAGE::all();
        $languageSelect = [];
        foreach ($languages as $language) {
            $languageSelect += [$language->id => $language->language_name];
        }
        foreach ($arrayOfKeys as $key => $value) {
            $form->setInputArgument($key, 'options', $languageSelect);
        }

        foreach ($arrayOfKeys as $key => &$value) {
            if(!isset($value)){
                continue;
            }
            //Set all the current translations for the modal dialog
            $translations = $classMapper->staticMethod('text', 'where', 'id', $value)->with('translations')->first()->translations;
            $translationSelect = [];
            if($translations){
                foreach ($translations as $translation) {
                    $translationSelect += [$key.'_'.$translation->language_id => $translation->translated_text];
                }
            }
            $form->setInputArgument($key, 'translations', $translationSelect);
        }
    }

    public static function saveTranslations($object, $objectName, $params, $classMapper, $currentUser, $arrayOfObjectWithKeyAsKey, $userActivityLogger, $updateTechnicalName){

        $textIds = TranslationsUtilities::addTranslations($params, $arrayOfObjectWithKeyAsKey, $classMapper, $currentUser->id, $objectName, $object, $userActivityLogger, $currentUser, $updateTechnicalName);

        foreach ($arrayOfObjectWithKeyAsKey as $key => $value) {
            $object->{$key} = $textIds[$key];
        }

        $object->save();
    }


    public static function deleteTranslations($object, $classMapper, $arrayOfObjectWithKeyAsKey, $userActivityLogger, $currentUser){
        foreach ($arrayOfObjectWithKeyAsKey as $key => $value) {
            $translations = $classMapper->staticMethod('translation', 'where', 'text_id', $object->{$key})->get();
            foreach ($translations as $translation) {
                $translation->delete();
            }
            $text = $classMapper->staticMethod('text', 'where', 'id', $object->{$key})->first();
            $userActivityLogger->info("User {$currentUser->user_name} deleted the text with the technical name {$text->technical_name}.", ['type' => 'text_deleted', 'user_id' => $currentUser->id]);
            $text->delete();
        }
    }

    public static function getTranslationTextBasedOnMainLanguage($textObject, $classMapper){
        $text = $classMapper->staticMethod('text', 'where', 'id', $textObject)
                ->first();

        $translations = $text->translations()->whereHas('language', function ($query) {
            $query->where('is_main_language', 1);
        })->get();

        $nameText = '';
        foreach ($translations as $translation) {
            $nameText .= $translation->translated_text . ' (' . $translation->language->language_name . ') ';
        }

        return $nameText;
    }

}