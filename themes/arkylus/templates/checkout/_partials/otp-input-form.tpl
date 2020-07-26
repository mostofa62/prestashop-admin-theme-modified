{block name='otp_input_form'}

{$mobile_no|@var_dump}

  {block name='otp_input_form_errors'}
    {include file='_partials/form-errors.tpl' errors=$errors['']}
  {/block}

  <form id="otp-input-form" action="{block name='otp_input_form_actionurl'}{$action}{/block}" method="post">

    <section>
      {block name='otp_input_form_fields'}
        {foreach from=$formFields item="field"}
          {block name='form_field'}
            {form_field field=$field}
          {/block}
        {/foreach}
      {/block}      
    </section>


{block name='form_buttons'}
  <button
    class="continue btn btn-primary float-xs-right"
    name="continue"
    data-link-action="sign-in"
    type="submit"
    value="1"
  >
    {l s='Continue' d='Shop.Theme.Actions'}
  </button>
{/block}
    
  </form>
{/block}


