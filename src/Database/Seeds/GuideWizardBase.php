<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Seeds;

use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Core\Facades\Seeder;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Language;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Step;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Text;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Translation;

/**
 * Seeder for the basics.
 */
class GuideWizardBase extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // We require the default guide wizard roles and permissions
        Seeder::execute('GuideWizardPermissions');
        $englishLanguage = $this->seedEnglishLanguage();
        // We require the default settings texts
        Seeder::execute('GuideWizardBaseTextSettings');
        // Create the first step (Questions)
        $this->seedQuestionStep($englishLanguage);
    }

    protected function seedEnglishLanguage()
    {
        $english = new Language([
            'language_code' => 'en',
            'language_name' => 'English',
            'is_active' => '1',
            'is_main_language' => '1',
            'creator_id' => '1'
        ]);
        $english->save();
        return $english;
    }

    protected function seedQuestionStep($englishLanguage)
    {
        $textName = new Text([
            'technical_name' => 'Step_1_name',
            'creator_id' => '1'
        ]);
        $textName->save();

        $textNameTranslation = new Translation([
            'text_id' => $textName->id,
            'translated_text' => 'Questions',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $textNameTranslation->save();

        $textDescription = new Text([
            'technical_name' => 'Step_1_description',
            'creator_id' => '1'
        ]);
        $textDescription->save();

        $textDescriptionTranslation = new Translation([
            'text_id' => $textDescription->id,
            'translated_text' => 'Answer some questions',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $textDescriptionTranslation->save();

        $step = new Step([
            'name' => $textName->id,
            'description' => $textDescription->id,
            'order' => '1',
            'image' => 'step_questions.jpg',
            'creator_id' => '1'
        ]);
        $step->save();

        return $step;
    }

}
