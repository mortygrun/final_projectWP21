<?php
session_start();
if(isset($_SESSION['name'])){
    $reaction = $_POST['variable'];
    $text_message ="<div>".$_SESSION['name']."'s&nbsp;Reaction Time is: ".$_POST['variable']." seconds</div>";
    file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
}
?>


