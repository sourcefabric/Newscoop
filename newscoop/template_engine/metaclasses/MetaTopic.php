<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
require_once __DIR__.'/MetaDbObject.php';

/**
 * Meta topic class.
 */
class MetaTopic extends MetaDbObject
{
    /**
     * Topic.
     *
     * @var Topic object
     */
    private $topic;

    /**
     * Topic id.
     *
     * @var int
     */
    public $identifier;

    /**
     * Topic name.
     *
     * @var string
     */
    public $name;

    /**
     * Topic full name e.g. topic:en.
     *
     * @var string
     */
    public $value;

    /**
     * Is topic root.
     *
     * @var bool
     */
    public $is_root;

    /**
     * Parent topic.
     *
     * @var MetaTopic
     */
    public $parent;

    /**
     * Checks if topic is defined.
     *
     * @var bool
     */
    public $defined;

    /**
     * Alias to identifier.
     *
     * @var int
     */
    public $id;

    /**
     * Construct.
     *
     * @param string $topicIdOrName
     */
    public function __construct($topicIdOrName = null, $languageCode = null)
    {
        if (!$topicIdOrName) {
            return;
        }

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('MetaTopic', $topicIdOrName), 'topic');
        if ($cacheService->contains($cacheKey)) {
            $this->topic = $cacheService->fetch($cacheKey);
        } else {
            if ($languageCode) {
                $locale = $languageCode;
            } else {
                $locale = $this->getLocale();
            }

            $topicService = \Zend_Registry::get('container')->getService('newscoop_newscoop.topic_service');
            $topic = $topicService->getTopicBy($topicIdOrName, $locale);
            if ($topic) {
                $topic->setTranslatableLocale($locale);
                $this->topic = $topic;
            }

            if (!$this->topic) {
                return;
            }

            $cacheService->save($cacheKey, $this->topic);
        }

        $this->id = $this->topic->getId();
        $this->identifier = $this->topic->getId();
        $this->name = $this->getName();
        $this->value = $this->getValue();
        $this->is_root = $this->isRoot();
        $this->parent = $this->getParent();
        $this->defined = isset($this->topic);
    }

    protected function getName()
    {
        if ($this->topic->getTitle() !== '') {
            return $this->topic->getTitle();
        }

        $titleByLanguage = null;
        $currentLocale = \CampTemplate::singleton()->context()->language->code;
        foreach ($this->topic->getTranslations() as $translation) {
            if ($translation->getLocale() === $currentLocale) {
                $titleByLanguage = $translation->getContent();
            }
        }

        return $titleByLanguage;
    }

    protected function getLocale()
    {
        return \CampTemplate::singleton()->context()->language->code;
    }

    protected function getValue()
    {
        if (!$this->topic && !$this->name) {
            return;
        }

        return $this->name.':'.$this->topic->getTranslatableLocale();
    }

    protected function isRoot()
    {
        if ($this->topic && $this->topic->getRoot()) {
            if ($this->topic->getRoot() == $this->id) {
                return true;
            }

            return false;
        }
    }

    protected function getParent()
    {
        if ($this->topic && $this->parent) {
            return new self($this->parent->getId(), $this->topic->getTranslatableLocale());
        }

        return;
    }

    public static function GetTypeName()
    {
        return 'topic';
    }
}
