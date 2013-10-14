<?php

class PointsTrackerServiceImpl implements PointsTrackerService{
	
	protected $playerRepository;
	protected $gameRepository;

	public static function factory(){
		$pointsTrackerService = new PointsTrackerServiceImpl();
		$pointsTrackerService->initialize();
		return $pointsTrackerService;
	}

	protected function initialize(){
		$this->playerRepository = Repository::factory("PlayerRepository", null);
		$this->gameRepository = Repository::factory("GameRepository", null);
	}

	/*****************************************
	 * Game Repository
	 *****************************************/
	public function createGame($playersArray, $points_to_win = 50){
		return $this->gameRepository->createGame($playersArray, $points_to_win);
	}
	public function deleteGame($game_id){
		return $this->gameRepository->deleteGame($game_id);
	}
	public function updateGame($game_id, $status, $complete_time, $tag = ""){
		return $this->gameRepository->updateGame($game_id, $status, $complete_time, $tag);
	}
	public function setOverPoints($game_id){
		return $this->gameRepository->setOverPoints($game_id);
	}
	public function isGameComplete($game_id){
		return $this->gameRepository->isGameComplete($game_id);
	}
	public function getActiveGames(){
		return $this->gameRepository->getActiveGames();
	}
	public function getInactiveGames(){
		return $this->gameRepository->getInactiveGames();
	}
	public function getLastGame(){
		return $this->gameRepository->getLastGame();
	}
	public function getGameById($game_id){
		return $this->gameRepository->getGameById($game_id);
	}
	public function getScoreByGame($game_id, $isSummed = true){
		return $this->gameRepository->getScoreByGame($game_id, $isSummed);
	}
	public function getLastestScore($game_id){
		return $this->gameRepository->getLastestScore($game_id);
	}
	public function deleteScore($score_id){
		return $this->gameRepository->deleteScore($score_id);
	}
	public function addScore($game_id, $p_data){
		return $this->gameRepository->addScore($game_id, $p_data);
	}

	/*****************************************
	 * Player Repository
	 *****************************************/
	public function createPlayer($player_name){
		return $this->playerRepository->createPlayer($player_name);
	}
	public function getAllPlayers(){
		return $this->playerRepository->getAllPlayers();
	}
	public function getPlayerByName($player_name){
		return $this->playerRepository->getPlayerByName($player_name);
	}
	public function getPlayerScore($version){
		return $this->playerRepository->getPlayerScore($version);
	}
	public function getAllPlayersWithStats($version){
		return $this->playerRepository->getAllPlayersWithStats($version);
	}
}