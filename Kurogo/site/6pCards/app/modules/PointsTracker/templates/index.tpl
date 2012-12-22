{include file="findInclude:common/templates/header.tpl" scalable=0}

<div class="active-games-container">
	<h3>Active Games:</h3>
	{if $activeGames|count == 0}
		<div>No active games</div>
	{else}
		<ul class="results active-games-list">
		{foreach from=$activeGames item=game}
			<li class="active-game">
				<a href="./pageGame?game_id={$game.game_id}">
					
					<span class="timestamp">{$game.timestamp}</span>
				</a>
			</li>
		{/foreach}
		</ul>
	{/if}

</div>

<div class="new-game-container">
	<button class="new-game-btn">New Game</button>
	
	<div class="game-details-container">
	
		<div class="players-selection-container">
		
			<h3>Select Players</h3>
			
				<div class="players-container droppable" title="{$player.player_name}">
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






