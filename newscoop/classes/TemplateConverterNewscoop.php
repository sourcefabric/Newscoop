<?php
/**
 * @package Newscoop
 *
 * @author Mugur Rus <mugur.rus@sourcefabric.org>
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

$_docRoot = dirname(dirname(__FILE__));
require_once($_docRoot.'/classes/TemplateConverter.php');

/**
 * Class TemplateConverterNewscoop
 */
class TemplateConverterNewscoop extends TemplateConverter
{
    /**
     * Parses the original template file and replaces old syntax with new one.
     *
     * @return boolean|null
     */
    public function parse()
    {
    	$this->m_templateContent = str_replace('$campsite->', '$gimme->', $this->m_templateOriginalContent);
    }
}

?>
