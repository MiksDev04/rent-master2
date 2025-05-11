<?php
session_start();
session_unset();
session_destroy();
header("Location: /rent-master2/client/"); // or /rent-master2/index.php
exit();
 