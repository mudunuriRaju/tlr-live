<?php

/**
 * Created by PhpStorm.
 * User: kesavam
 * Date: 15/5/15
 * Time: 12:05 PM
 */
?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html;charset=ISO-8859-I'>
    <title>Paytm</title>
    <script type='text/javascript'>
        function response() {
            return document.getElementById('response').value;
        }
    </script>
</head>
<body>
Redirect back to the app<br>

<form name='frm' method='post'>
    <input type='hidden' id='response' name='responseField' value='<?= $model ?>'>
</form>
</body>
</html>