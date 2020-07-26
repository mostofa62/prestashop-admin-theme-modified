{*$banner_url|@var_dump*}


{if isset($child_banner)}
<div class="banner-holder">
{if $single_banner['is_show_title'] > 0}
<div class="mb-1">
	<h2 class="h2">{$single_banner['title']}</h2>
	<div class="banner-holder-underline">&nbsp;</div>
</div>
{/if}
<div class="banners">
{if isset($child_banner)}
{foreach from=$child_banner item=$cbs}
<div class="row">
	{foreach from=$cbs item=$cb}
		{if $cb['is_show_title'] > 0}
		<div class="{$space_class}">

			<figure class="figure">
			  <a href="{$cb['home_url']}" class="thumbnail">
			  	<img src="{$banner_url}{$cb['image_name']}" class="figure-img img-fluid rounded" alt="{$cb['description']}">
			  </a>
			  <figcaption class="figure-caption">
			  	<p class="title">
		                      	<a href="{$cb['home_url']}">{$cb['title']}</a>
		                      </p>
		        <p class="description">
		                      	<a href="{$cb['home_url']}">{$cb['description']}</a>
		                      </p>
			  </figcaption>
			</figure>
			
    	</div>

		{else}
		<div class="{$space_class} px-1">
			<a class="banner" href="{$cb['home_url']}">
				<img src="{$banner_url}{$cb['image_name']}" alt="{$cb['title']}" title="{$cb['title']}" class="img-fluid">
			</a>
		</div>
		{/if}
	{/foreach}
	
</div>
{/foreach}
{/if}
</div>

</div>

{else}

<a class="banner" href="{$single_banner['home_url']}" title="">
      <img src="{$banner_url}{$single_banner['image_name']}" alt="{$single_banner['title']}" title="{$single_banner['title']}" class="img-fluid">
</a>

{*$single_banner['home_url']|@var_dump*}
{/if}