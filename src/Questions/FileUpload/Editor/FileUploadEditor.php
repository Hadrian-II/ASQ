<?php
declare(strict_types=1);

namespace srag\asq\Questions\FileUpload\Editor;

use ILIAS\Data\UUID\Factory;
use ILIAS\FileUpload\FileUpload;
use ILIAS\FileUpload\Location;
use ILIAS\FileUpload\DTO\ProcessingStatus;
use ilLanguage;
use ilTemplate;
use Fluxlabs\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Infrastructure\Helpers\PathHelper;
use srag\asq\Questions\FileUpload\FileUploadAnswer;
use srag\asq\Questions\FileUpload\Editor\Data\FileUploadEditorConfiguration;
use srag\asq\UserInterface\Web\PostAccess;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class FileUploadEditor
 *
 * @license Extended GPL, see docs/LICENSE
 *
 * @package srag/asq
 * @author Adrian Lüthi - Fluxlabs AG <adi@fluxlabs.ch>
 */
class FileUploadEditor extends AbstractEditor
{
    use PostAccess;
    use PathHelper;

    const VAR_CURRENT_ANSWER = 'fue_current_answer';

    const UPLOADPATH = 'asq/answers/';

    private FileUpload $upload;

    private ilLanguage $language;

    private FileUploadEditorConfiguration $configuration;

    public function __construct(QuestionDto $question)
    {
        global $DIC;

        $this->files = [];
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        $this->upload = $DIC->upload();
        $this->language = $DIC->language();

        parent::__construct($question);
    }

    public function readAnswer() : ?AbstractValueObject
    {
        $postkey = $this->getPostVar() . self::VAR_CURRENT_ANSWER;

        if (!$this->isPostVarSet($postkey)) {
            return null;
        }

        $this->files = json_decode(html_entity_decode($this->getPostValue($postkey)), true);

        if ($this->upload->hasUploads() && !$this->upload->hasBeenProcessed()) {
            $this->UploadNewFile();
        }

        $this->deleteOldFiles();

        return new FileUploadAnswer($this->files);
    }

    private function UploadNewFile() : void
    {
        $this->upload->process();

        foreach ($this->upload->getResults() as $result) {
            $folder = self::UPLOADPATH . $this->question->getId()->toString() . '/';
            $pathinfo = pathinfo($result->getName());

            $uuid_factory = new Factory();

            $filename = $uuid_factory->uuid4AsString() . '.' . $pathinfo['extension'];

            if ($result && $result->getStatus()->getCode() === ProcessingStatus::OK &&
                $this->checkAllowedExtension($pathinfo['extension'])) {
                $this->upload->moveOneFileTo(
                    $result,
                    $folder,
                    Location::WEB,
                    $filename
                );

                $this->files[$pathinfo['basename']] = ILIAS_HTTP_PATH . '/' .
                                            ILIAS_WEB_DIR . '/' .
                                            CLIENT_ID . '/' .
                                            $folder .
                                            $filename;
            }
        }
    }

    private function deleteOldFiles() : void
    {
        if (!empty($this->files)) {
            $answers = $this->files;

            foreach (array_keys($answers) as $key) {
                if ($this->isPostVarSet($this->getFileKey($key))) {
                    unset($this->files[$key]);
                }
            }
        }
    }

    private function checkAllowedExtension(string $extension) : bool
    {
        return empty($this->configuration->getAllowedExtensions()) ||
               in_array($extension, explode(',', $this->configuration->getAllowedExtensions()));
    }

    public function generateHtml() : string
    {
        $tpl = new ilTemplate($this->getBasePath(__DIR__) . 'templates/default/tpl.FileUploadEditor.html', true, true);
        $tpl->setVariable('TXT_UPLOAD_FILE', $this->language->txt('asq_header_upload_file'));
        $tpl->setVariable(
            'TXT_MAX_SIZE',
            sprintf(
                $this->language->txt('asq_text_max_size'),
                $this->configuration->getMaximumSize() ?? ini_get('upload_max_filesize')
            )
        );

        $tpl->setVariable('POST_VAR', $this->getPostVar());
        $tpl->setVariable('CURRENT_ANSWER_NAME', $this->getPostVar() . self::VAR_CURRENT_ANSWER);
        $tpl->setVariable('CURRENT_ANSWER_VALUE', htmlspecialchars(json_encode(is_null($this->answer) ? null : $this->answer->getFiles())));

        if (!empty($this->configuration->getAllowedExtensions())) {
            $tpl->setCurrentBlock('allowed_extensions');
            $tpl->setVariable(
                'TXT_ALLOWED_EXTENSIONS',
                sprintf(
                    $this->language->txt('asq_text_allowed_extensions'),
                    $this->configuration->getAllowedExtensions()
                )
            );

            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('files');

        if (!is_null($this->answer) && !is_null($this->answer->getFiles()) && count($this->answer->getFiles()) > 0) {
            foreach ($this->answer->getFiles() as $key => $value) {
                $tpl->setCurrentBlock('file');
                $tpl->setVariable('FILE_ID', $this->getFileKey($key));
                $tpl->setVariable('FILE_NAME', $key);
                $tpl->setVariable('FILE_PATH', $value);
                $tpl->parseCurrentBlock();
            }
        } else {
            $tpl->setCurrentBlock('no_file');
            $tpl->setVariable('TEXT_NO_FILE', $this->language->txt('asq_no_file'));
            $tpl->parseCurrentBlock();
        }

        $tpl->setVariable('HEADER_DELETE', $this->language->txt('delete'));
        $tpl->setVariable('HEADER_FILENAME', $this->language->txt('filename'));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    private function getPostVar() : string
    {
        return $this->question->getId()->toString();
    }

    private function getFileKey(string $filename) : string
    {
        return $this->getPostVar() . str_replace('.', '', $filename);
    }

    public function isComplete() : bool
    {
        return true;
    }
}
