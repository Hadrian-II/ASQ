<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Editor\MultipleChoice;

use ILIAS\DI\UIServices;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Feedback\Feedback;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Choice\MultipleChoiceAnswer;
use srag\asq\Questions\Choice\Editor\MultipleChoice\Data\MultipleChoiceEditorConfiguration;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class MultipleChoiceEditor
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    const VAR_MC_POSTNAME = 'multiple_choice_post_';

    /**
     * @var array
     */
    private $answer_options;

    /**
     * @var MultipleChoiceEditorConfiguration
     */
    private $configuration;

    /**
     * @var UIServices
     */
    private $ui;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        global $DIC;

        $this->answer_options = $question->getAnswerOptions()->getOptions();
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->ui = $DIC->ui();
        $this->ui->mainTemplate()->addCss($this->getBasePath(__DIR__) . 'css/asq.css');

        if ($this->configuration->isShuffleAnswers()) {
            shuffle($this->answer_options);
        }

        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.MultipleChoiceEditor.html', true, true);

        if ($this->isMultipleChoice()) {
            $tpl->setCurrentBlock('selection_limit_hint');
            $tpl->setVariable(
                'SELECTION_LIMIT_HINT',
                sprintf(
                    "Please select %d of %d answers!",
                    $this->configuration->getMaxAnswers(),
                    count($this->answer_options)
                )
            );

            $tpl->setVariable('MAX_ANSWERS', $this->configuration->getMaxAnswers());
            $tpl->parseCurrentBlock();
        }

        /** @var AnswerOption $answer_option */
        foreach ($this->answer_options as $answer_option) {
            /** @var ImageAndTextDisplayDefinition $display_definition */
            $display_definition = $answer_option->getDisplayDefinition();

            if (!empty($display_definition->getImage())) {
                $tpl->setCurrentBlock('answer_image');
                $tpl->setVariable('ANSWER_IMAGE_URL', $display_definition->getImage());
                $tpl->setVariable('ANSWER_IMAGE_ALT', $display_definition->getText());
                $tpl->setVariable('ANSWER_IMAGE_TITLE', $display_definition->getText());
                $tpl->setVariable(
                    'THUMB_SIZE',
                    is_null($this->configuration->getThumbnailSize()) ?
                        '' :
                        sprintf(' style="height: %spx;" ', $this->configuration->getThumbnailSize())
                );
                $tpl->parseCurrentBlock();
            }

            if ($this->render_feedback &&
                !is_null($this->answer) &&
                !is_null($this->question->getFeedback()) &&
                !is_null($this->question->getFeedback()->getFeedbackForAnswerOption($answer_option->getOptionId())) &&
                $this->showFeedbackForAnswerOption($answer_option)) {
                $tpl->setCurrentBlock('feedback');
                $tpl->setVariable('FEEDBACK', $this->question->getFeedback()
                    ->getFeedbackForAnswerOption($answer_option->getOptionId()));
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('answer_row');
            $tpl->setVariable('ANSWER_TEXT', $display_definition->getText());
            $tpl->setVariable('TYPE', $this->isMultipleChoice() ? "checkbox" : "radio");
            $tpl->setVariable('ANSWER_ID', $answer_option->getOptionId());
            $tpl->setVariable('POST_NAME', $this->getPostName($answer_option->getOptionId()));

            if (!is_null($this->answer) && in_array($answer_option->getOptionId(), $this->answer->getSelectedIds())) {
                $tpl->setVariable('CHECKED', 'checked="checked"');
            }

            $tpl->parseCurrentBlock();
        }

        $this->ui
            ->mainTemplate()
            ->addJavaScript($this->getBasePath(__DIR__) . 'src/Questions/Choice/Editor/MultipleChoice/MultipleChoiceEditor.js');

        return $tpl->get();
    }

    /**
     * @param AnswerOption $option
     * @return bool
     */
    private function showFeedBackForAnswerOption(AnswerOption $option) : bool
    {
        $is_selected = in_array($option->getOptionId(), $this->answer->getSelectedIds());

        switch ($this->question->getFeedback()->getAnswerOptionFeedbackMode()) {
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_ALL:
                return true;
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CHECKED:
                return $is_selected;
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CORRECT:
                $points_selected = $option->getScoringDefinition()->getPointsSelected();
                $points_unselected = $option->getScoringDefinition()->getPointsUnselected();
                return ($is_selected && ($points_selected > $points_unselected) || (!$is_selected && ($points_unselected > $points_selected)));
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    private function isMultipleChoice() : bool
    {
        return $this->configuration->getMaxAnswers() > 1;
    }

    /**
     * @param string $answer_id
     * @return string
     */
    private function getPostName(string $answer_id = null) : string
    {
        return $this->isMultipleChoice() ?
            self::VAR_MC_POSTNAME . $this->question->getId()->toString() . '_' . $answer_id :
            self::VAR_MC_POSTNAME . $this->question->getId()->toString();
    }

    public function readAnswer() : AbstractValueObject
    {
        if ($this->isMultipleChoice()) {
            $result = [];
            /** @var AnswerOption $answer_option */
            foreach ($this->answer_options as $answer_option) {
                $poststring = $this->getPostName($answer_option->getOptionId());
                if ($this->isPostVarSet($poststring)) {
                    $result[] = $this->getPostValue($poststring);
                }
            }
            $this->answer = MultipleChoiceAnswer::create($result);
        } else {
            $this->answer = MultipleChoiceAnswer::create([
                $this->getPostValue($this->getPostName())
            ]);
        }

        return $this->answer;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (is_null($this->question->getPlayConfiguration()
            ->getEditorConfiguration()
            ->getMaxAnswers())) {
            return false;
        }

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ImageAndTextDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getText())) {
                return false;
            }
        }

        return true;
    }
}
