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
     * @param SplFileInfo $file
     * @param string $root
     * @param bool $autopersist
     * @return Newscoop\Entity\Template
     */
    public function getTemplate(\SplFileInfo $file, $root, $autopersist = TRUE)
    {
        if ($file->isDir()) { // don't manage dirs
            return new Template($file, $root);
        }

        $template = $this->findOneBy(array(
            'root_path' => str_replace("$root/", '', $file->getPathname()),
        ));

        if (!empty($template)) { // managed template
            $template->setFile($file);
            return $template;
        }

        $template = new Template($file, $root);

        // start manage template
        if ($autopersist) {
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
            ->setContent($values['content'])
            ->setCacheLifetime((int) $values['cache_lifetime']);

        $em = $this->getEntityManager();
        $em->persist($template);
    }

    /**
     * Delete template
     *
     * @param \SplFileInfo $file
     * @param string $root
     * @return void
     */
    public function delete(Template $template, $root)
    {
        if (!$template->isWritable()) {
            throw new \InvalidArgumentException($template->getRealpath());
        }

        if ($template->isDir()) { // delete directory
            foreach (new \DirectoryIterator($template->getRealPath()) as $file) {
                if ($file->isDot()) {
                    continue; // ingore dots
                }

                $this->delete($this->getTemplate($file, $root, FALSE), $root);
            }

            // delete current dir at last
            rmdir($template->getRealPath());
            return;
        }

        // remove file
        unlink($template->getRealpath());
        if ($template->getId()) {
            $em = $this->getEntityManager();
            $em->remove($template);
        }
    }
}
