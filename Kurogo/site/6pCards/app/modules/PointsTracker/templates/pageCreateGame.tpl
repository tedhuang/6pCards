{include file="findInclude:common/templates/header.tpl" scalable=0}

<div class="new-game-container">
	
	<div class="game-details-container">
	
		<div class="players-selection-container">
		
			<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-accordion-header-active ui-state-active ui-corner-top ui-accordion-icons">Select Players</h3>

				<div class="players-container droppable ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" title="{$player.player_name}">
				{foreach from=$players item=player}
					<div class="player-container" title="{$player.player_name}">
						<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=60&d=mm"/>
						<span>{$player.player_name}</span>
						<img class="checkbox" src="./modules/PointsTracker/images/checkbox.png"/>
					</div>
				{foreachelse}
					No players loaded.
				{/foreach}
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="players-controls clear">
				<button class="players-submit">Done</button>
				<button class="players-reset">Reset</button>
			</div>
		</div>
		
		
		<div class="team-selection-container">
			
			<h3>Select Teams</h3>
			
			<div class="team-selection-control">
				<button class="team-reset">Reset</button>
				<button class="team-selection-randomize">Randomize</button>
			</div>
			
			<div class="team-container">
				<div class="team-container-red">
					<div class="player-num-red"><span>0</span>/3 players</div>
				</div>
				<div class="team-container-blue">
					<div class="player-num-blue"><span>0</span>/3 players</div>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="team-controls focal">
				<button class="team-submit">Done</button>
				<div class="team-summary"></div>
			</div>
		</div>
	</div>
</div>
