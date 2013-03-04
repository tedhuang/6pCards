<?php


class DashingController{
	
	//Example call:
	//curl -d '{ "auth_token": "YOUR_AUTH_TOKEN", "text": "Hey, Look what I can do!" }' \http://<%=request.host%>:<%=request.port%>/widgets/welcome<
	
	const AUTH_TOKEN = "tedsdashboard123";
	
	public function sendNewGame($team_1, $team_2){
		try {
			$ch = curl_init();
			$url = "http://localhost:3030/widgets/team1pts";
			
			$fields = array("auth_token" => self::AUTH_TOKEN, "items" => $team_1);
			$json = json_encode($fields);
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
						    'Content-Type: application/json',                                                                                
						    'Content-Length: ' . strlen($json))                                                                       
						);    

			$result = curl_exec($ch);
			
			
			$url = "http://localhost:3030/widgets/team2pts";
			
			$fields = array("auth_token" => self::AUTH_TOKEN, "items" => $team_2);
			$json = json_encode($fields);
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
						    'Content-Type: application/json',                                                                                
						    'Content-Length: ' . strlen($json))                                                                       
						);    

			$result = curl_exec($ch);
			
			
			curl_close($ch);
		}
		catch(Exception $e){
			Kurogo::log(LOG_ERR, "cURL call to ". $url ."failed. Exception:" . $e , 'DashingController');
			return false;
		}
	}
	
	public function sendPlacement($placement_array){
		try {
			$ch = curl_init();
			$url = "http://localhost:3030/widgets/positions";
			
			$fields = array("auth_token" => self::AUTH_TOKEN, "items" => $placement_array);
			$json = json_encode($fields);
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
						    'Content-Type: application/json',                                                                                
						    'Content-Length: ' . strlen($json))                                                                       
						);    

			$result = curl_exec($ch);
			curl_close($ch);

		}
		catch(Exception $e){
			Kurogo::log(LOG_ERR, "cURL call to ". $url ."failed. Exception:" . $e , 'DashingController');
			return false;
		}
	}
	
	public static function sendScore($team, $score, $players){
		
		switch($team){
			case "RED":
				$widget_id = "team1pts";
				break;
			case "BLUE":
				$widget_id = "team2pts";
				break;
			default:
				return false;
		}
		
		if(!is_numeric($score)){
			return false;
		}
		
		try {
			$ch = curl_init();
			$url = "http://localhost:3030/widgets/".$widget_id;

			$fields = array("auth_token" => self::AUTH_TOKEN, "text" => $score, "items" => $players);
			$json = json_encode($fields);
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
						    'Content-Type: application/json',                                                                                
						    'Content-Length: ' . strlen($json))                                                                       
						);    

			$result = curl_exec($ch);
			curl_close($ch);

		}
		catch(Exception $e){
			Kurogo::log(LOG_ERR, "cURL call to ". $url ."failed. Exception:" . $e , 'DashingController');
			return false;
		}
		
		
	}
	
	
	
	
}