<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require "../includes/db_conn.php";

if ( isset($_GET['q']) || isset($_GET['term']) ) {
    
    //advanced search
    if ( $_GET['action'] == 'advanced' ) {
        $q = "";
        //term
        $term = $_GET['term'];
        //mustnot
        $mustnot = $_GET['mustnot'];
    
        // PART A 
        // PART A1
        if ( $_GET['boolean'] == 'must' ) {
            $query = "SELECT u.*, b.title, b.post 
                FROM `eti_user_blog` b, `eti_user` u  
                WHERE MATCH(b.title, b.post) AGAINST('+{$term}' IN BOOLEAN MODE)
                AND b.poster_id = u.id";
        }
        // PART A2
        elseif ( $_GET['boolean'] == 'phrase' ) {
            $query = "SELECT u.*, b.title, b.post 
                FROM `eti_user_blog` b, `eti_user` u  
                WHERE MATCH(b.title, b.post) AGAINST('\"{$term}\" IN BOOLEAN MODE')
                AND b.poster_id = u.id";
        }
        // PART A3
        elseif ( $_GET['boolean'] == 'wildcard' ) {
            $query = "SELECT u.*, b.title, b.post 
                FROM `eti_user_blog` b, `eti_user` u  
                WHERE MATCH(b.title, b.post) AGAINST('{$term}*' IN BOOLEAN MODE)
                AND b.poster_id = u.id";
        }
        // PART A4
        else {
            // PART A4i
            if ( trim(strlen($mustnot)) ) {
                $query = "SELECT u.*, b.title, b.post 
                    FROM `eti_user_blog` b, `eti_user` u  
                    WHERE MATCH(b.title, b.post) AGAINST('{$term} -{$mustnot}' IN BOOLEAN MODE)
                    AND b.poster_id = u.id";
            }
            // PART A4ii
            else {
                $query = "SELECT u.*, b.title, b.post 
                    FROM `eti_user_blog` b, `eti_user` u  
                    WHERE MATCH(b.title, b.post) AGAINST('{$term}')
                    AND b.poster_id = u.id";                                
            }
        }
        $result = $mysqlconn->query($query);
    }
    // basic search
    else {
        $q = $_GET['q'];
        $term = "";
        $mustnot = "";
        // PART B -- BASIC SEARCH
        $query = "SELECT u.*, b.title, b.post 
                    FROM `eti_user_blog` b, `eti_user` u  
                    WHERE MATCH(b.title, b.post) AGAINST('{$q}')
                    AND b.poster_id = u.id";
        $result = $mysqlconn->query($query);
    }
    //echo "<br><b>{$query}</b><br>";
    $result_count = $result->num_rows;
}
else {
    $result_count = 0;
    $q = "";
    $term = "";
    $mustnot = "";
}

?>
<html>
    <head>
        <title>ETI 461</title>
    </head>
    <body>
        <h1>ETI 461 - Transaction Management Lab</h1>
<div>
    <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
</div>
<br>
<div>
  <a href="timeline.php?user_id=<?php echo $user_id; ?>">Timeline</a> | 
  <a href="create-post.php?user_id=<?php echo $user_id; ?>">Create Post</a> | 
  <a href="create-message.php?user_id=<?php echo $user_id; ?>">Send Message</a> |
  <a href="create-connection.php?user_id=<?php echo $user_id; ?>">Create Connections</a> | 
  <a href="create-blog.php?user_id=<?php echo $user_id; ?>">Create Blog Entry</a><br>    
  <a href="connections.php?user_id=<?php echo $user_id; ?>">View My Connections</a> |
  <a href="posts.php?user_id=<?php echo $user_id; ?>">View My Posts</a> | 
  <a href="messages.php?user_id=<?php echo $user_id; ?>">View My Messages</a> | 
  <a href="blogs.php?user_id=<?php echo $user_id; ?>">View My Blog Entries</a>
</div>


<br><br>
<h3>Basic Search</h3>
<div>
    <form action="search.php" method="get">
        <input type="hidden" name="action" value="basic">
        <input type="text" name="q" value="<?php echo $q; ?>">
        <input type="submit" value="Search">
    </form>
</div>
<h3>Advanced Search</h3>
<div>
    <form action="search.php" method="get">
        <input type="hidden" name="action" value="advanced">
        Search term: <input type="text" name="term" value="<?php echo $term; ?>"> 
            <input type="radio" name="boolean" value="must"> Exact
            <input type="radio" name="boolean" value="phrase"> Phrase
            <input type="radio" name="boolean" value="wildcard"> Wildcard
            <input type="radio" name="boolean" value="" checked> None<br>
        Must <b>not</b> include: <input type="text" name="mustnot" value="<?php echo $mustnot; ?>"><br>
        <input type="submit" value="Search">
    </form>
</div>
<br><br>
<?php
if ( isset($result) AND $result->num_rows) {
    echo "<div>Displaying: {$result->num_rows} blog posts</div>";


    while ($row = $result->fetch_assoc()) {
        echo "{$row['first_name']} {$row['last_name']} <br>
        {$row['title']}<br>
        {$row['post']}<br>";
        echo "<br><br>";

    }
}

?>

</table>
</body>
</html>