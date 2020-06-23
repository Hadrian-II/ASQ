<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use ilFormSectionHeaderGUI;
use ilHiddenInputGUI;
use ilNonEditableValueGUI;
use ilPropertyFormGUI;
use ilTextInputGUI;
use srag\asq\AsqGateway;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;

/**
 * Class QuestionFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFormGUI extends ilPropertyFormGUI
{
    use PathHelper;

    const VAR_AGGREGATE_ID = 'aggregate_id';

    const VAR_REVISION_NAME = 'rev_name';
    const VAR_STATUS = 'status';
    const VAR_ANSWER_OPTIONS = 'answer_options';

    const VAR_LEGACY = 'legacy';

    const FORM_PART_LINK = 'form_part_link';

    const CMD_CREATE_REVISON = 'createRevision';

    /**
     * @var AsqTableInput
     */
    protected $option_form;

    /**
     * @var \ilLanguage
     */
    protected $lang;

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
     * QuestionFormGUI constructor.
     *
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question) {
        global $DIC;
        $this->lang = $DIC->language();
        $this->initial_question = $question;

        $this->question_data_factory = new QuestionDataFormFactory($this->lang);

        $factory_class = $question->getType()->getFactoryClass();
        $this->form_factory = new $factory_class();

        $this->initForm($question);
        $this->setMultipart(true);
        $this->setTitle($question->getType()->getTitle());

        foreach ($this->form_factory->getScripts() as $script)
        {
            $DIC->ui()->mainTemplate()->addJavaScript($script);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->setValuesByPost();
            $this->post_question = $this->readQuestionFromPost();
        }

        $this->showQuestionState($this->post_question ?? $question);

        $this->addRevisionForm();

        parent::__construct();
    }


    /**
     * @param QuestionDto $question
     */
    private function initForm(QuestionDto $question) {
        global $DIC;

        $id = new ilHiddenInputGUI(self::VAR_AGGREGATE_ID);
        $id->setValue($question->getId());
        $this->addItem($id);

        foreach ($this->question_data_factory->getFormfields($question->getData()) as $field) {
            $this->addItem($field);
        }

        if (is_null($question->getPlayConfiguration())) {
            $question->setPlayConfiguration($this->form_factory->getDefaultPlayConfiguration());
        }

        foreach ($this->form_factory->getFormfields($question->getPlayConfiguration()) as $field) {
            $this->addItem($field);
        }

        if ($this->form_factory->hasAnswerOptions())
        {
            $this->option_form = new AsqTableInput(
                $this->lang->txt('asq_label_answer'),
                self::VAR_ANSWER_OPTIONS,
                $this->form_factory->getAnswerOptionValues($question->getAnswerOptions()),
                $this->form_factory->getAnswerOptionDefinitions($question->getPlayConfiguration()),
                $this->form_factory->getAnswerOptionConfiguration());

            $this->addItem($this->option_form);
        }

        $DIC->ui()->mainTemplate()->addJavaScript($this->getBasePath(__DIR__) . 'js/AssessmentQuestionAuthoring.js');
    }

    private function showQuestionState(QuestionDto $question) {
        global $DIC;

        $state = new ilNonEditableValueGUI($DIC->language()->txt('Status'), self::VAR_STATUS);

        if ($question->isComplete()) {
            $value = sprintf(
                'Complete. Max Points: %s Min Points: %s',
                AsqGateway::get()->answer()->getMaxScore($question),
                AsqGateway::get()->answer()->getMinScore($question));
        } else {
            $value = 'Not Complete';
        }

        $state->setValue($value);

        $this->addItem($state);
    }

    private function addRevisionForm() {
        global $DIC;

        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle($DIC->language()->txt('asq_version_title'));
        $this->addItem($spacer);

        $revision = new ilTextInputGUI($DIC->language()->txt('asq_label_new_revision'), self::VAR_REVISION_NAME);
        $revision->setInfo(sprintf(
            '%s<br /><input class="btn btn-default btn-sm" type="submit" name="cmd[%s]" value="%s" />',
            $DIC->language()->txt('asq_info_create_revision'),
            self::CMD_CREATE_REVISON,
            $DIC->language()->txt('asq_button_create_revision')
        ));
        $this->addItem($revision);
    }

    /**
     * @return QuestionDto
     */
    public function getQuestion() : QuestionDto {
        return $this->post_question ?? $this->initial_question;
    }

    /**
     * @return QuestionDto
     */
    private function readQuestionFromPost() : QuestionDto
    {
        $question = new QuestionDto();
        $question->setId($_POST[self::VAR_AGGREGATE_ID]);

        $question->setData($this->question_data_factory->readObjectFromPost());

        $question->setPlayConfiguration($this->form_factory->readQuestionPlayConfiguration());

        if ($this->form_factory->hasAnswerOptions()) {
            $question->setAnswerOptions($this->form_factory->readAnswerOptions($this->option_form->readValues()));
        }

        $question = $this->form_factory->performQuestionPostProcessing($question);

        return $question;
    }
}