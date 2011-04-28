<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Theme;

/**
 */
class ThemeRepository extends EntityRepository
{
    /**
     * Get theme by id
     *
     * @param string $id
     * @param string $from
     * @return Newscoop\Entity\Theme
     */
    public function get($id, $from)
    {
        $configFile = "$from/$id/theme.xml";
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException("'$id' not found in '$from'");
        }

        $config = simplexml_load_file($configFile);
        $theme = new Theme($id, $config);
        return $theme;
    }

    /**
     * Find all themes from given source
     *
     * @param string $from
     * @return array
     */
    public function findAll($from = null)
    {
        $path = realpath($from);
        if (!$path) {
            throw new \InvalidArgumentException("'$from' not found");
        }

        // get stored info per theme
        $installed = array();
        foreach (parent::findAll() as $theme) {
            $installed[$theme->getId()] = $theme->getInstalledVersion();
        }

        $themes = array();
        foreach (glob("$path/*/theme.xml") as $configFile) {
            $id = basename(dirname($configFile));
            $config = simplexml_load_file($configFile);
            $theme = new Theme($id, $config);

            if (isset($installed[$id])) {
                $theme->setInstalledVersion($installed[$id]);
            }

            $themes[] = $theme;
        }

        return $themes;
    }

    /**
     * Install theme
     *
     * @param string $id
     * @param string $from
     * @return void
     */
    public function install($id, $from)
    {
        $em = $this->getEntityManager();
        $theme = $this->get($id, $from);
        $theme->setInstalledVersion();
        $em->persist($theme);
    }

    /**
     * Delete theme
     *
     * @param string $id
     * @param string $from
     * @return void
     */
    public function delete($id, $from)
    {
        $em = $this->getEntityManager();
        $theme = $this->find($id);
        $em->remove($theme);
    }
}
