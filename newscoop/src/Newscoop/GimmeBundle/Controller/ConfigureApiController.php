<?php

/**
 * @package   Newscoop\Gimme
 * @author    Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Newscoop\GimmeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Newscoop\GimmeBundle\Entity\Client;
use Newscoop\GimmeBundle\Entity\PublicApiResource;
use Newscoop\GimmeBundle\Form\Type\PublicResourcesType;
use Newscoop\GimmeBundle\Form\Type\ClientType;
use Symfony\Component\Form\Form;

/**
 * Configure Newscop REST API
 */
class ConfigureApiController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/admin/configure-api", name="configure_api", options={"expose"=false})
     * @Method("GET|POST")
     * @Template()
     *
     * @return array
     */
    public function configureAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $publicResourcesForm = $this->getPublicResourcesForm();
        $clientForm = $this->getClientForm();
        $removeClientForm = $this->getRemoveClientForm();
        $response = null;

        if ($request->request->has($publicResourcesForm->getName())) {
            $response = $this->configurePublicResources($request);
        } elseif ($request->request->has($clientForm->getName())) {
            $response = $this->addClient($request);
        } elseif ($request->request->has($removeClientForm->getName())) {
            $response = $this->removeClient($request);
        }

        if ($response instanceof Response) {
            return $response;
        }

        $clients = $em->getRepository('\Newscoop\GimmeBundle\Entity\Client')->findAll();

        return array(
            'publicResourcesForm' => $publicResourcesForm->createView(),
            'clientForm' => $clientForm->createView(),
            'removeClientForm' => $removeClientForm->createView(),
            'clients' => $clients
        );
    }

    /**
     * Add client to database (handle form submission)
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function addClient(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $translator = $this->container->get('translator');
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();

        $clientForm = $this->getClientForm($client);

        $clientForm->handleRequest($request);
        if ($clientForm->isValid()) {
            $client->setAllowedGrantTypes(array('token', 'authorization_code', 'client_credentials'));
            $clientManager->updateClient($client);

            $this->get('session')->getFlashBag()->add(
                'success',
                $translator->trans('client.added', array(), 'api')
            );

            return $this->redirect($this->generateUrl('configure_api'));
        }
    }

    /**
     * Remove client (handle form submission)
     *
     * @param Request $request
     *
     * @return mixed
     */
    private function removeClient(Request $request)
    {
        $translator = $this->container->get('translator');
        $removeClientForm = $this->getRemoveClientForm();
        $removeClientForm->handleRequest($request);

        if ($removeClientForm->isValid()) {
            $data = $removeClientForm->getData();
            $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
            $client = $clientManager->findClientByPublicId($data['client_id']);

            if (!$client) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $translator->trans('client.notfound', array(), 'api')
                );

                return;
            }

            $clientManager->deleteClient($client);
            $this->get('session')->getFlashBag()->add(
                'success',
                $translator->trans('client.removed', array(), 'api')
            );

            return $this->redirect($this->generateUrl('configure_api'));
        }
    }

    /**
     * Save public resources in database (handle form submission)
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function configurePublicResources(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $publicResourcesForm = $this->getPublicResourcesForm();
        $translator = $this->container->get('translator');

        $publicResourcesForm->handleRequest($request);
        if ($publicResourcesForm->isValid()) {
            $data = $publicResourcesForm->getData();

            $existingResources = $em->getRepository('\Newscoop\GimmeBundle\Entity\PublicApiResource')->findAll();

            foreach ($existingResources as $resource) {
                if (!in_array($resource->getResource(), $data['routes'])) {
                    $em->remove($resource);
                } else {
                    unset($data['routes'][array_search($resource->getResource(), $data['routes'])]);
                }
            }

            foreach ($data['routes'] as $resource) {
                $publicResource = new PublicApiResource();
                $publicResource->setResource($resource);
                $em->persist($publicResource);
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $translator->trans('publicresources.updated', array(), 'api')
            );

            return $this->redirect($this->generateUrl('configure_api'));
        }
    }

    /**
     * Get form
     *
     * @return Form
     */
    private function getPublicResourcesForm()
    {
        $em = $this->getDoctrine()->getManager();
        $router = $this->container->get('router');
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();

        // TODO:
        // * add way to allow anonymous access for comments posting (if it's enabled in publications settings)
        // * add way to allow anonymous access for feedback posting

        $apiRoutes = array();
        foreach ($allRoutes as $key => $route) {
            if (strpos($key, 'newscoop_gimme_') !== false) {
                $routeMethods = $route->getMethods();
                if (in_array('GET', $route->getMethods())) {
                    $apiRoutes[$key] = '['.$routeMethods[0].'] '.str_replace('{_format}', 'json', $route->getPath());
                }
            }
        }

        $existingResources = array();
        foreach ($em->getRepository('\Newscoop\GimmeBundle\Entity\PublicApiResource')->findAll() as $resource) {
            $existingResources[$resource->getResource()] = $resource->getResource();
        }

        $form = $this->createForm(new PublicResourcesType(), null, array(
            'choices' => $apiRoutes,
            'data' => $existingResources,
            'action' => $this->generateUrl('configure_api'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Get form
     *
     * @param Client $client
     *
     * @return Form
     */
    private function getClientForm($client = null)
    {
        if ($client == null) {
            $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
            $client = $clientManager->createClient();
        }

        $form = $this->createForm(new ClientType(), $client, array(
            'action' => $this->generateUrl('configure_api'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Get form
     *
     * @return Form
     */
    private function getRemoveClientForm()
    {
        $form = $this->get('form.factory')->createNamedBuilder('removeClient', 'form', null, array())
            ->add('client_id', 'hidden')
            ->setAction($this->generateUrl('configure_api'))
            ->setMethod('POST')
            ->getForm();

        return $form;
    }
}
