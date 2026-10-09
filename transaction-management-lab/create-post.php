<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";
// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: timeline.php");
}
$user_id = $_GET['user_id'];
$user_sql = "SELECT first_name, last_name FROM `eti_user` WHERE id = {$user_id} ";
$user_result = $mysqlconn->query($user_sql);
$user = $user_result->fetch_assoc();

$all_users_sql = "SELECT first_name, last_name, id FROM `eti_user`";
$all_users_result = $mysqlconn->query($all_users_sql);

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    try {
        // PART A - 
        // TRANSACTION CODE GOES IN THIS TRY BLOCK
        // PART A1 - Turn autocommit off
        mysqli_autocommit($mysqlconn, FALSE);
        // PART A2 - begin transaction
        mysqli_begin_transaction($mysqlconn);

        $post = $mysqlconn->real_escape_string($_POST['post']);
        $insert_sql = "INSERT INTO `eti_user_post` (poster_id, post) VALUES ({$user_id}, '{$post}')";
        $mysqlconn->query($insert_sql);
        if (mysqli_affected_rows($mysqlconn) == -1) {
            throw new Exception("Could not add post.<br>{$insert_sql}");
        }
        // get the recently created post id in the eti_user_post table
        $post_id = mysqli_insert_id($mysqlconn);
        
        // are there any tagged users?
        $taguser_input = $_POST['myTaggedUser'];
        if (trim(strlen($taguser_input))) {
            // this will be format of First Name Last Name:id -- we just need the ID
            $tag_users = explode(':', $taguser_input);
            $tag_user_id = $tag_users[1];
            //echo "id:{$tag_user_id}";
            $insert_tag_sql = "INSERT INTO `eti_user_tag_post` (`post_id`, `tagged_user_id`) VALUES($post_id, $tag_user_id)";
            $mysqlconn->query($insert_tag_sql);
            if (mysqli_affected_rows($mysqlconn) == -1) {
                throw new Exception("Could not add tagged user.<br>{$insert_tag_sql}");
            }
        }
        // PART A3 - commit    
        mysqli_commit($mysqlconn);
        header("location: posts.php?user_id={$user_id}");    
    }
    catch (Exception $e) {
        // An exception has been thrown
        // PART A4 - Rollback the transaction
        mysqli_rollback($mysqlconn);
        echo "Transaction failed: " . $e->getMessage();
    }
}

?>

<html>
    <head><title>ETI 461 - Create a Post</title>
<link rel="stylesheet" href="styles.css">
<body>
    <h1>ETI 461 - Transaction Management Lab:Create a Post</h1>
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
    

<h3>Creating a post for: <?php echo "{$user['first_name']} {$user['last_name']}"; ?></h3>
<div>
    <form action="create-post.php?user_id=<?php echo "{$user_id}"; ?>" method="post" enctype="multipart/form-data">
        <label for="post">Post</label><br>
        <textarea name="post" cols="80" rows="5"></textarea><br><br>
        <!-- <input type="file" name="post_media[]" id="post_media" multiple><br><br> -->
        <div class="autocomplete" style="width:300px;">
            <label>Tag User</label><input id="myInput" type="text" name="myTaggedUser" placeholder="Tagged User">
        </div><br>
        <input type="submit" value="Post">
    </form>
</div>


<script>
    /***************************************************** */
    /*   YOU DO NOT NEED TO WORRY ABOUT THIS CODE BELOW    */
    /***************************************************** */


function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
  });
}

/*An array containing all the country names in the world:*/
<?php 
echo "var users = [";
while($all_row = $all_users_result->fetch_assoc()) {
    echo "\"{$all_row['first_name']} {$all_row['last_name']}:{$all_row['id']}\",";
}
echo "];"
?>
/*initiate the autocomplete function on the "myInput" element, and pass along the users array as possible autocomplete values:*/
window.onload = function () {
    autocomplete(document.getElementById("myInput"), users);
}
</script>

</body>
</html>
