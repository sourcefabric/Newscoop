<?php

use Newscoop\Controller\BaseTableController,
    Newscoop\Entity\User;

class Admin_UsersController extends BaseTableController
{
    public function init()
    {
        parent::init();

        $this->setEntity('Newscoop\Entity\User');

        $this->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $this->setHandle(function(User $user) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $user->getId(),
                )),
                getGS('Edit user $1', $user->getName()),
                $user->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $user->getId(),
                )),
                getGS('Delete user $1', $user->getName()),
                getGS('Delete')
            );

            return array(
                $editLink,
                $user->getUsername(),
                $user->getEmail(),
                $user->getTimeCreated()->format('Y-i-d H:i:s'),
                $deleteLink,
            );
        });
    }

    public function indexAction()
    {
        $this->_forward('table');
    }
}
