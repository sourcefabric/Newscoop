<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Meta topic class
 */
class MetaTopic
{
    /**
     * Topic
     * @var Topic object
     */
    private $topic;

    /**
     * Topic id
     * @var integer
     */
    public $identifier;

    /**
     * Topic name
     * @var string
     */
    public $name;

    /**
     * Topic full name e.g. topic:en
     * @var string
     */
    public $value;

    /**
     * Is topic root
     * @var boolean
     */
    public $is_root;

    /**
     * Parent topic
     * @var MetaTopic
     */
    public $parent;

    /**
     * Checks if topic is defined
     * @var boolean
     */
    public $defined;

    /**
     * Alias to identifier
     * @var integer
     */
    public $id;

    /**
     * Construct
     *
     * @param string $topicIdOrName
     */
    public function __construct($topicIdOrName = null)
    {
        if (!$topicIdOrName) {
            return null;
        }

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('MetaTopic', $topicIdOrName), 'topic');
        if ($cacheService->contains($cacheKey)) {
            $this->topic = $cacheService->fetch($cacheKey);
        } else {
            $em = \Zend_Registry::get('container')->getService('em');
            $repository = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
            $locale = $this->getLocale();
            $topic = $repository->getTopicByIdOrName($topicIdOrName, $locale)->getArrayResult();

            if (!empty($topic)) {
                $this->topic = $topic[0];
                $this->topic['locale'] = $locale;
            }

            $cacheService->save($cacheKey, $this->topic);
        }

        if (empty($this->topic)) {
            return null;
        }

        $this->id = $this->topic['id'];
        $this->identifier = $this->topic['id'];
        $this->name = $this->getName();
        $this->value = $this->getValue();
        $this->is_root = $this->isRoot();
        $this->parent = $this->getParent();
        $this->defined = isset($this->topic);
    }

    protected function getName($languageId = null)
    {
        if ($languageId) {
            $em = \Zend_Registry::get('container')->getService('em');
            $locale = $em->getReference('Newscoop\Entity\Language', $languageId)->getCode();
            $titleByLanguage = null;
            foreach ($this->topic['translations'] as $translation) {
                if ($translation['locale'] === $locale) {
                    $titleByLanguage = $translation['content'];
                }
            }

            return $titleByLanguage;
        }

        return $this->topic['title'];
    }

    protected function getLocale()
    {
        return \CampTemplate::singleton()->context()->language->code;
    }

    protected function getValue()
    {
        if (!isset($this->topic) || empty($this->topic)) {
            return null;
        }

        $name = $this->topic['title'];
        if (empty($name)) {
            return null;
        }

        return $name.':'.$this->topic['locale'];
    }

    protected function isRoot()
    {
        if (isset($this->topic['id']) && isset($this->topic['root'])) {
            if ($this->topic['root'] == $this->topic['id']) {
                return true;
            }

            return false;
        }
    }

    protected function getParent()
    {
        if (isset($this->topic['id']) && isset($this->topic['parent'])) {
            return new MetaTopic($this->topic['parent']['id']);
        }

        return null;
    }

    public static function GetTypeName()
    {
        return 'topic';
    }
}
