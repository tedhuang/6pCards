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
							
							
							$this->updateDashboardScore($placement, $score_result);

							
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
            	break;
            	
        	case "createPlayer":
        		$player_name = $this->getArg('player_name');
        		if( $this->PointsTrackerRepository->createPlayer($player_name) ){
        			$success = true;
        		}
        		
        		$response = array( 'success' =>  $success);     	
        		break;
        	
        	case "getPlayersFromLastGame":
        		$success = false;
        		$message = "no message";
        		$last_game = $this->PointsTrackerRepository->getLastGame();
        		$players = array();
        		
        		if($last_game){
        			$team_red = explode('|',$last_game['team_red']);
        			$team_blue = explode('|',$last_game['team_blue']);
        			
        			foreach($team_red as $player_name){
        				array_push($players, $player_name);
        			}
    				foreach($team_blue as $player_name){
        				array_push($players, $player_name);
        			}
        			$success = true;
        		}
        		else{
        			$mesage = "Unable to get last game";
        		}
        		
        		$response = array('success' => $success, 'message'=> $message, 'players'=>$players);
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
					
					$this->updateDashboardNewGame($players["RED"], $players["BLUE"]);
					
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
        		break;
        		
        	case "updateGame":
        		$success = false;
        		
        		
        		$response = array( 'success' =>  $success);     	
        		break;
        		
        }
		$this->setResponse($response);
    	$this->setResponseVersion(1);
    }
    
    public function updateDashboardNewGame($team_1, $team_2){
    	$team_1_array = array();
    	$team_2_array = array();
    	
    	foreach($team_1 as $player_name){
    		$player = $this->PointsTrackerRepository->getPlayerByName($player_name);
    		array_push($team_1_array, array('label' => $player_name, 'value' =>"http://www.gravatar.com/avatar/".md5(strtolower(trim($player['gravatar_email'])))."?s=100&d=mm"));
    		
    		Kurogo::log(LOG_ALERT, "player: " . $player_name, 'module');
    	}
    	
    	foreach($team_2 as $player_name){
    		$player = $this->PointsTrackerRepository->getPlayerByName($player_name);
    		array_push($team_2_array, array('label' => $player_name, 'value' =>"http://www.gravatar.com/avatar/".md5(strtolower(trim($player['gravatar_email'])))."?s=100&d=mm"));
    		
    		Kurogo::log(LOG_ALERT, "player: " . $player_name, 'module');
    	}
    	
    	DashingController::sendNewGame($team_1_array, $team_2_array);

    }
    
    //Updates the dashboard's points widget and positions widget
    public function updateDashboardScore($placement, $score_result){
		$placement_array = array();
		$team_1_array = array();
		$team_2_array = array();
		
		for($i = 0; $i < count($placement) ; $i++){
			
			$temp = explode('-',$placement[$i]);
			$player_name = $temp[0];
			$player = $this->PointsTrackerRepository->getPlayerByName($player_name);
			
			$item = array();
			$item['label'] = ($i+1). ". " .$player_name;
			$item['value'] = "http://www.gravatar.com/avatar/".md5(strtolower(trim($player['gravatar_email'])))."?s=150&d=mm";
			
			array_push($placement_array, $item);
			
			if($temp[1] == "RED"){
				array_push($team_1_array, array('label' => $player_name, 'value' =>"http://www.gravatar.com/avatar/".md5(strtolower(trim($player['gravatar_email'])))."?s=100&d=mm"));
			}
			else{
				array_push($team_2_array, array('label' => $player_name, 'value' =>"http://www.gravatar.com/avatar/".md5(strtolower(trim($player['gravatar_email'])))."?s=100&d=mm"));
			}
			
		}
		
		//Push result to dashboard
		DashingController::sendScore('RED', $score_result['scores']['RED'], $team_1_array);
		DashingController::sendScore('BLUE', $score_result['scores']['BLUE'], $team_2_array);
		DashingController::sendPlacement($placement_array);
		
		
    }
    
}



