<?php
// TODO: Uncomment require_once "/home/common/mail.php";
require_once "dbInterface.php";
// require_once "/home/common/mail.php";
processPageRequest();

function authenticateUser($username, $password) {
    $userArray = validateUser($username, $password);
    if (is_null($userArray)) {
        $errormsg = "<br /><span style='color:red;'>Username and/or password incorrect. Please try again.</span>";
        displayLoginForm($errormsg);
    } else {
        $_SESSION['user']['Id'] = $userArray[0];
        $_SESSION['user']['displayName'] = $userArray[3];
        $_SESSION['user']['emailAddress'] = $userArray[4];
        header("Location:index.php");
    }
}

function createAccount($username, $password, $displayName, $emailAddress) {
    $newUserId = addUser($username, $password, $displayName, $emailAddress);
    if ($newUserId > 0) {
        //sendValidationEmail($newUserId, $displayName, $emailAddress);
        $errormsg = "<br /><span style='color:green;'>User account created, please login.</span>";
        displayLoginForm($errormsg);
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
        if ($action == 'validate') {$userId = $_GET['userId']; validateAccount($userId); }
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

}

function sendForgotPasswordEmail($username) {

}

function sendValidationEmail($userId, $displayName, $emailAddress) {

}

function validateAccount($userId) {

}