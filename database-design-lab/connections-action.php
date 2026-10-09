<?php
// this page is called by connections.php
// where users can confirm/deny/remove a connection
require "../includes/db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $connection_action = $_GET['action'];
    $user_id = $_GET['user_id'];
    $connection_id = intval($_GET['id']);
    $sql = "";
    if ( $connection_action == 'confirm' ) {

        // F1 
        // WRITE SQL TO CONFIRM A CONNECTION

        $user_confirm = "UPDATE `eti_user_connection` 
                SET `confirmation_id` = 1, `response_date` = NOW() 
                WHERE `id` = {$connection_id}";
        $mysqlconn->query($user_confirm);

    }
    elseif ( $connection_action == 'deny' ) {

        // F2 
        // WRITE SQL TO DENY A CONNECTION

        $user_deny = "UPDATE `eti_user_connection` 
            SET `confirmation_id` = -1, `response_date` = NOW() 
            WHERE `id` = {$connection_id}";
        $mysqlconn->query($user_deny);

    }
    elseif ( $connection_action == 'remove' ) {
        
        // F3 
        // WRITE SQL TO REMOVE AN CONNECTION

        $user_remove = "UPDATE `eti_user_connection` 
            SET `confirmation_id` = -1, `response_date` = NOW() 
            WHERE `id` = {$connection_id}";
        $mysqlconn->query($user_remove);

    }
    else {
        $sql = "no action defined.";
    }
    header("location: connections.php?user_id={$user_id}");
}
?>