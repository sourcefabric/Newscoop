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
 * @title Wikipedia search
 */
class WikipediaGoogleGadget extends GoogleGadget
{
    /**
     * gadget code
     */
    protected $code = '<script src="http://www.gmodules.com/ig/ifr?url=http://widgetango.com/ig/photos/Zebu/366/technology/politics/farm3.static.flickr.com/2086/2183946749_97dee2899a_t.jpg/285/83.xml&amp;synd=open&amp;w=742&amp;h=120&amp;title=Wikipedia&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>';
}
