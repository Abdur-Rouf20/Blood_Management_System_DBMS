<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flash message system
function set_flash($msg, $type = 'success') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function show_flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $msg  = $_SESSION['flash']['msg'];
        echo "<div class='alert alert-$type text-center'>$msg</div>";
        unset($_SESSION['flash']);
    }
}
?>
