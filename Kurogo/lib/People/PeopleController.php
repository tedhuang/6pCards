<?php

/*
 * Copyright © 2010 - 2012 Modo Labs Inc. All rights reserved.
 *
 * The license governing the contents of this file is located in the LICENSE
 * file located at the root directory of this distribution. If the LICENSE file
 * is missing, please contact sales@modolabs.com.
 *
 */

/**
  * @package People
  */
  
/**
  * @package People
  */
abstract class PeopleController
{
    abstract public function lookupUser($id);
    abstract public function search($searchTerms);
    
    protected $debugMode=false;
    protected $personClass = 'Person';
    protected $capabilities=0;
    protected $errorNo;
    protected $errorMsg;

    public static function getPeopleControllers() {
        return array(
            ''=>'-',
            'LDAPPeopleController'=>'LDAP',
            'ADPeopleController'=>'Active Directory',
            'DatabasePeopleController'=>'Database'
        );
    }

    public function debugInfo() {
        return '';
    }

    public function getUser($id) {
        return $this->lookupUser($id);
    }
    
    public function getResponseError() {
        return $this->getError();
    }

    public function getResponseCode() {
        return $this->getErrorNo();
    }

    public function getErrorNo() {
        return $this->errorNo;
    }

    public function getError() {
        return $this->errorMsg;
    }

    public function setAttributes($attribs) {
        if (is_array($attribs)) {
            $this->attributes =$attribs;
        } elseif ($attribs) {
            throw new KurogoException('Invalid attributes');
        } else {
            $this->attributes = array();
        }
    }

    public function getCapabilities() {
        return $this->capabilities;
    }

    public function setDebugMode($debugMode) {
        $this->debugMode = $debugMode ? true : false;
    }
    
    public function setPersonClass($className) {
    	if ($className) {
    		if (!class_exists($className)) {
    			throw new KurogoConfigurationException("Cannot load class $className");
    		}

            $class = new ReflectionClass($className); 
            if (!$class->isSubclassOf('Person')) {
                throw new KurogoConfigurationException("$className is not a subclass of Person");
            }
			$this->personClass = $className;
		}
    }
    
    protected function init($args) {

        if (isset($args['PERSON_CLASS'])) {
            $this->setPersonClass($args['PERSON_CLASS']);
        }
    }

    public static function factory($controllerClass, $args) {

        if (!class_exists($controllerClass)) {
            throw new KurogoConfigurationException("Controller class $controllerClass not defined");
        }
        
        $controller = new $controllerClass;
        $controller->setDebugMode(Kurogo::getSiteVar('DATA_DEBUG'));

        if (!$controller instanceOf PeopleController) {
            throw new KurogoConfigurationException("$controller class is not a subclass of PeopleController");
        }
        
        $controller->init($args);
        
        return $controller;
    }
}
