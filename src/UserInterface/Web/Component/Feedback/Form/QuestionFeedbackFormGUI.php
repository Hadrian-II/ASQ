<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback\Form;

use ILIAS\DI\UIServices;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use Psr\Http\Message\RequestInterface;
use ilLanguage;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Feedback\Feedback;
use srag\asq\UserInterface\Web\Form\InputHandlingTrait;
use function PHPUnit\Framework\isNull;

/**
 * Class QuestionFeedbackFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class QuestionFeedbackFormGUI
{
    use InputHandlingTrait;

    const VAR_ANSWER_FEEDBACK_CORRECT = 'answer_feedback_correct';
    const VAR_ANSWER_FEEDBACK_WRONG = 'answer_feedback_wrong';
    const VAR_ANSWER_OPTION_FEEDBACK = 'answer_option_feedback';
    const VAR_ANSWER_OPTION_FEEDBACK_MODE = 'answer_option_feedback_mode';
    const VAR_FEEDBACK_FOR_ANSWER = "feedback_for_answer";

    // Essay scorings are ints, but need to be sent as string are then as indexes in arrays and later used to compare with value
    // PHP somehow autocasts "1" array index to int which makes $value === "1" fail as value is now an int
    // Add some string to value to prevent autocast
    const USELESS_PREFIX = 'x';

    /**
     * @var QuestionDto
     */
    private $question_dto;

    /**
     * @var Feedback
     */
    private $feedback;

    /**
     * @var ilLanguage
     */
    private $language;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Standard
     */
    private $form;

    /**
     * @param QuestionDto $question_dto
     * @param ilLanguage $language
     */
    public function __construct(QuestionDto $question_dto, string $action, ilLanguage $language, UIServices $ui, RequestInterface $request)
    {
        $this->language = $language;
        $this->ui = $ui;
        $this->request = $request;

        $this->question_dto = $question_dto;
        $this->feedback = $question_dto->getFeedback();

        $this->form = $this->ui->factory()->input()->container()->form()->standard($action, $this->generateFormFields());
    }

    /**
     * @return array
     */
    protected function generateFormFields() : array
    {
        $fields = [];

        $feedback_correct = $this->ui->factory()->input()->field()->textarea($this->language->txt('asq_input_feedback_correct'));

        $feedback_wrong = $this->ui->factory()->input()->field()->textarea($this->language->txt('asq_input_feedback_wrong'));

        if (!is_null($this->feedback)) {
            $feedback_correct = $feedback_correct->withValue($this->feedback->getAnswerCorrectFeedback() ?? '');
            $feedback_wrong = $feedback_wrong->withValue($this->feedback->getAnswerWrongFeedback() ?? '');
        }

        $fields[self::VAR_ANSWER_FEEDBACK_CORRECT] = $feedback_correct;
        $fields[self::VAR_ANSWER_FEEDBACK_WRONG] = $feedback_wrong;


        if ($this->question_dto->hasAnswerOptions()) {
            $feedback_setting =
                $this->ui->factory()->input()->field()->radio($this->language->txt('asq_label_feedback_setting'))
                    ->withOption(
                        self::USELESS_PREFIX . strval(Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_ALL),
                        $this->language->txt('asq_option_feedback_all')
                    )
                    ->withOption(
                        self::USELESS_PREFIX . strval(Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CHECKED),
                        $this->language->txt('asq_option_feedback_checked')
                    )
                    ->withOption(
                        self::USELESS_PREFIX . strval(Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CORRECT),
                        $this->language->txt('asq_option_feedback_correct')
                    );

            if (!is_null($this->feedback) && !is_null($this->feedback->getAnswerOptionFeedbackMode())) {
                $feedback_setting = $feedback_setting->withValue(self::USELESS_PREFIX . strval($this->feedback->getAnswerOptionFeedbackMode()));
            }

            $fields[self::VAR_ANSWER_OPTION_FEEDBACK_MODE] = $feedback_setting;

            $answer_fields = [];
            foreach ($this->question_dto->getAnswerOptions() as $answer_option) {
                /** @var AnswerOption $answer_option */
                $field = $this->ui->factory()->input()->field()->textarea($answer_option->getOptionId());

                if (!is_null($this->feedback) && $this->feedback->hasAnswerOptionFeedback(($answer_option->getOptionId()))) {
                    $field = $field->withValue($this->feedback->getFeedbackForAnswerOption($answer_option->getOptionId()));
                }

                $answer_fields[$this->getPostKey($answer_option)] = $field;
            }

            $section = $this->ui->factory()->input()->field()->section($answer_fields, $this->language->txt('asq_header_feedback_answers'));
            $fields[self::VAR_ANSWER_OPTION_FEEDBACK] = $section;
        }

        return $fields;
    }

    /**
     * @return string
     */
    public function getHTML() : string
    {
        $panel = $this->ui->factory()->panel()->standard(
            $this->language->txt('asq_feedback_form_title'),
            $this->form
        );

        return $this->ui->renderer()->render($panel);
    }

    /**
     * @return Feedback
     */
    public function getFeedbackFromPost() : Feedback
    {
        $this->form = $this->form->withRequest($this->request);
        $postdata = $this->form->getData();

        $feedback_correct = $this->readString($postdata[self::VAR_ANSWER_FEEDBACK_CORRECT]);
        $feedback_wrong = $this->readString($postdata[self::VAR_ANSWER_FEEDBACK_WRONG]);
        if (! empty($postdata[self::VAR_ANSWER_OPTION_FEEDBACK_MODE])) {
            $answer_option_feedback_mode = $this->readInt(substr($postdata[self::VAR_ANSWER_OPTION_FEEDBACK_MODE], strlen(self::USELESS_PREFIX)));
        }
        $answer_option_feedbacks = [];

        if ($this->question_dto->hasAnswerOptions()) {
            foreach ($this->question_dto->getAnswerOptions() as $answer_option) {
                /** @var AnswerOption $answer_option */
                $post_key = $this->getPostKey($answer_option);
                $post_val = $this->readString($postdata[self::VAR_ANSWER_OPTION_FEEDBACK][$post_key]);

                if (!empty($post_val)) {
                    $answer_option_feedbacks[$answer_option->getOptionId()] = $post_val;
                }
            }
        }

        return new Feedback($feedback_correct, $feedback_wrong, $answer_option_feedback_mode, $answer_option_feedbacks);
    }

    /**
     * @param AnswerOption $answer_option
     * @return string
     */
    private function getPostKey(AnswerOption $answer_option) : string
    {
        return self::VAR_FEEDBACK_FOR_ANSWER . $answer_option->getOptionId();
    }
}
