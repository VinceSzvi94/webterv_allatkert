<?php

    class Post {}
    class Comment {}
    // függvény a fórumon történő kommunikáció mentésére szolgálló forumdata.json létrehozására
    function forum_init() {
        if (!file_exists("forumdata.json")) {
            $comment1 = array(
                "author" => "admin",
                "id" => "post_0_c0",
                "content" => "Próbakomment!",
                "answer_to" => NULL,
                "liked_by" => [],
            );
            $post1 = array(
                "author" => "admin",
                "id" => "post_0",
                "content" => "Üdvözöljük a fórumon! Kérjük, hogy tartsák tiszteletben egymás véleményét, és ne használjanak trágár szavakat!",
                "comments" => [$comment1],
                "liked_by" => [],
            );

            $forum = array("forum" => [$post1]);

            file_put_contents("forumdata.json", json_encode($forum));
        }
    }
?>