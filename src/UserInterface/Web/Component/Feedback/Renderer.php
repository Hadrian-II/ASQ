<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Component\Scoring\ScoringComponent;

/**
 * Class Renderer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Renderer extends AbstractComponentRenderer
{
    use PathHelper;

    const CSS_CLASS_FEEDBACK_TYPE_CORRECT = 'ilc_qfeedr_FeedbackRight';
    const CSS_CLASS_FEEDBACK_TYPE_WRONG = 'ilc_qfeedw_FeedbackWrong';

    public function render(Component $component, RendererInterface $default_renderer) : string
    {
        switch (get_class($component)) {
            case FeedbackComponent::class:
                return $this->renderFeedback($component, $default_renderer);
            case AnswerFeedbackComponent::class:
                return $this->renderAnswerFeedback($component);
        }
    }

    private function renderFeedback(FeedbackComponent $component, RendererInterface $default_renderer) : string
    {
        $scoring_component = new ScoringComponent($component->getQuestion(), $component->getAnswer());
        $answer_feedback_component = new AnswerFeedbackComponent($component->getQuestion(), $component->getAnswer());

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.feedback.html', true, true);

        $tpl->setCurrentBlock('feedback_header');
        $tpl->setVariable('FEEDBACK_HEADER', $this->txt('asq_answer_feedback_header'));
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('answer_feedback');
        $tpl->setVariable('ANSWER_FEEDBACK', $default_renderer->render($answer_feedback_component));
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable('ANSWER_SCORING', $default_renderer->render($scoring_component));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    private function renderAnswerFeedback(AnswerFeedbackComponent $component) : string
    {
        $scoring_class = $component->getQuestion()->getType()->getScoringClass();
        $scoring = new $scoring_class($component->getQuestion());

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.answer_feedback.html', true, true);

        include_once("./Services/Style/Content/classes/class.ilObjStyleSheet.php");

        $tpl->setCurrentBlock('answer_feedback');

        $feedback_type = $scoring->getAnswerFeedbackType($component->getAnswer());

        if ($feedback_type === AbstractScoring::ANSWER_CORRECT) {
            $answer_feedback = $component->getQuestion()->getFeedback()->getAnswerCorrectFeedback();
            $answer_feedback_css_class = self::CSS_CLASS_FEEDBACK_TYPE_CORRECT;
        } elseif ($feedback_type === AbstractScoring::ANSWER_INCORRECT) {
            $answer_feedback = $component->getQuestion()->getFeedback()->getAnswerWrongFeedback();
            $answer_feedback_css_class = self::CSS_CLASS_FEEDBACK_TYPE_WRONG;
        }

        $tpl->setVariable('ANSWER_FEEDBACK', $answer_feedback);
        $tpl->setVariable('ILC_FB_CSS_CLASS', $answer_feedback_css_class);

        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    protected function getComponentInterfaceName() : array
    {
        return [
            FeedbackComponent::class,
            AnswerFeedbackComponent::class
        ];
    }
}
