<?php

class Application_Form_Contact extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod('post');
        
        // Add an Organization element
        $this->addElement(
                'text', 
                'organization', 
                array (
                    'label'     => 'Organization: ',
                    'filters'   => array('StringTrim'),
                    'value'     => "eCards",
                    'attribs' => array('readonly' => 'true')
                ));
        
        $hiddenControl = $this->createElement('hidden', 'accountId');
        $hiddenControl->setValue('11x125');
        $this->addElement($hiddenControl);
        
//        // Add an login element
//        $this->addElement(
//                'text', 
//                'login', 
//                array (
//                    'label'     => 'Login: ',
//                    'required'  => true,
//                    'filters'   => array('StringTrim'),
//                ));
        // Add an firstname element
        $this->addElement(
                'text', 
                'firstname', 
                array (
                    'label'     => 'Firstname: ',
                    'filters'   => array('StringTrim'),
                ));
        // Add an lastname element
        $this->addElement(
                'text', 
                'lastname', 
                array (
                    'label'     => 'Lastname: ',
                    'required'  => true,
                    'filters'   => array('StringTrim'),
                ));

        // Add an Passport Id element
        $this->addElement(
                'text', 
                'passport_id', 
                array (
                    'label'     => 'Passport Id: ',
                    'required'  => true,
                    'filters'   => array('StringTrim'),
                ));
        
//        // Add a captcha 
//        $this->addElement(
//                'captcha', 
//                'captcha', 
//                array(
//                    'label'      => 
//                        'Please enter the 5 letters displayed below:',
//                    'required'   => true,
//                    'captcha'    => array(
//                        'captcha' => 'Figlet',
//                        'wordLen' => 5,
//                        'timeout' => 300 )
//        ));
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Register',
        ));
        
        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        
    }


}

