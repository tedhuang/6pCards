
var placement_array = new Array();

$(document).ready(function(){
	
	placement_array[0] = null;
	placement_array[1] = null;
	placement_array[2] = null;
	placement_array[3] = null;
	placement_array[4] = null;
	placement_array[5] = null;
	
	$(".player-container").click(function(){
		var player_name = $(this).attr('title');
		
		if($(this).hasClass("PLACED")){
			
			player_placement = getPlayerPlacement(player_name);
			
			if(player_placement !== false){
				placement_array[player_placement] = null;
				$(this).removeClass("PLACED");
				$(this).find('.placement').hide();
				
				var message = generatePrompt(getValidPlacement());
				$(".score-control-message").html(message);
			}
			else{
				alert("Player not found");
			}
		}
		else{
			curr_placement = getValidPlacement()

			if(curr_placement !== false){
				
				if( $(this).hasClass("T_RED") ){
					placement_array[curr_placement] = player_name + "-RED";
				}
				else if( $(this).hasClass("T_BLUE") ) {
					placement_array[curr_placement] = player_name + "-BLUE";
				}
				
				$(this).addClass("PLACED");
				$(this).find('.placement').html(curr_placement+1).show();
				
				var message = generatePrompt(getValidPlacement()); 
				$(".score-control-message").html(message);
				
				
			}
			else{
				alert("No more valid placements");
			}
			
			//Check if placement is complete
			if(getValidPlacement() === false){
				$(".score-control-message").html("Complete");
			}
		}
	});
	
	$(".score-submit").click(function(){
		sendScore(placement_array);
	});
	
	$(".delete_score").click(function(){
		deleteScore($(this).attr('title'));
	});
});

//Gets the number for the smallest placement that haven't been used yet
// returns false if no more placement available
function getValidPlacement(){
	for(i=0; i<placement_array.length; i++){
		if(placement_array[i] == null){
			return i;
		}
	}
	
	return false;
}

//Gets the placement of the player, returns false if player isn't found
function getPlayerPlacement(player_name){
	for(i=0; i<placement_array.length; i++){
		if(placement_array[i] != null){
			if(placement_array[i].split('-')[0] == player_name){
				return i;
			}
		}
	}
	
	return false;
}

function generatePrompt(placement){
	switch(placement){
		case 0:
			return "Select 1st place";
		case 1:
			return "Select 2nd place";
		case 2:
			return "Select 3rd place";
		case 3:
		case 4:
		case 5:
			return "Select "+(placement+1)+"th place";
		default:
			return "Error";
	}
}

function sendScore(placement_array){
	
	 var params = {"placement" :  JSON.stringify(placement_array), "game_id" : getUrlVars()['game_id']};
	 
	 console.log(placement_array);
	 
	makeAPICall('POST', "PointsTracker" , 'sendScore', params, function(response){
		if(response.success){
			if(response.isComplete){
				//Game is complete, redirect to scoreboard
				alert("Game finished");
			}
			else{
				console.log(response);
				var new_score = "<tr><td class='round_num'>"+($(".round_num").length+1)+"</td>" +
									"<td>"+response.score_result.scores.RED+"</td>" +
									"<td>"+response.score_result.scores.BLUE+"</td>" +
									"<td><img class='delete_score' src='./common/images/critical.png' title='"+response.score_result.score_id+"' /></td></tr>";

				$(".score-table").append(new_score);
				
				resetScoreControl();
			}
		}
		else{
			alert("Failed to send score. Error: " + response.message);
		}
	});
}

function deleteScore(score_id){
	var r=confirm("Are you sure?");
	if (r==true){
		makeAPICall('POST', "d" , 'sendScore', {'score_id': score_id}, function(response){
			if(response.success){
				$(".delete_score[title="+score_id+"]").parents("tr").remove();
				alert("Score Removed");
			}
			else{
				alert("Failed to delete score. Error: " + response.message);
			}
		});
	}
}

function resetScoreControl(){
	$(".placement").each(function(){$(this).hide()});
	$(".player-container").each(function(){
		$(this).removeClass('PLACED');
	});
	
	placement_array[0] = null;
	placement_array[1] = null;
	placement_array[2] = null;
	placement_array[3] = null;
	placement_array[4] = null;
	placement_array[5] = null;
	
	var message = generatePrompt(getValidPlacement());
	$(".score-control-message").html(message);
	

}











