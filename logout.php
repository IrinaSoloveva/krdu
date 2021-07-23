<?
session_start();
session_unset();
session_destroy();
header('HTTP/1.1 302 Moved Temporarily');
header('Location: /');
exit;
?>