{include file="findInclude:common/templates/header.tpl" scalable=0}


<div class="active-games-container">
	{if $activeGames|count == 0}
		<div>No active games</div>
	{else}
		<ul class="nav active-games-list">
		{foreach from=$activeGames item=game}
			<li class="active-game">
				<a href="./pageGame?game_id={$game.game_id}">
					<div class="vs-container">
						{foreach from=explode('|', $game.team_red) item=player_name}
							{$player_name} 
						{/foreach}
						vs
						{foreach from=explode('|', $game.team_blue) item=player_name}
							{$player_name} 
						{/foreach}
					</div>
					<span class="timestamp">{$game.timestamp}</span>
					<div class="clear"></div>
				</a>
			</li>
		{/foreach}
		</ul>
	{/if}

</div>
