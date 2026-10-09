<html>
    <body><h1>ETI 461 - Lab 3</h1>
<?php

error_reporting (E_ALL);
ini_set("display_errors", 1);

require_once "../includes/db_conn.php";
$query = "SELECT * FROM eti_user";

// To Do #1
// if a user has submitted a search query, update
// the $query or overwrite $query so that the eti_user
// table is searched for anyone with a last name that begins with 
// the search string

if (isset($_GET['q']) && $_GET['q'] != "") {
  $search = trim($_GET['q']);
  $query = "SELECT * FROM eti_user WHERE last_name LIKE '{$search}%'";
}

// To Do #2
// check for the existence of the URL order by variable $_GET['order_by]
// if it exists then update your query string to order accordingly

if (isset($_GET['order_by']) && $_GET['order_by'] != "") {
  $order = $_GET['order_by'];
  $query .= " ORDER BY $order";
}

// To Do #3
// add an input field, named 'q' to search the users table
// add a submit button so you can submit your search query

echo "
  <form action=\"{$_SERVER['PHP_SELF']}\" method=\"get\">
    <input type=\"text\" name=\"q\" placeholder=\"Search by last name\">
    <input type=\"submit\" value=\"Search\">
  </form><br>";

echo "<table border=1 cellpadding=5 >";
echo "<tr>
        <th>ID</th>
        <th>First</th>";
echo "  <th>Last ";

// To Do #4
// add the SQL ORDER BY clause that you want to run
// and replace it with [ASC order by clause here] and
// [DESC order by clause here] remove the brackets [] as well
echo "<a href=\"list-users.php?order_by=last_name ASC\">&uarr;</a> 
      <a href=\"list-users.php?order_by=last_name DESC\">&darr;</a></th>";

echo "</th>";
echo "  <th>Email</th>
        <th>Username</th>
        <th>Password</th>
        <th>Registration Date</th>
        <th>Last Updated</th>
      </tr>";

// assign the result of the SQL query $query to
// $result variable, if there is more than 0 rows, output    

    $result = $mysqlconn->query($query);
    if ($result->num_rows > 0) {

  // assign each row in the result set from above
  // to an associative array $row, we can access the 
  // columns by name within the row i.e. $row['first_name'] will
  // output the first name of the user we are currently looping over

  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
      echo "<td>{$row['id']}</td>";
      echo "<td>{$row['first_name']}</td>";
      echo "<td>{$row['last_name']}</td>";
      echo "<td>{$row['email']}</td>";
      echo "<td>{$row['username']}</td>";
      echo "<td>{$row['password']}</td>";
      echo "<td>{$row['registration_date']}</td>";
      echo "<td>{$row['updated_date']}</td>";
    echo "</tr>";
  }
}

echo "</table>";
$mysqlconn->close();
?>
</body>
</html>