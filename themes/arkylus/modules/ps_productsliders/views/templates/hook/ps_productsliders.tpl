<section class="featured-products productsliders clearfix">
  {* <h2 class="h2 products-section-title text-uppercase">
    {l s='%s - Products' sprintf=$category->name d='Shop.Theme.Catalog'}
  </h2>
  *}
  <div class="products owl-carousel owl-theme">
    {foreach from=$products item="product"}
      <div class="item">
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
      </div>
    {/foreach}
  </div>  
</section>

