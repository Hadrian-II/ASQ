<?php
declare(strict_types = 1);

namespace srag\asq\Questions\Choice\Editor\MultipleChoice;

use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
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
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class MultipleChoiceEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    const VAR_MC_POSTNAME = 'multiple_choice_post_';

    private array $answer_options;

    private MultipleChoiceEditorConfiguration $configuration;

    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->answer_options = $question->getAnswerOptions();
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        if ($this->configuration->isShuffleAnswers()) {
            shuffle($this->answer_options);
        }

        parent::__construct($question, $is_disabled);
    }

    public function additionalJSFile() : ?string
    {
        return $this->getBasePath(__DIR__) . 'src/Questions/Choice/Editor/MultipleChoice/MultipleChoiceEditor.js';
    }

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

            if ($this->configuration->isSingleLine()) {
                if (!empty($display_definition->getImage()))
                {
                    $tpl->setCurrentBlock('answer_image');
                    $tpl->setVariable('ANSWER_IMAGE_URL', $display_definition->getImage());
                    $tpl->setVariable('ANSWER_IMAGE_ALT', $display_definition->getText());
                    $tpl->setVariable('ANSWER_IMAGE_TITLE', $display_definition->getText());
                    $tpl->setVariable(
                        'THUMB_SIZE',
                        is_null($this->configuration->getThumbnailSize()) ?
                        '' :
                        sprintf(' style="width: %spx;" ', $this->configuration->getThumbnailSize())
                        );
                    $tpl->parseCurrentBlock();
                }

                $tpl->setCurrentBlock('image_text');
                $tpl->setVariable('ANSWER_TEXT', $display_definition->getText());
                $tpl->parseCurrentBlock();
            }
            else
            {
                $tpl->setCurrentBlock('markup');
                $tpl->setVariable('ANSWER_TEXT', $display_definition->getText());
                $tpl->parseCurrentBlock();
            }

            if ($this->render_feedback &&
                !is_null($this->answer) &&
                !is_null($this->question->getFeedback()) &&
                !is_null($this->question->getFeedback()->getFeedbackForAnswerOption($answer_option->getOptionId())) &&
                $this->showFeedbackForAnswerOption($answer_option))
            {
                $tpl->setCurrentBlock('feedback');
                $tpl->setVariable('FEEDBACK', $this->question->getFeedback()
                    ->getFeedbackForAnswerOption($answer_option->getOptionId()));
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('answer_row');
            $tpl->setVariable('TYPE', $this->isMultipleChoice() ? "checkbox" : "radio");
            $tpl->setVariable('ANSWER_ID', $answer_option->getOptionId());
            $tpl->setVariable('POST_NAME', $this->getPostName($answer_option->getOptionId()));

            if ($this->is_disabled) {
                $tpl->setVariable('DISABLED', 'disabled="disabled"');
            }

            if (!is_null($this->answer) && in_array($answer_option->getOptionId(), $this->answer->getSelectedIds())) {
                $tpl->setVariable('CHECKED', 'checked="checked"');
            }

            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

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
                return (($is_selected && ($points_selected > $points_unselected)) || (!$is_selected && ($points_unselected > $points_selected)));
            default:
                return false;
        }
    }

    private function isMultipleChoice() : bool
    {
        return $this->configuration->getMaxAnswers() > 1;
    }

    private function getPostName(string $answer_id = null) : string
    {
        return $this->isMultipleChoice() ?
            self::VAR_MC_POSTNAME . $this->question->getId()->toString() . '_' . $answer_id :
            self::VAR_MC_POSTNAME . $this->question->getId()->toString();
    }

    public function readAnswer() : ?AbstractValueObject
    {
        if ($this->isMultipleChoice()) {
            $this->answer = $this->readMultiChoice();
        } else {
            $this->answer = $this->readSingleChoice();
        }

        return $this->answer;
    }

    private function readMultiChoice() : ?AbstractValueObject
    {
        $result = [];
        /** @var AnswerOption $answer_option */
        foreach ($this->answer_options as $answer_option) {
            $poststring = $this->getPostName($answer_option->getOptionId());
            if ($this->isPostVarSet($poststring)) {
                $result[] = $this->getPostValue($poststring);
            }
        }

        if (count($result[]) === null) {
            return null;
        }

        return new MultipleChoiceAnswer($result);
    }

    private function readSingleChoice(): ?MultipleChoiceAnswer
    {
        if (!$this->isPostVarSet($this->getPostName())) {
            return null;
        }

        return new MultipleChoiceAnswer([
            $this->getPostValue($this->getPostName())
        ]);
    }

    public function isComplete() : bool
    {
        if (is_null($this->question->getPlayConfiguration()
            ->getEditorConfiguration()
            ->getMaxAnswers())) {
            return false;
        }

        if (is_null($this->question->getAnswerOptions())) {
            return false;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var ImageAndTextDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if ($option_config->getText() === null ||
                strlen($option_config->getText()) === 0) {
                return false;
            }
        }

        return true;
    }
}
