<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Content;

use Doctrine\ORM\EntityManager;

/**
 * Section Service
 */
class SectionService
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
        $query = $this->orm->getRepository('Newscoop\Entity\Section')
            ->createQueryBuilder('s')
            ->select('s.number, s.name')
            ->orderBy('s.name, s.number')
            ->getQuery();

        $options = array();
        foreach ($query->getResult() as $row) {
            $options[$row['number']] = $row['name'];
        }

        return $options;
    }

    /**
     * Get name
     *
     * @param int $number
     * @param \Newscoop\Entity\Publication $publication
     * @param Newscoop\Entity\Language $language
     * @return string
     */
    public function getName($number, \Newscoop\Entity\Publication $publication, \Newscoop\Entity\Langauge $language = null)
    {
        foreach ($publication->getIssues() as $issue) {
            if ($language !== null && $issue->getLanguage() !== $language) {
                continue;
            }

            foreach ($issue->getSections() as $section) {
                if ($section->getNumber() == $number) {
                    return $section->getName();
                }
            }
        }

        return null;
    }
}
