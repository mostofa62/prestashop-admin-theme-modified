
<section class="featured-products clearfix">
  <h2 class="h2 products-section-title text-uppercase">
    {l s='Top %d discount product' d='Shop.Theme.Catalog' sprintf=[$limit]}
  </h2>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
    {/foreach}
  </div>
  
</section>
