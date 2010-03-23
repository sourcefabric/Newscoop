<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
final class MetaAudioclip extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['title'] = 'dc:title';
		$this->m_properties['creator'] = 'dc:creator';
		$this->m_properties['genre'] = 'dc:type';
		$this->m_properties['length'] = 'dcterms:extent';
		$this->m_properties['year'] = 'ls:year';
		$this->m_properties['bitrate'] = 'ls:bitrate';
		$this->m_properties['samplerate'] = 'ls:samplerate';
		$this->m_properties['album'] = 'dc:source';
		$this->m_properties['description'] = 'dc:description';
		$this->m_properties['format'] = 'dc:format';
		$this->m_properties['label'] = 'dc:publisher';
		$this->m_properties['composer'] = 'ls:composer';
		$this->m_properties['channels'] = 'ls:channels';
		$this->m_properties['rating'] = 'ls:rating';
		$this->m_properties['track_no'] = 'ls:track_num';
		$this->m_properties['disk_no'] = 'ls:disc_num';
		$this->m_properties['lyrics'] = 'ls:lyrics';
		$this->m_properties['copyright'] = 'dc:rights';
	}


    public function __construct($p_gunId = null)
    {
        $this->m_dbObject = new Audioclip($p_gunId);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Audioclip();
        }
        
        $this->m_getPropertyMethod = 'getMetatagValue';

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaAudioclip

?>