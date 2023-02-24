<?php
    $user = $_COOKIE['userid'];
    setcookie('userid', $user, time() - 3600);
    echo '<br>You have been logged out';
    echo "<br><a href='index.html'>project home page</a>";
    
?>