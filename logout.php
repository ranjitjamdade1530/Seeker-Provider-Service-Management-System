<?php
session_start();
session_destroy();
header("Location: sicker_login.php");
?>
