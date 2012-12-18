<?php

/*
 * Copyright © 2010 - 2012 Modo Labs Inc. All rights reserved.
 *
 * The license governing the contents of this file is located in the LICENSE
 * file located at the root directory of this distribution. If the LICENSE file
 * is missing, please contact sales@modolabs.com.
 *
 */

class CoreAPIModule extends APIModule
{
    protected $id = 'core';
    protected $vmin = 1;
    protected $vmax = 2;

    // special factory method for core
    public static function factory($id='core', $command='', $args=array()) {
        $module = new CoreAPIModule();
        $module->init($command, $args);
        return $module;
    }
 
    //always allow access
    protected function getAccessControlLists($type) {
        return array(AccessControlList::allAccess());
    }
    
    public function initializeForCommand() {  
    
        switch ($this->command) {
            case 'hello':
            
                $allmodules = $this->getAllModules();
                $homeModuleData = $this->getModuleNavigationData();
                $homeModules = array(
                    'primary'=> isset($homeModuleData['primary']) ? array_keys($homeModuleData['primary']) : array(),
                    'secondary'=>isset($homeModuleData['secondary']) ? array_keys($homeModuleData['secondary']) : array()
                );

                foreach ($allmodules as $moduleID=>$module) {
                    if ($module->isEnabled()) {
                        $home = false;
                        
                        
                        if ( ($key = array_search($moduleID, $homeModules['primary'])) !== FALSE) {
                            $home = array('type'=>'primary', 'order'=>$key, 'title'=>$homeModuleData['primary'][$moduleID]);
                        } elseif (($key = array_search($moduleID, $homeModules['secondary'])) !== FALSE) {
                            $home = array('type'=>'secondary', 'order'=>$key);
                        }
                        
                    
                        $modules[] = array(
                            'id'        =>$module->getID(),
                            'tag'       =>$module->getConfigModule(),
                            'title'     =>$module->getModuleVar('title','module'),
                            'access'    =>$module->getAccess(AccessControlList::RULE_TYPE_ACCESS),
                            'payload'   =>$module->getPayload(),
                            'bridge'    =>$module->getWebBridgeConfig(),
                            'vmin'      =>$module->getVmin(),
                            'vmax'      =>$module->getVmax(),
                            'home'      =>$home
                        );
                    }
                }
                $response = array(
                    'timezone'=>Kurogo::getSiteVar('LOCAL_TIMEZONE'),
                    'site'=>Kurogo::getSiteString('SITE_NAME'),
                    'organization'=>Kurogo::getSiteString('ORGANIZATION_NAME'),
                    'version'=>KUROGO_VERSION,
                    'modules'=>$modules,
                    'default'=>Kurogo::defaultModule()
                );
                $this->setResponse($response);
                $this->setResponseVersion(2);
                break;
                
            case 'classify':
                $userAgent = $this->getArg('useragent');
                if (!$userAgent) {
                    throw new KurogoException("useragent parameter not specified");
                }
                
                $response = Kurogo::deviceClassifier()->classifyUserAgent($userAgent);
                
                $this->setResponse($response);
                $this->setResponseVersion(1);
                break;
                
            default:
                $this->invalidCommand();
                break;
        }
    }
}
