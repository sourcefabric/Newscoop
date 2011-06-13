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
    /** @var string */
    private $basePath = '';

    /**
     * Set base path
     *
     * @param string $path
     * @return Newscoop\Entity\Repository\TemplateRepository
     */
    public function setBasePath($path)
    {
        $this->basePath = trim($path, '/');
        return $this;
    }

    /**
     * Get template for given key
     *
     * @param string $key
     * @return Newscoop\Entity\Template
     */
    public function getTemplate($key)
    {
        $key = $this->formatKey($key);

        $template = $this->findOneBy(array(
            'key' => $key,
        ));

        if (empty($template)) {
            $template = new Template($key);
            $em = $this->getEntityManager();
            $em->persist($template);
            $em->flush();
        }

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
            'key' => $this->formatKey($key),
        ));

        if (!empty($template)) {
            $em = $this->getEntityManager();
            $em->remove($template);
        }
    }

    /**
     * Update key
     *
     * @param string $old
     * @param string $new
     * @return void
     */
    public function updateKey($old, $new)
    {
        $em = $this->getEntityManager();

        $old = $this->formatKey($old);
        $new = $this->formatKey($new);

        $templates = $this->createQueryBuilder('t')
            ->where("t.key LIKE ?1")
            ->setParameter(1, "$old%")
            ->getQuery()
            ->getResult();

        foreach ($templates as $template) {
            if (strpos($template->getKey(), $old) === 0) {
                $template->setKey(str_replace($old, $new, $template->getKey()));
                $em->persist($template);
            }
        }

        $em->flush();
    }

    /**
     * Test is used
     *
     * @param string $key
     * @return bool
     */
    public function isUsed($key)
    {
        $em = $this->getEntityManager();

        $template = $this->findOneBy(array(
            'key' => $this->formatKey($key),
        ));

        if (!$template) {
            return false;
        }

        $dql = "SELECT COUNT(i.number)
                FROM Newscoop\Entity\Issue i
                WHERE i.template = ?1
                    OR i.sectionTemplate = ?1
                    OR i.articleTemplate = ?1";
        $query = $em->createQuery($dql);
        $query->setParameter(1, $template);
        if ($query->getSingleScalarResult()) {
            return true;
        }

        $dql = "SELECT COUNT(s.number)
                FROM Newscoop\Entity\Section s
                WHERE s.template = ?1
                    OR s.articleTemplate = ?1";
        $query = $em->createQuery($dql);
        $query->setParameter(1, $template);
        if ($query->getSingleScalarResult()) {
            return true;
        }

        return false;
    }

    /**
     * Format key
     *
     * @param string $key
     * @return string
     */
    public function formatKey($key)
    {
        $key = trim($key, '/');
        return ltrim("{$this->basePath}/$key", '/');
    }
}
