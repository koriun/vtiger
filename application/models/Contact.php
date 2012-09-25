<?php

class Application_Model_Contact
{
    protected $_login;
    protected $_firstname;
    protected $_lastname;
    protected $_pass_id;
    protected $_organization;
    protected $_email;
    protected $_id;
    protected $_contact_no;
    protected $_title;
    protected $_account_id; // organization id
    
    public function __construct(array $options = null) 
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function _set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid contact property');
        }
        $this->$method($value);
    }
    
    public function __get($name)
    {
        $method = 'get' . $name;
        if ( ('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid Contact property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
    }
    
    public function setLogin($login)
    {
        $this->_login = (string) $login;
        return $this;
    }
    
    public function getLogin()
    {
        return $this->_login;
    }
    
    public function setFirstname($name)
    {
        $this->_firstname = (string) $name;
        return $this;
    }
    
    public function getFirstname()
    {
        return $this->_firstname;
    }
    
    public function setLastname($lastname)
    {
        $this->_lastname = (string) $lastname;
        return $this;
    }
    
    public function getLastname()
    {
        return $this->_lastname;
    }
    
    public function setPassID($pass_id)
    {
        $this->_pass_id = (string) $pass_id;
        return $this;
    }
    
    public function getPassID()
    {
        return $this->_pass_id;
    }
    
    public function setEmail($email)
    {
        $this->_email = (string) $email;
        return $this;
    }
    
    public function getEmail()
    {
        return $this->_email;
    }
    
    public function setOrganization($organization)
    {
        $this->_organization = (string) $organization;
        return $this;
    }
    
    public function getOrganization()
    {
        return $this->_organization;
    }
    
    public function setId($id)
    {
        $this->_id = (string) $id;
        return $this;
    }
    
    public function getId()
    {
        return $this->_id;
    }

    public function setContactNo($contact_no)
    {
        $this->_contact_no = (string) $contact_no;
        return $this;
    }
    
    public function getContactNo()
    {
        return $this->_contact_no;
    }
    
        public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }

    public function setAccountId($account_id)
    {
        $this->_account_id = (string) $account_id;
        return $this;
    }
    
    public function getAccountId()
    {
        return $this->_account_id;
    }


}

