<?php

namespace spec\Newscoop\Image;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Newscoop\Entity\User;

class ImageServiceSpec extends ObjectBehavior
{
    function let($die, \Doctrine\ORM\EntityManager $em)
    {
        $this->beConstructedWith(array(
            'image_path' =>  realpath(__DIR__ . '/../../../newscoop/images/').'/',
            'thumbnail_path' => realpath(__DIR__ . '/../../../newscoop/images/thumbnails/').'/',
            'thumbnail_max_size' => 64,
            'cache_path' => realpath(__DIR__ . '/../../../newscoop/images/cache/').'/',
            'cache_url' => 'images/cache',
            'thumbnail_prefix' => 'cms-thumb-',
            'image_prefix' => 'cms-image-',
        ), $em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Image\ImageService');
    }

    function it_gives_you_src()
    {
        $image = 'images/picture.jpg';
        $this->getSrc($image, 300, 300)->shouldBe('300x300/fit/' . rawurlencode(str_replace('/', '|', $image)));
    }

    function it_upload_new_image()
    {
        $filesystem = new Filesystem();

        $newFileName = __DIR__.'/../../assets/images/temp-image.jpg';
        $filesystem->copy(__DIR__.'/../../assets/images/picture.jpg', $newFileName);
        $uploadedFile = new UploadedFile($newFileName, 'temp-image.jpg', 'image/jpg', null, null, true);
        $user = new User('test@newscoop.dev');
        $user->setAdmin(true);
        $this->upload($uploadedFile, array('user' => $user))->shouldHaveType('Newscoop\Image\LocalImage');
    }

    function letgo()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(realpath(__DIR__.'/../../../newscoop/images/cms-image-000000000.jpg'));
        $filesystem->remove(realpath(__DIR__.'/../../../newscoop/images/thumbnails/cms-thumb-000000000.jpg'));
    }
}
