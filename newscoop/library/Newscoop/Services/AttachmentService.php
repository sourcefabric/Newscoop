<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Attachment;
use Newscoop\Entity\User;
use Newscoop\Entity\Language;
use Newscoop\Entity\Translation;
use Newscoop\Entity\Article;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Attachment service
 */
class AttachmentService
{
    /**
     * @param array         $config
     * @param EntityManager $em
     */
    public function __construct(array $config, \Doctrine\ORM\EntityManager $em)
    {
        $this->config = $config;
        $this->em = $em;
    }

    /**
     * Upload new attachment
     *
     * @param UploadedFile $file
     * @param string       $descriptionText
     * @param Language     $language
     * @param array        $attributes
     * @param Attachment   $attachment
     *
     * @return Attachment
     */
    public function upload(
        UploadedFile $file,
        $descriptionText,
        Language $language,
        array $attributes,
        Attachment $attachment = null
    ) {
        $filesystem = new Filesystem();

        $filesize = $file->getClientSize();
        if ($filesize == false) {
            throw new FileException('File size is not valid');
        }

        if (!file_exists($this->config['file_directory']) || !is_writable($this->config['file_directory'])) {
            throw new FileException('Directory '.$this->config['file_directory'].' is not writable');
        }

        if (!is_null($attachment)) {
            if ($filesystem->exists($this->getStorageLocation($attachment))) {
                $filesystem->remove($this->getStorageLocation($attachment));
            }

            if ($descriptionText != $attachment->getDescription()->getTranslationText()) {
                $description = new Translation();
                $description->setLanguage($language);
                $description->setTranslationText($descriptionText);
                $this->em->persist($description);
            }
            unset($attributes['description']);
        } else {
            $attachment = new Attachment();
            $description = new Translation();
            $description->setLanguage($language);
            $description->setTranslationText($descriptionText);
            unset($attributes['description']);
            $attachment->setCreated(new \DateTime());

            $this->em->persist($description);
            $this->em->persist($attachment);
        }

        $attributes = array_merge(array(
            'language' => $language,
            'name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mimeType' => $file->getClientMimeType(),
            'contentDisposition' => Attachment::CONTENT_DISPOSITION,
            'sizeInBytes' => $file->getClientSize(),
            'description' => $description,
        ), $attributes);

        $this->fillAttachment($attachment, $attributes);
        if (is_null($attributes['name'])) {
            $attachment->setName($file->getClientOriginalName());
        }
        $this->em->flush();

        $target = $this->makeDirectories($attachment);

        try {
            $file->move($target, $this->getFileName($attachment));
            $filesystem->chmod($target.'/'.$this->getFileName($attachment), 0644);
        } catch (\Exceptiom $e) {
            $filesystem->remove($target);
            $this->em->remove($attachment);
            $this->em->flush();

            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $attachment;
    }

    public function remove(Attachment $attachment)
    {
        $filesystem = new Filesystem();

        $file = $this->getStorageLocation($attachment);
        $filesystem->remove($file);
        $this->em->remove($attachment);
        $this->em->flush();
    }

    public function addAttachmentToArticle(Attachment $attachment, Article $article)
    {

    }

    public function removeAttachmentFormArticle(Attachment $attachment, Article $article)
    {

    }

    private function fillAttachment(Attachment $attachment, $attributes)
    {
        $attributes = array_merge(array(
            'language' => null,
            'name' => null,
            'extension' => null,
            'mimeType' => null,
            'contentDisposition' => Attachment::CONTENT_DISPOSITION,
            'httpCharset' => null,
            'sizeInBytes' => null,
            'description' => null,
            'user' => null,
            'updated' => new \DateTime(),
            'source' => 'local',
            'status' => 'unaproved',
        ), $attributes);

        if (!is_null($attributes['language'])) {
            $attachment->setLanguage($attributes['language']);
        }

        if (!is_null($attributes['description'])) {
            $attachment->setDescription($attributes['description']);
        }

        $attachment
            ->setName($attributes['name'])
            ->setExtension($attributes['extension'])
            ->setMimeType($attributes['mimeType'])
            ->setContentDisposition($attributes['contentDisposition'])
            ->setHttpCharset($attributes['httpCharset'])
            ->setSizeInBytes($attributes['sizeInBytes'])
            ->setUser($attributes['user'])
            ->setUpdated($attributes['updated'])
            ->setSource($attributes['source'])
            ->setStatus($attributes['status']);

        return $attachment;
    }

    /**
     * Get attachment storage location
     *
     * @param Attachment $attachment
     *
     * @return string
     */
    public function getStorageLocation(Attachment $attachment)
    {
        if ($attachment->getId()) {
            $storageLocation = $this->config['file_directory']
               .$this->getLevel1DirectoryName($attachment)
               ."/".$this->getLevel2DirectoryName($attachment)
               ."/".$this->getFileName($attachment);
        } else {
            $storageLocation = $this->config['file_directory']
               .$this->getLevel1DirectoryName($attachment)
               ."/".$this->getLevel2DirectoryName($attachment)
               ."/".$this->getFileName($attachment);
        }

        return $storageLocation;
    }

    /**
     * Get new attachment file name
     *
     * @param Attachment $attachment
     *
     * @return string
     */
    private function getFileName(Attachment $attachment)
    {
        if ($attachment->getId()) {
            $fileName = sprintf('%09d', $attachment->getId());
        } else {
            $fileName = sprintf('%09d', 0);
        }

        if ($attachment->getExtension()) {
            $fileName .= '.'.$attachment->getExtension();
        }

        return $fileName;
    }

    /**
     * Get fist level directory name
     *
     * @param Attachment $attachment
     *
     * @return string
     */
    private function getLevel1DirectoryName(Attachment $attachment)
    {
        if ($attachment->getId()) {
            $level1Dir = floor(
                $attachment->getId() /
                ($this->config['file_num_dirs_level_1'] * $this->config['file_num_dirs_level_2'])
            );
        } else {
            $level1Dir = 0;
        }

        $level1ZeroPad = strlen($this->config['file_num_dirs_level_1']);

        return sprintf('%0'.$level1ZeroPad.'d', $level1Dir);
    }

    /**
     * Get second level directory name
     *
     * @param Attachment $attachment
     *
     * @return string
     */
    private function getLevel2DirectoryName(Attachment $attachment)
    {
        if ($attachment->getId()) {
            $level2Dir = floor(
                $attachment->getId() /
                ($this->config['file_num_dirs_level_2'] * $this->config['file_num_dirs_level_1'])
            );
        } else {
            $level2Dir = 0;
        }

        $level2ZeroPad = strlen($this->config['file_num_dirs_level_2']);

        return sprintf('%0'.$level2ZeroPad.'d', $level2Dir);
    }

    /**
     * Make directories for attachments
     *
     * @param Attachment $attachment
     *
     * @return string
     */
    private function makeDirectories(Attachment $attachment)
    {
        $filesystem = new Filesystem();

        $level1 = $this->config['file_directory'] . $this->getLevel1DirectoryName($attachment);
        $filesystem->mkdir($level1);
        $level2 = $level1."/".$this->getLevel2DirectoryName($attachment);
        $filesystem->mkdir($level2);

        return $level2;
    }
}
