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
        .fullscreen_bg {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-size: cover;
            background-position: 50% 50%;
        }
        .form-signin {
            max-width: 280px;
            padding: 15px;
            margin: 0 auto;
        }
        .form-signin .form-signin-heading, .form-signin {
            margin-bottom: 10px;
        }
        .form-signin .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-top-style: solid;
            border-right-style: solid;
            border-bottom-style: none;
            border-left-style: solid;
            border-color: #000;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-top-style: none;
            border-right-style: solid;
            border-bottom-style: solid;
            border-left-style: solid;
            border-color: #000;
        }
        .form-signin-heading {
            color: #fff;
            text-align: center;
            text-shadow: 0 2px 2px rgba(0,0,0,0.5);
        }

        .login_error {
            padding: 10px;
        }
        .forgot-password {margin-bottom: 10px;}
    </style>

  </head>

  <body>
    <div id="container">
        <form class="form-signin" action="{{ generate_url route="oauth_login_check" }}" method="post">
            <img src="/themes/system_templates/img/newscoop_logo_big.png" />
            <h1 class="form-signin-heading text-muted">Sign In to {{ $gimme->publication->name }}</h1>

            <div class="login_error" style="color: #ff2200;">
            {{ if $error }}
                {{ $error }}
            {{ /if }}
            </div>

            <input type="text" class="form-control" placeholder="Login" value="{{ $lastUsername }}" name="_username" required="" autofocus="">
            <input type="password" class="form-control" placeholder="Password" name="_password" required="">
            <input type="hidden" name="_target_path" value="{{ if $targetPath }}{{ $targetPath }}{{ else }}/oauth/v2/auth{{ /if }}" />
            <a class="forgot-password pull-left" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">Forgot password?</a>
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                Sign In
            </button>
        </form>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="/themes/system_templates/js/bootstrap.min.js"></script>
  </body>
</html>
