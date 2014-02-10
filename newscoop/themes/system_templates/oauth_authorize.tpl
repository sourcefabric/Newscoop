<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title></title>

    <!-- Bootstrap core CSS -->
    <link href="/themes/system_templates/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css" media="screen">
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #eee;
        }
        #container {
            max-width: 320px;
            padding: 15px;
            margin: 0 auto;
        }
    </style>
  </head>

  <body>
    <div id="container">
        <form class="form-signin" action="{{ generate_url route="fos_oauth_server_authorize" parameters=[
            "client_id" => {{ get_request_param name="client_id" default="" }},
            "response_type" => {{ get_request_param name="response_type" default="" }},
            "redirect_uri" => {{ get_request_param name="redirect_uri" default="" }},
            "state" => {{ get_request_param name="state" default="" }},
            "scope" => {{ get_request_param name="scope" default="" }}
        ] absolute=true}}" method="post">
            <img src="/themes/system_templates/img/newscoop_logo_big.png" />
            <h1 class="form-signin-heading text-muted" style="text-align: center; margin-bottom: 20px;">Grant access to: <br />"{{ $client->getName() }}"</h1>
            <p>Allow to access instance rest api resources like: read/write/delete articles, images, comments and more...</p>

            <input type="hidden" name="newscoop_gimme_oauth_authorize[allowAccess]" value="true" />


            <button class="btn pull-left btn-danger">
                Cancel
            </button>
            <button class="btn pull-right btn-primary" type="submit">
                Allow Access
            </button>
        </form>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="/themes/system_templates/js/bootstrap.min.js"></script>
  </body>
</html>
