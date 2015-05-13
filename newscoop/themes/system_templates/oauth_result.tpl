{{ dynamic }}
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newscoop OAuth Authentication Result</title>

    <!-- Bootstrap core CSS -->
    <link href="/themes/system_templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/system_templates/css/main.css" rel="stylesheet">
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
            margin-top: 60px;
        }
    </style>
  </head>

  <body>
    <div id="container">
        <center>
        <img src="/themes/system_templates/img/newscoop_logo_big.png" />
        <h1 class="form-signin-heading text-muted"><b>Authentication finished.</b></h1>
        <p>Check result in this page url (with javascript) and continue with returned data</p>
        </center>
    </div>
    <script type="text/javascript">
        var tokenRegex = new RegExp('access_token=(\\w+)'),
            matches = tokenRegex.exec(window.location.hash),
            token;

        token = matches[1];
        console.log('Your access_token is: ' + token);
        if (token) {
            console.log('Saving access_token in sessionStorage');
            sessionStorage.setItem('newscoop.token', token);
        }
    </script>
  </body>
</html>
{{ /dynamic }}