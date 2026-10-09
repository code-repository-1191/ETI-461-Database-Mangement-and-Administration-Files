<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_GET['user_id'];
    $comment_post_id = $_POST['comment_post_id'];
    $post = $mysqlconn->real_escape_string($_POST['post']);
    $insert_sql = "INSERT INTO `eti_user_post` (`comment_post_id`, `poster_id`, `post`) VALUES ({$comment_post_id}, {$user_id}, '{$post}')";
    //echo "$insert_sql<br>";
    $mysqlconn->query($insert_sql);
    header("location: timeline.php?user_id={$user_id}");  
}
?>