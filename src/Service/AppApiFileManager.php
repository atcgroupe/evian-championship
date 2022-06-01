<?php

namespace App\Service;

use App\Entity\Job;
use App\Enum\FileType;
use Symfony\Component\HttpFoundation\Request;

class AppApiFileManager
{
    public function __construct(
        private string $jobFileDir,
        private string $validationFileDir,
    ) {}

    /**
     * @param Job $job
     * @return string
     */
    public function getFilename(Job $job): string
    {
        $validationFilesCount = count($job->getValidationFiles()) + 1;
        $number = ($validationFilesCount < 10) ? '0' . $validationFilesCount : $validationFilesCount;

        return sprintf('%s - %s_%s.jpg', $job->getId(), $job->getCustomerReference(), $number);
    }

    /**
     * @param Request $request
     * @param string $filename e.g. '1 - AB6.jpg' or '1 - AB6'
     * @param FileType $fileType
     * @return string
     */
    public function saveFromRequestContent(Request $request, string $filename, FileType $fileType): string
    {
        if (!$this->isDir($fileType)) {
            $this->mkdir($fileType);
        }

        $safeName = $this->getSafeName($filename);
        $file = $this->getFilePath($safeName, $fileType);
        file_put_contents($file, $request->getContent(true));

        return $safeName;
    }

    /**
     * @param FileType $fileType
     * @return bool
     */
    private function isDir(FileType $fileType): bool
    {
        return is_dir($this->getFileDir($fileType));
    }

    /**
     * @param FileType $fileType
     * @return void
     */
    private function mkdir(FileType $fileType): void
    {
        mkdir($this->getFileDir($fileType));
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getSafeName(string $filename): string
    {
        if (str_ends_with($filename, '.jpg')) {
            $filename = substr($filename, 0, -4);
        }

        return $filename . '-' . uniqid() . '.jpg';
    }

    /**
     * @param string $safeName
     * @param FileType $fileType
     * @return string
     */
    private function getFilePath(string $safeName, FileType $fileType): string
    {
        return $this->getFileDir($fileType) . '/' . $safeName;
    }

    /**
     * @param FileType $type
     * @return string
     */
    private function getFileDir(FileType $type): string
    {
        return match ($type) {
            FileType::PRODUCTION => $this->jobFileDir,
            FileType::VALIDATION => $this->validationFileDir,
        };
    }
}
