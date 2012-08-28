<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Entity;

use InvalidArgumentException;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Entity\User;

/**
 * @Entity
 * @Table(name="plugin_debate_vote")
 */
class Vote
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer", name="id_vote")
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Tageswoche\Entity\Debate", inversedBy="votes")
     * @JoinColumn(name="fk_debate_nr", referencedColumnName="debate_nr")
     * @var Tageswoche\Entity\Debate
     */
    private $debate;

    /**
     * @Column(type="integer", name="fk_answer_nr")
     * @var int
     */
    private $answer;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(name="fk_user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @Column(type="string", length=80, name="device_id")
     * @var string
     */
    private $device;

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
        return $this->answer;
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
     * Cast vote
     *
     * @param Tageswoche\Entity\Debate $debate
     * @param int $answer
     * @param string $deviceId
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function cast(Debate $debate, $answer, $device = null, User $user = null)
    {
        if ($device === null && $user === null) {
            throw InvalidArgumentException("Device or user must be set");
        }

        $this->debate = $debate;
        $this->answer = $answer;
        $this->device = $device;
        $this->user = $user;
        $this->added = new DateTime('now');
    }
}
