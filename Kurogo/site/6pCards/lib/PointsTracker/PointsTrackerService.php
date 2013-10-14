<?php

interface PointsTrackerService{

	function createGame($playersArray, $points_to_win);
	function deleteGame($game_id);
	function updateGame($game_id, $status, $complete_time, $tag);
	function setOverPoints($game_id);
	function isGameComplete($game_id);
	function getActiveGames();
	function getInactiveGames();
	function getLastGame();
	function getGameById($game_id);
	function getScoreByGame($game_id, $isSummed);
	function getLastestScore($game_id);
	function deleteScore($score_id);
	function addScore($game_id, $p_data);

	function createPlayer($player_name);
	function getAllPlayers();
	function getPlayerByName($player_name);
	function getPlayerScore($version);
	function getAllPlayersWithStats($version);
}