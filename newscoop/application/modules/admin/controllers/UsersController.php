<?php

use Newscoop\Entity\User;

class Admin_UsersController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('table');
    }

    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User');

        $table->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(User $user) use ($view) {
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

        $table->dispatch();
    }
}
