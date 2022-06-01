<?php

namespace App\Service;

use App\Entity\AbstractAppFile;
use App\Entity\Job;
use App\Enum\FileType;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use ZipArchive;

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

    /**
     * @param AbstractAppFile $file
     * @return void
     */
    public function remove(AbstractAppFile $file): void
    {
        $this->filesystem->remove($this->getFileDir($file->getType()) . '/' . $file->getName());
    }

    /**
     * @param PersistentCollection $files
     * @return void
     */
    public function removeAll(PersistentCollection $files): void
    {
        foreach ($files as $file) {
            $this->remove($file);
        }
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
     * @param AbstractAppFile $file
     * @return string
     */
    public function getPath(AbstractAppFile $file): string
    {
        return $this->getFileDir($file->getType()) . '/' . $file->getName();
    }

    /**
     * @param Job $job
     * @return string|false
     */
    public function getValidationFilesZip(Job $job): string|false
    {
        if (count($job->getValidationFiles()) === 0) {
            return false;
        }

        $zip = new ZipArchive();
        $file = tempnam(sys_get_temp_dir(), 'order_xml_zip');
        $zip->open($file, ZipArchive::CREATE);

        foreach ($job->getValidationFiles() as $validationFile) {
            $zip->addFile($this->getPath($validationFile), $validationFile->getSourceName());
        }

        $zip->close();

        return $file;
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
