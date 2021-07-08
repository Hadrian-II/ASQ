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
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Renderer extends AbstractComponentRenderer
{
    use PathHelper;

    /**
     * @var ResourceRegistry
     */
    private $registry;

    /**
     * {@inheritDoc}
     * @see \ILIAS\UI\Implementation\Render\ComponentRenderer::render()
     */
    public function render(Component $input, RendererInterface $default_renderer) : string
    {
        /** @var $question QuestionDto */
        $question = $input->getQuestion();
        $editor_class = $question->getType()->getEditorClass();
        $editor = new $editor_class($question);

        if (! is_null($input->getAnswer())) {
            $editor->setAnswer($input->getAnswer());

            if ($input->doesShowFeedback() && $question->hasFeedback()) {
                $editor->setRenderFeedback(true);
            }
        }

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.question_view.html', true, true);

        $tpl->setCurrentBlock('question');
        $tpl->setVariable('TITLE', $question->getData()->getTitle());
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

    /**
     * @inheritdoc
     */
    public function registerResources(ResourceRegistry $registry)
    {
        parent::registerResources($registry);

        $registry->register($this->getBasePath(__DIR__) . 'js/question.js');

        $registry->register('src/UI/templates/default/Markdown/toastui-editor.css');
        $registry->register('src/UI/templates/js/Markdown/toastui-editor-all.js');

        $registry->register($this->getBasePath(__DIR__) . 'css/asq.css');

        $this->registry = $registry;
    }

    protected function getComponentInterfaceName()
    {
        return [QuestionComponent::class];
    }
}
