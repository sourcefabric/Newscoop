<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Newscoop\Entity\LocalImage;
use Newscoop\Entity\User;
use Newscoop\GimmeBundle\Form\Type\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Images controller
 */
class ImagesController extends FOSRestController
{
    /**
     * @Route("/images.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getImagesAction(Request $request)
    {
        $em = $this->container->get('em');

        $images = $em->getRepository('Newscoop\Image\LocalImage')
            ->getImages();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $images = $paginator->paginate($images, array(
            'distinct' => false
        ));

        return $images;
    }

    /**
     * @Route("/images/{number}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return Form
     */
    public function getImageAction(Request $request, $number)
    {
        $em = $this->container->get('em');

        $image = $em->getRepository('Newscoop\Image\LocalImage')
            ->getImage($number)
            ->getOneOrNullResult();

        if (!$image) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $image;
    }

    /**
     * @Route("/images.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createImageAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * @Route("/images/{number}.{_format}", defaults={"_format"="json"})
     * @Method("POST|PATCH")
     * @View()
     *
     * @return Form
     */
    public function updateImageAction(Request $request, $number)
    {
        return $this->processForm($request, $number);
    }

    /**
     * @Route("/images/{number}.{_format}", defaults={"_format"="json"})
     * @Method("DELETE")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function deleteImageAction(Request $request, $number)
    {
        $imageService = $this->container->get('image');
        $em = $this->container->get('em');
        $image = $em->getRepository('Newscoop\Image\LocalImage')->findOneById($number);

        if (!$image) {
            throw new EntityNotFoundException('Result was not found.');
        }

        $imageService->remove($image);
    }

    /**
     * Process image form
     *
     * @param Request $request
     * @param integer $image
     *
     * @return Form
     */
    private function processForm($request, $image = null)
    {
        $em = $this->container->get('em');
        $imageService = $this->container->get('image');

        if (!$image) {
            $statusCode = 201;
        } else {
            $statusCode = 200;
            $image = $em->getRepository('Newscoop\Image\LocalImage')->findOneById($image);

            if (!$image) {
                throw new EntityNotFoundException('Result was not found.');
            }
        }

        $form = $this->createForm(new ImageType(), array());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form['image']->getData();
            $attributes = $form->getData();
            $user = $this->getUser();

            if ($user) {
                $attributes['user'] = $user;
            }

            $image = $imageService->upload($file, $attributes, $image);

            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_images_getimage', array(
                    'number' => $image->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}
