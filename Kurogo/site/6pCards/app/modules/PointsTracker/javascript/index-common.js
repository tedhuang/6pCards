var MAX_PLAYERS_PER_TEAM = 3;

var team_red = new Array();
var team_blue = new Array();

$(document).ready(function(){
	
	$(".players-submit").click(function(){
		
	});	
	
	$(".players-submit").click(function(){
		
	});	
	
	$(".team-submit").click(function(){
		
	});	
	
	$(".team-reset").click(function(){
		revertDraggable($(".player-container"));
		$(".team-controls").hide();
		team_red = new Array();
		team_blue = new Array();
		$(".team-container-red").find('span').text(team_red.length);
		$(".team-container-blue").find('span').text(team_blue.length);
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
    
});



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
				$(".team-container-red").find('span').text(team_red.length);
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
				$(".team-container-blue").find('span').text(team_blue.length);
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




