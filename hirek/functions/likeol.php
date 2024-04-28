<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	// include_once 'hir_functions.php';
    // include_once 'Comment.php';

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
    function like_comment() {
        if (isset($_GET['c_like']) && isset($_GET['user'])) {
            $hirnev_es_id = $_GET['c_like'];
            $user = $_GET['user'];

            $parts = explode('-', $hirnev_es_id);
            $hirnev = $parts[1];
            $id = $parts[0];

            $hirpath = "hirek/" . $hirnev . ".json";
            $hir_arr = json_decode(file_get_contents($hirpath), false);
            $comments = array_slice($hir_arr, 1);
            
            foreach ($comments as &$comment) {
                $comment = unserialize($comment);
                if ($comment->getId() === $id) {
                    $comment->like($user);
                    $comment = serialize($comment);
                    break;
                }
                else if ($comment->isChild($id)) {
                    $comment->applyMethodOnChild($id, "like", $user);
                    $comment = serialize($comment);
                    break;
                }
                else { $comment = serialize($comment); continue; }
            }
            unset($comment);
            $hir_arr = array_merge([$hir_arr[0]], $comments);
            file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }

    function unlike_comment() {
        if (isset($_GET['c_unlike']) && isset($_GET['user'])) {
            $hirnev_es_id = $_GET['c_unlike'];
            $user = $_GET['user'];

            $parts = explode('-', $hirnev_es_id);
            $hirnev = $parts[1];
            $id = $parts[0];

            $hirpath = "hirek/" . $hirnev . ".json";
            $hir_arr = json_decode(file_get_contents($hirpath), false);
            $comments = array_slice($hir_arr, 1);
            
            foreach ($comments as &$comment) {
                $comment = unserialize($comment);
                if ($comment->getId() === $id) {
                    $comment->unlike($user);
                    $comment = serialize($comment);
                    break;
                }
                else if ($comment->isChild($id)) {
                    $comment->applyMethodOnChild($id, "unlike", $user);
                    $comment = serialize($comment);
                    break;
                }
                else { $comment = serialize($comment); continue; }
            }
            unset($comment);
            $hir_arr = array_merge([$hir_arr[0]], $comments);
            file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }

    function delete_comment() {
        if (isset($_GET['c_delete'])) {
            $hirnev_es_id = $_GET['c_delete'];

            $parts = explode('-', $hirnev_es_id);
            $hirnev = $parts[1];
            $id = $parts[0];

            $hirpath = "hirek/" . $hirnev . ".json";
            $hir_arr = json_decode(file_get_contents($hirpath), false);
            $comments = array_slice($hir_arr, 1);
            
            foreach ($comments as &$comment) {
                $comment = unserialize($comment);
                if ($comment->getId() === $id) {
                    $comment->delete();
                    $comment = serialize($comment);
                    break;
                }
                else if ($comment->isChild($id)) {
                    $comment->applyMethodOnChild($id, "delete", []);
                    $comment = serialize($comment);
                    break;
                }
                else { $comment = serialize($comment); continue; }
            }
            $hir_arr = array_merge([$hir_arr[0]], $comments);
            file_put_contents($hirpath, json_encode($hir_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
    }
    
?>