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
            $query = "SELECT u.first_name, u.last_name, b.title, b.post
                FROM `eti_user` u, `eti_user_blog` b
                WHERE MATCH (b.title, b.post) AGAINST ('+{$term}' IN BOOLEAN MODE)
                AND u.id = b.poster_id;";
        }
        // PART A2
        elseif ( $_GET['boolean'] == 'phrase' ) {
            $query = "SELECT u.first_name, u.last_name, b.title, b.post
                FROM `eti_user` u, `eti_user_blog` b
                WHERE MATCH (b.title, b.post) AGAINST ('\"{$term}\"' IN BOOLEAN MODE)
                AND u.id = b.poster_id;";
        }
        // PART A3
        elseif ( $_GET['boolean'] == 'wildcard' ) {
            $query = "SELECT u.first_name, u.last_name, b.title, b.post
                FROM `eti_user` u, `eti_user_blog` b
                WHERE MATCH (b.title, b.post) AGAINST ('{$term}*' IN BOOLEAN MODE)
                AND u.id = b.poster_id;";
        }
        // PART A4
        else {
            // PART A4i
            if ( trim(strlen($mustnot)) ) {
                $query = "SELECT u.first_name, u.last_name, b.title, b.post
                FROM `eti_user` u, `eti_user_blog` b
                WHERE MATCH (b.title, b.post) AGAINST ('{$term} -{$mustnot}' IN BOOLEAN MODE)
                AND u.id = b.poster_id;";
            }
            // PART A4ii
            else {
                $query = "SELECT u.first_name, u.last_name, b.title, b.post
                FROM `eti_user` u, `eti_user_blog` b
                WHERE MATCH (b.title, b.post) AGAINST ('{$term}')
                AND u.id = b.poster_id;";                                
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
        $query = "SELECT u.first_name, u.last_name, b.title, b.post
                FROM `eti_user` u, `eti_user_blog` b
                WHERE MATCH (b.title, b.post) AGAINST ('{$q}')
                AND u.id = b.poster_id;";
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
        <h1>ETI 461 - Database Performance Lab</h1>
<div>
    <a href="index.php">Home</a> | 
	<a href="create-user.php">Create User</a> | 
    <a href="search.php">Search Blog Posts</a> 
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
    echo "<table border='1'>";
    echo "<tr><th>Author</th><th>Title</th><th>Post</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['first_name']} 
        {$row['last_name']}</td>";
        echo "<td>{$row['title']}</td>";
        echo "<td>{$row['post']}</td>";
        echo "</tr>";

    }
    echo "</table>";
}

?>

</table>
</body>
</html>