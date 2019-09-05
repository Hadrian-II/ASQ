<?php
declare(strict_types=1);

namespace ILIAS\Services\AssessmentQuestion\PublicApi\Authoring;

use ilAsqQuestionAuthoringGUI;
use ILIAS\AssessmentQuestion\Application\PlayApplicationService;
use ILIAS\Services\AssessmentQuestion\PublicApi\Common\AssessmentEntityId;
use ILIAS\Services\AssessmentQuestion\PublicApi\Common\AuthoringContextContainer;
use ILIAS\UI\Component\Button\Button;
use ILIAS\UI\Component\Link\Standard as UiStandardLink;
use ILIS\AssessmentQuestion\Application\AuthoringApplicationService;

/**
 * Class QuestionAuthoring
 *
 * @package ILIAS\Services\AssessmentQuestion\PublicApi
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class Question
{

    /**
     * @var int
     */
    protected $container_obj_id;
    /**
     * @var int
     */
    protected $actor_user_id;
    /**
     * var string
     */
    protected $question_id;
    /**
     * AuthoringApplicationService
     */
    protected $authoring_application_service;

    /**
     * QuestionAuthoring constructor.
     *
     * @param int    $container_obj_id
     * @param string $question_uuid
     * @param int    $actor_user_id
     */
    public function __construct(int $container_obj_id, AssessmentEntityId $question_uuid, int $actor_user_id)
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $this->actor_user_id = $actor_user_id;
        $this->container_obj_id = $container_obj_id;
        $this->question_id = $question_uuid->getId();

        $this->authoring_application_service = new AuthoringApplicationService($container_obj_id, $actor_user_id);

        $DIC->language()->loadLanguageModule('asq');
    }


    public function widthAdditionalConfigSection(AdditionalConfigSection $additional_config_section) : Question
    {

    }


    public function getCreationLink(array $ctrl_stack) :UiStandardLink
    {
        global $DIC;

        array_push($ctrl_stack,ilAsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack,\ilAsqQuestionCreationGUI::class);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_create_question_link'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack)
        );
    }


    public function getAuthoringGUI(
        UiStandardLink $container_back_link,
        int $container_ref_id,
        string $container_obj_type,
        bool $actor_has_write_access
    ) : ilAsqQuestionAuthoringGUI
    {
        $authoringContextContainer = new AuthoringContextContainer(
            $container_back_link,
            $container_ref_id,
            $this->container_obj_id,
            $container_obj_type,
            $this->actor_user_id,
            $actor_has_write_access
        );

        return new ilAsqQuestionAuthoringGUI($authoringContextContainer);
    }


    /**
     */
    public function deleteQuestion() : void
    {
        // TODO: Implement deleteQuestion() method.
    }


    /**
     * @return UiStandardLink
     */
    public function getEditLink(array $ctrl_stack) :UiStandardLink
    {
        global $DIC;
        array_push($ctrl_stack,ilAsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack,\ilAsqQuestionConfigEditorGUI::class);

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_config'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack));
    }


    /**
     * @return UiStandardLink
     */
    //TODO this will not be the way! Do not save questions,
    // only simulate and show the points directly after submitting
    // Therefore, to Save Command has to
    public function getPreviewLink(array $ctrl_stack) : UiStandardLink
    {
        global $DIC;
        array_push($ctrl_stack,ilAsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack,\ilAsqQuestionPreviewGUI::class);

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_preview'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack)
        );
    }

    //TODO this will not be the way - see above
    public function getScoringOfPreviewedQuestion():float {
        global $DIC;
        $DIC->ctrl()->setParameterByClass(ilAsqQuestionAuthoringGUI::class,ilAsqQuestionAuthoringGUI::VAR_QUESTION_ID,$this->question_id);

        $player = new PlayApplicationService($this->container_obj_id,$this->actor_user_id);
        return $player->GetPointsByUser($this->question_id,$this->actor_user_id, $this->container_obj_id);

    }

    /**
     * @return UiStandardLink
     */
    public function getDisplayLink(array $ctrl_stack) : UiStandardLink
    {
        global $DIC;
        array_push($ctrl_stack,ilAsqQuestionAuthoringGUI::class);
        
        $this->setQuestionUidParameter();
        
        return $DIC->ui()->factory()->link()->standard('play by asq',$DIC->ctrl()->getLinkTargetByClass($ctrl_stack,ilAsqQuestionAuthoringGUI::CMD_DISPLAY_QUESTION));
    }

    /**
     * @return UiStandardLink
     */
    public function getEditPageLink() : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_pageview'),
            $DIC->ctrl()->getLinkTargetByClass(
                [ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionPageEditorGUI::class], 'edit'
            )
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getEditFeedbacksLink() : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_feedback'),
            $DIC->ctrl()->getLinkTargetByClass([
                ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionFeedbackEditorGUI::class
            ])
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getEditHintsLink() : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_hints'),
            $DIC->ctrl()->getLinkTargetByClass([
                ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionHintsEditorGUI::class
            ])
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getRecapitulationLink() : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_recapitulation'),
            $DIC->ctrl()->getLinkTargetByClass([
                ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionRecapitulationEditorGUI::class
            ])
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getStatisticLink() : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $this->setQuestionUidParameter();

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_statistics'),
            $DIC->ctrl()->getLinkTargetByClass([
                ilAsqQuestionAuthoringGUI::class, \ilAsqQuestionStatisticsGUI::class
            ])
        );
    }


    /**
     * sets the question uid parameter for the ctrl hub gui ilAsqQuestionAuthoringGUI
     */
    protected function setQuestionUidParameter()
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $DIC->ctrl()->setParameterByClass(
            ilAsqQuestionAuthoringGUI::class,
            ilAsqQuestionAuthoringGUI::VAR_QUESTION_ID,
            $this->question_id
        );
    }


    /**
     *
     */
    public function publishNewRevision() : void
    {
        $this->authoring_application_service->projectQuestion($this->question_id);
    }


    public function changeQuestionContainer(int $container_obj_id) : void
    {
        // TODO: Implement changeQuestionContainer() method.
    }
}