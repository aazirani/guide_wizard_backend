<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Seeds;

use UserFrosting\Sprinkle\Account\Database\Models\Permission;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Core\Facades\Seeder;
use UserFrosting\Sprinkle\Core\Util\ClassMapper;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Setting;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Text;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\TextSetting;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Translation;

/**
 * Seeder for the basics.
 */
class GuideWizardBaseTextSettings extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $englishLanguage = $this->getEnglishLanguage();
        $this->seedBaseAppTranslations($englishLanguage);
    }

    protected function getEnglishLanguage()
    {
        $classMapper = $this
            ->ci->classMapper;
        return $classMapper->staticMethod('language', 'where', 'language_code', 'en')
            ->first();
    }

    protected function seedBaseAppTranslations($englishLanguage)
    {

        $keys = [
            "textSetting_next_question_button_text", "textSetting_next_step_button_text", "textSetting_close",
            "textSetting_read_more", "textSetting_task", "textSetting_tasks", "textSetting_question", "textSetting_questions",
            "textSetting_deadline", "textSetting_continue", "textSetting_no_internet_message", "textSetting_cant_reach_server",
            "textSetting_no_internet_button_text", "textSetting_update_is_necessary_message_text", "textSetting_steps_title",
            "textSetting_steps", "textSetting_in_progress", "textSetting_description", "textSetting_url_dialog_title",
            "textSetting_url_dialog_message", "textSetting_cancel", "textSetting_open_link", "textSetting_could_not_load",
            "textSetting_update_steps", "textSetting_getting_updates", "textSetting_next_stage_check_internet",
            "textSetting_update_finished", "textSetting_done_task"
        ];

        $values = [
            "Next question", "Next Step", "Close", "Read More", "Task", "Tasks", "Question", "Questions",
            "Deadline", "Continue", "No Internet Connection", "Can't Reach The Server!", "Try Again",
            "Steps require an update. Please verify your internet connection.", "Guide Wizard", "Steps",
            "In Progress", "Description", "Open URL", "Do you want to open", "Cancel", "Open Link", "Couldn't load",
            "Update steps", "Updating", "Check your Internet Connection and Try Again", "Updating Finished", "Done"
        ];

        $alreadyAddedTechnicalNames = Text::whereIn('technical_name', $keys)->pluck('technical_name')->toArray();

        $importData = [];
        for ($i = 0; $i < count($keys); $i++) {
            if (!in_array($keys[$i], $alreadyAddedTechnicalNames)) {
                $importData[] = [
                    'technical_name' => $keys[$i],
                'translated_text' => $values[$i],
                'creator_id' => '1',
                'language_id' => $englishLanguage->id
                ];
            }
        }

        GuideWizardBaseTextSettings::importTextsAndTextSettings($importData);
    }

    static function importTextsAndTextSettings($importData)
    {
        $createdTextSettings = []; // To store created textSettings

        foreach ($importData as $data) {
            // Create and save Text
            $textName = new Text([
                'technical_name' => $data['technical_name'],
                'creator_id' => $data['creator_id']
            ]);
            $textName->save();

            // Create and save Translation
            $textNameTranslation = new Translation([
                'text_id' => $textName->id,
                'translated_text' => $data['translated_text'],
                'language_id' => $data['language_id'],
                'creator_id' => $data['creator_id']
            ]);
            $textNameTranslation->save();

            // Create and save TextSetting
            $textSetting = new TextSetting([
                'title' => $textName->id,
                'creator_id' => $data['creator_id']
            ]);
            $textSetting->save();

            $createdTextSettings[] = $textSetting;
        }

        return $createdTextSettings;
    }


}
