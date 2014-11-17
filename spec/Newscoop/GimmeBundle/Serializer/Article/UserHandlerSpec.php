<?php

namespace spec\Newscoop\GimmeBundle\Serializer\Article;

use PhpSpec\ObjectBehavior;
use JMS\Serializer\JsonSerializationVisitor;
use Newscoop\Image\ImageService;
use Newscoop\Services\PublicationService;
use Newscoop\Entity\User;
use Newscoop\Entity\Aliases;

class UserHandlerSpec extends ObjectBehavior
{
    public function let($die, ImageService $imagesService, PublicationService $publicationService)
    {
        $config = array('cache_url' => 'images/cache');
        $type = array(
            'name' => "user",
            'params' => array()
        );

        $this->beConstructedWith($imagesService, $publicationService, $config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Serializer\Article\UserHandler');
    }

    public function it_should_return_a_simple_user_array(JsonSerializationVisitor $visitor, User $user, $publicationService, Aliases $alias, $imagesService, $type)
    {
        $alias->getId()->willReturn(1);
        $alias->getName()->willReturn("newscoop.net");
        $publicationService->getPublicationAlias()->willReturn($alias);
        $user->getId()->willReturn(1);
        $user->getRealName()->willReturn("Jhon Doe");
        $user->getUsername()->willReturn("doe");
        $user->getEmail()->willReturn("doe@example.com");
        $user->getImage()->willReturn("e7b816f7d39bb6cbd151089baeeb542d9856bf21.jpg");

        $image = 'images/e7b816f7d39bb6cbd151089baeeb542d9856bf21.jpg';
        $imagesService->getSrc($image, 120, 120)->willReturn('120x120/fit/' . rawurlencode(str_replace('/', '|', $image)));

        $this->serializeToJson($visitor, $user, $type)->shouldReturn(array(
            'id' => 1,
            'realname' => "Jhon Doe",
            'username' => "doe",
            'image' => "newscoop.net/images/cache/120x120/fit/images%7Ce7b816f7d39bb6cbd151089baeeb542d9856bf21.jpg",
            'email' => 'doe@example.com',
        ));
    }

    public function it_should_return_null_when_no_user(JsonSerializationVisitor $visitor, $type)
    {
        $this->serializeToJson($visitor, null, $type)->shouldReturn(null);
    }
}
