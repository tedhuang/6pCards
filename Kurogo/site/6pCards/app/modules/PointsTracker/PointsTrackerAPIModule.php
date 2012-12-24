<?php

Kurogo::includePackage('PointsTracker');

class PointsTrackerAPIModule extends APIModule {
	
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
        	case "sendScore":
        		$success 		= false;
        		$isComplete 	= false;
        		$message		= "No message";
        		$score_result	= array();
				$placement 		= json_decode($this->getArg('placement', null));
				$game_id		= $this->getArg('game_id', null);

				
				if(is_null($game_id) || is_null($placement)){
					$message = "Parameters invalid. game_id: " + $game_id;					
				}
				else{
					
					
	        		//Make sure you can only add score if game is not complete
	        		if(!$this->PointsTrackerRepository->isGameComplete($game_id)){
	        			
	        			$p_data = array();
    					for($i = 0; $i < count($placement); $i++){
   						
							$temp = explode('-', $placement[$i]);
							$player = array();
							$player['player_name'] = $temp[0];
							$player['team_color'] = $temp[1];
							
							$p_data[$i]  = $player;
						}

	        			//Add a new score value with parameters
	        			$score_result = $this->PointsTrackerRepository->addScore($game_id, $p_data);
	        			
		        		if( $score_result !== false ){
		        			$success = true;

		        			//Check if adding score caused game to complete
		        			if($this->PointsTrackerRepository->isGameComplete($game_id)){
		        				$isComplete = true;
		        				
		        				if( !$this->PointsTrackerRepository->updateGame($game_id, "COMPLETE", date("Y-m-d H:i:s"))){
		        					$message = "update status failed";
		        				}
		        			}
		        		}
		        		else{
		        			$message = "score result was false";
		        		}
	        		}
	        		else{
	        			$message = "Game is already completed";
	        		}
				}
  
        		$response = array( 'success' =>  $success, 
								   'isComplete' => $isComplete, 
								   'message' => $message,
								   'score_result' => $score_result);     	
            	$this->setResponse($response);
            	$this->setResponseVersion(1);
        		break;
        	
        	case "removeScore":
        		$success = false;
        		$message = "none";
        		$score_id = $this->getArg('score_id', null);
        		
        		if(!is_null($score_id)){
        			$this->PointsTrackerRepository->deleteScore($score_id);
        			$success = true;
        		}
        		else{
        			$message = "score_id not given";
        		}
        	
        		$response = array( 'success' =>  $success, 'message' => $message);     	
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
        		$curr_game_id = false;
        		$message = "no message";
        		
				$points_to_win = $this->getArg('points_to_win', 50);
				$red_team = $this->getArg('red_team', null);
				$blue_team = $this->getArg('blue_team', null);
				
				$players = array();
				$players["RED"] = json_decode($red_team);
				$players["BLUE"] = json_decode($blue_team);
				
				if(count($players["RED"]) == 3 || count($players["BLUE"]) == 3){
					$curr_game_id = $this->PointsTrackerRepository->createGame($players, $points_to_win);
					
					if($curr_game_id !== false){
						$_SESSION['curr_game_id'] = $curr_game_id;
						$success = true;
					}
					else{
						$message = "Repository create game failed";
					}
				}
				else{
					$message = "Player numbers are invalid";
				}
				
        		
        		$response = array( 'success' =>  $success, "message" => $message, "game_id" =>  $curr_game_id);     	
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