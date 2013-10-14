
var placement_array = new Array();

$(document).ready(function(){
	
	placement_array[0] = null;
	placement_array[1] = null;
	placement_array[2] = null;
	placement_array[3] = null;
	placement_array[4] = null;
	placement_array[5] = null;
	
	$(".player-container").click(function(){
		if($(".status").text() == "COMPLETE"){
			return;
		}
			
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
		if(isPlacementComplete()){
			$(this).html("<div class='loadImg'><img src='./common/images/loader.gif' width=30 /></div>");
			$(this).attr("disabled", "disabled");
			sendScore(placement_array);
		}
		else{
			//Blinking effect
			$(".score-control-message").css('color', "#f00");
			setTimeout(function(){$(".score-control-message").css('color', "#000")}, 200)
			setTimeout(function(){$(".score-control-message").css('color', "#f00")}, 400)
			setTimeout(function(){$(".score-control-message").css('color', "#000")}, 600)
			setTimeout(function(){$(".score-control-message").css('color', "#f00")}, 800)
			setTimeout(function(){$(".score-control-message").css('color', "#000")}, 1000)
		}		
	});
	
	$(".delete_score").live('click',function(){
		deleteScore($(this).attr('title'));
	});
});

function isPlacementComplete(){
	var i;
	for(i=0; i<6; i++){
		if( placement_array[i] == null ){
			return false;
		}
	}
	return true;
}

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
	makeAPICall('POST', "PointsTracker" , 'sendScore', params, function(response){
		$(".score-submit").attr('disabled', false);
		if(response.success){
			var new_score = "<tr><td class='round_num'>"+($(".round_num").length+1)+"</td>" +
								"<td>"+response.score_result.scores.RED+"</td>" +
								"<td>"+response.score_result.scores.BLUE+"</td>" +
								"<td><img class='delete_score' src='./common/images/remove.png' title='"+response.score_result.score_id+"' /></td></tr>";
			$(".score-table").append(new_score).show();
			$(".score-submit").html("Submit");
			
			if(response.isComplete){
				//Game is complete, redirect to scoreboard
				alert("Game finished");
				window.location = "./pageScoreboard?game_id=" + getUrlVars()['game_id'];
			}
			else{
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
		$(".delete_score[title="+score_id+"]").parents("tr").fadeOut(3000);
		makeAPICall('POST', "PointsTracker" , 'removeScore', {'score_id': score_id}, function(response){
			if(response.success){
				// window.location.reload();
				$(".delete_score[title="+score_id+"]").parents("tr").remove();
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












