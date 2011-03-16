<?php

require_once APPLICATION_PATH . '/../classes/SimplePager.php';

class Admin_LogsController extends Zend_Controller_Action
{
    /** @var \Doctrine\ORM\EntityRepository */
    private $eventRepository = NULL;

    /** @var \Doctrine\ORM\EntityRepository */
    private $logRepository = NULL;

    /**
     * Check permissions
     */
    public function preDispatch()
    {
        global $g_user;

        // permissions check
        if (!$g_user->hasPermission('ViewLogs')) {
	        camp_html_display_error(getGS("You do not have the right to view logs."));
        }
    }

    /**
     * Init Doctrine
     */
    public function init()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $em = $bootstrap->getResource('doctrine')
            ->getEntityManager();

        $this->logRepository = $em->getRepository('\Newscoop\Entity\Log');
        $this->eventRepository = $em->getRepository('\Newscoop\Entity\Event');

        camp_load_translation_strings('logs');
    }

    /**
     * List logs
     */
    public function indexAction()
    {
        // get event form
        $event = NULL;
        $form = $this->getEventForm()
            ->setMethod('get')
            ->setAction($this->view->url());

        // handle form if valid
        if ($form->isValid($this->getRequest()->getParams())) {
            $values = $form->getValues();
            $event = $this->eventRepository->find($values['event']);
        }

        // pass form and event to view
        $this->view->form = $form;
        $this->view->event = $event;

        // fetch logs
        $limit = 15;
        $offset = max(0, camp_session_get('f_log_page_offset', 0));
        $this->view->logs = $this->logRepository->getLogs($offset, $limit, $event);

        // set pager
        $count = $this->logRepository->getCount($event);
        $eventId = isset($event) ? $event->getId() : 0;
        $this->view->pager = new SimplePager($count, $limit, 'f_log_page_offset', "?event={$eventId}&");
    }

    /**
     * Get event form
     *
     * @return \Zend_Form
     */
    private function getEventForm()
    {
        $form = new Zend_Form;

        // get events
        $events = array();
        foreach ($this->eventRepository->findAll() as $event) {
            $events[$event->getId()] = $event->getName();
        }

        $form->addElement('select', 'event', array(
            'multioptions' => array(getGS('All')) + $events,
            'label' => getGS('Event'),
        ));

        $form->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => getGS('Search'),
        ));

        return $form;
    }
}
