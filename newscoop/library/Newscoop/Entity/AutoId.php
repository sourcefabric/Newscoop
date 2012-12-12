<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="AutoId")
 */
class AutoId
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="ArticleId")
     * @var int
     */
    private $articleId;

    /**
     * @ORM\Column(type="datetime", name="LogTStamp")
     * @var datetime
     */
    private $logTimestamp;

    /**
     * @ORM\Column(type="integer", name="TopicId")
     * @var int
     */
    private $topicId;

    /**
     * @ORM\Column(type="integer", name="translation_phrase_id")
     * @var int
     */
    private $translationPhraseId;

    /**
     * Get articleid
     * @return integer
     */
    public function getArticleId() {
        return $this->articleId;
    }

    /**
     * Get logTimestamp
     * @return datetime
     */
    public function getLogTimestamp() {
        return $this->logTimestamp;
    }

    /**
     * Set logTimestamp
     * @param \DateTime $logTimestamp
     */
    public function setLogTimestamp(\DateTime $logTimestamp) {
        $this->logTimestamp = $logTimestamp;

        return $this;
    }

    /**
     * Get topicId
     * @return integer
     */
    public function getTopicId() {
        return $this->topicId;
    }

    /**
     * Get translationPhraseId
     * @return integer
     */
    public function getTranslationPhraseId() {
        return $this->translationPhraseId;
    }
}
