<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\AutoIdRepository")
 * @ORM\Table(name="AutoId")
 */
class AutoId
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="ArticleId")
     * @var int
     */
    protected $articleId;

    /**
     * @ORM\Column(type="datetime", name="LogTStamp")
     * @var datetime
     */
    protected $logTimestamp;

    /**
     * @ORM\Column(type="integer", name="TopicId")
     * @var int
     */
    protected $topicId;

    /**
     * @ORM\Column(type="integer", name="translation_phrase_id")
     * @var int
     */
    protected $translationPhraseId;

    /**
     * Gets the value of articleId.
     *
     * @return int
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * Sets the value of articleId.
     *
     * @param int $articleId the article id
     *
     * @return self
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * Get logTimestamp
     *
     * @return datetime
     */
    public function getLogTimestamp()
    {
        return $this->logTimestamp;
    }

    /**
     * Set logTimestamp
     *
     * @param \DateTime $logTimestamp
     *
     * @return datetime
     */
    public function setLogTimestamp(\DateTime $logTimestamp)
    {
        $this->logTimestamp = $logTimestamp;

        return $this;
    }

    /**
     * Gets the value of translationPhraseId.
     *
     * @return int
     */
    public function getTranslationPhraseId()
    {
        return $this->translationPhraseId;
    }

    /**
     * Sets the value of translationPhraseId.
     *
     * @param int $translationPhraseId the translation phrase id
     *
     * @return self
     */
    public function setTranslationPhraseId($translationPhraseId)
    {
        $this->translationPhraseId = $translationPhraseId;

        return $this;
    }

    /**
     * Get topicId
     *
     * @return integer
     */
    public function getTopicId()
    {
        return $this->topicId;
    }
}
