<?php

class ContactController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        $aConfig = $bootstrap->getOptions('vtiger');
                
        $contact = new Application_Model_ContactMapper($aConfig['vtiger']);
        $this->view->entries = $contact->fetchAll();
        
    }

    public function registerAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_Contact();
        
        if ($this->getRequest()->isPost()) {
//            echo "<pre>"; print_r($form->getValues()); 
            if ($form->isValid($request->getPost())) {
                $contact = new Application_Model_Contact (
                        $form->getValues());
                
                $bootstrap = $this->getInvokeArg('bootstrap');
                $aConfig = $bootstrap->getOptions('vtiger');
                $mapper = new Application_Model_ContactMapper($aConfig['vtiger']);
                $mapper->save($contact);
                
                return $this->_helper->redirector('index');
            }
//            echo "<pre>"; print_r($form->getValues()); exit;
        }
        
        $this->view->form = $form;
    }

}





