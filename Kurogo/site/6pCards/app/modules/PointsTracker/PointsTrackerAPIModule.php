<?php

Kurogo::includePackage('PointsTracker');

class PointsTrackerAPIModule extends PointsTrackerAPIModule {
	
	protected $PointsTrackerRepository;
	
    protected $id = 'PointsTracker';
    protected $vmin = 1;
    protected $vmax = 1;
    public function availableVersions() {
        return array(1);
    }
	
    protected function initializeForCommand()  {
    	
		$session = $this->getSession();    	
		$this->PointsTrackerRepository = Repository::factory("PointsTrackerRepository", null);

        switch ($this->command) {
        	case "setScore":
        		$success 		= false;
        		$isComplete 	= false;
        		$team_color 	= $this->getArg('team_color');
        		$player_name 	= $this->getArg('player_name');
    			$points_earned 	= $this->getArg('points_earned');
        		
        		//Make sure you can only add score if game is not complete
        		if(!$this->PointsTrackerRepository->isGameComplete($_SESSION['game_id'])){
        			
        			//Add a new score value with parameters
	        		if( $this->PointsTrackerRepository->setScore($_SESSION['game_id'], $team_color, $player_name, $points_earned) ){
	        			$success = true;
	        			
	        			//Check if adding score caused game to complete
	        			if($this->PointsTrackerRepository->isGameComplete($_SESSION['game_id'])){
	        				$isComplete = true;
	        				
	        				$this->PointsTrackerRepository->updateGame($_SESSION['game_id'], "COMPLETE");
	        			}
	        		}
        		}

        		$response = array( 'success' =>  $success, 'isComplete' => $isComplete);     	
            	$this->setResponse($response);
            	$this->setResponseVersion(1);
        		break;
        	
        	case "createPlayer":
        		$player_name = $this->getArg('player_name');
        		if( $this->PointsTrackerRepository->createPlayer($player_name) ){
        			$success = true;
        		}
        		
        		$response = array( 'success' =>  $success);     	
            	$this->setResponse($response);
            	$this->setResponseVersion(1);
        		break;
        	
        	case "createGame":
        		$success = false;
        		
				$points_to_win = $this->getArg('points_to_win', 50);
				$red_team = $this->getArg('red_team', null);
				$blue_team = $this->getArg('blue_team', null);
				
				
				
				
				$curr_game_id = $this->PointsTrackerRepository->createGame($points_to_win);
				
				if($curr_game_id){
					$_SESSION['curr_game_id'] = $curr_game_id;
				}
				else{
					$this->redirectTo("pageError", array("message"=>""));
				}
        		
        		
        		$response = array( 'success' =>  $success);     	
            	$this->setResponse($response);
            	$this->setResponseVersion(1);
        		break;
        		
        	case "updateGame":
        		$success = false;
        		
        		
        		$response = array( 'success' =>  $success);     	
            	$this->setResponse($response);
            	$this->setResponseVersion(1);
        		break;
        		
        }
        
    }
    
}