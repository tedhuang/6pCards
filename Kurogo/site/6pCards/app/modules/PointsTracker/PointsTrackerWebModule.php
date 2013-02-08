<?php

Kurogo::includePackage('PointsTracker');

class PointsTrackerWebModule extends WebModule
{
	protected $id='PointsTracker';
	protected $PointsTrackerRepository;
	
	protected function initialize() {
		$this->PointsTrackerRepository = Repository::factory("PointsTrackerRepository", null);
	}
  
	protected function initializeForPage() {
  		
		//$this->addJQuery();
		$session = $this->getSession();
		
		
		/* Load Javascript CSS Libraries */
		$this->addInternalJavascript('/common/javascript/lib/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js');
		$this->addInternalJavascript('/common/javascript/lib/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js');
		$this->addInternalJavascript('/common/javascript/lib/jquery.ui.touch-punch.min.js');
		$this->addInternalJavascript('/common/javascript/lib/json2.js');
		$this->addInternalJavascript('/common/javascript/lib/jquery.easing.1.3.js');
		
//		$this->addInternalJavascript('/common/javascript/lib/jquery-ui-1.9.2.custom/js/');
//		$this->addInternalJavascript('/common/javascript/lib/d3.v2.min.js');
//		$this->addInternalJavascript('/common/javascript/lib/swipe.js');
//		$this->addInternalJavascript('/common/javascript/lib/jquery.validate.min.js');
		
		$this->addInternalCSS('/common/javascript/lib/jquery-ui-1.9.2.custom/css/pepper-grinder/jquery-ui-1.9.2.custom.css');
		$this->addExternalCSS('http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300,300italic');
		//$this->addExternalCSS('http://fonts.googleapis.com/css?family=Josefin+Slab:400,600,700');
		
		$jennysIP = "75.157.140.7";
		
		if($_SERVER['REMOTE_ADDR'] == $jennysIP && $this->page != "piggysPage"){
			$this->redirectTo('piggysPage');
		}

		
		switch ($this->page) {
			case "piggysPage":
				
				break;
			case 'index':
				$game_list = array('title' => 'Game List', 'url' => 'pageGameList', 'image' => './modules/PointsTracker/images/pageGameList.png');
				$create_game = array('title' => 'New Game', 'url' => 'pageCreateGame', 'image' => './modules/PointsTracker/images/pageCreateGame.png');
				$stats = array('title' => 'Statistics', 'url' => 'pageStats', 'image' => './modules/PointsTracker/images/pageStats.png');
				$placeholder = array('title' => 'Leaderboard', 'url' => 'pageLeaderboard', 'image' => './modules/PointsTracker/images/pageLeaderboard.png');
				
				$dashboardItems = array($game_list, $create_game, $stats, $placeholder);					
				$this->assign('dashboardItems', $dashboardItems);
				break;
			
			case 'pageGameList':
				$activeGames = $this->PointsTrackerRepository->getActiveGames();
				$inactiveGames = $this->PointsTrackerRepository->getInactiveGames();
				$this->assign("inactiveGames", $inactiveGames);
				$this->assign("activeGames", $activeGames);
				break;
				
			case 'pageCreateGame':
				$players = $this->PointsTrackerRepository->getAllPlayers();
				$this->assign("players", $players);
				break;
				
			case 'pageGame':
				$game_id = $this->getArg("game_id");
				$game =  $this->PointsTrackerRepository->getGameById($game_id);
				$score_record = $this->PointsTrackerRepository->getScoreByGame($game_id, true);
				
				if($game === false){
					$this->redirectTo('pageError', array("message" => "Game does not exist"));
				}
				
				foreach( explode('|',$game['team_red']) as $name){
					$player =  $this->PointsTrackerRepository->getPlayerByName($name);
					$game['t_red'][$name] = $player;
				}
								
				foreach( explode('|',$game['team_blue']) as $name){
					$player =  $this->PointsTrackerRepository->getPlayerByName($name);
					$game['t_blue'][$name] = $player;
				}
				
				
				$deviceClassifier = Kurogo::deviceClassifier();
		        $this->assign("isTablet", $deviceClassifier->isTablet());
				$this->assign("score_record", $score_record);
				$this->assign("game", $game);
				break;		
			
			case 'pageScoreboard':
				$game_id = $this->getArg("game_id");
				$game =  $this->PointsTrackerRepository->getGameById($game_id);
				$scores =  $this->PointsTrackerRepository->getScoreByGame($game_id, true);
				
				if(count($scores) < 2){
					$this->redirectTo('pageError', array("message" => "Data not available"));
				}
				
				
				$score_data_js = $this->generateScoreGraphData($scores, explode('|', $game['team_red']), explode('|', $game['team_blue']) );

				$round_duration_data = $this->getRoundDuration($scores,$game['timestamp'] );
				
				$this->assign("game", $game);
				$this->assign("scores", $scores);
				$this->assign("avg_round_duration", array_sum($round_duration_data)/count($round_duration_data));
				$this->assign("max_round_duration", max($round_duration_data));

				$this->addInlineJavascript($score_data_js);
				$this->addInlineJavascript('var score_count = ' . count($scores));
				$this->addExternalJavascript("https://www.google.com/jsapi");
				$this->addInlineJavascript('google.load("visualization", "1", {packages:["corechart"]}); google.setOnLoadCallback(drawChart);');
				break;
			
			case 'pageStats':
				
				break;
			
			case 'pageLeaderboard':
				$data = $this->PointsTrackerRepository->getAllPlayersWithStats();
				
				$win_ratio = array();
				foreach($data['player_stats'] as $player_name => &$stats){
					$player = $this->PointsTrackerRepository->getPlayerByName($player_name);

					$stats['gravatar_email'] = $player['gravatar_email'];
					$stats['win_ratio'] = $stats['games_won']/$stats['games_played'];
					$win_ratio[$player_name] = $stats['games_won']/$stats['games_played'];
				}
				

				
				//sort array by win ratio
				array_multisort($win_ratio, SORT_DESC, $data['player_stats']);

				$json_data =  $this->generateWinRatioGraphJson($data);

				$this->assign('player_stats' , $data['player_stats']);	
				$this->addInlineJavascript($json_data);
				
				$this->addInternalJavascript('/common/javascript/lib/nvd3/lib/d3.v2.js');			
				$this->addInternalJavascript('/common/javascript/lib/nvd3/nv.d3.js');			
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/tooltip.js');			
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/utils.js');			
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/legend.js');			
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/axis.js');	
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/scatter.js');	
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/line.js');	
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/historicalBar.js');
//				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/multiBar.js');	
//				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/multiBarChart.js');			
				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/linePlusBarChart.js');		
//				$this->addInternalJavascript('/common/javascript/lib/nvd3/src/models/cumulativeLineChart.js');		
				$this->addInternalCSS("/common/javascript/lib/nvd3/src/nv.d3.css");
				break;
			
			case 'pageProfile':
				
				
				break;
			
			case 'pageError':
				$this->assign("message", $this->getArg("message", "No message given."));
				break;
			default:
				$this->redirectTo('index');
				break;
		}
		
	}
	
	private function generateWinRatioGraphJson($data){
		
		$json_data = "var data=[";
		
		$count = count($data['player_stats']);
		
		foreach($data['player_stats'] as $player_name => $player_data){
			
			
			$json_data .= "{'key' : '".$player_name."', 'values' : [";
			
//			echo json_encode($data['player_stats']['Ted']['ratio_history']);

			foreach($player_data['ratio_history'] as $entry){
				
				$timestamp = date("U", strtotime($entry['timestamp']))*1000;
				
				$json_data .= "[".$timestamp.",".$entry['win_ratio']."],";
			}
			$json_data = trim($json_data, ",");
			$json_data .= "]},";	
		}
		$json_data = trim($json_data, ",");		
		$json_data .= "];";
		
		return $json_data;
	}
	
	private function getRoundDuration($scores, $game_start_time){
		$duration = 0;
		$duration_array = array();
		
		if(count($scores) < 2){
			return false;
		}
		
		$lastElement = $scores[0];
		for($i=0; $i<count($scores); $i++){
			//echo strtotime($scores[$i]['timestamp']). ' ';
			if($i == 0 ){ //first element
				$duration = strtotime($scores[$i]['timestamp']) - strtotime($game_start_time);
			}
			else{
				$duration = strtotime($scores[$i]['timestamp']) - strtotime($lastElement['timestamp']);
			}
			
			$lastElement = $scores[$i];
			array_push($duration_array, $duration*1000 );
		}
		
		return $duration_array;
	}
	
	
	private function generateScoreGraphData($scores, $team_red, $team_blue){
		$score_data = "function getScoreData(){ return google.visualization.arrayToDataTable([";
		
		$t_red_str = "";
		$t_blue_str = "";
		foreach($team_red as $p_name){
			$t_red_str .= $p_name . " ";
		}
		
		foreach($team_blue as $p_name){
			$t_blue_str .= $p_name . " ";
		}
		
		$score_data .= "['number', '".$t_red_str."', '".$t_blue_str."'],";
		
		$count = 1;
		foreach($scores as $score){
			$score_data .= 
				"[".$count.",".
					$score['score_red_team'].",".
					$score['score_blue_team'].
				"],";
					
			$count++;
		}
		$score_data = trim($score_data, ",");
		$score_data .= "])};";
		
		return $score_data;
	}
	
	
}












