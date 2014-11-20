<?php
/**
 * @package Campsite
 */


/**
 * @package Campsite
 */
final class MetaPlaylist {

    /**
     * Constructor
     *
     */
    public function __construct($p_playlistId = null)
    {
    }

    public function __get($p_property)
    {
        switch (strtolower($p_property))
        {
            default:
                $this->trigger_invalid_property_error($p_property);
                return null;
        }
    }

    /**
     * Process the body field content (except subtitles):
     *  - internal links
     *  - image links
     *
     * @param string $p_content
     * @return string
     */
    private static function ProcessContent($p_content)
    {
    	$content = trim($p_content);
    	if (empty($content)) {
    		return $p_content;
    	}
    }

    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        . OF_OBJECT_STRING . ' subtitle';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }
}

?>