<?php

class GameRepository extends Repository{
	protected $scoreEngine;

	protected function init($options){
		$this->createTables();
		$this->scoreEngine = new ScoreEngine();
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

	public function getCurrentSeasonNum(){
		$conn = self::connection();
		$sql = "SELECT MAX(season_num) AS season_num FROM seasons";
		$result = $conn->query($sql);
		$row = $result->fetch();
		return $row['season_num'];
	}

	public function createGame($playersArray, $points_to_win = 50){
		$conn = self::connection();
		
		$teamRed = implode('|',$playersArray['RED']);
		$teamBlue = implode('|',$playersArray['BLUE']);
		$curr_season = $this->getCurrentSeasonNum();
		
		$sql = "INSERT INTO games (points_to_win, team_red, team_blue, season) VALUES (?, ?, ?, ?)";
		$conn->query($sql, array($points_to_win, $teamRed, $teamBlue, $curr_season));
		
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
	

	public function updateGame($game_id, $status, $complete_time, $tag = ""){
		$conn = self::connection();
		$sql = "UPDATE games SET status=?, complete_time=?, tag=? WHERE game_id=?";
		$conn->query($sql, array($status, $complete_time, $tag, $game_id) );
		
		return true;
	}
	
	public function setOverPoints($game_id){
		$conn = self::connection();
		$sql = "UPDATE games SET isOverpoints=1 WHERE game_id=?";
		$conn->query($sql, array($game_id));
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

		if($score_red >= $points_to_win || $score_blue >= $points_to_win){
			if(abs($score_red - $score_blue) <= 3){
				$this->setOverPoints($game_id);
				return false;	
			}
			else{
				return true;
			}
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
	
	public function getLastGame(){
		$conn = self::connection();
		$sql = "SELECT * from games ORDER BY game_id DESC LIMIT 1";
		$result = $conn->query($sql, array());
		$row = $result->fetch();
		
		if(!empty($row)){
			return $row;
		}
		else{
			return false;
		}
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

		$result = $this->scoreEngine->calculateGameScore($p_data);

		if($result !== false){
			$sql = "INSERT INTO game_score (game_id, score_red_team, score_blue_team) VALUES (?,?,?)";
			$conn->query($sql, array($game_id, $result['RED'],  $result['BLUE']));

			$lastInsert = $conn->lastInsertId('score_id');		
			if($lastInsert != ""){
				for($i=0; $i < count($p_data); $i++){
					$sql = "INSERT INTO player_score (game_id, team_color, position, player_name, score_id) VALUES (?,?,?,?,?)";
					$conn->query( $sql, array($game_id, $p_data[$i]['team_color'], ($i+1), $p_data[$i]['player_name'], $lastInsert ) );
				}
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
}