{{extends file="layout.tpl"}}

{{block title}}{{ #registerYourself# }}{{/block}}

{{block content}}

{{ assign var="userindex" value=1 }}

<h3>{{ #confirmationSent# }}</h3>

<div class="alert alert-info">
    <p>{{ #followEmailSteps# }}</p>
    <p>{{ #thanksForRegistering# }}</p>
</div>

{{/block}}
