<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Plugin;

/**
 * Plugin repository
 */
class PluginRepository extends EntityRepository
{
    public function addPlugin($pluginDetails, $details = '', $installedWith = 'packagist')
    {
        $em = $this->getEntityManager();

        $plugin = $em->getRepository('Newscoop\Entity\Plugin')
            ->findOneByName($pluginDetails['name']);

        if (!$plugin) {
            $authors = array();
            foreach ($pluginDetails['authors'] as $key => $value) {
                $authors[] = $value['name'] . '<' . $value['email'] . '>';
            }

            $plugin = new Plugin();
            $plugin->setName($pluginDetails['name']);
            $plugin->setVersion($pluginDetails['version']);
            $plugin->setDetails($details);
            $plugin->setDescription($pluginDetails['description']);
            $plugin->setAuthor(implode(',', $authors));
            $plugin->setLicense(implode(',', $pluginDetails['license']));
            $plugin->setType('thirdparty');
            $plugin->setInstalledWith($installedWith);

            $em->persist($plugin);
            $em->flush();
        }
    }

    public function removePlugin($pluginName)
    {
        $em = $this->getEntityManager();

        $plugin = $em->getRepository('Newscoop\Entity\Plugin')
            ->findOneByName($pluginName);

        if ($plugin) {
            $em->remove($plugin);
            $em->flush();
        }
    }

    public function updatePlugin($pluginDetails, $details = null)
    {
        $em = $this->getEntityManager();

        $plugin = $em->getRepository('Newscoop\Entity\Plugin')
            ->findOneByName($pluginDetails['name']);

        if ($plugin) {
            $authors = array();
            foreach ($pluginDetails['authors'] as $key => $value) {
                $authors[] = $value['name'] . '<' . $value['email'] . '>';
            }

            $plugin->setName($pluginDetails['name']);
            $plugin->setVersion($pluginDetails['version']);

            if ($details) {
                $plugin->setDetails($details);
            }

            $plugin->setDescription($pluginDetails['description']);
            $plugin->setAuthor(implode(',', $authors));
            $plugin->setLicense(implode(',', $pluginDetails['license']));
            $plugin->setUpdatedAt(new \DateTime());

            $em->flush();
        }
    }
}
