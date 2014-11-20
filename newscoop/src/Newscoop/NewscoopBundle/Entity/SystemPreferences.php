<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * System Preferences entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="SystemPreferences")
 */
class SystemPreferences 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, name="varname")
     * @var string
     */
    public $option;

    /**
     * @ORM\Column(type="string", length=100, name="value", nullable=true)
     * @var string
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime", name="last_modified")
     * @var datetime
     */
    protected $created_at;

    public function __construct() {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param  string $value
     * @return SystemPreferences
     */
    public function setValue($value)
    {
        $this->value = $value;
        
        return $this;
    }

    /**
     * Set option
     *
     * @param  string $option
     * @return SystemPreferences
     */
    public function setOption($option)
    {
        $this->option = $option;
        
        return $this;
    }

    /**
     * Set create date
     *
     * @param \DateTime $created_at
     * @return SystemPreferences
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
        
        return $this;
    }
}