<?php

function pear__autoload($classname)
{
    // name your classes and filenames with underscores, i.e., Net_Whois stored in Net_Whois.php
    $classfile = str_replace("_", "/", $classname) . ".php";
 
    include_once($classfile);
}

spl_autoload_register("pear__autoload");

class Application_Model_ContactMapper
{
    protected $_module_name = 'Contacts';
    protected $_endpointUrl = "";
    protected $_httpc = null;
    protected $_sessionId = null;
    protected $_user_id = null;
    protected $_organization_id = null;

    public function __construct(array $aConfig) {
        $port = (empty( $aConfig['port'] )) ? "" : ":" . $aConfig['port']; 
        
        $this->_endpointUrl = $aConfig['schema'] .  
                $aConfig['host'] .
                $port .
                $aConfig['wspath'];
        
        $this->_organization_id = $aConfig['account_id'];
        $this->connect($aConfig);
    }
    
    public function connect(array $aConfig)
    {
        $this->_httpc = new HTTP_Client();
        // getchallenge request must be a GET request
        $this->_httpc->get($this->_endpointUrl . 
                "?operation=getchallenge&username=" .
                $aConfig['userName']);
        $response = $this->_httpc->currentResponse();
        // decode the json encode response from the server.
        $jsonResponse = Zend_JSON::decode($response['body']);
        
        //check for whethere the requested operation was successful or not.
        if ($jsonResponse['success'] == false) {
            // handle the failure case.
            die('getchallenge failed: ' . $jsonResponse['error']['message']);
        }
        
        // operation was successful get  the token from the response.
        $challengeToken = $jsonResponse['result']['token'];
        
        // Create md5 string concatenating user accesskey from my preference page
        // and the challenge token obtained from get challenge result
        $generatedKey = md5($challengeToken . $aConfig['userAccessKey']);
        // login request must be POST;
        $this->_httpc->post($this->_endpointUrl,
                array('operation' => 'login',
                    'username' => $aConfig['userName'],
                    'accessKey' => $generatedKey),
                true);
        $response = $this->_httpc->currentResponse();
        // decode the json encode response from the server;
        $jsonResponse = Zend_JSON::decode($response['body']);
        
        // operation was successful get the token from the response
        if ($jsonResponse['success'] == false) {
            // handle the failure case.
            die ('login failed: ' . $jsonResponse['error']['message']);
        }
        
        // login sucessful extract sessionId and userId from LoginResult
        // so it can used for further calls.
        $this->_sessionId = $jsonResponse['result']['sessionName'];
        $this->_user_id = $jsonResponse['result']['userId'];
    }

//public function setDbTable(){}
//public function getDbTable(){}

public function save(Application_Model_Contact $contact)
{
    $data = array (
        'firstname' => $contact->getFirstname() ,
        'lastname' => $contact->getLastname() ,
        'account_id' => $contact->getAccountId() ,
        );
    
    if (null === ($id = $contact->getId() )) {
        unset($data['id']);
        $this->insert($data);
    } else {
        $this->update($data, array('id' => $id));
    }
}

public function insert($data)
{
    // userId is obtained from loginREsult.
    $data['assigned_user_id'] =  $this->_user_id;
    // encode the object in JSON format to communicate with the server.
    $objectJson = Zend_JSON::encode($data);
    // SessionId is obtained from loginREsult.
    $params = array('sessionName' => $this->_sessionId, 
      'operation' => 'create',
      "element" => $objectJson,
      "elementType" => $this->_module_name);

    // Create must be POST Request. 
    $this->_httpc->post($this->_endpointUrl, $params, true);
    $response = $this->_httpc->currentResponse();
    // decode the json response from the server.
    $jsonResponse = Zend_JSON::decode($response['body']);

    // operation was succcessful get the token from the response.
    if ($jsonResponse['success'] == false) {
      // handle the failure case.
      die('Accoount create failed: ' . $jsonResponse['error']['message']);
    }

    return $jsonResponse['result'];
}

public function find($id, Application_Model_Contact $contact)
{

}

public function query($query) 
{
    //urlencode to as its sent over http
    $queryParam = urlencode($query);
    // sessionId is obtained from login result
    $params = "sessionName=" . $this->_sessionId .
            "&operation=query" .
            "&query=" . $queryParam;
    // query must be GET request.
    $this->_httpc->get($this->_endpointUrl . "?" . $params);
    $response = $this->_httpc->currentResponse();    
    // decode the jsonencode response from the server.
    $jsonResponse = Zend_JSON::decode($response['body']);

    // operation was successful get the token from the response
    if ($jsonResponse['success'] == false) {
        // handle the failure case.
        die('query failed: ' . $jsonResponse['error']['message']);
    }

    // Array of vtigerObjects
    return $jsonResponse['result'];
}

public function fetchAll()
{
    //echo "<pre>";
    //print_r($jsonResponse);exit;
    $fields = "lastname, firstname, account_id, assigned_user_id, id, title";
    $query = "select * " . 
            "from Contacts where account_id='" .
            $this->_organization_id . "';";
    $results = $this->query($query);
    
    $entries = array();
    foreach ($results as $row) {
  //      echo "<pre>";        print_r($row);exit;
        $entry = new Application_Model_Contact();
        $entry->setFirstname($row['firstname'])
                ->setLastname($row['lastname'])
                ->setTitle($row['title'])
                ->setAccountId($row['account_id'])
                ->setContactNo($row['contact_no'])
                ->setId($row['id']);
        
        $entry->setOrganization(
                $this->getOrganizationName( $row['account_id']) );
        $entries[] = $entry;
    }

    return $entries;
}

public function getOrganizationName($account_id)
{
    static $organizations = array();
    if (! array_key_exists($account_id, $organizations) ) {
        $organizations[$account_id] = 
            $this->getOrganizationNameWS($account_id);
    }
    return $organizations[$account_id];
}

public function getOrganizationNameWS($account_id)
{
//    $query = "select * from Accounts;"; 
    $query = "select accountname, account_no, id " 
        . "from Accounts where id='" . $account_id . "';";
    $result = $this->query($query);
    $name = '';
    if (is_array($result) && count($result)>0 ) {
        $name = $result[0]['accountname'];
    } 
    return $name;
}

}



