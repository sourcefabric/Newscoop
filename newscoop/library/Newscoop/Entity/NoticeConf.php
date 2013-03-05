<?php

namespace Newscoop\Entity;
/**
 * @Table(name="notice_conf")
 * @Entity(repositoryClass="Newscoop\Entity\Repository\NoticeConfRepository")
 */
class NoticeConf
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(name="conf_name", type="string", length=64)
     */
    private $conf_name;

    /**
     * @Column(name="options", type="array")
     */
    private $options;

    public function setConfName($conf_name)
    {
        $this->conf_name = $conf_name;
    }

    public function getConfName()
    {
        return $this->conf_name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }
}