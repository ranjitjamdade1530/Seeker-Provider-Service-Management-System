<?php
session_start();
session_destroy();
header("Location: provider_login.php");
exit();
