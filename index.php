<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
// TODO: UNCOMMENT REQUIRE_ONCE BELOW PRIOR TO PUBLISHING
// require_once '/home/common/mail.php';
processPageRequest();

function addMovieToCart($movieID) {
    $currentdb = readMovieData();
    array_push($currentdb, $movieID . "\n");
    writeMovieData($currentdb);
    displayCart();
}

// TODO: SET SENDMAIL() PARAMETERS $result = sendMail([mail_id], $_SESSION['useremail'], $_SESSION['userdisplayname'], $subject, $message);
function checkout($name, $address) {

    $subject = "Your receipt from myMovies Xpress!";

    $message = "<!DOCTYPE HTML>".
        "<html lang='en'>".
        "<head><meta http-equiv='content-type' content='text/html; charset=UTF-8'></head>".
        "<p>Thank you ".$_SESSION['user']['displayName']." for your myMovies Xpress! order.</p>" .
        "<p>You have ordered the following movies. If there are any issues with your order please contact customer care at: 1-888-888-8888</p>".
        "<table><tr><th>Movie Image</th><th>Title (Year)</th>";

    $movies = readMovieData();

    foreach($movies as $movieID) {
        $return = file_get_contents("http://www.omdbapi.com/?apikey=39959a56&i=" . urlencode(trim($movieID)) . "&type=movie&r=json");
        $movie = json_decode($return, true);
        $poster = $movie['Poster']; $title = $movie['Title']; $id = $movie['imdbID']; $year = $movie['Year'];
        $message .= "<tr><td><img style=\"height:100px\" src=\"$poster\"></td><td><a href=\"https://www.imdb.com/title/'.$id.'/\" target=\"_blank\">$title ($year)</a></td></tr>";
    }
    unset($movieID);

    $message .= "</body></html>";

    $mailid = "n00205187@unf.edu";
    $result = sendMail($mailid, $_SESSION['user']['emailAddress'], $_SESSION['user']['displayName'], $subject, $message);

    if ($result === 0) {
        $_SESSION['order'] = "Your order was completed successfully! Thank you from myMovies Xpress!";
    } else {
        $_SESSION['order'] = "There was an error completing your order, please wait (1) minute and try again.";
    }
    header("Location:index.php");
}


function displayCart() {
    $movies = readMovieData();
    if (sizeof($movies) != 0) {
        $_SESSION['cartsize'] = "<span style='color:purple;font-weight:900;'>" . sizeof($movies) . "</span> Movies in your Shopping Cart";
    } else {
        $_SESSION['cartsize'] = "<span style='color:red;'>Add Some Movies to Your Cart</span>";
    }
    require_once("./templates/cart_form.html");

}

function processPageRequest() {
    if(!isset($_SESSION['user']['displayName']) || !isset($_SESSION['user']['emailAddress'])) {
        header("Location:logon.php");
    }
    if(isset($_SESSION['order'])) {
        $ordermsg = $_SESSION['order'];
        echo "<script>alert('$ordermsg')</script>";
        unset($_SESSION['order']);
    }
    if(empty($_GET['action'])) {
        displayCart();
    } else {
        if ($_GET['action'] == "add") {
            addMovieToCart($_GET['movie_id']);
        } elseif ($_GET['action'] == "checkout") {
            checkout($_SESSION['user']['displayName'], $_SESSION['user']['emailAddress']);
        } elseif ($_GET['action'] == "remove") {
            removeMovieFromCart($_GET['movie_id']);
        }
    }
}

function readMovieData() {
    $data = file("./data/cart.db");
    return $data;
}

function removeMovieFromCart($movieID) {
    $newdb = [];
    $currentdb = readMovieData();
    foreach ($currentdb as $movie) {
        if (trim($movie) != $movieID && !empty($movie)) {
            array_push($newdb, trim($movie) . "\n");
        }
    }
    writeMovieData($newdb);
    displayCart();
}

function writeMovieData($array) {
    file_put_contents("./data/cart.db", $array);
}