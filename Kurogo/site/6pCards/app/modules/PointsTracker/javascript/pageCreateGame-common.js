var MAX_PLAYERS_PER_TEAM = 3;

var players_selected = new Array();
var player_selection_locked = false;

var team_red = new Array();
var team_blue = new Array();


$(document).ready(function(){
	
	$(".players-submit").click(function(){
		startTeamSelection();
	});	
	
	$(".players-reset").click(function(){
		players_selected = new Array();
		$(".player-container.SELECTED").each(function(){
			$(this).removeClass("SELECTED");
			$(this).find(".checkbox").hide();
		});
	});
	
	//Player selection
	$(".player-container").click(function(){
		if(!player_selection_locked){
			if($(this).hasClass("SELECTED")){
				$(this).removeClass("SELECTED");
				$(this).find('.checkbox').hide();
				players_selected.pop( $(this).attr("title") );
			}
			else{
				if(players_selected.length != MAX_PLAYERS_PER_TEAM*2){
					$(this).addClass("SELECTED");
					$(this).find('.checkbox').show();
					players_selected.push( $(this).attr("title") );
					
					if(players_selected.length == MAX_PLAYERS_PER_TEAM*2 ){
						$(".players-controls").show();
					}
				}
				else{
					alert("Max players reached");
				}
			}
		}
	});	
	
	


});


function startTeamSelection(){
	
	player_selection_locked = true;
	$(".players-controls").hide();
	$(".players-selection-container").find('h3').hide();
	$(".player-container").each(function(){
		if($(this).hasClass("SELECTED")){
			$(this).find('.checkbox').hide();
		}
		else{
			$(this).remove();
		}
	})
	
	$(".team-selection-container").show();
	
	
	
	
	$(".team-selection-randomize").click(function(){
		clearPlayers();
		$(".players-selection-container").hide();
		
		var rand_team = players_selected;
		
		rand_team.sort(function(){
			return (Math.round(Math.random())-0.5);
		});
	
		for(i=0; i<rand_team.length; i++){
			avatar = $(".SELECTED.player-container[title='"+rand_team[i]+"']").find('img').attr('src');			
			player_container = '<div class="player-container"><img src="'+avatar+'"/><span>'+rand_team[i]+'</span></div>';
			if(i < 3){
				team_red.push(rand_team[i]);
				$(".team-container-red").append(player_container);
			}
			else{
				team_blue.push(rand_team[i]);
				$(".team-container-blue").append(player_container);		
			}
		}
		
		
		$(".team-controls").show();
		$(".team-summary").html( 
				'Red Team: ' + team_red+ 
				'<br> Blue Team: ' + team_blue);
		
		$(".team-container-red").find('.player-num-red').find("span").text(team_red.length);
		$(".team-container-blue").find('.player-num-blue').find("span").text(team_blue.length);
	});
	
	$(".team-submit").click(function(){
		startGame();
	});	
	
	$(".team-reset").click(function(){
		clearPlayers();
	});	
	
	$(".player-container").draggable({
		addClasses: false,
    	snap: true,
    	snapMode: "inner",
    	snapTolerance: 25,
    	revert: 'invalid'
	});
	
	$(".players-container").droppable({
		drop: function(event,ui){},
		tolerance: "fit",
		accept: ".player-container"
	})

    $( ".team-container-red" ).droppable({
    	accept: ".player-container",
    	tolerance: "fit",
        drop: function( event, ui ) {
            if (!ui.draggable.data("originalPosition")) {
                ui.draggable.data("originalPosition",
                    ui.draggable.data("draggable").originalPosition);
            }

        	player_name = $(ui.draggable).attr('title');
        	
        	if( !addPlayer(player_name, "RED") ){
        		//revert the drag
        		revertDraggable($(ui.draggable));
        	}
        	
        },
	    out: function(event, ui){
	    	player_name = $(ui.draggable).attr('title');
	    	removePlayer(player_name, "RED");
	    }
    
    });

    $( ".team-container-blue" ).droppable({
    	accept: ".player-container",
    	tolerance: "fit",
        drop: function( event, ui ) {
        	
            if (!ui.draggable.data("originalPosition")) {
                ui.draggable.data("originalPosition",
                    ui.draggable.data("draggable").originalPosition);
            }

        	player_name = $(ui.draggable).attr('title');
        	
        	if( !addPlayer(player_name, "BLUE") ){
        		//revert the drag
        		revertDraggable($(ui.draggable));
        	}
        	
        },
        out: function(event, ui){
        	player_name = $(ui.draggable).attr('title');
	    	removePlayer(player_name, "BLUE");
        }
    });
}

function clearPlayers(){
	
	revertDraggable($(".player-container"));
	$(".team-controls").hide();

	team_red = new Array();
	team_blue = new Array();
	$(".team-container-red").find('.player-num-red').find("span").text(team_red.length);
	$(".team-container-blue").find('.player-num-blue').find("span").text(team_blue.length);
	$(".team-container").find(".player-container").remove();
	
}


function addPlayer(player_name, team_color){
	
	if(team_color == "RED"){
		
		//Check if player exists
		if( team_red.indexOf(player_name) < 0 ){
			
			//Check if max players reached for current team
			if(team_red.length + 1 > MAX_PLAYERS_PER_TEAM ){
				//alert("Failed to add to team, Exceeded max players in team");
				return false;
			}
			else{
				//ADD player to red
				team_red.push(player_name);
				$(".team-container-red").find('.player-num-red').find("span").text(team_red.length);
			}
		}
		
	}
	else if(team_color == "BLUE"){
		//Check if player exists
		if( team_blue.indexOf(player_name) < 0 ){
		
			//Check if max players reached for current team
			if(team_blue.length + 1 > MAX_PLAYERS_PER_TEAM ){
				//alert("Failed to add to team, Exceeded max players in team");
				return false;
			}
			else{
				//ADD player to blue
				team_blue.push(player_name);
				$(".team-container-blue").find('.player-num-blue').find("span").text(team_blue.length);
			}
		}
	}
	else{
		alert("Team color not found");
		return false;
	}
	
	//Check to see if teams are set
	if(team_red.length + team_blue.length == MAX_PLAYERS_PER_TEAM*2){
		$(".team-controls").show();
		$(".team-summary").html( 
				'Red Team: ' + team_red+ 
				'<br> Blue Team: ' + team_blue);
	}
	
	return true;
}

function removePlayer(player_name, team_color){

	if(team_color == "RED"){
		//Check if player exists
		if( team_red.indexOf(player_name) >= 0 ){
			//ADD player to red
			team_red.pop(player_name);
			$(".team-container-red").find('span').text(team_red.length);
		}
	}
	else if(team_color == "BLUE"){
		//Check if player exists
		if( team_blue.indexOf(player_name) >= 0 ){
			//ADD player to blue
			team_blue.pop(player_name);
			$(".team-container-blue").find('span').text(team_blue.length);
		}
	}
	else{
		alert("Team color not found");
		return false;
	}
	
	if(team_red.length + team_blue.length != MAX_PLAYERS_PER_TEAM*2){
		$(".team-controls").hide();
	}
	
	return true;
}




function revertDraggable($selector) {
    $selector.each(function() {
        var $this = $(this),
            position = $this.data("originalPosition");

        if (position) {
            $this.animate({
                left: position.left,
                top: position.top
            }, 500, function() {
                $this.data("originalPosition", null);
            });
        }
    });
}


function startGame(){
	if(team_red.length != MAX_PLAYERS_PER_TEAM ||  team_blue.length != MAX_PLAYERS_PER_TEAM){
		alert("Player numbers incorrect");
	}
	else{
		 var params = {"points_to_win" : 50, 
				 	   "red_team" :  JSON.stringify(team_red),
				 	   "blue_team" : JSON.stringify(team_blue)};
		 
		makeAPICall('POST', "PointsTracker" , 'createGame', params, function(response){
			if(response.success){
				alert("success");
				window.location = "./pageGame?game_id=" + response.game_id;
			}
			else{
				alert("Failed to create new game. Error: " + response.message);
			}
		});
	}
}























