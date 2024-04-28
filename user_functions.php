<?php

    // függvény a userdata.json létrehozására, ha még nem létezik, admin létrehozása
    function user_init() {
        if (!file_exists("userdata.json")) {
            $admin_user = array(
                "email" => "admin@gothamzoo.hu",
                "username" => "admin",
                "password" => password_hash("Admin123", PASSWORD_DEFAULT),
                "profpic" => "zoo3.png",
                "role" => "admin",
				"banned" => false,
				"newuser" => false,
				"életkor" => 44,
				"nem" => "(egyéb)",
				"vércsoport" => "O+" // A+, A-, B+, B-, O+, O-, AB+, AB-
            );

            $users = array("users" => [$admin_user]);

            file_put_contents("userdata.json", json_encode($users));
        }
    }

    function load_users(string $path): array {
        if (!file_exists($path))
            die("Felhasználói adatok betöltése sikertelen!");

        $json = file_get_contents($path);

        return json_decode($json, true);
    }

    function save_users(string $path, array $data) {
        $users = load_users($path);

        $users["users"] = array_merge($users["users"], $data);

        $json_data = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($path, $json_data);
    }

	function update_users(string $path, $users) {
        $json_data = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($path, $json_data);
    }
?>
