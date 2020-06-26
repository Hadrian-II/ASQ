<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback\Form;

use ilFormSectionHeaderGUI;
use ilObjAdvancedEditing;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTextAreaInputGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Feedback;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;

/**
 * Class QuestionFeedbackFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class QuestionFeedbackFormGUI extends \ilPropertyFormGUI
{
    use InputHandlingTrait;

    const VAR_ANSWER_FEEDBACK_CORRECT = 'answer_feedback_correct';
    const VAR_ANSWER_FEEDBACK_WRONG = 'answer_feedback_wrong';
    const VAR_ANSWER_OPTION_FEEDBACK_MODE = 'answer_option_feedback_mode';
    const VAR_FEEDBACK_FOR_ANSWER = "feedback_for_answer";

    /**
     * @var QuestionDto
     */
    protected $question_dto;
    /**
     * @var Feedback
     */
    protected $feedback;

    /**
     * @param QuestionDto $question_dto
     * @param Feedback $feedback
     * @param AnswerOptions $answer_options
     */
    public function __construct(QuestionDto $question_dto)
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        parent::__construct();


        $this->question_dto = $question_dto;
        $this->feedback = $question_dto->getFeedback();

        $this->setTitle($DIC->language()->txt('asq_feedback_form_title'));

        $this->initForm();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->setValuesByPost();
        }
    }

    protected function initForm()
    {
        global $DIC;
        /* @var \ILIAS\DI\Container $DIC */

        $feedback_correct = new ilTextAreaInputGUI($DIC->language()->txt('asq_input_feedback_correct'),  self::VAR_ANSWER_FEEDBACK_CORRECT);
        $feedback_correct->setUseRte(true);
        $feedback_correct->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
        $this->addItem($feedback_correct);

        $feedback_wrong = new ilTextAreaInputGUI($DIC->language()->txt('asq_input_feedback_wrong'), self::VAR_ANSWER_FEEDBACK_WRONG);
        $feedback_wrong->setUseRte(true);
        $feedback_wrong->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
        $this->addItem($feedback_wrong);



        if (!is_null($this->feedback)) {
            $feedback_correct->setValue($this->feedback->getAnswerCorrectFeedback());
            $feedback_wrong->setValue($this->feedback->getAnswerWrongFeedback());
        }

        if ($this->question_dto->hasAnswerOptions()) {
            $header = new ilFormSectionHeaderGUI();
            $header->setTitle($DIC->language()->txt('asq_header_feedback_answers'));
            $this->addItem($header);

            $feedback_setting = new ilRadioGroupInputGUI($DIC->language()->txt('asq_label_feedback_setting'),  self::VAR_ANSWER_OPTION_FEEDBACK_MODE);
            $feedback_setting->addOption(new ilRadioOption($DIC->language()->txt('asq_option_feedback_all'), Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_ALL));
            $feedback_setting->addOption(new ilRadioOption($DIC->language()->txt('asq_option_feedback_checked'), Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CHECKED));
            $feedback_setting->addOption(new ilRadioOption($DIC->language()->txt('asq_option_feedback_correct'), Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CORRECT));
            $feedback_setting->setRequired(true);
            $this->addItem($feedback_setting);

            if (!is_null($this->feedback)) {
                $feedback_setting->setValue($this->feedback->getAnswerOptionFeedbackMode());
            }

            foreach ($this->question_dto->getAnswerOptions()->getOptions() as $answer_option) {
                /** @var AnswerOption $answer_option */
                $field = new ilTextAreaInputGUI($answer_option->getOptionId(), $this->getPostKey($answer_option));
                $field->setUseRte(true);
                $field->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));

                if (!is_null($this->feedback) && $this->feedback->hasAnswerOptionFeedback(($answer_option->getOptionId()))) {
                    $field->setValue($this->feedback->getFeedbackForAnswerOption($answer_option->getOptionId()));
                }

                $this->addItem($field);
            }
        }
    }


    /**
     * @param AnswerOptions $answer_options
     *
     * @return Feedback
     */
    public function getFeedbackFromPost() {

        $feedback_correct = $this->readString(self::VAR_ANSWER_FEEDBACK_CORRECT);
        $feedback_wrong = $this->readString(self::VAR_ANSWER_FEEDBACK_WRONG);
        $answer_option_feedback_mode = $this->readInt(self::VAR_ANSWER_OPTION_FEEDBACK_MODE);

        $answer_option_feedbacks = [];

        if ($this->question_dto->hasAnswerOptions()) {
            foreach ($this->question_dto->getAnswerOptions()->getOptions() as $answer_option) {
                /** @var AnswerOption $answer_option */
                $post_key = $this->getPostKey($answer_option);
                $post_val = $this->readString($post_key);

                if(!empty($post_val)) {
                    $answer_option_feedbacks[$answer_option->getOptionId()] = $post_val;
                }
            }
        }

        return Feedback::create($feedback_correct, $feedback_wrong, $answer_option_feedback_mode, $answer_option_feedbacks);
    }

    private function getPostKey(AnswerOption $answer_option) {
        return self::VAR_FEEDBACK_FOR_ANSWER . $answer_option->getOptionId();
    }
}
