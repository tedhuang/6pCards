{include file="findInclude:common/templates/header.tpl" scalable=0}


<div id="chart">
  <svg></svg>
</div>
<div class="show-chart">Show Graph</div>

<ul class="results leaderboard">
	{foreach $player_stats as $player_name => $stats}
	<li>
		<div class="front">
			<div class="score-background"></div>
			<div class="player-score">
				{round($stats.score*100)/100}
			</div>
			<img class="display-picture" src="http://www.gravatar.com/avatar/{md5(strtolower(trim({$stats.gravatar_email})))}?s=120&d=mm" width=60/>
			<div class="stat-text">{$player_name} won {$stats.games_won} / {$stats.games_played} games - {round($stats.win_ratio*100)}%</div>
			<div class="clear"></div>
		</div>
		<div class="back" style="display:none;">
		<div class="stat-details">
			<table>
				<tr>
					<td><i class="icon-bolt"></i></td>
					<td>Nemsis : {$stats.nemesis.name}</td>
					<td>{$stats.nemesis.count}</td>
				</tr>
				<tr>
					<td><i class="icon-thumbs-down-alt"></i></td>
					<td>Bad luck charm : {$stats.bad_luck_charm.name}</td>
					<td>{$stats.bad_luck_charm.count}</td>
				</tr>
				<tr>
					<td><i class="icon-heart-empty"></i></td>
					<td> Best teammate : {$stats.best_teammate.name}</td>
					<td>{$stats.best_teammate.count}</td>
				</tr>
			</table>
		</div>
		</div>
	</li>
	{/foreach}
</ul>