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
        // Create question title text
        $questionTitleText = new Text([
            'technical_name' => 'Question_1_title',
            'creator_id' => '1'
        ]);
        $questionTitleText->save();

        $questionTitleTranslation = new Translation([
            'text_id' => $questionTitleText->id,
            'translated_text' => 'What is your current status?',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $questionTitleTranslation->save();

        // Create the question
        $question = new Question([
            'step_id' => $step->id,
            'title' => $questionTitleText->id,
            'type' => 'IMAGE',
            'axis_count' => 2,
            'is_multiple_choice' => false,
            'creator_id' => '1'
        ]);
        $question->save();

        // Create Answer 1
        $answer1TitleText = new Text([
            'technical_name' => 'Answer_1_title',
            'creator_id' => '1'
        ]);
        $answer1TitleText->save();

        $answer1Translation = new Translation([
            'text_id' => $answer1TitleText->id,
            'translated_text' => 'I am a student',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $answer1Translation->save();

        $answer1 = new Answer([
            'question_id' => $question->id,
            'title' => $answer1TitleText->id,
            'order' => 1,
            'image' => 'student_answer.jpg',
            'creator_id' => '1'
        ]);
        $answer1->save();

        // Create Answer 2
        $answer2TitleText = new Text([
            'technical_name' => 'Answer_2_title',
            'creator_id' => '1'
        ]);
        $answer2TitleText->save();

        $answer2Translation = new Translation([
            'text_id' => $answer2TitleText->id,
            'translated_text' => 'I am a professional',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $answer2Translation->save();

        $answer2 = new Answer([
            'question_id' => $question->id,
            'title' => $answer2TitleText->id,
            'order' => 2,
            'image' => 'professional_answer.jpg',
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
            'image' => 'step_tasks.jpg',
            'creator_id' => '1'
        ]);
        $step->save();

        return $step;
    }

    protected function seedTaskWithSubtask($step, $answers, $englishLanguage)
    {
        // Create task text
        $taskTextText = new Text([
            'technical_name' => 'Task_1_text',
            'creator_id' => '1'
        ]);
        $taskTextText->save();

        $taskTextTranslation = new Translation([
            'text_id' => $taskTextText->id,
            'translated_text' => 'Registration Process',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $taskTextTranslation->save();

        $taskDescriptionText = new Text([
            'technical_name' => 'Task_1_description',
            'creator_id' => '1'
        ]);
        $taskDescriptionText->save();

        $taskDescriptionTranslation = new Translation([
            'text_id' => $taskDescriptionText->id,
            'translated_text' => 'Complete your registration based on your status',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $taskDescriptionTranslation->save();

        // Create the task
        $task = new Task([
            'step_id' => $step->id,
            'text' => $taskTextText->id,
            'description' => $taskDescriptionText->id,
            'image_1' => 'registration_process.jpg',
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

        // Attach the answer to the logic
        $logicStudent->answers()->attach($answers[0]->id);

        // Create subtask for students - title
        $subtaskStudentTitleText = new Text([
            'technical_name' => 'SubTask_1_title',
            'creator_id' => '1'
        ]);
        $subtaskStudentTitleText->save();

        $subtaskStudentTitleTranslation = new Translation([
            'text_id' => $subtaskStudentTitleText->id,
            'translated_text' => 'Register at University',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskStudentTitleTranslation->save();

        // Create subtask for students - markdown
        $subtaskStudentMarkdownText = new Text([
            'technical_name' => 'SubTask_1_markdown',
            'creator_id' => '1'
        ]);
        $subtaskStudentMarkdownText->save();

        $subtaskStudentMarkdownTranslation = new Translation([
            'text_id' => $subtaskStudentMarkdownText->id,
            'translated_text' => 'Visit the university registration office to complete your enrollment',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskStudentMarkdownTranslation->save();

        $subtaskStudent = new SubTask([
            'task_id' => $task->id,
            'title' => $subtaskStudentTitleText->id,
            'markdown' => $subtaskStudentMarkdownText->id,
            'order' => 1,
            'creator_id' => '1'
        ]);
        $subtaskStudent->save();

        // Attach logic to subtask
        $subtaskStudent->logics()->attach($logicStudent->id, ['creator_id' => '1']);

        // Create logic for professional answer (answer 2)
        $logicProfessional = new Logic([
            'name' => 'Show professional subtask',
            'expression' => (string)$answers[1]->id,
            'creator_id' => '1'
        ]);
        $logicProfessional->save();

        // Attach the answer to the logic
        $logicProfessional->answers()->attach($answers[1]->id);

        // Create subtask for professionals - title
        $subtaskProfessionalTitleText = new Text([
            'technical_name' => 'SubTask_2_title',
            'creator_id' => '1'
        ]);
        $subtaskProfessionalTitleText->save();

        $subtaskProfessionalTitleTranslation = new Translation([
            'text_id' => $subtaskProfessionalTitleText->id,
            'translated_text' => 'Register at Job Center',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskProfessionalTitleTranslation->save();

        // Create subtask for professionals - markdown
        $subtaskProfessionalMarkdownText = new Text([
            'technical_name' => 'SubTask_2_markdown',
            'creator_id' => '1'
        ]);
        $subtaskProfessionalMarkdownText->save();

        $subtaskProfessionalMarkdownTranslation = new Translation([
            'text_id' => $subtaskProfessionalMarkdownText->id,
            'translated_text' => 'Visit the local job center to register as a job seeker',
            'language_id' => $englishLanguage->id,
            'creator_id' => '1'
        ]);
        $subtaskProfessionalMarkdownTranslation->save();

        $subtaskProfessional = new SubTask([
            'task_id' => $task->id,
            'title' => $subtaskProfessionalTitleText->id,
            'markdown' => $subtaskProfessionalMarkdownText->id,
            'order' => 2,
            'creator_id' => '1'
        ]);
        $subtaskProfessional->save();

        // Attach logic to subtask
        $subtaskProfessional->logics()->attach($logicProfessional->id, ['creator_id' => '1']);

        return $task;
    }
}
