{{ extends file="layout.tpl" }}

{{ block content }}
{{ assign var="userindex" value=1 }}
<header>
	<h3>User account</h3>
</header>
<div class="alert alert-info">
    <h5 class="checkHeading">We've sent you an e-mail.</h5>
    <p>Please check your inbox and click on the link in the email to reset your password.</p>
</div>

{{ /block }}
