<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Comment;

use Newscoop\Entity\Languages,
    Newscoop\Entity\Publications;

/**
 * Publications entity
 * @entity
 * @table(name="CommentAcceptance")
 */
class Acceptance
{
    /**
     * @var string to code mapper for type
     */
    static $type_enum = array(
        'allow',
        'deny'
    );

    /**
     * @var string to code mapper for search_type
     */
    static $seach_type_enum = array(
        'normal',
        'regex'
    );

    /**
     * @var string to code mapper for for_colum
     */
    static $for_colum_enum = array(
        'ip',
        'name',
        'email'
    );

    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @manyToOne(targetEntity="Publications")
     * @joinColumn(name="fk_forum_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publications
     */
    private $forum;

    /**
     * @column(type="integer")
     * @var int
     */
    private $for_column;

    /**
     * @column(type="integer")
     * @var int
     */
    private $type;

    /**
     * @column(type="string", length=255)
     * @var int
     */
    private $search;

    /**
     * @column(type="integer")
     * @var int
     */
    private $search_type;


    /**
     * Set acceptance id
     *
     * @param int $p_id
     * @return Newscoop\Entity\CommentsAcceptance
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get acceptance id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id = $p_id;
    }

    /**
     * Set acceptance forum
     *
     * @param Newscoop\Entity\Publications $p_forum
     * @return Newscoop\Entity\CommentsAcceptance
     */
    public function setForum(Publications $p_forum)
    {
        $this->forum = $p_forum;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get acceptance iforum
     *
     * @return int
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Set acceptance for_column
     *
     * @param string $p_for_column
     * @return Newscoop\Entity\CommentsAcceptance
     */
    public function setForColumn($p_for_column)
    {
        $for_column_enum = array_flip(self::$for_column_enum);
        $this->for_column = $for_column_enum[$p_type];
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get acceptance for_column
     *
     * @return int
     */
    public function getForColumn()
    {
        return self::$for_column_enum[$this->for_column];
    }

    /**
     * Set acceptance type
     *
     * @param string $p_type
     * @return Newscoop\Entity\CommentsAcceptance
     */
    public function setType($p_type)
    {
        $type_enum = array_flip(self::$type_enum);
        $this->type = $type_enum[$p_type];
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get acceptance type
     *
     * @return int
     */
    public function getType()
    {
        return self::$type_enum[$this->type];
    }

    /**
     * Set acceptance search by
     *
     * @param string $p_search
     * @return Newscoop\Entity\CommentsAcceptance
     */
    public function setSearch($p_search)
    {
        $this->search = $p_search;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get acceptance search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set acceptance search type
     *
     * @param string $p_search_type
     * @return Newscoop\Entity\CommentsAcceptance
     */
    public function setSearchType($p_search_type)
    {
        $search_type_enum = array_flip(self::$search_type_enum);
        $this->search_type = $search_type_enum[$p_type];
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get acceptance type
     *
     * @return int
     */
    public function getSearchType()
    {
        return self::$search_type_enum[$this->search_type];
    }

}
