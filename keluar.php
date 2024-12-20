<?php
session_start();
$_SESSION = [];
session_unset();
session_destroy();
echo "<script>alert('Anda telah keluar!'); location='index.php'; </script>";
?>