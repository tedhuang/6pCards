var MAX_PLAYERS_PER_TEAM = 3;

var players_selected = new Array();
var player_selection_locked = false;

var team_red = new Array();
var team_blue = new Array();


$(document).ready(function(){
	
	$(".players-submit").click(function(){
		if(players_selected.length >MAX_PLAYERS_PER_TEAM*2 ){
			var player_count = 0;
			var shuffled_players = shuffle(players_selected);

			players_selected = [];
			
			for(i=0; i<MAX_PLAYERS_PER_TEAM*2 ; i++){
				players_selected.push(shuffled_players[i]);
			}
			
			$(".player-container.SELECTED").each(function(){
				$(this).removeClass("SELECTED");
				$(this).find(".checkbox").hide();
			});
			
			for(i=0; i<MAX_PLAYERS_PER_TEAM*2 ; i++){
				$(".player-container").each(function(){
					if($(this).attr('title') == players_selected[i] ){
						$(this).addClass("SELECTED");
					}
				});				
			}
			
			console.log(players_selected);
		}

		
		startTeamSelection();
	});	
	
	$(".players-reset").click(function(){
		players_selected = new Array();
		$(".player-container.SELECTED").each(function(){
			$(this).removeClass("SELECTED");
			$(this).find(".checkbox").hide();
		});
	});
	
	$(".use-same-players").click(function(){
		$(this).html("<div class='loadImg'><img src='./common/images/loader.gif' width=30 /></div>");
		usePlayersFromLastGame();
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
				
				$(this).addClass("SELECTED");
				$(this).find('.checkbox').show();
				players_selected.push( $(this).attr("title") );
				
				if(players_selected.length == MAX_PLAYERS_PER_TEAM*2 ){
					$(".players-controls").show();
				}
				
				
//				if(players_selected.length != MAX_PLAYERS_PER_TEAM*2){
//					$(this).addClass("SELECTED");
//					$(this).find('.checkbox').show();
//					players_selected.push( $(this).attr("title") );
//					
//					if(players_selected.length == MAX_PLAYERS_PER_TEAM*2 ){
//						$(".players-controls").show();
//					}
//				}
//				else{
//
//					
////					alert("Max players reached");
//				}
			}
		}
	});	

});

function shuffle(array) {
    var tmp, current, top = array.length;

    if(top) while(--top) {
    	current = Math.floor(Math.random() * (top + 1));
    	tmp = array[current];
    	array[current] = array[top];
    	array[top] = tmp;
    }

    return array;
}


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
	$(".message").html("Select Team");
	
	$(".team-selection-randomize").click(function(){
		clearPlayers();
		$(".players-selection-container").hide();
		
		var rand_team = players_selected;
		
		rand_team.sort(function(){
			return (Math.round(Math.random())-0.5);
		});
	
		for(i=0; i<rand_team.length; i++){
			avatar = $(".SELECTED.player-container[title='"+rand_team[i]+"']").find('img').attr('src');			
			player_container = '<div class="player-container"><img src="'+avatar+'" width=60/><span>'+rand_team[i]+'</span></div>';
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
		
		$('.player-num-red').find("span").html(team_red.length);
		$('.player-num-blue').find("span").html(team_blue.length);
	});
	
	$(".team-submit").click(function(){
		$(this).html("<div class='loadImg'><img src='./common/images/loader.gif' width=30 /></div>");
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
	$('.player-num-red').find("span").text(team_red.length);
	$('.player-num-blue').find("span").text(team_blue.length);
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
				$('.player-num-red').find("span").text(team_red.length);
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
				$('.player-num-blue').find("span").text(team_blue.length);
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

function usePlayersFromLastGame(){
	
	$(".players-reset").click();
	
	makeAPICall('GET', "PointsTracker" , 'getPlayersFromLastGame', null, function(response){
		if(response.success){
			$(".use-same-players").hide();
			for(var i=0; i < response.players.length; i++){
				players_selected.push(response.players[i]);
			}
			
			$(".player-container").each(function(){
				if( $.inArray( $(this).attr('title'), players_selected ) != -1){
					$(this).addClass("SELECTED");
				}
			});
			
			startTeamSelection();
		}
		else{
			alert("Failed to get players from last game. Error: " + response.message);
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
				$(this).html("Submit")
				window.location = "./pageGame?game_id=" + response.game_id;
			}
			else{
				alert("Failed to create new game. Error: " + response.message);
			}
		});
	}
}























