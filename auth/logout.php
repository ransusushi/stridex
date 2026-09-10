<?php
session_start();
session_destroy();
header('Location: /stride/auth/login.php');
exit;  