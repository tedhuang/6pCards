<?php

class ScoreEngine{

	/* 
	 * Used to calculate the score earned by both teams according to the rules of our
	 * card game.
	 * @param: p_data is an array of length 6 representating the 6 possible positions
	 * 		   that players fall into with their respective team color
	 * @return: an associative array of team RED's score and team BLUE's score
	 */
	public function calculateGameScore($p_data){
		$red_score = 0;
		$blue_score = 0;
		
		if($this->isTenZero($p_data, 'RED')){
			$red_score = 10;
		}
		else if($this->isTenZero($p_data, 'BLUE')){
			$blue_score = 10;
		}
		else if($this->isFiveZero($p_data, 'RED')){
			$red_score = 5;
		}
		else if($this->isFiveZero($p_data, 'BLUE')){
			$blue_score = 5;
		}
		else if($this->isFiveTwo($p_data, 'RED')){
			$red_score = 5;
			$blue_score = 2;
		}
		else if($this->isFiveTwo($p_data, 'BLUE')){
			$red_score = 2;
			$blue_score = 5;
		}
		else if($this->isFourThree($p_data, 'RED')){
			$red_score = 4;
			$blue_score = 3;
		}
		else if($this->isFourThree($p_data, 'BLUE')){
			$red_score = 3;
			$blue_score = 4;
		}
		else{
			return false;
		}
		
		return array('RED' => $red_score, 'BLUE' => $blue_score);
	}

	public function calculatePlayerScore($version, $games, $players){		
		switch($version){
			case 2:
				return $this->calculatePlayerScoreV2($games, $players);
			case 1:
			default:
				return $this->calculatePlayerScoreV1($games, $players);
				break;
		}
	}



//////////////////////////////////////////////////
//////////// Helper functions ////////////////////
//////////////////////////////////////////////////

	// POKER implementation
	// Generates customized player score based on game records
	// Also calculates the number of games won and played by each player
	private function calculatePlayerScoreV2($games, $players){
		$DEFAULT_WAGER = 2 * 0.01; // 2%

		$player_stats = array();
		
		foreach($players as $player){
			$player_name = $player['player_name'];
			$player_stats[$player_name] = array(
				'score' => 100,
				'games_played' => 0,
				'games_won' => 0,
				'ratio_history' => array(),
				//Extra statistics
				// ['player_name' => 'count'];
				'nemesis' => array(), 		 // beaten you the most
				'bad_luck_charm' => array(), // whom you lost with the most
				'best_teammate' => array()	 // whom you won with the most
			);
		}
		
		foreach($games as $game_data){
			$curr_wager = $DEFAULT_WAGER;
			$pot = 0;

			if($game_data['score_red_team'] > $game_data['score_blue_team']){
				$winners = explode('|', $game_data['team_red']);
				$losers = explode('|', $game_data['team_blue']);
			}
			else{
				$winners = explode('|', $game_data['team_blue']);		
				$losers = explode('|', $game_data['team_red']);
			}
			
			if($game_data['isOverpoints']){
				$curr_wager = $curr_wager * 2;
			}

			//Calculate score for losers
			foreach($losers as $player_name){
				$pot += ( $player_stats[$player_name]['score'] * $curr_wager ); 

				$player_stats[$player_name]['last_score'] = $player_stats[$player_name]['score'];
				$player_stats[$player_name]['score'] -= ($player_stats[$player_name]['score'] * $curr_wager);
				$player_stats[$player_name]['games_played']++;

				//store counts for nemesis
				foreach($winners as $winner_name){
					if( isset($player_stats[$player_name]['nemesis'][$winner_name])){
						$player_stats[$player_name]['nemesis'][$winner_name]++;
					}
					else{
						$player_stats[$player_name]['nemesis'][$winner_name] = 1;
					}
				}

				//store counts for badluckcharm
				foreach($losers as $loser_name){
					if($loser_name != $player_name){
						if( isset($player_stats[$player_name]['bad_luck_charm'][$loser_name])){
							$player_stats[$player_name]['bad_luck_charm'][$loser_name]++;
						}
						else{
							$player_stats[$player_name]['bad_luck_charm'][$loser_name] = 1;
						}
					}
				}
			}

			//Calculate score for winners
			foreach($winners as $player_name){
				$player_stats[$player_name]['last_score'] = $player_stats[$player_name]['score'];
				$player_stats[$player_name]['score'] += ( $pot / 3 );				
				$player_stats[$player_name]['games_played']++;
				$player_stats[$player_name]['games_won']++;

				//store counts for best teammate
				foreach($winners as $winner_name){
					if($winner_name != $player_name){
						if( isset($player_stats[$player_name]['best_teammate'][$winner_name])){
							$player_stats[$player_name]['best_teammate'][$winner_name]++;
						}
						else{
							$player_stats[$player_name]['best_teammate'][$winner_name] = 1;
						}
					}
				}
			}

			foreach($players as $player){
				$player_name = $player['player_name'];
				
				if($player_stats[$player_name]['games_played'] == 0){
					$win_ratio = 0;
				}
				else{
					$win_ratio = $player_stats[$player_name]['games_won']/$player_stats[$player_name]['games_played'];
				}
				
				array_push($player_stats[$player_name]['ratio_history'], 
						   array("timestamp" => $game_data['complete_time'],
								 "win_ratio" => $win_ratio ));		
			}
		}

		//Get the player's nemesis, bad_luck_charm etc.
		foreach($players as $player){
			$player_name = $player['player_name'];

			$nemesis = null;
			$bad_luck_charm = null;
			$best_teammate = null;

			$nemesis_count = 0;
			$bad_luck_charm_count = 0;
			$best_teammate_count = 0;

			foreach($player_stats[$player_name]["nemesis"] as $p => $count){
				if($count > $nemesis_count){
					$nemesis = $p;
					$nemesis_count = $count;
				}
			}
			foreach($player_stats[$player_name]["bad_luck_charm"] as $p => $count){
				if($count > $bad_luck_charm_count){
					$bad_luck_charm = $p;
					$bad_luck_charm_count = $count;
				}
			}
			foreach($player_stats[$player_name]["best_teammate"] as $p => $count){
				if($count > $best_teammate_count){
					$best_teammate = $p;
					$best_teammate_count = $count;
				}
			}

			$player_stats[$player_name]["nemesis"] = array(
				"name" => $nemesis,
				"count" => $nemesis_count
			);

			$player_stats[$player_name]["bad_luck_charm"] = array(
				"name" => $bad_luck_charm,
				"count" => $bad_luck_charm_count
			);

			$player_stats[$player_name]["best_teammate"] = array(
				"name" => $best_teammate,
				"count" => $best_teammate_count
			);
		}

		return $player_stats;
	}

	//SCALED points implementation
	private function calculatePlayerScoreV1($games, $players){
		$isOverpoints = false;
		$player_stats = array();

		foreach($players as $player){
			$player_name = $player['player_name'];
			$player_stats[$player_name] = array(
				'score' => 0,
				'games_played' => 0
			);
		}
		
		foreach($games as $game_data){
			if($game_data['score_red_team'] > $game_data['score_blue_team']){
				$winners = explode('|', $game_data['team_red']);
				$losers = explode('|', $game_data['team_blue']);
			}
			else{
				$winners = explode('|', $game_data['team_blue']);		
				$losers = explode('|', $game_data['team_red']);
			}
			
			$point_diff = abs($game_data['score_red_team'] - $game_data['score_blue_team']);
			
			foreach($winners as $player_name){
				
				if($point_diff < 26){
					$awarded_score = 10 + ($point_diff/10);
				}
				else{
					$awarded_score = 10 - ($point_diff/10);
				}
								
				$player_stats[$player_name]['score'] += $awarded_score;				
				$player_stats[$player_name]['games_played']++;
				
				if($game_data['isOverpoints']){
					$player_stats[$player_name]['score'] += 3;
				}
			}
			
			foreach($losers as $player_name){
				$player_stats[$player_name]['games_played']++;
				
				if($game_data['isOverpoints']){
					$player_stats[$player_name]['score'] += 3;
				}
			}
		
		}
		
		foreach($player_stats as &$entry){
			$entry['score'] = ($entry['score']/$entry['games_played'])*100;
		}

		return $player_stats;
	}

	//Checks below are implemented so that they assume the cases above them have been checked already
	private function isTenZero($data, $team_color){
		if( $data[0]['team_color'] == $team_color  && 
			$data[1]['team_color'] == $team_color &&
			$data[2]['team_color'] == $team_color )
		{
			return true;
		}
		else{
			return false;
		}
	}
	
	//Must be 1st, 2nd, and 4th or 5th
	private function isFiveZero($p_data, $team_color){
		if( $p_data[0]['team_color'] == $team_color  && $p_data[1]['team_color'] == $team_color && 
			( $p_data[3]['team_color'] == $team_color || $p_data[4]['team_color'] == $team_color  ) )
		{
			return true;
		}
		else{
			return false;
		}
	}
	
	//Must be 1st, 2nd or 1st, 5th and not be last
	private function isFiveTwo($p_data, $team_color){
		if( $p_data[0]['team_color'] == $team_color  && 
			(
				($p_data[1]['team_color'] == $team_color && $p_data[5]['team_color'] == $team_color ) ||  //2nd, 6th
				($p_data[2]['team_color'] == $team_color && $p_data[3]['team_color'] == $team_color) ||	  //3rd, 4th
				($p_data[2]['team_color'] == $team_color && $p_data[4]['team_color'] == $team_color) ||   //3rd, 5th
				($p_data[3]['team_color'] == $team_color && $p_data[4]['team_color'] == $team_color)      //4th, 5th
			)
		){
			return true;
		}
		else{
			return false;
		}
	}
	
	//Must be 2nd, 5th or 2nd, 3rd and 4th (not 1st)
	private function isFourThree($p_data, $team_color){
		if( $p_data[0]['team_color'] != $team_color &&
		    $p_data[1]['team_color'] == $team_color && 
		    ( 
			    ($p_data[4]['team_color'] == $team_color && $p_data[5]['team_color'] != $team_color) || 
			    ($p_data[2]['team_color'] == $team_color && $p_data[3]['team_color'] == $team_color )
		    )
		){
			return true;
		}
		else{
			return false;
		}
	}
}