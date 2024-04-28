<?php

    // include_once "Comment.php";

    function create_hir(string $path, array $hir) {
        $hir_data = [$hir];
        $json_data = json_encode($hir_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($path, $json_data);
    }

    function load_hir(string $path): array {
        if (!file_exists($path))
            die("A hírt tartalmazó fájl betöltése sikertelen!");

        $json = file_get_contents($path);
        $data_decoded = json_decode($json, true);

        // a hír mindig a tömb első eleme, míg az összes komment ehhez van fűzve
        return $data_decoded[0];
    }

    function load_comments(string $path): array {
        if (!file_exists($path))
            die("A kommentek betöltése sikertelen!");

        $json = file_get_contents($path);
        $data_decoded = json_decode($json, true);

        return array_slice($data_decoded, 1);
    }

    function save_comments(string $path, Comment $comment) {
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        $ser_comment = serialize($comment);
        array_push($data, $ser_comment); 
        $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($path, $json_data);
    }
?>