<?php

/**
 * Subscription Section Json
 */
class Admin_View_Helper_SubscriptionSectionJson extends Zend_View_Helper_Abstract
{
    /**
     * Get json representation of subscription section
     *
     * @param Newscoop\Subscription\Section $section
     * @return array
     */
    public function SubscriptionSectionJson(\Newscoop\Subscription\Section $section)
    {
        return array(
            'id' => $section->getId(),
            'section' => array(
                'number' => $section->getSectionNumber(),
                'name' => $section->getName(),
            ),
            'language' => $section->hasLanguage() ? array(
                'id' => $section->getLanguageId(),
                'name' => $section->getLanguageName(),
            ) : null,
            'startDate' => $section->getStartDate()->format('Y-m-d'),
            'days' => $section->getDays(),
            'paidDays' => $section->getPaidDays(),
        );
    }
}
