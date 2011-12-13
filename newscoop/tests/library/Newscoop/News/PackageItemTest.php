<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class PackageItemTest extends \PHPUnit_Framework_TestCase
{
    const PACKAGE_XML = 'packageItem.xml';
    const UPDATED_XML = 'updatedPackageItem.xml';


    /** @var Newscoop\News\PackageItem */
    protected $item;

    /** @var SimpleXMLElement */
    protected $xml;

    public function setUp()
    {
        $this->xml = simplexml_load_file(APPLICATION_PATH . '/../tests/fixtures/' . self::PACKAGE_XML);
        $this->item = PackageItem::createFromXml($this->xml->itemSet->packageItem);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\PackageItem', $this->item);
    }

    public function testGetGroupSet()
    {
        $groupSet = $this->item->getGroupSet();
        $this->assertInstanceOf('Newscoop\News\GroupSet', $groupSet);

        $groups = $groupSet->getGroups();
        $this->assertEquals(2, count($groups));

        $rootGroup = $groupSet->getRootGroup();
        $this->assertInstanceOf('Newscoop\News\Group', $rootGroup);
        $this->assertEquals('grpRole:SNEP', $rootGroup->getRole());
        $this->assertEquals('seq', $rootGroup->getMode());
        $rootGroupRefs = $rootGroup->getRefs();
        $this->assertEquals(1, count($rootGroupRefs));
        $this->assertEquals('main', $rootGroupRefs[0]->getIdRef());

        $refGroup = $groupSet->getGroup($rootGroupRefs[0]);
        $refGroupRefs = $refGroup->getRefs();
        $this->assertEquals(10, count($refGroupRefs));

        $itemRef = $refGroupRefs[0];
        $this->assertInstanceOf('Newscoop\News\ItemRef', $itemRef);
        $this->assertEquals('tag:example.com,0000:newsml_TRE7B50LE', $itemRef->getResidRef());
        $this->assertEquals('7', $itemRef->getVersion());
        $this->assertEquals('application/vnd.iptc.g2.packageitem+xml', $itemRef->getContentType());
        $this->assertEquals('icls:composite', $itemRef->getItemClass());
        $this->assertEquals('example.com', $itemRef->getProvider());
        $this->assertEquals(date_create('2011-12-06T13:50:04.000Z')->getTimestamp(), $itemRef->getVersionCreated()->getTimestamp());
        $this->assertEquals('stat:usable', $itemRef->getPubStatus());
        $this->assertEquals('US-EUROZONE-VISIONS', $itemRef->getSlugline());
        $this->assertEquals('Insight: Conflicting visions at core of euro zone crisis', $itemRef->getHeadline());
    }
}
