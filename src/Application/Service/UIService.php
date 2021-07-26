<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\QuestionComponent;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInput\AsqTableInputFieldDefinition;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\Refinery\Factory;
use srag\asq\UserInterface\Web\Fields\DurationInput\DurationInput;
use srag\asq\Questions\Choice\Form\Editor\ImageMap\ImageFormPopup\ImageFormPopup;
use srag\asq\UserInterface\Web\Fields\AsqImageUpload\AsqImageUpload;
use srag\asq\UserInterface\Web\Component\Feedback\Form\QuestionFeedbackFormGUI;
use srag\asq\UserInterface\Web\Component\Hint\Form\HintFormGUI;
use ilLanguage;
use ILIAS\DI\UIServices;
use ILIAS\DI\HTTPServices;

/**
 * Class UIService
 *
 * Service providing options to display a question on screen
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class UIService
{
    private ilLanguage $lng;

    private UIServices $ui;

    private HTTPServices $http;

    private DataFactory $data_factory;

    private Factory $refinery;

    public function __construct(
        ilLanguage $lng,
        UIServices $ui,
        HTTPServices $http,
        DataFactory $data_factory,
        Factory $refinery)
    {
        $this->lng = $lng;
        $this->ui = $ui;
        $this->http = $http;
        $this->data_factory = $data_factory;
        $this->refinery = $refinery;
    }

    /**
     * Gets a component able to display a question
     *
     * @param QuestionDto $question
     * @return QuestionComponent
     */
    public function getQuestionComponent(QuestionDto $question) : QuestionComponent
    {
        $this->lng->loadLanguageModule('asq');

        return new QuestionComponent($question);
    }

    /**
     * Gets the question authoring form for a question
     *
     * @param QuestionDto $question
     * @param string $action
     * @return QuestionFormGUI
     */
    public function getQuestionEditForm(QuestionDto $question, string $action) : QuestionFormGUI
    {
        return new QuestionFormGUI(
            $question,
            $action,
            $this->lng,
            $this->ui,
            $this->http->request(),
            $this
        );
    }

    /**
     * Gets the question feedback form for a question
     *
     * @param QuestionDto $question
     * @param string $action
     * @return QuestionFeedbackFormGUI
     */
    public function getQuestionFeedbackForm(QuestionDto $question, string $action) : QuestionFeedbackFormGUI
    {
        return new QuestionFeedbackFormGUI(
            $question,
            $action,
            $this->lng,
            $this->ui,
            $this->http->request()
        );
    }

    /**
     * Gets the question feedback form for a question
     *
     * @param QuestionDto $question
     * @param string $action
     * @return HintFormGUI
     */
    public function getQuestionHintForm(QuestionDto $question, string $action) : HintFormGUI
    {
        return new HintFormGUI(
            $question,
            $action,
            $this->lng,
            $this->ui,
            $this->http->request(),
            $this
        );
    }

    public function getAsqTableInput(string $label, array $columns, string $byline = null) : AsqTableInput
    {
        return new AsqTableInput(
            $label,
            $columns,
            $this->data_factory,
            $this->refinery,
            $byline
        );
    }

    public function getDurationInput(string $label, string $byline = null) : DurationInput
    {
        return new DurationInput(
            $this->data_factory,
            $this->refinery,
            $label,
            $byline
        );
    }

    public function getImageUpload(string $label) : AsqImageUpload
    {
        return new AsqImageUpload(
            $this->data_factory,
            $this->refinery,
            $label,
            null
        );
    }

    public function getImageFormPopup() : ImageFormPopup
    {
        return new ImageFormPopup(
            $this->data_factory,
            $this->refinery,
            '',
            null
        );
    }
}
