<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Utils;

/**
 * Old permission to acl translator
 */
class PermissionToAcl
{
    /** @var array */
    private static $filters = array(
        'ToArticle' => '',
        'ArticleTypes' => 'Article-Type',
        'plugin_manager' => 'ManagePlugin',
        'MailNotify' => 'GetNotification',
        'Publish' => 'PublishArticle',
        'plugin_poll' => 'EnablePluginPoll',
        'SyncPhorumUsers' => 'SynchronizePhorumUsers',
        'Change' => 'Edit',
        'Users' => 'User',
        'Topics' => 'Topic',
        'Languages' => 'Language',
        'UserTypes' => 'User-Group',
        'Authors' => 'Author',
        'Countries' => 'Country',
        'Logs' => 'Log',
        'Readers' => 'Subscriber',
        'Subscriptions' => 'Subscription',
        'SystemPreferences' => 'System-Preferences',
    );

    /** @var array */
    private static $resources = array(
        'Editor',
        'Comment',
    );

    /** @var array */
    private static $actions = array(
        'Add',
        'Edit',
        'Attach',
        'Clear',
        'Delete',
        'Manage',
        'Move',
        'View',
        'Translate',
        'Get',
        'Publish',
        'Enable',
        'Synchronize',
    );

    /** @var array */
    private static $rename = array(
        'Templ' => 'Template',
        'Pub' => 'Publication',
    );

    /**
     * Translate permission to resource - action pair
     * 
     * @param string $perm
     * @return array
     * @throws InvalidArgumentException
     */
    public static function translate($perm)
    {
        // apply filters
        foreach (self::$filters as $search => $replace) {
            $perm = str_replace($search, $replace, $perm);
        }

        // find known resource
        foreach (self::$resources as $resource) {
            if (strpos($perm, $resource) !== FALSE) {
                $action = str_replace($resource, '', $perm);
                return array($resource, $action);
            }
        }

        // find known action
        foreach (self::$actions as $action) {
            if (strpos($perm, $action) !== FALSE) {
                $resource = str_replace($action, '', $perm);
                if (isset(self::$rename[$resource])) {
                    $resource = self::$rename[$resource];
                }
                return array($resource, $action);
            }
        }

        // find plugins
        $perm_ary = explode('_', $perm);
        if (sizeof($perm_ary) == 3) {
            $perm_ary = array_map('ucfirst', $perm_ary);
            $resource = $perm_ary[0] . $perm_ary[1];
            $action = $perm_ary[2];
            return array($resource, $action);
        }

        throw new \InvalidArgumentException();
    }
}
