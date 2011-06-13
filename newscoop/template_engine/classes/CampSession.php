<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

define('ACTIVE', 1);
define('EXPIRED', 2);
define('DESTROYED', 3);

/**
 * Class CampSession
 */
final class CampSession
{
    /**
     * Holds instance of the class
     *
     * @var object
     */
    private static $m_instance = null;

    /**
     * Status of the current session
     *
     * @var int
     */
    private $m_status = ACTIVE;

    /**
     * Session expiration time
     *
     * @var int
     */
    private $m_expirationTime = 30;


    /**
     * Class constructor
     */
    private function __construct()
    {
        // makes sure session will use files to store its data
        @ini_set('session.save_handler', 'files');

        $this->start();

        $this->setStatus(ACTIVE);

        $this->setTimer();
        $this->setCounter();
    } // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @return CampSession
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampSession();
        }

        return self::$m_instance;
    } // fn singleton


    /**
     * Starts the session.
     */
    function start()
    {
    	require_once 'Zend/Session.php';
        Zend_Session::start();
//        @session_cache_limiter('none');
//        @session_start();
    } // fn start


    /**
     * Destroys the session.
     */
    function destroy()
    {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 50000, '/');
        }

        session_unset();
        session_destroy();

        return true;
    } // fn destroy


    /**
     *
     */
    function unsetData($p_name, $p_namespace = 'default')
    {
        if ($this->dataExists($p_name, $p_namespace)) {
            unset($_SESSION[$p_namespace][$p_name]);
        }
    } // fn unsetData


    /**
     *
     */
    function close()
    {
        session_write_close();
    } // fn close


    /**
     *
     */
    function getStatus()
    {
        return $this->m_status;
    } // fn getStatus


    /**
     *
     */
    function getExpirationTime()
    {
        return $this->m_expirationTime;
    } // fn getExpirationTime


    /**
     *
     */
    function getId()
    {
        return session_id();
    } // fn getId


    /**
     *
     */
    function getName()
    {
        return session_name();
    } // fn getName


    /**
     *
     */
    function getData($p_name, $p_namespace = 'default')
    {
        return ($this->dataExists($p_name, $p_namespace)) ? $_SESSION[$p_namespace][$p_name] : null;
    } // fn getData


    /**
     *
     */
    function getToken()
    {
        $token = $this->getData('session.token');
        if ($token === null) {
            $token = $this->generateToken();
            $this->setData('session.token', $token);
        }

        return $token;
    } // fn getToken


    /**
     *
     */
    function validateToken($p_token)
    {
        if ($p_token !== $this->getToken()) {
            $this->setStatus(EXPIRED);
        }

        return true;
    } // fn validateToken


    /**
     *
     */
    function dataExists($p_name, $p_namespace = 'default')
    {
        return isset($_SESSION[$p_namespace]) && isset($_SESSION[$p_namespace][$p_name]);
    } // fn dataExists


    /**
     *
     */
    function setStatus($p_status)
    {
        $this->m_status = $p_status;
    } // fn setStatus


    /**
     *
     */
    function setData($p_name, $p_value, $p_namespace = 'default', $p_force = false)
    {
        if (!$this->dataExists($p_name, $p_namespace) || $p_force == true) {
            if (!empty($p_value)) {
                $_SESSION[$p_namespace][$p_name] = $p_value;
            } else {
                unset($_SESSION[$p_namespace][$p_name]);
            }
        }
    } // fn setData


    /**
     *
     */
    function setCounter()
    {
        $cnt = $this->getData('session.counter');
        $this->setData('session.counter', ++$cnt);
    } // fn setCounter


    /**
     *
     */
    function setTimer()
    {
        if (!$this->dataExists('session.timer.start')) {
            $now = time();
            $this->setData('session.timer.start', $now);
            $this->setData('session.timer.current', $now);
            $this->setData('session.timer.finish', $now);
        }

        $this->setData('session.timer.finish', $this->getData('session.timer.current'));
        $this->setData('session.timer.current', time());
    } // fn setTimer


    /**
     *
     */
    function generateToken()
    {
        $length = 32;
        $values = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= substr($values, rand(0, (strlen($values) - 1)), 1);
        }

        return $token;
    } // fn generateToken

} // class CampSession

?>
