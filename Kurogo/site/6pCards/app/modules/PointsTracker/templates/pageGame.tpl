{include file="findInclude:common/templates/header.tpl"  scalable=0}

<div class="game-details-container focal">
	<div>GAME ID: {$game.game_id}</div>
	<div>Status: {$game.status}</div>
	<div>Points to win: {$game.points_to_win}</div>
	<div>Time started: {$game.timestamp}</div>
	<!--<div>Tags: {$game.tag}</div> -->
</div>
<hr />
<div class="score-container">
	<table class="score-table">
		<tr class="heading-row">
			<th>Round</th>
			<th>{foreach from=$game.t_red item=player}{$player.player_name} {/foreach}</th>
			<th>{foreach from=$game.t_blue item=player}{$player.player_name} {/foreach}</th>
		</tr>
		{foreach from=$score_record item=score}
		<tr>
			<td class="round_num">{$score.count}</td>
			<td>{$score.score_red_team}</td>
			<td>{$score.score_blue_team}</td>
			<td><img class="delete_score" src="./common/images/critical.png" /></td>
		</tr>
		{/foreach}
	</table>
	
	<hr />
	
	<div class="score-control">
	
		<div class="score-control-message"></div>
		
		<div class="players-container">
		{foreach from=$game.t_red item=player}
			<div class="player-container T_RED" title="{$player.player_name}">
				<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=40&d=mm"/>
				<span>{$player.player_name}</span>
				<div class="placement">1</div>
			</div>
		{/foreach}
		{foreach from=$game.t_blue item=player}
			<div class="player-container T_BLUE" title="{$player.player_name}">
				<img src="http://www.gravatar.com/avatar/{md5(strtolower(trim($player.gravatar_email)))}?s=40&d=mm"/>
				<span>{$player.player_name}</span>
				<div class="placement">6</div>
			</div>
		{/foreach}
		</div>
	</div>
	<div class="clear"></div>
	<div class="score-control">
		<button class="score-submit">Submit</button>
	</div>
</div>


<button class="goIndex">Go Home</button>


{*include file="findInclude:common/templates/footer.tpl"*}





