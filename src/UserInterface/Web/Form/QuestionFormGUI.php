<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use Exception;
use ilFormSectionHeaderGUI;
use ilHiddenInputGUI;
use ilNonEditableValueGUI;
use ilPropertyFormGUI;
use ilTextInputGUI;
use srag\asq\AsqGateway;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\Config\AnswerOptionForm;

/**
 * Class QuestionFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFormGUI extends ilPropertyFormGUI {
    const VAR_AGGREGATE_ID = 'aggregate_id';

    const VAR_TITLE = 'title';
    const VAR_AUTHOR = 'author';
    const VAR_DESCRIPTION = 'description';
    const VAR_QUESTION = 'question';
    const VAR_WORKING_TIME = 'working_time';
    const VAR_LIFECYCLE = 'lifecycle';
    const VAR_REVISION_NAME = 'rev_name';
    const VAR_STATUS = 'status';

    const VAR_LEGACY = 'legacy';

    const SECONDS_IN_MINUTE = 60;
    const SECONDS_IN_HOUR = 3600;

    const FORM_PART_LINK = 'form_part_link';

    const CMD_CREATE_REVISON = 'createRevision';

    /**
     * @var AnswerOptionForm
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

        $this->initQuestionDataConfiguration($question);

        if (is_null($question->getPlayConfiguration())) {
            $question->setPlayConfiguration($this->createDefaultPlayConfiguration());
        }

        $this->initiatePlayConfiguration($question->getPlayConfiguration());

        if (!is_null($question->getPlayConfiguration()) &&
            $question->getPlayConfiguration()->hasAnswerOptions() &&
            $this->canDisplayAnswerOptions())
        {
            $this->option_form = new AnswerOptionForm(
                $this->lang->txt('asq_label_answer'),
                $question->getPlayConfiguration(),
                $question->getAnswerOptions(),
                $this->getAnswerOptionDefinitions($question->getPlayConfiguration()),
                $this->getAnswerOptionConfiguration());

            $this->addItem($this->option_form);
        }

        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'js/AssessmentQuestionAuthoring.js');

        $this->postInit();
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

    protected function getAnswerOptionConfiguration() {
        return null;
    }

    protected function getAnswerOptionDefinitions(?QuestionPlayConfiguration $play) : ?array {
        return null;
    }

    protected function canDisplayAnswerOptions() {
        return true;
    }

    protected function postInit() {
        //i am a virtual function :)
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

        $question->setData($this->readQuestionData());

        $question->setPlayConfiguration($this->readPlayConfiguration());

        $question->setAnswerOptions($this->readAnswerOptions($question));

        $question = $this->processPostQuestion($question);

        return $question;
    }

    /**
     * @param QuestionDto $question
     * @return QuestionDto
     */
    protected function processPostQuestion(QuestionDto $question) : QuestionDto
    {
        return $question;
    }

    protected function readAnswerOptions(QuestionDto $question) : ?AnswerOptions {
        if (!is_null($this->option_form)) {
            $this->option_form->setConfiguration($question->getPlayConfiguration());
            $this->option_form->readAnswerOptions();
            return $this->option_form->getAnswerOptions();
        }

        return null;
    }

    /**
     * @param QuestionPlayConfiguration $play
     */
    protected abstract function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void ;

    /**
     * @return QuestionData
     * @throws Exception
     */
    private function readQuestionData(): QuestionData {
        return QuestionData::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_TITLE]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_QUESTION]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_AUTHOR]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_DESCRIPTION]),
            $this->readWorkingTime($_POST[self::VAR_WORKING_TIME]),
            InputHelper::readInt(self::VAR_LIFECYCLE));
    }

    /**
     * @return QuestionPlayConfiguration
     */
    protected abstract function readPlayConfiguration(): QuestionPlayConfiguration;

    /**
     * @return QuestionPlayConfiguration
     */
    protected abstract function createDefaultPlayConfiguration() : QuestionPlayConfiguration;

    /**
     * @param $postval
     *
     * @return int
     * @throws Exception
     */
    private function readWorkingTime($postval) : int {
        $HOURS = 'hh';
        $MINUTES = 'mm';
        $SECONDS = 'ss';

        if (
            is_array($postval) &&
            array_key_exists($HOURS, $postval) &&
            array_key_exists($MINUTES, $postval) &&
            array_key_exists($SECONDS, $postval)) {
                return $postval[$HOURS] * self::SECONDS_IN_HOUR + $postval[$MINUTES] * self::SECONDS_IN_MINUTE + $postval[$SECONDS];
            } else {
                throw new Exception("This should be impossible, please fix implementation");
            }
    }
}