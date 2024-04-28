<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	include_once 'hirek/functions/hir_functions.php';
	include_once 'hirek/functions/Comment.php';
	include_once 'hirek/functions/addhir.php';
	include_once 'hirek/functions/likeol.php';
	include_once 'hirek/functions/kommentel.php';
    
?>