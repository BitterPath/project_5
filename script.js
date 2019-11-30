function confirmLogout() {
    if (confirm("Are you sure you wish to logout?")) {
        window.location.replace("./logon.php?action=logoff");
        return true;
    }
    return false;
}

function confirmCheckout() {
    if (confirm("Do you wish to checkout from myMovies Xpress!?")) {
        window.location.replace("./index.php?action=checkout");
        return true;
    }
    return false;
}

function addMovie(movieID) {
    window.location.replace("./index.php?action=add&movie_id=" + movieID);
}

function confirmRemove(title, movieID) {
    if (confirm("You are about to remove " + title + " from your cart. Are you sure?")) {
        window.location.replace("./index.php?action=remove&movie_id=" + movieID);
        return true;
    }
    return false;
}