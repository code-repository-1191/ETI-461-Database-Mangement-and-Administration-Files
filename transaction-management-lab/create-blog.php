<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require "../includes/db_conn.php";

if ($mysqlconn->connect_error) {
    die("Connection failed: " . $mysqlconn->connect_error);
}
// if there is not user id then redirect user 
if ( !isset($_GET['user_id']) ) {
    header("location: timeline.php");
    exit;
}
$user_id = intval($_GET['user_id']);
$user_sql = "SELECT first_name, last_name FROM `eti_user` WHERE id = {$user_id} ";
$user_result = $mysqlconn->query($user_sql);
if (!$user_result) {
    die("User query failed: " . $mysqlconn->error);
}
$user = $user_result->fetch_assoc();
if (!$user) {
    // invalid user; show message instead of redirect loop
    echo "<p style='color:red;'>No user found for ID {$user_id}. Please go back and choose a valid user.</p>";
    echo "<p><a href='blogs.php?user_id={$user_id}'>Back to blogs</a></p>";
    exit;
}

$all_users_sql = "SELECT first_name, last_name, id FROM `eti_user`";
$all_users_result = $mysqlconn->query($all_users_sql);
if (!$all_users_result) {
    die("All users query failed: " . $mysqlconn->error);
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    try {
        // PART C - 
        // TRANSACTION CODE GOES IN THIS TRY BLOCK
        // PART C1 - Turn autocommit off
        mysqli_autocommit($mysqlconn, FALSE);
        // PART C2 - begin transaction
        mysqli_begin_transaction($mysqlconn);

        $blog_text = $mysqlconn->real_escape_string($_POST['blog']);
        $title = $mysqlconn->real_escape_string($_POST['title']);
        // PART C3 - write the SQL to insert a new entry into the user_blog table
        $insert_sql = "INSERT INTO eti_user_blog (blogger_id, title, blog_text, blog_date) 
                        VALUES ($user_id, '$title', '$blog_text', NOW())";
        
        $mysqlconn->query($insert_sql);
        if (mysqli_affected_rows($mysqlconn) == -1) {
            throw new Exception("Could not add blog.<br>{$insert_sql}");
        }
        // get the recently created blog id in the eti_user_blog table
        $blog_id = mysqli_insert_id($mysqlconn);
        
        // are there any tagged topics?
        $tagsubject_input = $_POST['myTaggedSubject'];
        if (trim(strlen($tagsubject_input))) {
          // PART C4 - write the SQL to insert a new entry into the user_tag_blog table
          $insert_tag_sql = "INSERT INTO eti_user_tag_blog (blog_id, tagged_subject) 
                            VALUES ($blog_id, '$tagsubject_input')";
          $mysqlconn->query($insert_tag_sql);
          // PART C5 - write the code to throw a new exception if there was an error in the 
          // above sql insert statement. You should be able to copy this from code above
          // only having to change the string in the exception.
          if (mysqli_affected_rows($mysqlconn) == -1) {
            throw new Exception("Could not add tag.<br>{$insert_tag_sql}");
        }
          
        }


        // PART C6 - commit SQL Transaction
        mysqli_commit($mysqlconn);
        header("location: blogs.php?user_id={$user_id}");    
    }
    catch (Exception $e) {
        // An exception has been thrown
        // Rollback the transaction
        // PART C7 - there was an error so rollback the SQL transaction
        mysqli_rollback($mysqlconn);
        echo "Transaction failed: " . $e->getMessage();
    }
}

?>

<html>
    <head><title>ETI 461 - Create a Blog Entry</title>
<link rel="stylesheet" href="styles.css">
<body>
    <h1>ETI 461 - Transaction Management Lab:Create a Blog Entry</h1>
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
    

<h3>Creating a blog for: <?php echo "{$user['first_name']} {$user['last_name']}"; ?></h3>
<div>
    <form action="create-blog.php?user_id=<?php echo "{$user_id}"; ?>" method="post" enctype="multipart/form-data">
        <label for="blog">title</label><br>
        <input type="text" name="title" value=""><br><br>
        
        <label for="blog">Blog</label><br>
        <textarea name="blog" cols="80" rows="5"></textarea><br><br>
        <div class="autocomplete" style="width:300px;">
            <label>Blog topic</label><input id="myInput" type="text" name="myTaggedSubject" placeholder="Tagged Subject">
        </div><br>
        <input type="submit" value="Submit Blog">
    </form>
</div>


<script>
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

/*An array containing all the blog tag subjects*/
const blogCategories = [
  "nonsense",
  "Technology",
  "Programming",
  "Web Development",
  "Mobile Development",
  "Artificial Intelligence",
  "Machine Learning",
  "Cybersecurity",
  "Cloud Computing",
  "Data Science",
  "Big Data",
  "Blockchain",
  "Cryptocurrency",
  "Internet of Things",
  "Software Engineering",
  "Gadgets & Reviews",
  "Startups & Entrepreneurship",
  "Business & Finance",
  "Investing & Stocks",
  "Marketing & SEO",
  "Social Media",
  "E-commerce",
  "Personal Finance",
  "Real Estate",
  "Career & Job Search",
  "Productivity & Time Management",
  "Freelancing",
  "Side Hustles",
  "Leadership & Management",
  "Personal Development",
  "Motivation & Inspiration",
  "Mental Health & Wellness",
  "Fitness & Exercise",
  "Healthy Eating",
  "Weight Loss",
  "Self-Care",
  "Relationships & Dating",
  "Parenting & Family",
  "Education & Learning",
  "Online Courses & E-Learning",
  "College & University",
  "Travel & Adventure",
  "Budget Travel",
  "Luxury Travel",
  "Photography",
  "Videography",
  "Music & Audio",
  "Movies & TV Shows",
  "Books & Literature",
  "Writing & Blogging",
  "Poetry & Creative Writing",
  "Art & Design",
  "Graphic Design",
  "UI/UX Design",
  "Fashion & Style",
  "Beauty & Skincare",
  "Makeup & Cosmetics",
  "Haircare",
  "DIY & Crafts",
  "Home Improvement",
  "Interior Design",
  "Gardening",
  "Automotive",
  "Sports & Outdoor Activities",
  "Hiking & Camping",
  "Gaming & Esports",
  "Board Games & Tabletop Games",
  "Science & Space",
  "Psychology & Human Behavior",
  "Philosophy",
  "History",
  "Politics & Current Events",
  "World News",
  "Religion & Spirituality",
  "Astrology & Horoscopes",
  "Ethics & Morality",
  "Food & Recipes",
  "Cooking Tips",
  "Wine & Spirits",
  "Coffee & Tea",
  "Vegan & Vegetarian Lifestyle",
  "Pets & Animal Care",
  "Dogs & Dog Training",
  "Cats & Cat Care",
  "Birdwatching",
  "Environment & Sustainability",
  "Zero Waste Living",
  "Minimalism",
  "Tiny Homes & Van Life",
  "Survival & Prepping",
  "Self-Sufficiency",
  "Hobbies & Interests",
  "Collecting & Antiques",
  "Woodworking",
  "Knitting & Sewing",
  "Magic & Illusions",
  "Technology Reviews",
  "Smart Home Technology",
  "Augmented & Virtual Reality",
  "Drones & UAVs",
  "Ethical Hacking",
  "Networking & IT",
  "Programming Tutorials",
  "Career Advice for Developers",
  "Open Source Projects"
];

/*initiate the autocomplete function on the "myInput" element, and pass along the users array as possible autocomplete values:*/
window.onload = function () {
    autocomplete(document.getElementById("myInput"), blogCategories);
}
</script>

</body>
</html>
