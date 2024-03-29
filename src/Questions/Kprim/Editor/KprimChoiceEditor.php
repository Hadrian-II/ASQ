<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim\Editor;

use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Feedback\Feedback;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\Generic\Data\ImageAndTextDisplayDefinition;
use srag\asq\Questions\Kprim\KprimChoiceAnswer;
use srag\asq\Questions\Kprim\Editor\Data\KprimChoiceEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class KprimChoiceEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class KprimChoiceEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    const STR_TRUE = "True";
    const STR_FALSE = "False";

    private ?array $answer_options;

    private KprimChoiceEditorConfiguration $configuration;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question, bool $is_disabled = false)
    {
        $this->answer_options = $question->getAnswerOptions();
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        if ($this->configuration->isShuffleAnswers()) {
            shuffle($this->answer_options);
        }

        parent::__construct($question, $is_disabled);
    }

    public function readAnswer() : ?AbstractValueObject
    {
        $answers = [];

        if (! is_null($this->answer_options)) {
        /** @var AnswerOption $answer_option */
        foreach ($this->answer_options as $answer_option) {
                $answer = $this->getPostValue($this->getPostName($answer_option->getOptionId()));

                if ($answer === self::STR_TRUE) {
                    $answers[$answer_option->getOptionId()] = true;
                } elseif ($answer === self::STR_FALSE) {
                    $answers[$answer_option->getOptionId()] = false;
                } else {
                    $answers[$answer_option->getOptionId()] = null;
                }
            }
        }

        foreach ($answers as $answer) {
            if ($answer !== null) {
                return new KprimChoiceAnswer($answers);
            }
        }

        return null;
    }

    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.KprimChoiceEditor.html', true, true);

        $tpl->setCurrentBlock('header');
        $tpl->setVariable('INSTRUCTIONTEXT', "You have to decide on every statement: [{$this->configuration->getLabelTrue()}] or [{$this->configuration->getLabelFalse()}]");
        $tpl->setVariable('OPTION_LABEL_TRUE', $this->configuration->getLabelTrue());
        $tpl->setVariable('OPTION_LABEL_FALSE', $this->configuration->getLabelFalse());
        $tpl->parseCurrentBlock();

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
                    sprintf(' style="width: %spx;" ', $this->configuration->getThumbnailSize())
                );
                $tpl->parseCurrentBlock();
            }

            if ($this->render_feedback
                && !is_null($this->answer)
                && !is_null($this->question->getFeedback())
                && !is_null($this->question->getFeedback()->getFeedbackForAnswerOption($answer_option->getOptionId()))
                && $this->showFeedbackForAnswerOption($answer_option)) {
                $tpl->setCurrentBlock('feedback');
                $tpl->setVariable('FEEDBACK', $this->question->getFeedback()->getFeedbackForAnswerOption($answer_option->getOptionId()));
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('answer_row');
            $tpl->setVariable('ANSWER_TEXT', $display_definition->getText());
            $tpl->setVariable('ANSWER_ID', $this->getPostName($answer_option->getOptionId()));
            $tpl->setVariable('VALUE_TRUE', self::STR_TRUE);
            $tpl->setVariable('VALUE_FALSE', self::STR_FALSE);
            $tpl->setVariable('DISABLED', $this->is_disabled ? 'disabled="disabled"' : '');

            if (!is_null($this->answer)) {
                $answer = $this->answer->getAnswerForId($answer_option->getOptionId());
                if ($answer === true) {
                    $tpl->setVariable('CHECKED_ANSWER_TRUE', 'checked="checked"');
                } elseif ($answer === false) {
                    $tpl->setVariable('CHECKED_ANSWER_FALSE', 'checked="checked"');
                }
            }

            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    private function showFeedBackForAnswerOption(AnswerOption $option) : bool
    {
        switch ($this->question->getFeedback()->getAnswerOptionFeedbackMode()) {
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_ALL:
                return true;
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CHECKED:
                return $this->answer->getAnswerForId($option->getOptionId());
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CORRECT:
                return $this->answer->getAnswerForId($option->getOptionId()) === $option->getScoringDefinition()->isCorrectValue();
            default:
                return false;
        }
    }

    private function getPostName(string $id) : string
    {
        return $this->question->getId()->toString() . $id;
    }

    public function isComplete() : bool
    {
        if (empty($this->configuration->getLabelFalse()) ||
            empty($this->configuration->getLabelTrue())) {
            return false;
        }

        foreach ($this->question->getAnswerOptions() as $option) {
            /** @var ImageAndTextDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getText())) {
                return false;
            }
        }

        return true;
    }
}
