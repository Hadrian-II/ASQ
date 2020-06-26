<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload;

use ILIAS\Data\UUID\Factory;
use ILIAS\FileUpload\Location;
use ILIAS\FileUpload\DTO\ProcessingStatus;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\PostAccess;

/**
 * Class FileUploadEditor
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class FileUploadEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    const VAR_CURRENT_ANSWER = 'fue_current_answer';

    const UPLOADPATH = 'asq/answers/';


    /**
     * @var FileUploadEditorConfiguration
     */
    private $configuration;

    public function __construct(QuestionDto $question) {
        $this->files = [];
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question);
    }

    public function readAnswer(): AbstractValueObject
    {
        global $DIC;

        $postkey = $this->getPostVar() . self::VAR_CURRENT_ANSWER;

        if (!$this->isPostVarSet($postkey))
        {
            return null;
        }

        $this->files = json_decode(html_entity_decode($this->getPostValue($postkey)), true);

        if ($DIC->upload()->hasUploads() && !$DIC->upload()->hasBeenProcessed()) {
            $this->UploadNewFile();
        }

        $this->deleteOldFiles();

        return FileUploadAnswer::create($this->files);
    }

    private function UploadNewFile() {
        global $DIC;

        $DIC->upload()->process();

        foreach ($DIC->upload()->getResults() as $result)
        {
            $folder = self::UPLOADPATH . $this->question->getId() . '/';
            $pathinfo = pathinfo($result->getName());

            $uuid_factory = new Factory();

            $filename = $uuid_factory->uuid4AsString() . '.' . $pathinfo['extension'];

            if ($result && $result->getStatus()->getCode() === ProcessingStatus::OK &&
                $this->checkAllowedExtension($pathinfo['extension'])) {
                $DIC->upload()->moveOneFileTo(
                    $result,
                    $folder,
                    Location::WEB,
                    $filename);

                $this->files[$pathinfo['basename']] = ILIAS_HTTP_PATH . '/' .
                                            ILIAS_WEB_DIR . '/' .
                                            CLIENT_ID .  '/' .
                                            $folder .
                                            $filename;
            }
        }
    }

    private function deleteOldFiles() {
        if(!empty($this->files)) {
            $answers = $this->files;

            foreach (array_keys($answers) as $key) {
                if ($this->isPostVarSet($this->getFileKey($key))) {
                    unset($this->files[$key]);
                }
            }
        }
    }

    /**
     * @param string $extension
     * @return bool
     */
    private function checkAllowedExtension(string $extension) :bool {
        return empty($this->configuration->getAllowedExtensions()) ||
               in_array($extension, explode(',', $this->configuration->getAllowedExtensions()));
    }

    public function generateHtml(): string
    {
        global $DIC;

        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.FileUploadEditor.html', true, true);
        $tpl->setVariable('TXT_UPLOAD_FILE', $DIC->language()->txt('asq_header_upload_file'));
        $tpl->setVariable('TXT_MAX_SIZE',
                          sprintf($DIC->language()->txt('asq_text_max_size'),
                                  $this->configuration->getMaximumSize() ?? ini_get('upload_max_filesize')));
        $tpl->setVariable('POST_VAR', $this->getPostVar());
        $tpl->setVariable('CURRENT_ANSWER_NAME', $this->getPostVar() . self::VAR_CURRENT_ANSWER);
        $tpl->setVariable('CURRENT_ANSWER_VALUE', htmlspecialchars(json_encode(is_null($this->answer) ? null : $this->answer->getFiles())));

        if (!empty($this->configuration->getAllowedExtensions())) {
            $tpl->setCurrentBlock('allowed_extensions');
            $tpl->setVariable('TXT_ALLOWED_EXTENSIONS',
                              sprintf($DIC->language()->txt('asq_text_allowed_extensions'),
                                      $this->configuration->getAllowedExtensions()));
            $tpl->parseCurrentBlock();

        }

        $tpl->setCurrentBlock('files');

        if (!is_null($this->answer) && count($this->answer->getFiles()) > 0) {
            foreach ($this->answer->getFiles() as $key => $value) {
                $tpl->setCurrentBlock('file');
                $tpl->setVariable('FILE_ID', $this->getFileKey($key));
                $tpl->setVariable('FILE_NAME', $key);
                $tpl->setVariable('FILE_PATH', $value);
                $tpl->parseCurrentBlock();
            }
        } else {
            $tpl->setCurrentBlock('no_file');
            $tpl->setVariable('TEXT_NO_FILE', $DIC->language()->txt('asq_no_file'));
            $tpl->parseCurrentBlock();
        }

        $tpl->setVariable('HEADER_DELETE', $DIC->language()->txt('delete'));
        $tpl->setVariable('HEADER_FILENAME', $DIC->language()->txt('filename'));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    private function getPostVar() : string {
        return $this->question->getId();
    }

    private function getFileKey(string $filename) {
        return $this->getPostVar() . str_replace('.', '', $filename);
    }

    public static function getDisplayDefinitionClass() : string {
        return EmptyDefinition::class;
    }

    public function isComplete(): bool
    {
        return true;
    }
}