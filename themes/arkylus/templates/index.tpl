
{extends file='page.tpl'}

    {block name='page_content_container'}
      <section id="content" class="page-home">
        {block name='page_content_top'}{/block}

        {block name='page_content'}
          {block name='hook_home'}
            {$HOOK_HOME nofilter}
          {/block}
          
          {widget name="ps_categoryproduct" category="155"}
          
          {widget name="ps_productsliders" category="155" sort="desc"}

          {widget name="ps_categoryproduct" category="157"}
          
          
          {* widget name="ps_bannermanager" id_banner="1" *}
          
          
          
          {* widget name="ps_bannermanager" id_banner="5" *}
          
          
          
          
          
          
          {widget name="ps_productsliders" category="147"}
        {/block}
        
      </section>
    {/block}
