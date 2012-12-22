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
				$game_list = array('title' => 'Recent Games', 'url' => 'pageGameList', 'image' => 'module/PointsTracker/images/pageGameList.png');
				$create_game = array('title' => 'New Game', 'url' => 'pageCreateGame', 'image' => 'module/PointsTracker/images/pageCreateGame.png');
				$stats = array('title' => 'Statistics', 'url' => 'pageStats', 'image' => 'module/PointsTracker/images/pageStats.png');
				$placeholder = array('title' => 'Something Else', 'url' => '.', 'image' => 'module/PointsTracker/images/placeholder.png');
				
				
				$dashboardItems = array($game_list, $create_game, $stats, $placeholder);					
				$this->assign('dashboardItems', $dashboardItems);
				break;
			
			case 'pageGameList':
				$activeGames = $this->PointsTrackerRepository->getActiveGames();
				$this->assign("activeGames", $activeGames);
				break;
				
			case 'pageCreateGame':
				$players = $this->PointsTrackerRepository->getAllPlayers();
				$this->assign("players", $players);
				break;
				
			case 'pageGame':
				$game_id = $this->getArg("game_id");
				$game =  $this->PointsTrackerRepository->getGameById($game_id);
				$score_record = $this->PointsTrackerRepository->getScoreByGame($game_id, true);
				
				if($game === false){
					$this->redirectTo('pageError', array("message" => "Game does not exist"));
				}
				
				foreach( explode('|',$game['team_red']) as $name){
					$player =  $this->PointsTrackerRepository->getPlayerByName($name);
					$game['t_red'][$name] = $player;
				}
								
				foreach( explode('|',$game['team_blue']) as $name){
					$player =  $this->PointsTrackerRepository->getPlayerByName($name);
					$game['t_blue'][$name] = $player;
				}
				
				$this->assign("score_record", $score_record);
				$this->assign("game", $game);
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