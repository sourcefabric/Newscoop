<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Theme,
    Newscoop\Entity\Theme\Loader;

/**
 */
class ThemeRepository extends EntityRepository
{
    /** @var Newscoop\Theme\Loader\Loader */
    private $loader;

    /**
     * Set theme loader
     *
     * @param Newscoop\Theme\Loader\Loader
     * @return void
     */
    public function setLoader(Loader\Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Find all themes from given source
     *
     * @param string $from
     * @return array
     */
    public function findAll()
    {
        // get installed versions
        $installed = array();
        foreach (parent::findAll() as $theme) {
            $installed[$theme->getOffset()] = $theme;
        }

        $themes = $this->loader->findAll();
        foreach ($themes as $theme) {
            $offset = $theme->getOffset();
            if (isset($installed[$offset])) {
                $installedTheme = $installed[$offset];
                $theme
                    ->setId($installedTheme->getId())
                    ->setInstalledVersion($installedTheme->getInstalledVersion());
            }
        }

        return $themes;
    }

    /**
     * Install theme
     *
     * @param string $id
     * @return void
     */
    public function install($offset)
    {
        $theme = $this->loader->find($offset);
        $theme->setInstalledVersion();
        $em = $this->getEntityManager();
        $em->persist($theme);
    }

    /**
     * Uninstall theme
     *
     * @param int $id
     * @return void
     */
    public function uninstall($id)
    {
        $em = $this->getEntityManager();
        $theme = $em->getReference($this->getEntityName(), (int) $id);
        $em->remove($theme);
    }
}
