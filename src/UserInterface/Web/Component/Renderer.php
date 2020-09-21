<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component;

use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ilTemplate;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
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

    //TODO stole method from Input/Field/Renderer, see to integrate this into input field renderer
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
        }

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.question_view.html', true, true);

        $tpl->setCurrentBlock('question');
        $tpl->setVariable('TITLE', $question->getData()->getTitle());
        $tpl->setVariable('QUESTION', $question->getData()->getQuestionText());
        $tpl->setVariable('EDITOR', $editor->generateHtml());
        $tpl->parseCurrentBlock();

        if ($input->doesShowFeedback() && $question->hasFeedback()) {
            $feedback_component = new FeedbackComponent($question, $input->getAnswer());
            $tpl->setCurrentBlock('feedback');
            $tpl->setVariable('QUESTION_FEEDBACK', $default_renderer->render($feedback_component));
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    protected function getComponentInterfaceName()
    {
        return [QuestionComponent::class];
    }
}
