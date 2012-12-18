<?php

Kurogo::includePackage('PointsTracker');

class PointsTrackerWebModule extends WebModule
{
	protected $id='PointsTracker';
	protected $PointsTrackerRepository;
	
	protected function initialize() {
		$this->PointsTrackerRepository = Repository::factory("PointsTrackerRepository", null);
	}
  
	protected function initializeForPage() {
  		
		$this->addJQuery();
		$session = $this->getSession();
		
		switch ($this->page) {
			case 'index':
				
				$curr_game_id = $this->PointsTrackerRepository->createGame();
				$_SESSION['game_id'] = $curr_game_id;
				
				
				//$this->PointsTrackerRepository->createPlayer('Ted');
				
				$this->PointsTrackerRepository->setScore(1, "RED", "Ted");
				
				break;
			
			case 'scoreboard':
			
				break;		
						
			case 'stats':
			
				break;
				
			default:
				$this->redirectTo('index');
				break;
		}
		
	}
	
	
}