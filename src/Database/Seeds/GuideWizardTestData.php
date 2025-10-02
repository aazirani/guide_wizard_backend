<?php

namespace UserFrosting\Sprinkle\GuideWizard\Database\Seeds;

use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Answer;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Language;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Logic;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Question;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Step;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\SubTask;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Task;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Text;
use UserFrosting\Sprinkle\GuideWizard\Database\Models\Translation;

/**
 * Seeder for test data demonstrating Guide Wizard functionality.
 */
class GuideWizardTestData extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Get the English language (assuming it was seeded by GuideWizardBase)
        $englishLanguage = Language::where('language_code', 'en')->first();

        if (!$englishLanguage) {
            throw new \Exception('English language not found. Please run GuideWizardBase seeder first.');
        }

        // Get the first step (Questions) created by GuideWizardBase
        $step1 = Step::where('order', '1')->first();

        if (!$step1) {
            throw new \Exception('Question step not found. Please run GuideWizardBase seeder first.');
        }

        $answers = $this->seedQuestionWithAnswers($step1, $englishLanguage);
        $step2 = $this->seedTaskStep($englishLanguage);
        $this->seedTaskWithSubtask($step2, $answers, $englishLanguage);
    }

    protected function seedQuestionWithAnswers($step, $englishLanguage)
    {
        // Create question text
        $questionTextName = new Text([
            'technical_name' => 'Question_1_text',
            'creator_id' => '1'
        ]);
        $questionTextName->save();

        $questionTranslation = new Translation([
            'text_id' => $questionTextName->id,
            'translated_text' => 'What is your current status?',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $questionTranslation->save();

        // Create the question
        $question = new Question([
            'step_id' => $step->id,
            'text' => $questionTextName->id,
            'order' => '1',
            'question_type' => 'single_choice',
            'creator_id' => '1'
        ]);
        $question->save();

        // Create Answer 1
        $answer1TextName = new Text([
            'technical_name' => 'Answer_1_text',
            'creator_id' => '1'
        ]);
        $answer1TextName->save();

        $answer1Translation = new Translation([
            'text_id' => $answer1TextName->id,
            'translated_text' => 'I am a student',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $answer1Translation->save();

        $answer1 = new Answer([
            'question_id' => $question->id,
            'text' => $answer1TextName->id,
            'order' => '1',
            'creator_id' => '1'
        ]);
        $answer1->save();

        // Create Answer 2
        $answer2TextName = new Text([
            'technical_name' => 'Answer_2_text',
            'creator_id' => '1'
        ]);
        $answer2TextName->save();

        $answer2Translation = new Translation([
            'text_id' => $answer2TextName->id,
            'translated_text' => 'I am a professional',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $answer2Translation->save();

        $answer2 = new Answer([
            'question_id' => $question->id,
            'text' => $answer2TextName->id,
            'order' => '2',
            'creator_id' => '1'
        ]);
        $answer2->save();

        return [$answer1, $answer2];
    }

    protected function seedTaskStep($englishLanguage)
    {
        $textName = new Text([
            'technical_name' => 'Step_2_name',
            'creator_id' => '1'
        ]);
        $textName->save();

        $textNameTranslation = new Translation([
            'text_id' => $textName->id,
            'translated_text' => 'Your Tasks',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $textNameTranslation->save();

        $textDescription = new Text([
            'technical_name' => 'Step_2_description',
            'creator_id' => '1'
        ]);
        $textDescription->save();

        $textDescriptionTranslation = new Translation([
            'text_id' => $textDescription->id,
            'translated_text' => 'Complete these tasks based on your profile',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $textDescriptionTranslation->save();

        $step = new Step([
            'name' => $textName->id,
            'description' => $textDescription->id,
            'order' => '2',
            'creator_id' => '1'
        ]);
        $step->save();

        return $step;
    }

    protected function seedTaskWithSubtask($step, $answers, $englishLanguage)
    {
        // Create task text
        $taskTextName = new Text([
            'technical_name' => 'Task_1_name',
            'creator_id' => '1'
        ]);
        $taskTextName->save();

        $taskNameTranslation = new Translation([
            'text_id' => $taskTextName->id,
            'translated_text' => 'Registration Process',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $taskNameTranslation->save();

        $taskTextDescription = new Text([
            'technical_name' => 'Task_1_description',
            'creator_id' => '1'
        ]);
        $taskTextDescription->save();

        $taskDescriptionTranslation = new Translation([
            'text_id' => $taskTextDescription->id,
            'translated_text' => 'Complete your registration based on your status',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $taskDescriptionTranslation->save();

        // Create the task
        $task = new Task([
            'step_id' => $step->id,
            'name' => $taskTextName->id,
            'description' => $taskTextDescription->id,
            'order' => '1',
            'creator_id' => '1'
        ]);
        $task->save();

        // Create logic for student answer (answer 1)
        $logicStudent = new Logic([
            'name' => 'Show student subtask',
            'expression' => (string)$answers[0]->id,
            'creator_id' => '1'
        ]);
        $logicStudent->save();

        // Create subtask for students
        $subtaskStudentTextName = new Text([
            'technical_name' => 'SubTask_1_name',
            'creator_id' => '1'
        ]);
        $subtaskStudentTextName->save();

        $subtaskStudentNameTranslation = new Translation([
            'text_id' => $subtaskStudentTextName->id,
            'translated_text' => 'Register at University',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskStudentNameTranslation->save();

        $subtaskStudentTextDescription = new Text([
            'technical_name' => 'SubTask_1_description',
            'creator_id' => '1'
        ]);
        $subtaskStudentTextDescription->save();

        $subtaskStudentDescriptionTranslation = new Translation([
            'text_id' => $subtaskStudentTextDescription->id,
            'translated_text' => 'Visit the university registration office to complete your enrollment',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskStudentDescriptionTranslation->save();

        $subtaskStudent = new SubTask([
            'task_id' => $task->id,
            'name' => $subtaskStudentTextName->id,
            'description' => $subtaskStudentTextDescription->id,
            'logic_id' => $logicStudent->id,
            'order' => '1',
            'creator_id' => '1'
        ]);
        $subtaskStudent->save();

        // Create logic for professional answer (answer 2)
        $logicProfessional = new Logic([
            'name' => 'Show professional subtask',
            'expression' => (string)$answers[1]->id,
            'creator_id' => '1'
        ]);
        $logicProfessional->save();

        // Create subtask for professionals
        $subtaskProfessionalTextName = new Text([
            'technical_name' => 'SubTask_2_name',
            'creator_id' => '1'
        ]);
        $subtaskProfessionalTextName->save();

        $subtaskProfessionalNameTranslation = new Translation([
            'text_id' => $subtaskProfessionalTextName->id,
            'translated_text' => 'Register at Job Center',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskProfessionalNameTranslation->save();

        $subtaskProfessionalTextDescription = new Text([
            'technical_name' => 'SubTask_2_description',
            'creator_id' => '1'
        ]);
        $subtaskProfessionalTextDescription->save();

        $subtaskProfessionalDescriptionTranslation = new Translation([
            'text_id' => $subtaskProfessionalTextDescription->id,
            'translated_text' => 'Visit the local job center to register as a job seeker',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskProfessionalDescriptionTranslation->save();

        $subtaskProfessional = new SubTask([
            'task_id' => $task->id,
            'name' => $subtaskProfessionalTextName->id,
            'description' => $subtaskProfessionalTextDescription->id,
            'logic_id' => $logicProfessional->id,
            'order' => '2',
            'creator_id' => '1'
        ]);
        $subtaskProfessional->save();

        return $task;
    }
}
