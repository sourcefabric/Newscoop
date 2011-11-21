<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity(repositoryClass="Newscoop\Entity\Repository\AuditRepository")
 * @Table(name="audit_event")
 */
class AuditEvent
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(length=80)
     * @var string
     */
    private $resource_type;

    /**
     * @Column(length=80, nullable=True, name="resource_id")
     * @var string
     */
    private $resource_id;

    /**
     * @Column(length=255, nullable=True)
     * @var string
     */
    private $resource_title;

    /**
     * @Column(type="text", nullable=True, name="resource_diff")
     * @var string
     */
    private $resource_diff;

    /**
     * @Column(length=80)
     * @var string
     */
    private $action;

    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    private $created;

    /**
     * @Column(type="boolean")
     * @var bool
     */
    private $is_public = FALSE;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     */
    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set resource type.
     *
     * @param string $resourceType
     * @return Newscoop\Entity\AuditEvent
     */
    public function setResourceType($resourceType)
    {
        $this->resource_type = (string) $resourceType;
        return $this;
    }

    /**
     * Get resource type.
     *
     * @return string
     */
    public function getResourceType()
    {
        return $this->resource_type;
    }

    /**
     * Set action.
     *
     * @param string $action
     * @return Newscoop\Entity\AuditEvent
     */
    public function setAction($action)
    {
        $this->action = (string) $action;
        return $this;
    }

    /**
     * Get action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set resource id.
     *
     * @param mixed $resourceId
     * @return Newscoop\Entity\AuditEvent
     */
    public function setResourceId($resourceId)
    {
        $this->resource_id = json_encode($resourceId);
        return $this;
    }

    /**
     * Get resource id.
     *
     * @return mixed
     */
    public function getResourceId()
    {
        return json_decode($this->resource_id, True);
    }

    /**
     * Set resource title.
     *
     * @param string $resourceTitle
     * @return Newscoop\Entity\AuditEvent
     */
    public function setResourceTitle($resourceTitle)
    {
        $this->resource_title = $resourceTitle;
        return $this;
    }

    /**
     * Get resource title.
     *
     * @return string
     */
    public function getResourceTitle()
    {
        return $this->resource_title;
    }

    /**
     * Set user
     *
     * @param Newscoop\Entity\User $user
     * @return Newscoop\Entity\AuditEvent
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user.
     *
     * @return Newscoop\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set resource diff.
     *
     * @param array $diff
     * @return Newscoop\Entity\AuditEvent
     */
    public function setResourceDiff($diff)
    {
        $this->resource_diff = json_encode($diff);
        return $this;
    }

    /**
     * Get resource diff.
     *
     * @return array
     */
    public function getResourceDiff()
    {
        return json_decode($this->resource_diff, true);
    }

    /**
     * Get created.
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
