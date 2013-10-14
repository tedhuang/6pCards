<?php

class PlayerRepository extends Repository{
	protected $scoreEngine;

	private $SCORE_ALGO_VERSION = 2;
	private $GET_GAMES_WITH_SCORE = "SELECT team_red, team_blue, complete_time, game_id, isOverpoints, 
									(SELECT SUM(score_red_team) FROM game_score AS gs WHERE gs.game_id=g.game_id ) AS score_red_team, 
									(SELECT SUM(score_blue_team) FROM game_score AS gs WHERE gs.game_id=g.game_id ) AS score_blue_team 
									FROM games AS g 
									WHERE status='COMPLETE' AND season=(SELECT max(season) FROM games) 
									ORDER BY complete_time ASC";

	protected function init($options){
		$this->scoreEngine = new ScoreEngine();
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
	
	public function getPlayerScore($version){
		$conn = self::connection();
		$sql = $this->GET_GAMES_WITH_SCORE;
		
		$result = $conn->query($sql,array());
		$games = $result->fetchAll();
		$players = $this->getAllPlayers();

		return $this->scoreEngine->calculatePlayerScore($version, $games, $players);
	}
	
	public function getAllPlayersWithStats($version) {
		$conn = self::connection();
		
		$sql = $this->GET_GAMES_WITH_SCORE;
		$result = $conn->query($sql,array());
		
		$player_stats = array();
		$games = $result->fetchAll();
		$players = $this->getAllPlayers();
		
		foreach($players as $player){
			$player_name = $player['player_name'];
			$player_stats[$player_name] = array(
				'games_played' => 0,
				'games_won' => 0,
				'ratio_history' => array()
			);
		}
		$player_stats = $this->scoreEngine->calculatePlayerScore($version, $games, $players);
		
		return array('player_stats' => $player_stats,
					 'score_data' => $games
					);
	}
}