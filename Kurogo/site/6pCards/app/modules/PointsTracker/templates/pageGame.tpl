{include file="findInclude:common/templates/header.tpl"  scalable=0}

<div class="game-details-container focal">
	<div>GAME ID: {$game.game_id}</div>
	<div>Status: {$game.status}</div>
	<div>Points to win: {$game.points_to_win}</div>
	<div>Time started: {$game.timestamp}</div>
	<!--<div>Tags: {$game.tag}</div> -->
</div>


<div class="score-container focal">
	<table class="score-table">
		<tr>
			<th>{foreach from=$game.t_red item=player}{$player.player_name} {/foreach}</th>
			<th>{foreach from=$game.t_blue item=player}{$player.player_name} {/foreach}</th>
		</tr>
		<tr class="separator">
			<td></td><td></td>
		</tr>
		<tr class="score-control-row">
			<td>
				{foreach from=$game.t_red item=player}
					<div class="player-container" title="{$player.player_name}">
						<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=40&d=mm"/>
						<span>{$player.player_name}</span>
					</div>
				{/foreach}
			</td>
			<td>
				{foreach from=$game.t_blue item=player}
					<div class="player-container" title="{$player.player_name}">
						<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=40&d=mm"/>
						<span>{$player.player_name}</span>
					</div>
				{/foreach}
			</td>
		</tr>
	</table>
	
	
</div>






{include file="findInclude:common/templates/footer.tpl"}