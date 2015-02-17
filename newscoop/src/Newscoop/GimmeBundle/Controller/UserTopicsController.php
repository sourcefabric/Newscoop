<?php

/**
 * @package Newscoop\GimmeBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Newscoop\NewscoopBundle\Entity\Topic;
use Newscoop\Exception\InvalidParametersException;

class UserTopicsController extends FOSRestController
{
    /**
     * Get topics followed by user
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when topics are not found"
     *     },
     *     parameters={
     *         {"name"="language", "dataType"="string", "required"=false, "description"="Language code"}
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="User Id"}
     *     }
     * )
     *
     * @Route("/users/{id}/topics.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getUserTopicsAction(Request $request, $id)
    {
        $user = $this->findUserByIdOr404($id);
        $userTopicsService = $this->get('user.topic');
        $language = $request->query->get('language', null);
        $userTopics = $userTopicsService->getTopics($user, $language);
        $paginator = $this->get('newscoop.paginator.paginator_service');
        $paginator->setUsedRouteParams(array('id' => $id, 'language' => $language));
        $userTopics = $paginator->paginate($userTopics, array(
            'distinct' => false,
        ));

        return $userTopics;
    }

    /**
     * Link topic to user
     *
     * **topics headers**:
     *
     *     header name: "link"
     *     header value: "</api/topics/1; rel="topic">"
     *
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="User Id"}
     *     }
     * )
     *
     * @Route("/users/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("LINK")
     * @View(statusCode=201)
     */
    public function linkToUserAction(Request $request, $id)
    {
        $user = $this->findUserByIdOr404($id);
        $this->linkOrUnlinkResources($request->attributes->get('links', array()), $user, true);
    }

    /**
     * Unlink topic from the user
     *
     * **topics headers**:
     *
     *     header name: "link"
     *     header value: "</api/topics/1; rel="topic">"
     *
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="User Id"}
     *     }
     * )
     *
     * @Route("/users/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("UNLINK")
     * @View(statusCode=204)
     */
    public function unlinkFromUserAction(Request $request, $id)
    {
        $user = $this->findUserByIdOr404($id);
        $this->linkOrUnlinkResources($request->attributes->get('links', array()), $user);
    }

    private function findUserByIdOr404($id)
    {
        $em = $this->container->get('em');
        $user = $em->getRepository('Newscoop\Entity\User')->findOneBy(array(
            'id' => $id,
        ));

        if (!$user) {
            throw new NotFoundHttpException('User was not found');
        }

        return $user;
    }

    private function linkOrUnlinkResources(array $resources, $user, $follow = false)
    {
        $matched = false;
        foreach ($resources as $key => $objectArray) {
            if (!is_array($objectArray)) {
                return true;
            }

            $object = $objectArray['object'];
            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof Topic) {
                $userTopicService = $this->get('user.topic');
                if ($follow) {
                    $userTopicService->followTopic($user, $object);
                } else {
                    $userTopicService->unfollowTopic($user, $object);
                }

                $matched = true;

                continue;
            }
        }

        if ($matched === false) {
            throw new InvalidParametersException('Any supported link object not found');
        }
    }
}
