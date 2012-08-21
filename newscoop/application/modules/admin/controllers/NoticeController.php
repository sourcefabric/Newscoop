<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use \Newscoop\Entity\Notice as Notice;

/**
 * @Acl(resource="notice", action="manage")
 */
class Admin_NoticeController extends Zend_Controller_Action
{
    /**
     * @var
     */
    private $em;

    /**
     * @var
     */
    private $noticeRepo;

    /**
     * @return Admin_NoticeController
     */
    public function init()
    {
        $this->em = $this->_helper->service('em');
        // get notice repository
        $this->noticeRepo = $repo = $this->em->getRepository('Newscoop\Entity\Notice');

        camp_load_translation_strings('notices');

        return $this;
    }

    /**
     * index action forwarding to table action
     */
    public function indexAction()
    {
        $this->_forward('table');
    }

    /**
     * (jqueryDataTable) notice table/listing
     */
    public function tableAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('table', 'json')->initContext();
        $view = $this->view;
        $table = $this->getHelper('Datatable');
        /* @var $table Action_Helper_Datatable */
        $table->setDataSource($this->noticeRepo);
        $table->setOption('oLanguage', array('sSearch' => ''));
        $table->setCols(
            array('id' => $view->toggleCheckbox(),
                'lastname' => getGS('Name'),
                'firstname' => getGS('First Name'),
                'title' => getGS('Text'),
                'categories' => getGS('Categories'),
                'priority' => getGS('Prio'),
                'author' => getGS('Author'),
                'status' => getGS('Status'),
                'published' => 'Publish/Unpublish'),
            array('id' => false,'categories' => false));

        //$table->setSorting(array('published' => 'desc'));
        $table->setInitialSorting(array('id' => 'desc'));

        $index = 1;
        $acl = array();
        $acl['edit'] = $this->_helper->acl->isAllowed('comment', 'edit');
        $acl['enable'] = $this->_helper->acl->isAllowed('comment', 'enable');

        $table->setHandle(function($notice) use ($view, $acl, &$index)
        {
            $categories = $notice->getCategories();
            $categoriesArray = array();
            foreach ($categories as $category) {
                $categoriesArray[] = $category->getTitle();
            }

            return array(
                'index' => $index++,
                'can' => array('enable' => $acl['enable'], 'edit' => $acl['edit']),
                'user' =>
                array(
                    'firstname' => $notice->getFirstname(),
                    'lastname' => $notice->getLastname(),
                ),
                'notice' => array(
                    'id' => $notice->getId(),
                    'created' => array('date' => $notice->getCreated()->format('Y-m-d')),
                    'published' => array('date' => $notice->getPublished()->format('Y-m-d')),
                    'unpublished' => array('date' => $notice->getUnpublished()->format('Y-m-d')),
                    'title' => $view->escape($notice->getTitle()),
                    'sub_title' => $view->escape($notice->getSubTitle()),
                    'body' => $view->escape($notice->getBody()),
                    'author' => $notice->getAuthor()->getName(),
                    'priority' => $notice->getPriority(),
                    'categories' => implode(',', $categoriesArray),
                    'status' => $notice->getStatusName(),
                    'recommended' => 'meeh',
                    'action' => array(
                        'update' => $view->url(
                            array(
                                'action' => 'update',
                                'format' => 'json')),
                        'reply' => $view->url(
                            array('action' => 'reply',
                                'format' => 'json'))),
                    'link' => array(
                        'edit' => $view->baseUrl("admin/notice/edit/id/") . $notice->getId()
                    ))
            );
        });

        $table->setOption('fnDrawCallback', 'datatableCallback.draw')
            ->setOption('fnRowCallback', 'datatableCallback.row')
            ->setOption('fnServerData', 'datatableCallback.addServerData')
            ->setOption('fnInitComplete', 'datatableCallback.init')
            ->setOption('sDom', '<"top">lf<"#actionExtender">rit<"bottom"ip>')
            ->setStripClasses()
            ->toggleAutomaticWidth(false)
            ->setDataProp(
            array('id' => null,
                'lastname' => null,
                'firstname' => null,
                'title' => null,
                'categories' => null,
                'priority' => null,
                'author' => null,
                'status' => null,
                'published' => null))
        //->setVisible(array('actions' => false))
            ->setClasses(
            array('id' => 'noticeId',
                'lastname' => 'noticeLastname',
                'firstname' => 'noticeFirstname',
                'title' => 'noticeTitle',
                'categories' => 'noticeCategories',
                'priority' => 'noticePriority',
                'author' => 'noticeAuthor',
                'status' => 'noticeStatus',
                'published' => 'noticePublished'
            ));

        try{
            $table->dispatch();
        }catch(Exception $e){
            Doctrine\Common\Util\Debug::dump($e);
        }
    }

    /**
     * Edit notice action
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $noticeForm = new \Admin_Form_NoticeItem();
        $noticeForm->setAction($this->view->baseUrl('admin/notice/edit'));

        //get config storage ready
        $confRepo = $this->em->getRepository('Newscoop\Entity\NoticeConf');
        $confRecord = $confRepo->findOneBy(array('conf_name'=>'notice_hidden_elements'));

        $noticeForm->disableFields($confRecord->getOptions());

        if ($request->isPost() && $noticeForm->isValid($request->getPost())) {

            // load notice in case a notice id is present
            if ($noticeForm->id->getValue()) {
                $noticeRecord = $this->noticeRepo->find($noticeForm->id->getValue());
            } else {
                $noticeRecord = new \Newscoop\Entity\Notice();
            }

            //set data
            $noticeRecord->setTitle($noticeForm->title->getValue());
            $noticeRecord->setSubTitle($noticeForm->sub_title->getValue());
            $noticeRecord->setBody($noticeForm->body->getValue());
            $noticeRecord->setFirstname($noticeForm->firstname->getValue());
            $noticeRecord->setLastname($noticeForm->lastname->getValue());

            $userRecord = Zend_Registry::get('user');
            $noticeRecord->setAuthor($userRecord);

            $dateTimeDate = new DateTime($noticeForm->date->getValue());
            $noticeRecord->setDate($dateTimeDate);

            $dateTimePub = new DateTime($noticeForm->published->getValue());
            $noticeRecord->setPublished($dateTimePub);

            $dateTimeUnpub = new DateTime($noticeForm->unpublished->getValue());
            $noticeRecord->setUnpublished($dateTimeUnpub);

            $noticeRecord->setStatus('submitted');

            $noticeRecord->setPriority($noticeForm->priority->getValue());

            //category ids separated by comma
            $catIds = explode(',', $noticeForm->categories->getValue());
            $categories = array();

            //if we have valid category ids try to find their records and relate them to notice record
            if ("" !== $catIds[0]) {
                $catRepo = $this->em->getRepository('Newscoop\\Entity\\NoticeCategory');
                foreach ($catIds as $id) {
                    if ($cat = $catRepo->find($id))
                        $categories[] = $cat;
                }
                $noticeRecord->setCategories($categories);
            }

            $this->em->persist($noticeRecord);
            $this->em->flush();

            $this->_helper->flashMessenger("Notice saved");

            if (!$noticeForm->submit->getValue()) {
                $this->_helper->redirector->gotoUrl('/admin/notice/edit');
            }
            $this->_helper->redirector->gotoUrl('/admin/notice');

        } else {

            //load data and populate form for existing notice
            $noticeId = $this->getRequest()->getParam('id', null);
            $repo = $this->em->getRepository('Newscoop\Entity\Notice');

            $notice = $repo->find($noticeId);
            if (!$notice) {
                $notice = new \Newscoop\Entity\Notice();
            } else {
                $noticeForm->setDefaultsFromEntity($notice);
            }

            $repo = $this->em->getRepository('Newscoop\Entity\NoticeCategory');
            $rootNodes = $this->view->categoryCollection = $repo->getRootNodes();

            $trees = array();
            foreach ($rootNodes as $node) {
                $treeArray = $repo->childrenHierarchy($node);
                $trees[$node->getSlug()] = array('root' => $node, 'children' => $treeArray);
            }
            $this->view->trees = $trees;

            $this->view->noticeForm = $noticeForm;
            $this->view->notice = $notice;

        }

    }

    /**
     * Delete notice action
     */
    public function deleteAction()
    {
        $noticeId = $this->getRequest()->getParam('id');

        $notice = $this->service->find($noticeId);

        $this->em->remove($notice);
        $this->em->flush();

        $this->_helper->flashMessenger("Notice {$noticeId} deleted");
        $this->_helper->redirector->gotoUrl('/admin/notice');

    }


    /**
     * set notice status action
     */
    public function setStatusAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('set-status', 'json')->initContext();
        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = getGS('Invalid security token!');
            return;
        }

        $status = $this->getRequest()->getParam('status');
        $notices = $this->getRequest()->getParam('notice');

        if (!is_array($notices)) {
            $notices = array($notices);
        }


        $repo = $this->em->getRepository('Newscoop\Entity\Notice');

        $status_enum = array_flip(Notice::$status_enum);

        try {
            foreach ($notices as $id) {
                $notice = $repo->find($id);
                $msg = getGS('Notice "$2" status was changed to $3 by $1', Zend_Registry::get('user')->getName(),
                    $notice->getTitle(), $status);
                $this->_helper->flashMessenger($msg);
                $notice->setStatus($status_enum[$status]);
                $this->em->persist($notice);
            }
            $this->em->flush();
        } catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }
        $this->view->status = 200;
        $this->view->message = "succcesful";
    }


    /**
     * manage notice categories action
     */
    public function categoryAction()
    {

        $categoryRepo = $this->em->getRepository('Newscoop\Entity\NoticeCategory');
        $rootNodes = $this->view->categoryCollection = $categoryRepo->getRootNodes();

        $trees = array();
        foreach ($rootNodes as $node) {
            $leafNodes = $categoryRepo->getLeafs($node, 'lft');
            $trees[$node->getSlug()] = array('root' => $node, 'children' => $leafNodes);
        }

        $this->view->Trees = $trees;
        $categoryForm = new Admin_Form_NoticeCategory();
        $categoryArray = $categoryRepo->getAllCategoriesArray();
        $availableParentCategories = array();
        foreach ($categoryArray as $category) {
            if ($category['lvl'] == 0) {
                $availableParentCategories[$category['id']] = str_repeat('-', $category['lvl']) . $category['title'];
            }
        }
        $categoryForm->setCategories($availableParentCategories);
        $categoryForm->setAction($this->view->baseUrl('admin/notice/category-add'));
        $this->view->categoryForm = $categoryForm;

    }

    /**
     * add category action
     */
    public function categoryAddAction()
    {
        $request = $this->getRequest();

        $categoryForm = new Admin_Form_NoticeCategory();
        $categoryRepo = $this->em->getRepository('Newscoop\Entity\NoticeCategory');

        $categoriesArray = $categoryRepo->getAllCategoriesArray();


        if (count($categoriesArray)) {
            $availableParentCategories = array();
            foreach ($categoriesArray as $category) {
                if ($category['lvl'] == 0) {
                    $availableParentCategories[$category['id']] = str_repeat('-', $category['lvl']) . $category['title'];
                }
            }
            $categoryForm->setCategories($availableParentCategories);
        }

        if ($request->isPost() && $categoryForm->isValid($request->getPost())) {


            $categoryRecord = new \Newscoop\Entity\NoticeCategory();
            $categoryRecord->setTitle($categoryForm->title->getValue());

            if ('' !== $categoryForm->parent->getValue()) {
                $parentCategory = $categoryRepo->findOneById($categoryForm->parent->getValue());
                $categoryRecord->setParent($parentCategory);
            }

            $this->em->persist($categoryRecord);

            $this->em->flush();

            //reorder siblings alphabetically
            if(isset($parentCategory)){
                $categoryRepo->reorder($parentCategory, 'title', 'ASC');
            }

            $this->_helper->redirector->gotoUrl('/admin/notice/category');
        } else {
            $this->_helper->json(array('status' => 'error', 'errors' => $categoryForm->getErrors()));
        }

    }

    /**
     * Delete NoticeCategory action
     */
    public function deletecatAction()
    {
        $categoryId = $this->getRequest()->getParam('id');

        $repo = $this->em->getRepository('Newscoop\Entity\NoticeCategory');
        $treeToRemove = $repo->find($categoryId);
        $this->em->remove($treeToRemove);
        $this->em->flush();
        $this->_helper->redirector->gotoUrl('/admin/notice/category');

    }

    public function configureFormDisplayAction(){
        $noticeForm = new \Admin_Form_NoticeItem();

        $fixedElements = array('id','categories','published','submit','submit_next');
        $elements = $noticeForm->getElements();

        $configurables = array();
        foreach($elements as $name => $element){
            if(!in_array($name,$fixedElements)){
                $configurables[$name] = $element->getLabel();
            }
        }

        //get config storage ready
        $confRepo = $this->em->getRepository('Newscoop\Entity\NoticeConf');
        $confRecord = $confRepo->findOneBy(array('conf_name'=>'notice_hidden_elements'));

        if(empty($confRecord)){

            $confRecord = new \Newscoop\Entity\NoticeConf;
            $confRecord->setConfName('notice_hidden_elements');
            $confRecord->setOptions(array('date'));
            $this->em->persist($confRecord);
            $this->em->flush();
        }

        $confForm = new \Admin_Form_NoticeElementsConf();
        $confForm->init($configurables);

        if ($this->_request->isPost() && $confForm->isValid($this->_request->getPost())) {
            $formValues = $confForm->getValues();

            if(isset($formValues['notice_elements'])){
                $confRecord->setOptions($formValues['notice_elements']);
                $this->em->persist($confRecord);
                $this->em->flush();
            }

            $confForm->setDefaults($formValues);
        }else{
            $confArray = $confRecord->getOptions();
            $confRecord->getId();
            $confForm->setDefault('notice_elements',$confArray);
        }

        $this->view->configForm =  $confForm;

    }
}
