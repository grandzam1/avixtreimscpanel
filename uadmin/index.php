<?php
// Automatic 301 Permanent Redirect to new Admin Controller
$req_uri = $_SERVER['REQUEST_URI'] ?? '';
$target = preg_replace('/^\/?uadmin\/?/i', '/controller/', $req_uri);
if (empty($target) || $target === $req_uri) {
    $target = '../controller/index.php';
}
header("HTTP/1.1 301 Moved Permanently");
header("Location: " . $target);
exit();
?>