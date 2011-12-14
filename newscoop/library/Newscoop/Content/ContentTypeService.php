<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Content;

use Doctrine\ORM\EntityManager;

/**
 * Content Type Service
 */
class ContentTypeService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(EntityManager $orm)
    {
        $this->orm = $orm;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
	    $types = \ArticleType::GetArticleTypes($p_includeHidden = false);

        $options = array();
        foreach ($types as $type) {
            $options[$type] = $type;
        }

        return $options;
    }
}
