<html>
    <head>
    <style>
        body {
            margin: 0px;
            padding: 0px;
            font: 12px Courier New, Sans;
        }
    </style>
    <script>
        function toggleErrorWindow() {
            var button = document.getElementById('error_frame');
            if (button.value == 'show') {
                parent.document.getElementById('frameset').rows = "*, 200px";
                button.value = 'hide';
            }
            else {
                parent.document.getElementById('frameset').rows = "*, 30px";
                button.value = 'show';
            }
        }
    </script>
    </head>
    <body>
        Parse errors: <span id="error_count"></span>
        <input id="error_frame" type="button" value="show" onClick="toggleErrorWindow();"><br>
        <div id="error_list"></div>
    </body>
</html>
