<?php

class PointsTrackerRepository extends Repository{
	
	protected function init($options){
		$this->createTables();
	}
	
	private function createTables(){
//		$conn = self::connection();
//		$checkSql = "SELECT 1 FROM score";
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
	
	public function setScore($game_id, $team_color, $player_name, $points_earned){
		$conn = self::connection();
		$sql = "INSERT INTO score (game_id, team_color, player_name, date, points_earned ) VALUES (?,?,?,CURDATE(),?)";
		$conn->query($sql, array($game_id, $team_color, $player_name, $points_earned));
		
		$lastInsert = $conn->lastInsertId('score_id');		
		if($lastInsert != ""){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function updateGame($game_id, $status, $tag = null, $isOverpoints = 0){
		$conn = self::connection();
		$sql = "UPDATE games SET status=?, tag=? isOverpoints=? WHERE game_id=?";
		$conn->query($sql, array($status, $tag, $isOverpoints, $game_id));
		
		return true;
	}
	
	public function isGameComplete($game_id){
		$conn = self::connection();
		$sql = "SELECT SUM(points_earned) AS total_points FROM score WHERE game_id=?";
		$results = $conn->query($sql, array($game_id));
		$row = $results->fetch();
		
		$total_points = $row['total_points'];
		
		$sql = "SELECT points_to_win FROM games";
		$result = $conn->query($sql);
		$row = $results->fetch();
	
		$points_to_win = $row['points_to_win'];
		
		//TODO: implement checks for overpoints feature
		
		if($total_points >= $points_to_win){			
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getActiveGames(){
		$conn = self::connection();
		$sql = "SELECT * FROM games WHERE status='STARTED'";
		$result = $conn->query($sql);
		return $result->fetchAll();
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
	
	
	public function getScoreboard(){
		$conn = self::connection();
		
	}
	
	public function getStats(){
		$conn = self::connection();
		
	}
	
}













