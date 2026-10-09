<?php
// this page is called by connections.php
// where users can confirm/deny/remove a connection
require "../includes/db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $connection_action = $_GET['action'];
    $user_id = $_GET['user_id'];
    $sql = "";
    if ( $connection_action == 'create' ) {
        $sql = "INSERT INTO `eti_user_connection` (requestor_id, responder_id, confirmation_id) 
                VALUES ({$_GET['requestor_id']}, {$_GET['responder_id']}, 0)";
        $mysqlconn->query($sql);
    }
    elseif ( $connection_action == 'confirm' ) {
        $sql = "UPDATE `eti_user_connection` SET confirmation_id = 1
                WHERE id = {$_GET['id']}";
        $mysqlconn->query($sql);
    }
    elseif ( $connection_action == 'deny' ) {
        $sql = "UPDATE `eti_user_connection` SET confirmation_id = -1
                WHERE id = {$_GET['id']}";
        $mysqlconn->query($sql);
    }
    elseif ( $connection_action == 'remove' ) {
        $sql = "UPDATE `eti_user_connection` SET confirmation_id = -1
                WHERE id = {$_GET['id']}";
        $mysqlconn->query($sql);
    }
    else {
        $sql = "no action defined.";
    }
    header("location: connections.php?user_id={$user_id}");
}
?>