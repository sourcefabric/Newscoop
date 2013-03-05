<?php

namespace Newscoop\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
/**
 * @Gedmo\Tree(type="nested")
 * @Table(name="notice_category")
 * use repository for handy tree functions
 * @Entity(repositoryClass="Newscoop\Entity\Repository\NoticeCategoryRepository")
 */
class NoticeCategory
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @Gedmo\Sluggable(slugField="slug")
     * @Column(name="title", type="string", length=64)
     */
    private $title;

    /**
     * @Gedmo\Slug
     * @Column(name="slug", type="string", length=128, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\TreeLeft
     * @Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ManyToOne(targetEntity="NoticeCategory", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @OneToMany(targetEntity="NoticeCategory", mappedBy="parent")
     * @OrderBy({"lft" = "ASC"})
     */
    private $children;

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setParent(NoticeCategory $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getLvl()
    {
        return $this->lvl;
    }
}