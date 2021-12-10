<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ilTemplate;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Component\Feedback\FeedbackComponent;

/**
 * Class Renderer
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class Renderer extends AbstractComponentRenderer
{
    use PathHelper;

    private ResourceRegistry $registry;

    /**
     * @param QuestionComponent $input
     * @param RendererInterface $default_renderer
     * @return string
     */
    public function render(Component $input, RendererInterface $default_renderer) : string
    {
        /** @var $question QuestionDto */
        $question = $input->getQuestion();
        $editor_class = $question->getType()->getEditorClass();
        $editor = new $editor_class($question, $input->isDisabled());

        if (! is_null($input->getAnswer())) {
            $editor->setAnswer($input->getAnswer());

            if ($input->doesShowFeedback() && $question->hasFeedback()) {
                $editor->setRenderFeedback(true);
            }
        }

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.question_view.html', true, true);

        $tpl->setCurrentBlock('question');
        $tpl->setVariable('TITLE', $this->renderTitle($input->getTitleDisplay(), $question));
        $tpl->setVariable('QUESTION', $question->getData()->getQuestionText());
        $tpl->setVariable('EDITOR', $editor->generateHtml());
        $tpl->parseCurrentBlock();

        $additional_js = $editor->additionalJSFile();
        if ($additional_js !== null) {
            $this->registry->register($additional_js);
        }


        if ($input->doesShowFeedback() && $question->hasFeedback()) {
            $feedback_component = new FeedbackComponent($question, $input->getAnswer());
            $tpl->setCurrentBlock('feedback');
            $tpl->setVariable('QUESTION_FEEDBACK', $default_renderer->render($feedback_component));
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    private function renderTitle(int $display_mode, QuestionDto $question) : string
    {
        switch ($display_mode) {
            case QuestionComponent::SHOW_HEADER_WITH_POINTS:
                global $ASQDIC;
                $max = $ASQDIC->asq()->answer()->getMaxScore($question);
                return sprintf(
                    '%s (%s: %s)',
                    $question->getData()->getTitle(),
                    $this->txt('asq_header_points'),
                    $max
                );
            case QuestionComponent::SHOW_HEADER:
                return $question->getData()->getTitle();
            default:
                return '';
        }
    }

    public function registerResources(ResourceRegistry $registry) : void
    {
        parent::registerResources($registry);

        $registry->register($this->getBasePath(__DIR__) . 'js/question.js');

        $registry->register($this->getBasePath(__DIR__) . 'css/toastui-editor.css');
        $registry->register($this->getBasePath(__DIR__) . 'js/toastui-editor-all.min.js');

        $registry->register($this->getBasePath(__DIR__) . 'css/asq.css');

        $this->registry = $registry;
    }

    protected function getComponentInterfaceName() : array
    {
        return [QuestionComponent::class];
    }
}
