<style type="text/css">
  .hide{
    display: none;
  }
</style>

<script type="text/javascript">
  
  var url = "{$link->getModuleLink('ps_otp', 'otp', array())}";
  
</script>

{block name='otp_form'}

  {block name='otp_form_errors'}
    {include file='_partials/form-errors.tpl' errors=$errors['']}
  {/block}
  {* {$show_otp_input|@var_dump} *}
  <form id="otp-form" action="{block name='otp_form_actionurl'}{$action}{/block}" method="post">

    <section>
      {block name='otp_form_fields'}
        {foreach from=$formFields item="field"}
          {block name='form_field'}

          <div id="{$field.name}" class="{if $show_otp_input && $field.name == 'mobile_no'} hide {/if} form-group row {if isset($field.availableValues.class) && !$show_otp_input} {$field.availableValues.class} {/if}">
            <label class="col-md-3 form-control-label {if $field.required} required {/if}">
                {$field.label}
            </label>
            <div class="col-md-6">
                  
                    
                    <input class="form-control" name="{$field.name}" type="{$field.type}" value="{$field.value}" {if $field.required}required{/if}>

                              
                               
            </div>

            <div class="col-md-3 form-control-comment">
            {if !$field.required}OPTIONAL{/if}            
            </div>
          </div>


            {* {form_field field=$field} *}
            
          {/block}
        {/foreach}
      {/block}      
    </section>

    {block name='otp_form_footer'}
      <footer class="form-footer text-sm-center clearfix">
        <input type="hidden" name="submitLoginOtp" value="1">
        {block name='form_buttons'}
          <button id="submit-otp" class="btn btn-primary {if $show_otp_input} hide {/if}" data-link-action="submit-otp" type="button" class="form-control-submit">
            {l s='Login using Code' d='Shop.Theme.Actions'}
          </button>

          <button id="submit-otp-resend" class="btn btn-primary {if !$show_otp_input} hide {/if}" data-link-action="submit-otp-resend" type="button" class="form-control-submit">
            {l s='Resend Code' d='Shop.Theme.Actions'}
          </button>

          <button
            class="continue btn btn-primary float-xs-right {if !$show_otp_input} hide {/if}"
            name="continue"
            data-link-action="sign-in"
            type="submit"
            value="1"
          >
            {l s='Continue' d='Shop.Theme.Actions'}
          </button>
        {/block}
      </footer>

      
      
    {/block}

  </form>
{/block}

