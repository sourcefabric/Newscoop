<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Plugins;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityManager;
use Newscoop\EventDispatcher\EventDispatcher;
use Newscoop\EventDispatcher\Events\PluginHooksEvent;
use Newscoop\EventDispatcher\Events\CollectObjectsDataEvent;
use Newscoop\EventDispatcher\Events\PluginPermissionsEvent;

/**
 * Plugins Service
 *
 * Service for plugins stuff
 */
class PluginsService
{
    /**
     * @var Newscoop\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Avaiable plugins
     * @var Collection
     */
    protected $availablePlugins;

    /**
     * @param Newscoop\EventDispatcher\EventDispatcher $dispatcher
     * @param Doctrine\ORM\EntityManager               $em
     */
    public function __construct($dispatcher, EntityManager $em)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    public function getAllAvailablePlugins()
    {
        if ($this->availablePlugins) {
            return $this->availablePlugins;
        }

        return $this->availablePlugins = $this->em->getRepository('Newscoop\Entity\Plugin')->findAll();
    }

    public function getEnabledPlugins()
    {
        $eb = new ExpressionBuilder();
        $expr = $eb->eq('enabled', true);
        $criteria = new Criteria($expr);

        $availablePlugins = new ArrayCollection($this->getAllAvailablePlugins());

        return $availablePlugins->matching($criteria);
    }

    /**
     * @param string $criteria
     */
    public function getPluginByCriteria($criteria, $needle)
    {
        $eb = new ExpressionBuilder();
        $expr = $eb->eq($criteria, $needle);
        $criteria = new Criteria($expr);

        $availablePlugins = new ArrayCollection($this->getAllAvailablePlugins());

        return $availablePlugins->matching($criteria);
    }

    /**
     * Dispatch hook event and render collected Response objects
     *
     * @param string $eventName
     * @param mixed  $subject
     * @param array  $options
     *
     * @return string
     */
    public function renderPluginHooks($eventName, $subject = null, $options = null)
    {
        $content = '';
        $pluginHooksEvent = $this->dispatcher->dispatch($eventName, new PluginHooksEvent($subject, $options));

        foreach ($pluginHooksEvent->getHooksResponses() as $key => $response) {
            $content .= $response->getContent();
        }

        return $content;
    }

    /**
     * Dispatch event for list objects registration
     *
     * @param mixed $subject
     * @param array $options
     *
     * @return string
     */
    public function collectListObjects($subject = null, $options = array())
    {
        $collectedData = array('listObjects' => array(), 'objectTypes' => array());
        $listObjectsRegistration = $this->dispatcher->dispatch('newscoop.listobjects.register', new CollectObjectsDataEvent($subject, $options));

        foreach ($listObjectsRegistration->getListObjects() as $key => $object) {
            $collectedData['listObjects'][$key] = $object;
        }

        foreach ($listObjectsRegistration->getObjectTypes() as $key => $object) {
            $collectedData['objectTypes'][$key] = $object;
        }

        return $collectedData;
    }

    public function isEnabled($pluginName)
    {
        $plugin = $this->getPluginByCriteria('name', $pluginName)->first();
        if ($plugin) {
            if ($plugin->getEnabled()) {
                return true;
            }
        }

        return false;
    }

    public function isInstalled($pluginName)
    {
        $plugin = $this->getPluginByCriteria('name', $pluginName)->first();
        if ($plugin) {
            return true;
        }

        return false;
    }

    public function getPluginsDir()
    {
        $pluginsDir = __DIR__ . '/../../../../plugins';

        if (file_exists($pluginsDir)) {
            return realpath($pluginsDir);
        }

        return $pluginsDir;
    }

    /**
     * Dispatch event for plugins permissions
     *
     * @param mixed $subject
     * @param array $options
     *
     * @return array
     */
    public function collectPermissions($subject = null, $options = array())
    {
        $collectedPermissionsData = array();
        $collectedPermissions = $this->dispatcher->dispatch('newscoop.plugins.permissions.register', new PluginPermissionsEvent($subject, $options));

        foreach ($collectedPermissions->getPermissions() as $key => $object) {
            $collectedPermissionsData[$key] = $object;
        }

        return $collectedPermissionsData;
    }

    /**
     * Add dynamic right
     *
     * @param array $right
     * @return void
     *
     * @deprecated will be deprecated since version 4.5
     */
    public function addRight(array $right)
    {
        $connection = $this->em->getConnection();
        // get next id
        $query = $connection->query('SELECT MAX(right_id) as max FROM liveuser_rights')->fetch();
        $lastId = (int) $query['max'];
        $nextId = $lastId + 1;
        $areaId = (int) $right['area_id'];

        $connection->executeUpdate('INSERT IGNORE INTO liveuser_rights (right_id, area_id, right_define_name) VALUES (:next, :area, :name)', array(
            'next' => $nextId,
            'area' => $areaId,
            'name' => $right['right_define_name']
        ));
    }

    /**
     * Save plugin permissions in Newscoop ACL
     *
     * @param  array  $pluginPermissions Plugin permissions
     *
     * @return void
     */
    public function savePluginPermissions(array $pluginPermissions)
    {
        try {
            foreach ($pluginPermissions as $permissionArray) {
                foreach ($permissionArray as $permission => $permissionLabel) {
                    $this->addRight(array(
                        'area_id' => 0,
                        'right_define_name' => $permission,
                        'has_implied' => 1
                    ));

                    $permissionsArray = explode('_', $permission);
                    $values = array(
                        'role' => 1, //give access for admin group by default
                        'type' => 'allow',
                        'resource' => $permissionsArray[0] . '-' . $permissionsArray[1],
                        'action' => $permissionsArray[2]
                    );

                    $this->em->getRepository('Newscoop\Entity\Acl\Rule')->save($values);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Error setting up plugin permissions", 1);
        }
    }

    /**
     * Get rights
     *
     * @param array $params
     * @return array
     */
    public function getRights(array $params)
    {
        $connection = $this->em->getConnection();

        $permission = $params['filters']['right_define_name'];
        $query = $connection->executeQuery('SELECT right_id FROM liveuser_rights WHERE right_define_name = :permission', array(
            'permission' => $permission
        ));

        return $query->fetch();
    }

    /**
     * Remove right
     *
     * @param array $params
     * @return void
     */
    public function removeRight(array $params)
    {
        $rightId = (int) $params['right_id'];

        // get permission
        $connection = $this->em->getConnection();
        $query = $connection->executeQuery('SELECT right_define_name as name FROM liveuser_rights WHERE right_id  = :rightId', array(
            'rightId' => $rightId
        ));

        $permission = $query->fetch();
        $permission = explode('_', $permission['name']);

        $rules = $this->em->getRepository('Newscoop\Entity\Acl\Rule')->findBy(array(
            'resource' => $permission[0] . '-' . $permission[1]
        ));

        if ($rules) {
            foreach ($rules as $key => $rule) {
                $this->em->remove($rule);
            }

            $this->em->flush();
        }

        $connection->executeUpdate('DELETE FROM liveuser_rights WHERE right_id = :rightId', array(
            'rightId' => $rightId
        ));
    }

    /**
     * Remove plugin permissions from database
     *
     * @param  array  $pluginPermissions Plugin permissions
     *
     * @return void
     */
    public function removePluginPermissions(array $pluginPermissions)
    {
        try {
            foreach ($pluginPermissions as $permissionArray) {
                foreach ($permissionArray as $permission => $permissionLabel) {
                    $filter = array(
                        "fields" => array("right_id"),
                        "filters" => array("right_define_name" => $permission)
                    );

                    $rights = $this->getRights($filter);
                    if (!empty($rights)) {
                        $this->removeRight(array('right_id' => $rights['right_id']));
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Error removing plugin permissions", 1);
        }
    }
}
