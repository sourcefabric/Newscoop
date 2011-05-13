<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Template;

/**
 * Template repository
 */
class TemplateRepository extends EntityRepository
{
    /**
     * Get template entity for given file
     *
     * @param string $key
     * @param SplFileInfo $fileInfo
     * @param bool $autopersist
     * @return Newscoop\Entity\Template
     */
    public function getTemplate($key)
    {
        $template = $this->findOneBy(array(
            'key' => $key,
        ));

        if (empty($template)) {
            $template = new Template($key);
        }

        $em = $this->getEntityManager();
        $em->persist($template);
        $em->flush();

        return $template;
    }

    /**
     * Save template
     *
     * @param Newscoop\Entity\Template $template
     * @param array $values
     * @return void
     */
    public function save(Template $template, array $values)
    {
        $template
            ->setCacheLifetime((int) $values['cache_lifetime']);

        $em = $this->getEntityManager();
        $em->persist($template);
    }

    /**
     * Delete template
     *
     * @param string $key
     * @param string $root
     * @return void
     */
    public function delete($key)
    {
        $template = $this->findOneBy(array(
            'key' => $key,
        ));

        if (!empty($template)) {
            $em = $this->getEntityManager();
            $em->remove($template);
        }
    }
}
