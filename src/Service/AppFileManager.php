<?php

namespace App\Service;

use App\Entity\AbstractAppFile;
use App\Enum\FileType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFileManager
{
    public function __construct(
        private SluggerInterface $slugger,
        private Filesystem $filesystem,
        private string $jobFileDir,
        private string $validationFileDir,
    ) {}

    /**
     * If success, returns the file safeName
     *
     * @param UploadedFile $file
     * @param FileType $type
     * @return string
     */
    public function save(UploadedFile $file, FileType $type): string
    {
        $safeName = $this->getSafeName($file);
        $file->move($this->getFileDir($type), $safeName);

        return $safeName;
    }

    public function remove(AbstractAppFile $file): void
    {
        $this->filesystem->remove($this->getFileDir($file->getType()) . '/' . $file->getName());
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function getOriginalName(UploadedFile $file): string
    {
        return $this->getOriginalBaseName($file) . '.' . $file->guessExtension();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function getSafeName(UploadedFile $file): string
    {
        $safeFilename = $this->slugger->slug($this->getOriginalBaseName($file));

        return $safeFilename . '-' . uniqid() . '.' .$file->guessExtension();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function getOriginalBaseName(UploadedFile $file): string
    {
        return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    private function getFileDir(FileType $type): string
    {
        return match ($type) {
            FileType::PRODUCTION => $this->jobFileDir,
            FileType::VALIDATION => $this->validationFileDir,
        };
    }
}
