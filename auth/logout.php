<?php
session_start();
require '../database/config.php';
logout();
header('Location: /stride/index.php');   // ← fixed
exit;