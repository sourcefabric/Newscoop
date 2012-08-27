<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="plugin_debate_vote")
 */
class Vote
{
    /**
     * @Id @Column(type="integer", name="id_vote")
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Tageswoche\Entity\Debate", inversedBy="votes")
     * @JoinColumn(name="fk_debate_nr", referencedColumnName="debate_nr")
     */
    private $debate;

    /**
     * @Column(type="integer", name="fk_answer_nr")
     * @var int
     */
    private $answerId;

    /**
     * @Column(type="datetime", name="added")
     * @var DateTime
     */
    private $added;

    /**
     * Get answer id
     *
     * @return int
     */
    public function getAnswerId()
    {
        return $this->answerId;
    }

    /**
     * Test if vote was added between given dates
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function isWithin($from, $to)
    {
        $added = $this->added->format('Y-m-d');
        if (empty($from) && empty($to)) {
            return true;
        }

        return $added >= $from && $added < $to;
    }

    /**
     * Get added
     *
     * @return DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }
}
