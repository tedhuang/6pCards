<?php

Kurogo::includePackage('PointsTracker');

class PointsTrackerAPIModule extends PointsTrackerAPIModule {
	
	protected $DoItDailyRepository;
	
    protected $id = 'PointsTracker';
    protected $vmin = 1;
    protected $vmax = 1;
    public function availableVersions() {
        return array(1);
    }
	
    protected function initializeForCommand()  {
    	
		//$session = $this->getSession();    	
		$this->DoItDailyRepository = Repository::factory("PointsTrackerRepository", null);
		
		
        switch ($this->command) {
        	case "pledgeItem":
        		
        		break;
        		
        }
        
    }
    
}