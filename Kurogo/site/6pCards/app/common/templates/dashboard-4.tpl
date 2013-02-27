<div id="dashboard">

	<ul id='dashboard-item-list'>
	{foreach from=$dashboardItems item=item}	
		<a href="{if $item.disabled}#{else}{$item.url}{/if}">
			<li class="dashboard-item" style="background: {$item.background}">
				<!--<img src="{$item.image}"/> -->
				{$item.title}
			</li>
		</a>		
	{/foreach}
	</ul>
</div>
