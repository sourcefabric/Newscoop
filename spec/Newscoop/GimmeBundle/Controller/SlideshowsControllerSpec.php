<?php

namespace spec\Newscoop\GimmeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\AbstractQuery;
use Newscoop\Gimme\PaginatorService;
use Knp\Component\Pager\Paginator;
use Newscoop\Package\Package;
use Newscoop\Package\PackageRepository;
use Newscoop\Entity\Repository\ArticleRepository;
use Newscoop\Services\PublicationService;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Language;
use Newscoop\Entity\Article;
use Symfony\Component\HttpFoundation\ParameterBag;
use Newscoop\Criteria\SlideshowCriteria;

class SlideshowsControllerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Controller\SlideshowsController');
        $this->shouldImplement('FOS\RestBundle\Controller\FOSRestController');
    }

    public function let(
        Container $container,
        EntityManager $entityManager,
        Request $request,
        AbstractQuery $query,
        PaginatorService $paginator,
        Paginator $knpPaginator,
        Package $package,
        PackageRepository $packageRepository,
        ArticleRepository $articleRepository,
        PublicationService $publicationService,
        Publication $publication,
        Language $language,
        Article $article
    ) {
        $container->get('em')->willReturn($entityManager);
        $container->get('request')->willReturn($request);
        $container->get('newscoop.paginator.paginator_service')->willReturn($paginator);
        $container->get('newscoop.publication_service')->willReturn($publicationService);

        $this->setContainer($container);

        $entityManager->getRepository('Newscoop\Package\Package')->willReturn($packageRepository);
    }

    public function its_getSlideshowItemsAction_should_return_slideshow_info_and_its_items(
        $request,
        $package,
        $packageRepository
    ) {
        $package->getId()->willReturn(1);
        $package->getHeadline()->willReturn('Tempore qui nisi voluptatibus.');
        $package->getDescription()->willReturn('Tempore qui nisi voluptatibus caption.');
        $items = array('items' => array(
            new \Newscoop\Package\RemoteVideo('https://www.youtu.be/8xfbum8dmqw'),
        ));

        $package->getItems()->willReturn($items);

        $packageRepository->findOneBy(array(
            'id' => 1,
        ))->willReturn($package);

        $this->getSlideshowItemsAction($request, 1)->shouldReturn($package);
    }

    public function its_getSlideshowItemsAction_should_throw_NotFoundHttpException_when_no_slideshow($request, $packageRepository)
    {
        $packageRepository->findOneBy(array(
            'id' => 1,
        ))->willReturn(null);

        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('getSlideshowItemsAction', array($request, 1));
    }

    public function its_getArticleSlideshowsAction_should_return_list_of_article_slideshows(
        ParameterBag $parameterBag,
        ArticleRepository $articleRepository,
        $request,
        $entityManager,
        $query,
        $paginator,
        $knpPaginator,
        $publicationService,
        $publication,
        $language,
        $article,
        $packageRepository
    ) {
        $entityManager->getRepository('Newscoop\Entity\Article')->willReturn($articleRepository);
        $parameterBag->get("language", "en")->willReturn("en");
        $request->request = $parameterBag;
        $language->getCode()->willReturn('en');
        $publication->getLanguage()->willReturn($language);
        $publicationService->getPublication()->willReturn($publication);

        $article->getNumber()->willReturn(64);
        $article->getName()->willReturn('test article');
        $article->getLanguage()->willReturn($language);

        $articleRepository->getArticle(64, "en")->willReturn($query);
        $query->getOneOrNullResult()->willReturn($article);

        $criteria = new SlideshowCriteria();
        $criteria->articleNumber = 64;
        $slideshows = $packageRepository->getListByCriteria($criteria)->willReturn($query);

        $result = array(
            'id' => 1,
            'headline' => 'Tempore qui nisi voluptatibus.',
            'items' => array(array(
                'type' => 'video',
                'link' => 'https://www.youtu.be/8xfbum8dmqw',
            ), array(
                'caption' => "",
                'type' => "image",
                'link' => "http:\/\/newscoop.dev\/images\/cache\/3200x2368\/fit\/images%7Ccms-image-000000131.jpg",
            )),
            'itemsCount' => 2,
        );

        $paginator->setUsedRouteParams(array("number" => 64, "language" => "en"))->willReturn($knpPaginator);
        $paginator->paginate($query, array(
            'distinct' => false,
        ))->willReturn($result);

        $this->getArticleSlideshowsAction($request, 64, 'en')->shouldReturn($result);
    }
}
