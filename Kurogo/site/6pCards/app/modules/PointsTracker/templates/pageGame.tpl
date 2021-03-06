{include file="findInclude:common/templates/header.tpl"  scalable=0}

<div class="game-details-container content-container">
	<div>
		<div class="game-title">Game {$game.game_id}</div>
		<div class="status">{$game.status}</div>
		<div class="pts_win">{$game.points_to_win} points to win</div>
		<div class="timestamp">Started on {date( 'Y-m-d h:iA',strtotime($game.timestamp))}</div>
	</div>
	<div class="clear"></div>
</div>

<div class="score-container">
	<table class="score-table content-container" {if $score_record|count == 0}style="display:none;"{/if}>
		<tr class="heading-row">
			<th></th>
			<th>{foreach from=$game.t_red item=player}{$player.player_name} {/foreach}</th>
			<th>{foreach from=$game.t_blue item=player}{$player.player_name} {/foreach}</th>
		</tr>
		{foreach from=$score_record item=score}
		<tr>
			<td class="round_num">{$score.count}</td>
			<td>{$score.score_red_team}</td>
			<td>{$score.score_blue_team}</td>
			<td><i class="delete_score icon-remove-sign" title="{$score.score_id}"></i></td>
		</tr>
		{/foreach}
	</table>
	<div class="score-control content-container">
		{if $game.status!="COMPLETE"}
		<div class="score-control-message">Select 1st place</div>
		{/if}
		<div class="players-container">
			{foreach from=$game.t_red item=player}
				<div class="player-container T_RED" title="{$player.player_name}">
					<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=200&d=mm" width=80/>
					<span>{$player.player_name}</span>
					<div class="placement">1</div>
				</div>
			{/foreach}
			{foreach from=$game.t_blue item=player}
				<div class="player-container T_BLUE" title="{$player.player_name}">
					<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=200&d=mm" width=80/>
					<span>{$player.player_name}</span>
					<div class="placement">6</div>
				</div>
			{/foreach}
		</div>
		<div class="clear"></div>
	</div>

	<div class="end-btns">
		<!-- <button class="goIndex leftButton">Go Home</button> -->
		{if $game.status=="COMPLETE"}
			<button class="fullButton" onclick="window.location='./pageScoreboard?game_id={$game.game_id}'">View Summary</button>
		{else}
			<button class="score-submit fullButton">Submit Score</button>
		{/if}
	</div>
</div>
{*include file="findInclude:common/templates/footer.tpl"*}





