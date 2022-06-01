<?php

namespace App\Service;

use App\Enum\FileType;
use Symfony\Component\HttpFoundation\Request;

class AppApiFileManager
{
    public function __construct(
        private string $jobFileDir,
        private string $validationFileDir,
    ) {}

    /**
     * @param Request $request
     * @param string $filename e.g. '1 - AB6.jpg' or '1 - AB6'
     * @param FileType $fileType
     * @return string
     */
    public function saveFromRequestContent(Request $request, string $filename, FileType $fileType): string
    {
        $safeName = $this->getSafeName($filename);
        $file = $this->getFilePath($safeName, $fileType);
        file_put_contents($file, $request->getContent(true));

        return $safeName;
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
