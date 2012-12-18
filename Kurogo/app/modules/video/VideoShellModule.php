<?php

/*
 * Copyright © 2010 - 2012 Modo Labs Inc. All rights reserved.
 *
 * The license governing the contents of this file is located in the LICENSE
 * file located at the root directory of this distribution. If the LICENSE file
 * is missing, please contact sales@modolabs.com.
 *
 */

class VideoShellModule extends ShellModule {

    protected static $defaultModel = 'VideoDataModel';
    protected static $defaultController = 'VideoDataController';
    protected $id='video'; 
    protected $feeds = array();
    protected $legacyController = false;
    
    protected function getFeed($index) {
        $feeds = $this->loadFeedData();
        
        if (isset($feeds[$index])) {
            $feedData = $feeds[$index];
            
            if (isset($feedData['CONTROLLER_CLASS'])) {
				$modelClass = $feedData['CONTROLLER_CLASS'];
			} else {
				$modelClass = isset($feedData['MODEL_CLASS']) ? $feedData['MODEL_CLASS'] : self::$defaultModel;
			}
			
			$controller = VideoDataModel::factory($modelClass, $feedData);
			
			return $controller;
			
        } else {
            throw new KurogoConfigurationException($this->getLocalizedString('ERROR_INVALID_FEED', $index));
        }
    }

    protected function preFetchData(DataModel $controller, &$response) {
		$maxPerPage = $this->getOptionalModuleVar('MAX_RESULTS', 10);
		$controller->setStart(0);
		$controller->setLimit($maxPerPage);
		return parent::preFetchData($controller, $response);
    }
    
    protected function initializeForCommand() {
        
        switch($this->command) {
            case 'fetchAllData':
                $this->preFetchAllData();
                
                return 0;
                break;
            default:
                $this->invalidCommand();
                break;
        }
    }
}
