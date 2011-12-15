<?php

/**
 * Render date view helper
 */
class Admin_View_Helper_RenderDate extends Zend_View_Helper_Abstract
{
    /**
     * Render date
     *
     * @param DateTime $date
     * @return void
     */
    public function renderDate(\DateTime $date)
    {
        echo '<time>';

        $today = new \DateTime('now');
        if ($today->format('Y-m-d') === $date->format('Y-m-d')) {
            echo $date->format('H:i');
        } else if ($today->format('Y') === $date->format('Y')) {
            echo $date->format('M d');
        } else {
            echo $date->format('M d Y');
        }

        echo '</time>';
    }
}
