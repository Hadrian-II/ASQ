<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use ILIAS\DI\UIServices;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use Psr\Http\Message\RequestInterface;
use ilFormSectionHeaderGUI;
use ilLanguage;
use ilTextInputGUI;
use srag\asq\AsqGateway;
use srag\asq\PathHelper;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Form\Factory\QuestionDataFormFactory;
use srag\asq\UserInterface\Web\Form\Factory\QuestionFormFactory;

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
     * @var QuestionFormFactory
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
     * QuestionFormGUI constructor.
     *
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question, string $action, ilLanguage $language, UIServices $ui, RequestInterface $request)
    {
        $this->language = $language;
        $this->ui = $ui;
        $this->request = $request;

        $this->initial_question = $question;

        $this->question_data_factory = new QuestionDataFormFactory($this->language, $this->ui);

        $factory_class = $question->getType()->getFactoryClass();
        $this->form_factory = new $factory_class($this->language, $this->ui);

        $this->initInputs($question);

        foreach ($this->form_factory->getScripts() as $script) {
            $this->ui->mainTemplate()->addJavaScript($script);
        }

        $this->showQuestionState($this->post_question ?? $question);

        $this->addRevisionForm();

        $this->form = $this->ui->factory()->input()->container()->form()->standard($action, $this->inputs);

        if ($this->request->getMethod() === 'POST') {
            $this->post_question = $this->readQuestionFromPost($question);
        }
    }

    /**
     * @return string
     */
    public function getHTML() : string
    {
        $panel = $this->ui->factory()->panel()->standard(
            $this->language->txt($this->initial_question->getType()->getTitleKey()),
            $this->form);

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
            $this->form_factory->getFormfields($question->getPlayConfiguration()));

        if ($this->form_factory->hasAnswerOptions())
        {
            $option_form = AsqGateway::get()->ui()->getAsqTableInput(
                $this->language->txt('asq_label_answer'),
                $this->form_factory->getAnswerOptionDefinitions($question->getPlayConfiguration()));

            $option_form = $option_form
                ->withOptions($this->form_factory->getAnswerOptionConfiguration())
                ->withValue($this->form_factory->getAnswerOptionValues($question->getAnswerOptions()));

            $this->inputs[self::VAR_ANSWER_OPTIONS] = $option_form;
        }

        $this->ui->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'js/AssessmentQuestionAuthoring.js');
    }

    /**
     * @param QuestionDto $question
     */
    private function showQuestionState(QuestionDto $question) : void
    {
        if ($question->isComplete()) {
            $value = sprintf(
                'Complete. Max Points: %s Min Points: %s',
                AsqGateway::get()->answer()->getMaxScore($question),
                AsqGateway::get()->answer()->getMinScore($question)
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

    private function addRevisionForm() : void
    {
        return;

        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle($this->language->txt('asq_version_title'));
        $this->addItem($spacer);

        $revision = new ilTextInputGUI($this->language->txt('asq_label_new_revision'), self::VAR_REVISION_NAME);
        $revision->setInfo(sprintf(
            '%s<br /><input class="btn btn-default btn-sm" type="submit" name="cmd[%s]" value="%s" />',
            $this->language->txt('asq_info_create_revision'),
            self::CMD_CREATE_REVISON,
            $this->language->txt('asq_button_create_revision')
        ));
        $this->addItem($revision);
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
