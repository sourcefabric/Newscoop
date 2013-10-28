<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/GoogleGadget.php';

/**
 * @title Maps search
 */
class MapsGoogleGadget extends GoogleGadget
{
    /**
     * @setting
     * @label Default location
     */
    protected $location = 'Praha, Salvatorska 10';

    public function __construct()
    {   
       $translator = \Zend_Registry::get('container')->getService('translator');
       $this->title = $translator->trans('Maps search', array(), 'extensions');
    }

    /**
     * gadget code
     */
    protected $code = '<script src="http://www.gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/mapsearch.xml&amp;up_location=&amp;up_largeMapMode=1&amp;up_kml=0&amp;up_traffic=&amp;up_locationCacheString=&amp;up_locationCacheLat=&amp;up_locationCacheLng=&amp;up_mapType=m&amp;up_idleZoom=11&amp;up_transitionQuery=&amp;up_rawquery=&amp;up_selectedtext=&amp;synd=open&amp;w=742&amp;h=375&amp;title=__MSG_title__&amp;lang=cs&amp;country=ALL&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>';

    public function render()
    {
        // set default location by setting
        $this->code = str_replace('up_location=&amp;',
            sprintf('up_location=%s&amp;', $this->getLocation()),
            $this->code);

        parent::render();
    }
}
