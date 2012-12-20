{include file="findInclude:common/templates/header.tpl" scalable=0}

<div class="active-games-container">
	<h3>Active Games:</h3>
	{if $activeGames|count == 0}
		<div>No active games</div>
	{else}
		<ul class="results active-games-list">
		{foreach from=$activeGames item=game}
			<li class="active-game">
				
				<span class="timestamp">{$game.timestamp}</span>
			</li>
		{/foreach}
		</ul>
	{/if}

</div>

<div class="new-game-container">
	<button class="new-game-btn">New Game</button>
	
	<div class="game-details-container">
	
		<h3>Select Players</h3>
		
		<div class="players-container droppable" title="{$player.player_name}">
		{foreach from=$players item=player}
			<div class="player-container" title="{$player.player_name}">
				<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=60&d=mm"/>
				<span>{$player.player_name}</span>
			</div>
		{foreachelse}
			No players loaded.
		{/foreach}
			<div class="clear"></div>
		</div>
		
		<div class="players-controls clear">
			<button class="players-submit">Done</button>
			<button class="players-reset">Reset</button>
		</div>
		
		<div class="team-selection-control">
			<button>Randomize</button>
			<button>Manual</button>
		</div>
		
		<div class="team-container">
			<div class="team-container-red">
				<span>0</span>/3 players
			</div>
			<div class="team-container-blue">
				<span>0</span>/3 players
			</div>
			<div class="clear"></div>
		</div>
		
		<div class="team-controls focal">
			<button class="team-submit">Done</button>
			<button class="team-reset">Reset</button>
			<div class="team-summary"></div>
		</div>
		
		<button class="random-team-btn">Randomize Teams</button>
		
	</div>
</div>

{include file="findInclude:common/templates/footer.tpl"}





