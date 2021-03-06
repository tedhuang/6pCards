{include file="findInclude:common/templates/header.tpl" scalable=0}


<div class="active-games-container">

	<div class="heading">Active Games</div>
	{if $activeGames|count == 0}
		<div class="response-message heading">No active games</div>
	{else}
		<ul class="nav active-games-list">
		{foreach from=$activeGames item=game}
			<li class="game">
				<a href="./pageGame?game_id={$game.game_id}">

					<table class="vs-container">
						<tr>
							<td><div class="score">{$game.score_red|default: "0"}</div></td>
							<td></td>
							<td><div class="score">{$game.score_blue|default: "0"}</div></td>
						</tr>
						<tr>
							<td class="players">
							{foreach from=explode('|', $game.team_red) item=player_name}
								{$player_name}
							{/foreach}
							</td>
							<td class="vs-text">vs</td>
							<td class="players">
							{foreach from=explode('|', $game.team_blue) item=player_name}
								{$player_name}
							{/foreach}
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<div class="timestamp">{date( 'Y-m-d h:iA',strtotime($game.timestamp))}</div>
					<div class="clear"></div>
				</a>
			</li>
		{/foreach}
		</ul>
	{/if}

	
	<div class="heading">Completed Games</div>
	
	{if $inactiveGames|count == 0 || $inactiveGames[0]['game_id']==null}
		<div class="response-message">No completed games</div>
	{else}
			<ul class="nav inactive-games-list">
		{foreach from=$inactiveGames item=game name=foo}
			{if $lastTimestamp != date('Y-m-d',strtotime($game.timestamp)) && $smarty.foreach.foo.index != 0}
			</ul>
			<ul class="nav inactive-games-list">
			{/if}
			<li class="game">
				<a href="./pageGame?game_id={$game.game_id}">

					<table class="vs-container">
						<tr>
							<td><div class="score {if $game.score_red > $game.score_blue}WINNER{/if}">{$game.score_red}</div></td>
							<td></td>
							<td><div class="score {if $game.score_red < $game.score_blue}WINNER{/if}">{$game.score_blue}</div></td>
						</tr>
						<tr>
							<td  class="players">
							{foreach from=explode('|', $game.team_red) item=player_name}
								{$player_name}
							{/foreach}
							</td>
							<td class="vs-text">vs</td>
							<td  class="players">
							{foreach from=explode('|', $game.team_blue) item=player_name}
								{$player_name}
							{/foreach}
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<div class="timestamp">{date( 'Y-m-d h:iA',strtotime($game.timestamp))}</div>
					<div class="clear"></div>
				</a>
			</li>
			{$lastTimestamp = date( 'Y-m-d',strtotime($game.timestamp))}
		{/foreach}
		</ul>
	{/if}
</div>
