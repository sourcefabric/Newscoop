{{ dynamic }}
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newscoop Oauth Authentication Result</title>

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
        }
    </style>
  </head>

  <body>
    <div id="container">
        <img src="/themes/system_templates/img/newscoop_logo_big.png" />
        <h1 class="form-signin-heading text-muted">Authentication is finished</h1>
        <p>Check result in this page url (with javascript) and continue with returned data</p>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="/themes/system_templates/js/jquery.cookie.js"></script>
    <script src="/themes/system_templates/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    function getHashParams() {
        var hashParams = {};
        var e,a = /\+/g,r = /([^&;=]+)=?([^&;]*)/g,d = function (s) { return decodeURIComponent(s.replace(a, " ")); },q = window.location.hash.substring(1);
        while (e = r.exec(q))hashParams[d(e[1])] = d(e[2]);
        return hashParams;
    }
    var hashParams = getHashParams();
    
    // check if authentication was succesfull and play with access_token
    if (jQuery.inArray("access_token", hashParams)) {
        console.log('Your access_token is: ' + hashParams.access_token);

        if ($.cookie('newscoop_access_token') == null || $.cookie('newscoop_access_token') != hashParams.access_token) {
            // create new cookie with access_token value
            console.log('Creating cookie with access_token value');
            var date = new Date();
            date.setTime(date.getTime() + (hashParams.expires_in * 1000));
            $.cookie('newscoop_access_token', hashParams.access_token, { expires: date, path: '/' });
        } else {
            console.log('You have valid access_token under "newscoop_access_token" cookie');
        }
    } else if (jQuery.inArray("error", hashParams)) {
        // there was an error on authentication process
        console.log('error:' + hashParams.error);
    }
    </script>
  </body>
</html>
{{ /dynamic }}