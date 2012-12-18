<?php

Kurogo::includePackage('PointsTracker');

class PointsTrackerWebModule extends WebModule
{
	protected $id='PointsTracker';
	protected $PointsTrackerRepository;
	
	protected function initialize() {
		$this->$PointsTrackerRepository = Repository::factory("$PointsTrackerRepository", null);
		parent::initialize();
	}
  
	protected function initializeForPage() {
  		
		$this->addJQuery();
		$session = $this->getSession();
		
		$userId = $this->getUserId();
		

		$this->addInlineJavascript("var moduleId='{$this->id}';\n"); //used for module API calls
		$this->assign('moduleId', $this->id ); //used inside template paths to find module template files

		
		switch ($this->page) {
			
		}
		
	}
	
}