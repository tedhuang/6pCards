{include file="findInclude:common/templates/header.tpl" scalable=0}


<div class="active-games-container">
	{if $activeGames|count == 0}
		<div>No active games</div>
	{else}
		<ul class="nav active-games-list">
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
