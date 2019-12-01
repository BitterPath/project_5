<?php
// TODO: Remove first require_once, uncomment other two.
require_once "/home/common/dbInterface.php";
require_once "/home/common/mail.php";
processPageRequest();

function authenticateUser($username, $password) {
    $userArray = validateUser($username, $password);
    if (is_null($userArray)) {
        $errormsg = "<br /><span style='color:red;'>Username and/or password incorrect. Please try again.</span>";
        displayLoginForm($errormsg);
    } else {
        $_SESSION['user']['Id'] = $userArray["ID"];
        $_SESSION['user']['displayName'] = $userArray["DisplayName"];
        $_SESSION['user']['emailAddress'] = $userArray["Email"];
        header("Location:index.php");
    }
}

function createAccount($username, $password, $displayName, $emailAddress) {
    $newUserId = addUser($username, $password, $displayName, $emailAddress);
    if ($newUserId > 0) {
        sendValidationEmail($newUserId, $displayName, $emailAddress);
    } else {
        $errormsg = "<br /><span style='color:red;'>Username already in use, please choose another.</span>";
        displayLoginForm($errormsg);
    }
}

function displayCreateAccountForm() {
    require_once "./templates/create_form.html";
}

function displayForgotPasswordForm() {
    require_once "./templates/forgot_form.html";
}

function displayLoginForm($message="") {
    require_once "./templates/logon_form.html";
}

function displayResetPasswordForm($userId) {
    require_once "./templates/reset_form.html";
}

function processPageRequest() {
    session_start(); session_unset();
    if(empty($_POST) && empty($_GET)) {
        displayLoginForm();
    } else if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action == 'create') { createAccount($_POST['userName'], $_POST['password'], $_POST['displayName'], $_POST['emailAddress']); }
        else if ($action == 'forgot') { sendForgotPasswordEmail($_POST['userName']); }
        else if ($action == 'login') { authenticateUser($_POST['userName'], $_POST['password']); }
        else if ($action == 'reset') { resetPassword($_POST['userId'], $_POST['password']); }
        else { displayLoginForm(); }
    } else if (isset($_GET['action'])) {
        $action = $_GET['action'];
        if ($action == 'validate') {$userId = $_GET['user-id']; validateAccount($userId); }
        else { displayLoginForm(); }
    } else if (isset($_GET['form'])) {
        $form = $_GET['form'];
        if ($form == 'create') { displayCreateAccountForm(); }
        else if ($form == 'forgot') { displayForgotPasswordForm(); }
        else if ($form == 'reset') { $userId = $_GET['userId']; displayResetPasswordForm($userId); }
        else { displayLoginForm(); }
    } else {
        displayLoginForm();
    }
}

function resetPassword($userId, $password) {
    if (resetUserPassword($userId, $password)) {
        $errormsg = "<br /><span style='color:green;'>Password successfully updated! You may now login using your new password.</span>";
        displayLoginForm($errormsg);
    } else {
        $errormsg = "<br /><span style='color:red;'>Password update failed. You may try again or contact the system administrators.</span>";
        displayLoginForm($errormsg);
    }
}

function sendForgotPasswordEmail($username) {

    $userData = getUserData($username);
    if (is_array($userData)) {
        $subject = "myMovies Xpress! Password Reset";
        $message = "Hi ". $userData["DisplayName"] . ",<br/>" .
            "In order to reset your password please click the following link:<br/>".
            "<a href='http://139.62.210.181/~lr48307/project5/logon.php?form=reset&user-id=".$userData['ID']."'>http://139.62.210.181/~lr48307/project5/logon.php?form=reset&user-id=".$userData["ID"]."</a>";

        $mailid = "505047167";
        $result = sendMail($mailid, $userData["Email"], $userData["DisplayName"], $subject, $message);

        if ($result === 0) {
            $msg = "<br/><span style='color:green;'>An email was sent to you with instructions on how to reset your password.</span>";
        } else {
            $msg = "<br/><span style='color:red;'>There was an error attempting to send a password reset email. Please try again.</span>";
        }
        displayLoginForm($msg);
    } else {
        $msg = "<br/><span style='color:red;'>There is no account associated with this username. Please try again.</span>";
        displayLoginForm($msg);
    }
}

function sendValidationEmail($userId, $displayName, $emailAddress) {
    $subject = "myMovie Xpress! Account Validation";
    $message = "<h4>myMovies Xpress!</h4><br/>".
        $displayName . "," .
        "You have recently created an account with myMovies Xpress! You must verify your account in order to continue to".
        " use our service. Please click the link below to verify:<br/> ".
        "<a href='http://139.62.210.181/~lr48307/project5/logon.php?action=validate&user-id=".$userId."'>http://139.62.210.181/~lr48307/project5/logon.php?action=validate&user-id=".$userId."</a>";

    $mailid = "505047167";
    $result = sendMail($mailid, $emailAddress, $displayName, $subject, $message);

    if ($result === 0) {
        $msg = "<br/><span style='color:green;'>An email was sent to you with instructions on how to verify your account.</span>";
    } else {
        $msg = "<br/><span style='color:red;'>Account was created but there was an error sending your confirmation email.</span>";
    }
    displayLoginForm($msg);
}

function validateAccount($userId) {
    if(activateAccount($userId)) {
        $msg = "<br/><span style='color:green;'>You have successfully verified your account and may now login using your username and password.</span>";
        displayLoginForm($msg);
    } else {
        $msg = "<br/><span style='color:red;'>There was an error validating your account. Please use the link you were sent in your email to verify your account.</span>";
    }
}