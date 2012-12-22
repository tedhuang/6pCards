<div id="dashboard">

	<h1>{$title}</h1>

	<ul id='dashboard-item-list'>
	{foreach from=$dashboardItems item=item}	
	<a href="{$item.url}">
	<li class="dashboard-{if $item.featured}featured{else}item{/if}">
				<img src="{$item.image}"/>
				{$item.title}
	</li></a>		
	{/foreach}
	</ul>
</div>
