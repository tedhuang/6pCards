{include file="findInclude:common/templates/header.tpl" scalable=0 customHeader=""}

<ul class="results leaderboard">
	{foreach $player_score as $player_name => $score_data}
	<li>
		<div class="score-background"></div>
		<div class="win-ratio">
			{round($score_data.score*100)/100}
		</div>
		<img class="display-picture" src="http://www.gravatar.com/avatar/{md5(strtolower(trim({$score_data.gravatar_email})))}?s=45&d=mm" width=45/>
		<div class="stat-text">{$player_name}</div>
		<div class="clear"></div>
	</li>
	{/foreach}
<!--
	{foreach $player_stats as $player_name => $stats}
	<li>
		<div class="score-background"></div>
		<div class="win-ratio">
			{round($stats.win_ratio*100)}%
		</div>
		<img class="display-picture" src="http://www.gravatar.com/avatar/{md5(strtolower(trim({$stats.gravatar_email})))}?s=45&d=mm" width=45/>
		<div class="stat-text">{$player_name} won {$stats.games_won} / {$stats.games_played} games</div>
		<div class="clear"></div>
	</li>
	{/foreach}
	-->
</ul>
