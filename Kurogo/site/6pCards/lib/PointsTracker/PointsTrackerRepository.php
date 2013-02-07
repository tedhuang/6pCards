<?php

class PointsTrackerRepository extends Repository{
	
	protected function init($options){
		$this->createTables();
	}
	
	private function createTables(){
//		$conn = self::connection();
//		$checkSql = "SELECT 1 FROM games";
//
//		if (!$result = $conn->query($checkSql, array(), db::IGNORE_ERRORS)) {
//			$createSQL = file_get_contents(DATA_DIR . "/PointsTracker/create_tables.sql");
//			$conn->query($createSQL);
//		}

		return true;
	}

	public function createGame($playersArray, $points_to_win = 50){
		$conn = self::connection();
		
		$teamRed = implode('|',$playersArray['RED']);
		$teamBlue = implode('|',$playersArray['BLUE']);
		
		$sql = "INSERT INTO games (points_to_win, team_red, team_blue) VALUES (?, ?, ?)";
		$conn->query($sql, array($points_to_win, $teamRed, $teamBlue));
		
		$lastInsert = $conn->lastInsertId('game_id');		
		if($lastInsert != ""){
			return $lastInsert;
		}
		else{
			return false;
		}
	}
	
	public function deleteGame($game_id){
		$conn = self::connection();
		
		$sql = "DELETE FROM games WHERE game_id=?";
		$conn->query($sql, array($game_id));
		
		return true;
	}
	

	public function updateGame($game_id, $status, $complete_time, $tag = "", $isOverpoints = 0){
		$conn = self::connection();
		$sql = "UPDATE games SET status=?, complete_time=?, tag=?, isOverpoints=? WHERE game_id=?";
		$conn->query($sql, array($status, $complete_time, $tag, $isOverpoints, $game_id) );
		
		return true;
	}
	
	public function isGameComplete($game_id){		
		$conn = self::connection();
		
		$current_score = $this->getLastestScore($game_id);
		
		$score_red = $current_score['RED'];
		$score_blue = $current_score['BLUE'];
		
		$sql = "SELECT points_to_win FROM games";
		$result = $conn->query($sql);
		$row = $result->fetch();
		$points_to_win = $row['points_to_win'];

//		$points_to_win = 50;


		//TODO: implement checks for overpoints feature
		
		if($score_red >= $points_to_win || $score_blue >= $points_to_win){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getActiveGames(){
		$conn = self::connection();
		$sql = "SELECT *, 
				(SELECT SUM(score_red_team) AS score_red 
				    FROM game_score WHERE game_id=games.game_id
				) AS score_red,
				(SELECT SUM(score_blue_team) AS score_blue 
				    FROM game_score WHERE game_id=games.game_id
				) AS score_blue 
				FROM games WHERE status='STARTED' ORDER BY timestamp DESC";
		$result = $conn->query($sql);
		return $result->fetchAll();
	}
	
	public function getInactiveGames(){
		$conn = self::connection();
		$sql = "SELECT *, 
				( SELECT SUM(score_red_team) AS score_red 
				    FROM game_score WHERE game_id=games.game_id
				) AS score_red,
				(SELECT SUM(score_blue_team) AS score_blue 
				    FROM game_score WHERE game_id=games.game_id
				) AS score_blue  
				FROM games WHERE status='COMPLETE' ORDER BY timestamp DESC";
		$result = $conn->query($sql);
		return $result->fetchAll();
	}
	
	public function getGameById($game_id){
		$conn = self::connection();
		$sql = "SELECT * FROM games WHERE game_id=?";
		$result = $conn->query($sql, array($game_id));
		$row = $result->fetch();
		
		if(!empty($row)){
			return $row;
		}
		else{
			return false;
		}
	}
	
	public function createPlayer($player_name){
		$conn = self::connection();
		$sql = "INSERT IGNORE INTO players (player_name) VALUES (?) ";
		$conn->query($sql, array($player_name));
		
		$lastInsert = $conn->lastInsertId('player_id');		
		if($lastInsert != ""){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getAllPlayers(){
		$conn = self::connection();
		$sql = "SELECT * FROM players";
		$result = $conn->query($sql);
		
		return $result->fetchAll();
	}
	
	public function getPlayerByName($name){
		$conn = self::connection();
		$sql = "SELECT * FROM players WHERE player_name=?";
		$result = $conn->query($sql, array($name));
		$row = $result->fetch();
		if(!empty($row)){
			return $row;
		}
		else{
			return false;
		}
	}
	
	public function getScoreByGame($game_id, $isSummed=true){
		$conn = self::connection();
		$sql = "SELECT * FROM game_score WHERE game_id=? ORDER BY timestamp ASC";
		$result = $conn->query($sql, array($game_id));
		
		if(!$isSummed){
			return $result->fetchAll();
		}
		else{
			$score_summed = $result->fetchAll();
			$sum_red = 0;
			$sum_blue = 0;

			for($i = 0; $i <count($score_summed); $i++){
				$score_summed[$i]['count'] = $i+1;
				
				$score_summed[$i]['score_red_team'] += $sum_red;
				$sum_red = $score_summed[$i]['score_red_team'];

				$score_summed[$i]['score_blue_team'] += $sum_blue;
				$sum_blue = $score_summed[$i]['score_blue_team'];
			}
			
			return $score_summed;
		}
		
	}
	
	public function getLastestScore($game_id){
		$conn = self::connection();
		$sql = "SELECT SUM(score_red_team) AS t_red, SUM(score_blue_team) AS t_blue FROM game_score WHERE game_id=?";
		$result = $conn->query($sql, array($game_id));
		$row = $result->fetch();
		return array('RED' => $row['t_red'], 'BLUE' => $row['t_blue']);	
	}
	
	public function deleteScore($score_id){
		$conn = self::connection();
		$sql = "DELETE FROM game_score WHERE score_id=?";
		$conn->query($sql, array($score_id));
		return true;
	}
	
	public function addScore($game_id, $p_data){
		$conn = self::connection();

		$result = $this->calculateScore($p_data);

		if($result !== false){
			$sql = "INSERT INTO game_score (game_id, score_red_team, score_blue_team) VALUES (?,?,?)";
			$conn->query($sql, array($game_id, $result['RED'],  $result['BLUE']));

			$lastInsert = $conn->lastInsertId('score_id');		
			if($lastInsert != ""){
				
				//TODO: insert individual player scores here				
//				$sql = "INSERT INTO player_score (game_id, team_color, points_earned, player_name, score_id) VALUES (?,?,?,?,?)";
//				$conn->query($sql, array($game_id, ));
				
								
				return array('score_id' => $lastInsert, 'scores' => $this->getLastestScore($game_id));
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	
	private function calculateScore($p_data){
		
		/*
		 * Checks below are implemented so that they assume the cases above them have been checked already
		 */
		function isTenZero($data, $team_color){
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
		function isFiveZero($p_data, $team_color){
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
		function isFiveTwo($p_data, $team_color){
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
		function isFourThree($p_data, $team_color){
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
		
		
		$red_score = 0;
		$blue_score = 0;
		
		if(isTenZero($p_data, 'RED')){
			$red_score = 10;
		}
		else if(isTenZero($p_data, 'BLUE')){
			$blue_score = 10;
		}
		else if(isFiveZero($p_data, 'RED')){
			$red_score = 5;
		}
		else if(isFiveZero($p_data, 'BLUE')){
			$blue_score = 5;
		}
		else if(isFiveTwo($p_data, 'RED')){
			$red_score = 5;
			$blue_score = 2;
		}
		else if(isFiveTwo($p_data, 'BLUE')){
			$red_score = 2;
			$blue_score = 5;
		}
		else if(isFourThree($p_data, 'RED')){
			$red_score = 4;
			$blue_score = 3;
		}
		else if(isFourThree($p_data, 'BLUE')){
			$red_score = 3;
			$blue_score = 4;
		}
		else{
			return false;
		}
		
		return array('RED' => $red_score, 'BLUE' => $blue_score);
		
	}
	
	
	
	
////////////////////////////////////////////////
/***********************************************
 * STATISTICS CALCULATION SECTION
 ***********************************************/
/////////////////////////////////////////////////
	
	public function getAllPlayersWithStats() {
		$conn = self::connection();
		
		$sql = "SELECT team_red, team_blue, complete_time, 
					(SELECT SUM(score_red_team) FROM game_score AS gs WHERE gs.game_id=g.game_id ) AS score_red_team, 
					(SELECT SUM(score_blue_team) FROM game_score AS gs WHERE gs.game_id=g.game_id ) AS score_blue_team 
				FROM games AS g WHERE status='COMPLETE' ORDER BY complete_time ASC";
		
		$result = $conn->query($sql,array());
		
		$player_stats = array();
		
		$score_data = $result->fetchAll();
		
		//Calculate the number of games won and played by each player
		foreach($score_data as $game_data){
			if($game_data['score_red_team'] > $game_data['score_red_team']){
				$winners = explode('|', $game_data['team_red']);
				$losers = explode('|', $game_data['team_blue']);
			}
			else{
				$winners = explode('|', $game_data['team_blue']);		
				$losers = explode('|', $game_data['team_red']);
			}
			
			foreach($winners as $player_name){
				if(!isset($player_stats[$player_name])){
					$player_stats[$player_name] = array(
						'games_played' => 0,
						'games_won' => 0
					);
				}

				$player_stats[$player_name]['games_won']++;
				$player_stats[$player_name]['games_played']++;
			}
			
			foreach($losers as $player_name){
				if(!isset($player_stats[$player_name])){
					$player_stats[$player_name] = array(
						'games_played' => 0,
						'games_won' => 0
					);
				}
				
				$player_stats[$player_name]['games_played']++;
			}			
		}
		
		
		return array('player_stats' => $player_stats,
					 'score_data' => $score_data
					);
	}
	

	
}













