{include file="findInclude:common/templates/header.tpl"  scalable=0}


<div id="score_graph"></div>

<div class="game-stats content-container">	
	<div class="stats">Number of rounds: {$scores|count}</div>
	<div class="stats">Average round duration: 
					 {if $avg_round_duration/1000 < 60}
					 	{$avg_round_duration/1000} sec
					{else}
						{round(($avg_round_duration/1000)/60)} min
					{/if}
	</div>
	<div class="stats">Longest round duration: 
					 {if $max_round_duration/1000 < 60}
					 	{$max_round_duration/1000} sec
					{else}
						{round(($max_round_duration/1000)/60)} min
					{/if}
	</div>
	
	{$game_duration = strtotime($game.complete_time) - strtotime($game.timestamp)}
	<div class="stats">
		{$game_duration}
		Total Game Duration: 
		{if $game_duration < 60}
			{$game_duration} sec 
		{else}
			{round($game_duration/60)} min {$game_duration%60} sec
		{/if}
	</div>
</div>