<?php
session_start();
processPageRequest();

function displaySearchForm() {
    require_once("./templates/search_form.html");
}

function displaySearchResults($searchString) {
    $results = file_get_contents("http://www.omdbapi.com/?apikey=39959a56&s=".urlencode($searchString)."&type=movie&r=json");
    $resulterror = '{"Response":"False","Error":"Movie not found!"}';
    if ($results != $resulterror) {
        $array = json_decode($results, true)["Search"];
    }
    require_once("./templates/results_form.html");
}

function processPageRequest() {
    if(empty($_POST)) {
        displaySearchForm();
    } else {
        displaySearchResults($_POST["keyword"]);
    }
}