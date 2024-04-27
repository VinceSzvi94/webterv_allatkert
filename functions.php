<?php

    // függvény a userdata.json létrehozására, ha még nem létezik, admin létrehozása
    function user_init() {
        if (!file_exists("userdata.json")) {
            $admin_user = array(
                "email" => "admin@gothamzoo.hu",
                "username" => "admin",
                "password" => password_hash("Admin123", PASSWORD_DEFAULT),
                "profpic" => "img/zoo3.png",
                "role" => "admin",
            );

            $users = array("users" => [$admin_user]);

            file_put_contents("userdata.json", json_encode($users));
        }
    }

    // függvény a fórumon történő kommunikáció mentésére szolgálló forumdata.json létrehozására
    function forum_init() {
        if (!file_exists("forumdata.json")) {
            $post1 = array(
                "author" => "admin",
                "id" => "post_0",
                "content" => "Üdvözöljük a fórumon! Kérjük, hogy tartsák tiszteletben egymás véleményét, és ne használjanak trágár szavakat!",
                "answers" => [],
                "liked_by" => [],
            );

            $forum = array("forum" => [$post1]);

            file_put_contents("forumdata.json", json_encode($forum));
        }
    }

    function load_data(string $path): array {
        if (!file_exists($path))
            die("Felhasználói adatok betöltése sikertelen!");

        $json = file_get_contents($path);

        return json_decode($json, true);
    }

    function save_data(string $path, array $data) {
        $users = load_data($path);

        $users["users"] = array_merge($users["users"], $data);

        $json_data = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($path, $json_data);
    }

?>
