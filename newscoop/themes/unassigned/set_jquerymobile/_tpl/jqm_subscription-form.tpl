
{{ subscription_form type="by_publication" template="jqm_subscription.tpl" button_html_code="class=\"submitbutton\"" }}
<p>Please fill in the following form in order to create the subscription.</p>
<ul>
<li>Trial subscription: {{ $gimme->publication->subscription_trial_time }} {{ $gimme->publication->subscription_time_unit }}</li>
<li>Paid subscription: {{ $gimme->publication->subscription_paid_time }} {{ $gimme->publication->subscription_time_unit }}</li>
</ul>
<h4>Subscription costs:</h4>
<ul>
<li>{{ $gimme->publication->subscription_currency }} {{ $gimme->publication->subscription_unit_cost }} (access one language)</li>
<li>{{ $gimme->publication->subscription_currency }} {{ $gimme->publication->subscription_unit_cost_all_lang }} (access all languages)</li>
</ul>
    <div data-role="fieldcontain">
      <label for="subtype">Choose subscription:</label>
      <select name="SubsType" id="subtype">
        <option value="trial">Trial</option>
        <option value="paid">Paid</option>
      </select>
    </div>
    <div data-role="fieldcontain">
      <label for="langall">Subscribe to all languages</label>
      {{ camp_select object="subscription" attribute="alllanguages" html_code=" id=\"langall\"" }}
    </div>
    <div data-role="fieldcontain">
      <label for="langone">Or select one language:</label>
      {{ camp_select object="subscription" attribute="languages" html_code=" id=\"langone\"" }}
    </div>
{{ /subscription_form }}
