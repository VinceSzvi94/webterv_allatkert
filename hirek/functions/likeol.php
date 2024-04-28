<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	include_once 'hir_functions.php';
    include_once 'Comment.php';

    // hírek like/unlike-olása
    function like_hir() {
        if (isset($_GET['like']) && isset($_GET['user'])) {
            $hirnev = $_GET['like'];
            $user = $_GET['user'];
        
            $hirpath = "hirek/" . $hirnev . ".json";
            $hir_arr = json_decode(file_get_contents($hirpath), true);
            $hir = $hir_arr[0];
        
            if (!in_array($user, $hir['likes'])) {
                $hir['likes'][] = $user;
            }

            $hir_arr[0] = $hir;
            file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }

    function unlike_hir() {
        if (isset($_GET['unlike']) && isset($_GET['user'])) {
            $hirnev = $_GET['unlike'];
            $user = $_GET['user'];
        
            $hirpath = "hirek/" . $hirnev . ".json";
            $hir_arr = json_decode(file_get_contents($hirpath), true);
            $hir = $hir_arr[0];
        
            if (in_array($user, $hir['likes'])) {
                $hir['likes'] = array_diff($hir['likes'], [$user]);
            }

            $hir_arr[0] = $hir;
            file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }

	// kommentek like/unlike-olása
    
?>