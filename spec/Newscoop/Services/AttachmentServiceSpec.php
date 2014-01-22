<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Newscoop\Entity\Attachment;
use Newscoop\Entity\Language;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentServiceSpec extends ObjectBehavior
{
    function let($die, \Doctrine\ORM\EntityManager $em)
    {
        $this->beConstructedWith(array(
            'file_base_url' => "files/",
            'file_directory' => realpath(__DIR__ . '/../../../newscoop/public/files').'/',
            'file_num_dirs_level_1' => 1000,
            'file_num_dirs_level_2' => 1000,
        ), $em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\AttachmentService');
    }

    function it_should_return_path_for_attachment()
    {
        $this->getStorageLocation(new Attachment())
            ->shouldBe(realpath(__DIR__ . '/../../../newscoop/public/files').'/0000/0000/000000000');

        $attachment = new Attachment();
        $attachment->setExtension('pdf')
            ->setId(34);

        $this->getStorageLocation($attachment)
            ->shouldBe(realpath(__DIR__ . '/../../../newscoop/public/files').'/0000/0000/000000034.pdf');

    }

    function it_should_upload_attachment()
    {
        $filesystem = new Filesystem();

        $newFileName = __DIR__.'/../../assets/images/temp-image.jpg';
        $filesystem->copy(__DIR__.'/../../assets/images/picture.jpg', $newFileName);
        $uploadedFile = new UploadedFile($newFileName, 'temp-image.jpg', 'image/jpg', filesize($newFileName), null, true);
        $language = new Language();

        $this->upload($uploadedFile, 'Test file', $language, array());
    }

    function it_should_update_attachment()
    {

    }

    function it_should_remove_attachemnt()
    {

    }
}
