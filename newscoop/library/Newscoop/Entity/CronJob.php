<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cron job entity
 *
 * @ORM\Entity
 * @ORM\Table(name="cron_jobs")
 */
class CronJob
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="name")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="command")
     * @var string
     */
    protected $command;

    /**
     * @ORM\Column(type="string", name="schedule")
     * @var string
     */
    protected $schedule;

    /**
     * @ORM\Column(type="boolean", name="is_debug")
     * @var boolean
     */
    protected $debug;

    /**
     * @ORM\Column(type="string", name="dateFormat", nullable=true)
     * @var string
     */
    protected $dateFormat;

    /**
     * @ORM\Column(type="string", name="output", nullable=true)
     * @var string
     */
    protected $output;

    /**
     * @ORM\Column(type="string", name="runOnHost", nullable=true)
     * @var string
     */
    protected $runOnHost;

    /**
     * @ORM\Column(type="string", name="environment", nullable=true)
     * @var string
     */
    protected $environment;

    /**
     * @ORM\Column(type="string", name="runAs", nullable=true)
     * @var string
     */
    protected $runAs;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @var boolean
     */
    protected $enabled;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var datetime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->enabled = true;
        $this->debug = false;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of command.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Sets the value of command.
     *
     * @param string $command the command
     *
     * @return self
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Gets the value of schedule.
     *
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Sets the value of schedule.
     *
     * @param string $schedule the schedule
     *
     * @return self
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Gets the value of dateFormat.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Sets the value of dateFormat.
     *
     * @param string $dateFormat the date format
     *
     * @return self
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * Gets the value of output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets the value of output.
     *
     * @param string $output the output
     *
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Gets the value of runOnHost.
     *
     * @return string
     */
    public function getRunOnHost()
    {
        return $this->runOnHost;
    }

    /**
     * Sets the value of runOnHost.
     *
     * @param string $runOnHost the run on host
     *
     * @return self
     */
    public function setRunOnHost($runOnHost)
    {
        $this->runOnHost = $runOnHost;

        return $this;
    }

    /**
     * Gets the value of environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Sets the value of environment.
     *
     * @param string $environment the environment
     *
     * @return self
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Gets the value of runAs.
     *
     * @return string
     */
    public function getRunAs()
    {
        return $this->runAs;
    }

    /**
     * Sets the value of runAs.
     *
     * @param string $runAs the run as
     *
     * @return self
     */
    public function setRunAs($runAs)
    {
        $this->runAs = $runAs;

        return $this;
    }

    /**
     * Gets the value of createdAt.
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the value of createdAt.
     *
     * @param datetime $createdAt the created at
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Gets the value of enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the value of enabled.
     *
     * @param boolean $enabled the enabled
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Gets the value of debug.
     *
     * @return boolean
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Sets the value of debug.
     *
     * @param boolean $debug the debug
     *
     * @return self
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }
}
