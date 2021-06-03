<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use ILIAS\DI\UIServices;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use Psr\Http\Message\RequestInterface;
use ilLanguage;
use srag\asq\Application\Service\UIService;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\UserInterface\Web\Form\Factory\QuestionDataFormFactory;
use srag\asq\UserInterface\Web\Form\Factory\IQuestionFormFactory;

/**
 * Class QuestionFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFormGUI
{
    use PathHelper;

    const VAR_REVISION_NAME = 'rev_name';
    const VAR_STATUS = 'status';
    const VAR_ANSWER_OPTIONS = 'answer_options';

    const VAR_LEGACY = 'legacy';

    const FORM_PART_LINK = 'form_part_link';

    const CMD_CREATE_REVISON = 'createRevision';

    /**
     * @var ilLanguage
     */
    protected $language;

    /**
     * @var UIServices
     */
    protected $ui;

    /**
     * @var QuestionDto
     */
    protected $initial_question;

    /**
     * @var QuestionDto
     */
    protected $post_question;

    /**
     * @var IQuestionFormFactory
     */
    protected $form_factory;

    /**
     * @var QuestionDataFormFactory
     */
    protected $question_data_factory;

    /**
     * @var array
     */
    protected $inputs;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Standard
     */
    protected $form;

    /**
     * @var UIService     */
    protected $asq_ui;

    /**
     * QuestionFormGUI constructor.
     *
     * @param QuestionDto $question
     */
    public function __construct(
        QuestionDto $question,
        string $action,
        ilLanguage $language,
        UIServices $ui,
        RequestInterface $request,
        UIService $asq_ui)
    {
        $this->language = $language;
        $this->ui = $ui;
        $this->request = $request;
        $this->asq_ui = $asq_ui;

        $this->initial_question = $question;

        $this->question_data_factory = new QuestionDataFormFactory($this->language, $this->ui, $this->asq_ui);

        $factory_class = $question->getType()->getFactoryClass();
        $this->form_factory = new $factory_class($this->language, $this->ui, $this->asq_ui);

        foreach ($this->form_factory->getScripts() as $script) {
            $this->ui->mainTemplate()->addJavaScript($script);
        }

        $this->initInputs($question);

        $this->showQuestionState($question);

        $this->form = $this->ui->factory()->input()->container()->form()->standard($action, $this->inputs);

        if ($this->request->getMethod() === 'POST')
        {
            $this->post_question = $this->readQuestionFromPost($question);
            $this->initInputs($this->post_question);
            $this->showQuestionState($this->post_question);
            $this->form = $this->ui->factory()->input()->container()->form()->standard($action, $this->inputs);
        }
    }

    /**
     * @return string
     */
    public function getHTML() : string
    {
        $panel = $this->ui->factory()->panel()->standard(
            $this->language->txt($this->initial_question->getType()->getTitleKey()),
            $this->form
        );

        return $this->ui->renderer()->render($panel);
    }

    /**
     * @param QuestionDto $question
     */
    private function initInputs(QuestionDto $question) : void
    {
        $this->inputs = $this->question_data_factory->getFormfields($question->getData());

        if (is_null($question->getPlayConfiguration())) {
            $question->setPlayConfiguration($this->form_factory->getDefaultPlayConfiguration());
        }

        $this->inputs = array_merge(
            $this->inputs,
            $this->form_factory->getFormfields($question->getPlayConfiguration())
        );

        if ($this->form_factory->hasAnswerOptions()) {
            $option_form = $this->asq_ui->getAsqTableInput(
                $this->language->txt('asq_label_answer'),
                $this->form_factory->getAnswerOptionDefinitions($question->getPlayConfiguration())
            );

            $option_form = $option_form
                ->withOptions($this->form_factory->getAnswerOptionConfiguration())
                ->withValue($this->form_factory->getAnswerOptionValues($question->getAnswerOptions()));

            $this->inputs[self::VAR_ANSWER_OPTIONS] = $option_form;
        }
    }

    /**
     * @param QuestionDto $question
     */
    private function showQuestionState(QuestionDto $question) : void
    {
        global $ASQDIC;

        if ($question->isComplete()) {
            $value = sprintf(
                'Complete. Max Points: %s Min Points: %s',
                $ASQDIC->asq()->answer()->getMaxScore($question),
                $ASQDIC->asq()->answer()->getMinScore($question)
            );
        } else {
            $value = 'Not Complete';
        }

        $state = $this->ui->factory()->input()->field()
                    ->text($this->language->txt('Status'))
                    ->withDisabled(true)
                    ->withValue($value);

        $this->inputs[] = $state;
    }

    /**
     * @return QuestionDto
     */
    public function getQuestion() : QuestionDto
    {
        return $this->post_question ?? $this->initial_question;
    }

    /**
     * @return QuestionDto
     */
    private function readQuestionFromPost(QuestionDto $original_question) : QuestionDto
    {
        $this->form = $this->form->withRequest($this->request);
        $postdata = $this->form->getData();

        $question = new QuestionDto();
        $question->setId($original_question->getId());
        $question->setType($original_question->getType());

        $question->setData($this->question_data_factory->readObjectFromPost($postdata));

        $question->setPlayConfiguration($this->form_factory->readQuestionPlayConfiguration($postdata));

        if ($this->form_factory->hasAnswerOptions()) {
            $question->setAnswerOptions($this->form_factory->readAnswerOptions($postdata[self::VAR_ANSWER_OPTIONS]));
        }

        $question = $this->form_factory->performQuestionPostProcessing($question);

        return $question;
    }

    public function checkInput() : bool
    {
        return true;
    }
}
