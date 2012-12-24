{include file="findInclude:common/templates/header.tpl" scalable=0}


<div class="active-games-container">

	<h3>Active Games</h3>
	{if $activeGames|count == 0}
		<h3>No active games</h3>
	{else}
		<ul class="nav active-games-list">
		{foreach from=$activeGames item=game}
			<li class="game">
				<a href="./pageGame?game_id={$game.game_id}">

					<table class="vs-container">
						<tr>
							<td><div class="score">{$game.score_red}</div></td>
							<td></td>
							<td><div class="score">{$game.score_blue}</div></td>
						</tr>
						<tr>
							<td>
							{foreach from=explode('|', $game.team_red) item=player_name}
								{$player_name}
							{/foreach}
							</td>
							<td class="vs-text">vs</td>
							<td>
							{foreach from=explode('|', $game.team_blue) item=player_name}
								{$player_name}
							{/foreach}
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<span class="timestamp">Started on {date( 'Y-m-d h:iA',strtotime($game.timestamp))}</span>
					<div class="clear"></div>
				</a>
			</li>
		{/foreach}
		</ul>
	{/if}

	
	<h3>Completed Games</h3>
	
	{if $inactiveGames|count == 0 || $inactiveGames[0]['game_id']==null}
		<div class="response-message">No completed games</div>
	{else}
		<ul class="nav inactive-games-list">
		{foreach from=$inactiveGames item=game}
			<li class="game">
				<a href="./pageGame?game_id={$game.game_id}">

					<table class="vs-container">
						<tr>
							<td><div class="score {if $game.score_red > $game.score.blue}WINNER{/if}">{$game.score_red}</div></td>
							<td></td>
							<td><div class="score {if $game.score_red < $game.score.blue}WINNER{/if}">{$game.score_blue}</div></td>
						</tr>
						<tr>
							<td>
							{foreach from=explode('|', $game.team_red) item=player_name}
								{$player_name}
							{/foreach}
							</td>
							<td class="vs-text">vs</td>
							<td>
							{foreach from=explode('|', $game.team_blue) item=player_name}
								{$player_name}
							{/foreach}
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<span class="timestamp">Started on {date( 'Y-m-d h:iA',strtotime($game.timestamp))}</span>
					<div class="clear"></div>
				</a>
			</li>
		{/foreach}
		</ul>
	{/if}
</div>
