<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>LiveUser Example 5</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css" media="screen">@import "layout_frontend.css";</style>
</head>

<body>

<div class="content">
    <h1>LiveUser Example 5</h1>
    <p>Welcome !</p>
    <p>This simple two pages example lets you edit the news of this site. <br />
    Its aim is mostly to show how can LiveUser be used in real life application.</p>
    <p>Below are sample accounts you can use to play around</p>
    <table>
        <thead>
        <tr>
            <th></th>
            <th>Username</th>
            <th>Password</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Admin who can modify news</td>
            <td>johndoe</td>
            <td>johndoe</td>
        </tr>
        <tr>
            <td>Guest who can go to the admin page but cannot modify anything</td>
            <td>guest</td>
            <td>guest</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="content">

    <!-- Displaying the news themselves -->

    <h2>News:</h2>
        {GENERALNEWS}
    </div>

    <div id="navAlpha">
    <ul>
        <li><a href="">Tutorials</a></li>
        <li><a href="">API docs</a></li>
    </ul>
    </div>

    <div id="navBeta">
        {LOGIN}
    </div>

</div>

<!-- BlueRobot was here. -->
<!-- This code and css were shamelessly taken from bluerobot.com :) -->

</body>
</html>