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
 * @package Config
 */
 
/**
 * Handles multiple config files
 * @package Config
 */
class ConfigGroup extends Config
{
    protected $configs = array();

    public function addConfig(Config $config)
    {
       $this->configs[] = $config;
       $config->addConfig($this);
       $this->addVars($config->getVars());
       $this->addSectionVars($config->getSectionVars());
    }
    
  /* values with {XXX} in the config are replaced with other config values */
  protected function replaceCallback($matches)
  {
    $configs = array_merge(array($this), $this->configs);
    foreach ($configs as $config) {
        $vars = $config->getVars();
        if (isset($vars[$matches[1]])) {
            return $vars[$matches[1]];
        }
    }
    return $matches[0];
  }
        
}
