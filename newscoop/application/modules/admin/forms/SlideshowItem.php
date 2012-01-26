<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_Form_SlideshowItem extends Zend_Form
{
    /**
     */
    public function init()
    {
        $this->addElement('hidden', 'coords');

        $this->addElement('text', 'caption', array(
            'label' => getGS('Caption'),
        ));
    }

    /**
     * Set default for given entity
     *
     * @param Newscoop\Package\Item $item
     * @return Admin_Form_SlideshowItem
     */
    public function setDefaultsFromEntity(\Newscoop\Package\Item $item)
    {
        $this->setDefaults(array(
            'caption' => $item->getCaption(),
        ));
        return $this;
    }
}
