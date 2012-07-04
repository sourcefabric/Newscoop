<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

require_once __DIR__ . '/AbstractController.php';
require_once __DIR__ . '/../forms/Profile.php';

/**
 * Profile API
 */
class Api_ProfileController extends AbstractController
{
    const TYPE_EDITOR = 'editor';
    const TYPE_BLOGGER = 'blogger';
    const TYPE_MEMBER = 'community_member';

    const IMAGE_FIELD = 'profile_image_data';

    /**
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $form = new Api_Form_Profile();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $values['image'] = $this->getImage();
            $this->_helper->service('user')->updateProfile($values, $user);
            $this->_helper->redirector('index');
        } else if ($this->getRequest()->isPost()) {
            $this->_helper->json($form->getErrors());
        }

        $subscriptionValidUntil = $this->_helper->service('user_subscription')->getValidUntilDate($user);
        $this->_helper->json(array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'customer_id' => $user->getSubscriber(),
            'birth_date' => $user->getAttribute('birth_date'),
            'facebook' => $user->getAttribute('facebook'),
            'google' => $user->getAttribute('google'),
            'twitter' => $user->getAttribute('twitter'),
            'website' => $user->getAttribute('website'),
            'bio' => $user->getAttribute('bio'),
            'email_public' => (bool) $user->getAttribute('email_public'),

            'member_since' => $user->getCreated()->format('Y-m-d'),
            'account_type' => $this->getUserType($user),
            'profile_image_url' => $this->getUserImage($user),
            'verified' => (bool) $user->getAttribute('is_verified'),
            'print_subscription' => $subscriptionValidUntil !== null,
            'print_subscription_valid_until' => $subscriptionValidUntil !== null ? $subscriptionValidUntil->format('Y-m-d') : null,
            'digital_upgrade' => (bool) $user->getAttribute(self::DIGITAL_UPGRADE),
            'free_digital_upgrade_consumed' => $this->_helper->service('mobile.free_upgrade')->isConsumed($user),
            'subscriber_topics' => array_map(function($topic) {
                return array(
                    'topic_id' => $topic->getTopicId(),
                    'topic_name' => $topic->getName(),
                );
            }, $this->_helper->service('user.topic')->getTopics($user)),
        ));
    }

    /**
     * Get user type
     *
     * @param Newscoop\Entity\User $user
     * @return string
     */
    private function getUserType(User $user)
    {
        if ($this->_helper->service('user')->isEditor($user)) {
            return self::TYPE_EDITOR;
        }

        if ($this->_helper->service('blog')->isBlogger($user)) {
            return self::TYPE_BLOGGER;
        }

        return self::TYPE_MEMBER;
    }

    /**
     * Get user image
     *
     * @param Newscoop\Entity\User $user
     * @return string
     */
    private function getUserImage(User $user)
    {
        $image = $this->_helper->service('image')->getUserImage($user);
        return $image ? $this->view->serverUrl($this->view->url(array(
            'src' => $image,
        ), 'image')) : null;
    }

    /**
     */
    public function subscriptionstatusAction()
    {
        $this->assertIsSecure();

        $user = $this->getUser();
        $subscription = $this->_helper->service('user_subscription')->getActiveSubscription($user);

        $this->_helper->json(array(
            'print_subscription' => !empty($subscription),
            'print_subscription_valid_until' => !empty($subscription) ? $subscription['valid_until'] : null,
            'digital_upgrade' => !empty($subscription) ? (bool) $user->getAttribute(self::DIGITAL_UPGRADE) : false,
            'free_digital_upgrade_consumed' => $this->_helper->service('mobile.free_upgrade')->isConsumed($user),
        ));
    }

    /**
     * Get image name based on type
     *
     * @param string $mime
     * @return string
     */
    private function getImageExtension($mime)
    {
        switch($mime) {
            case 'image/jpeg':
                return 'jpg';

            case 'image/png':
                return 'png';

            case 'image/gif':
                return 'gif';

            default:
                throw \InvalidArgumentException("Mime not supported");
        }
    }

    /**
     * Get image
     *
     * @return string
     */
    protected function getImage()
    {
        if (empty($_POST[self::IMAGE_FIELD])) {
            return null;
        }

        $tempnam = tempnam('/tmp', 'img');
        file_put_contents($tempnam, $_POST[self::IMAGE_FIELD]);
        $info = getimagesize($tempnam);
        if (!$info) {
            return null;
        }

        return $this->_helper->service('image')->save(array(
            'tmp_name' => $tempnam,
            'name' => uniqid() . '.' . $this->getImageExtension($info['mime']),
            'type' => $info['mime'],
        ));
    }
}
