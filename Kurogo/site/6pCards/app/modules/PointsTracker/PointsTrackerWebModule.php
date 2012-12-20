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
  		
		//$this->addJQuery();
		$session = $this->getSession();
		
		/* Load Javascript CSS Libraries */
		$this->addInternalJavascript('/common/javascript/lib/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js');
		$this->addInternalJavascript('/common/javascript/lib/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js');
		$this->addInternalJavascript('/common/javascript/lib/jquery.ui.touch-punch.min.js');
		$this->addInternalJavascript('/common/javascript/lib/json2.js');
//		$this->addInternalJavascript('/common/javascript/lib/jquery-ui-1.9.2.custom/js/');
//		$this->addInternalJavascript('/common/javascript/lib/d3.v2.min.js');
//		$this->addInternalJavascript('/common/javascript/lib/swipe.js');
//		$this->addInternalJavascript('/common/javascript/lib/jquery.validate.min.js');
		
		$this->addInternalCSS('/common/javascript/lib/jquery-ui-1.9.2.custom/css/pepper-grinder/jquery-ui-1.9.2.custom.css');
		
		switch ($this->page) {
			case 'index':
				
				//$curr_game_id = $this->PointsTrackerRepository->createGame();
				//$_SESSION['game_id'] = $curr_game_id;
				
				//$this->PointsTrackerRepository->createPlayer('Ted');
				//$this->PointsTrackerRepository->setScore(1, "RED", "Ted");
				
				$activeGames = $this->PointsTrackerRepository->getActiveGames();
				$players = $this->PointsTrackerRepository->getAllPlayers();
				
				$this->assign("activeGames", $activeGames);
				$this->assign("players", $players);
				break;
			
			
			case 'pageGame':

				
				break;		
			
			case 'pageScoreboard':
			
				break;
			
			case 'pageStats':
			
				break;
			
			case 'pageError':
				$this->assign("message", $this->getArg("message", "No message given."));
				break;
			default:
				$this->redirectTo('index');
				break;
		}
		
	}
	
	
}