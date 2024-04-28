<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }


    function show_comment(string $type, string $pic_path, Comment $comment, string $logged_in_user, bool $admin, bool $banned, string $hirnev, array $hibak) {

        $depth = $comment->getDepth();
        if ($depth > 5) { $depth = 5; }
        // maximum 5 szintig van ábrázolva a kommentfa ekkor a komment szélessége 65%
        $width = 100 - $depth * 7;

        if ($comment->isDeleted()) {
            echo '<table class="' . $type . '" style="width: ' . $width . '%;">';
                echo '<tr>';
                    echo '<td class="c_main">';
                        echo '<p> A komment törölve lett! </p>';
                    echo '</td>';
                echo '</tr>';
            echo '</table>';
        }
        else {
            $liked_by_user = in_array($logged_in_user, $comment->getLikedBy());
            $action = $liked_by_user ? 'c_unlike' : 'c_like';
            $no_of_likes = $comment->countLikes();

            $answer = "";
            if ($comment->getAnswerTo() !== "" && $comment->getAnswerTo() !== null) {
                $answer = '<span class="answer-icon"></span> ' . $comment->getAnswerTo();
            }

            echo '<table class="' . $type . '" style="width: ' . $width . '%;">';
                echo '<tr>';

                    echo '<td class="c_aux">';
                        echo '<p><img src="' . $pic_path . '" alt="Profile Picture"> ' . $comment->getAuthor() . $answer . ' - ' . $comment->getDate() . '</p>';
                    echo '</td>';

                echo '</tr>';
                echo '<tr>';

                    echo '<td class="c_main">';
                        echo '<p>' . nl2br($comment->getContent()) . '</p>';
                    echo '</td>';

                echo '</tr>';
                echo '<tr>';

                    echo '<td class="c_main">';

                        // div 1: egymás felett - egy sor like/törlés, 1 sor válasz form, ha nem saját komment
                        echo '<div style="display: flex; align-items: center; flex-direction: column;">';
                            
                            // div 2: egymás melett, balra zárt
                            echo '<div style="display: flex; justify-content: flex-start; flex-direction: row;">';
                                // az adott hir es komment kod egy string-ként van továbbítva, elvileg egyik összefűzött string sem tartalmazhat - karaktert így ez alapján lehet szétszedni a hir és komment kódot
                                echo '<p><a href="?' . $action . '=' . urlencode($comment->getId().'-'.$hirnev) . '&user=' . urlencode($logged_in_user) . '"><span class="heart-icon ' . ($liked_by_user ? 'filled' : '') . '"></span></a>' . $no_of_likes .  ' | </p>';
                                if ($comment->getAuthor() == $logged_in_user || $admin) {
                                    echo '<p><a href="?c_delete=' . urlencode($comment->getId().'-'.$hirnev) . '"><span class="trash-icon"></span></a></p>';
                                }
                            echo '</div>';
                            
                            // if ($comment.getAuthor() !== $logged_in_user) { - saját kommentre is válaszolhat!
                            // válaszolás űrlap
                            if ($banned) {
                                echo '<p> Ön ki van tiltva a kommentelés lehetőségétől! </p>';
                            }
                            else {
                                echo '<form class="kom_urlap" action="hirek.php" method="POST">';

                                    echo '<input type="hidden" name="answer_to" value="' . $comment->getId() . '">';
                                    // echo '<input type="hidden" name="type" value="answer">';
                                    echo '<input type="hidden" name="hirnev" value="' . $hirnev . '">';
                                    echo '<input type="hidden" name="user" value="' . $logged_in_user . '">';

                                    echo '<div style="width: 100%; display: flex; justify-content: space-between; flex-direction: row;">';

                                        echo '<div style="display: flex; align-items: center; flex-direction: column; width: 20%;">';
                                            echo '<input type="reset" value="Elvet">';
                                            echo '<input type="submit" class="submitclass" name="valasz" value="Válasz">';
                                        echo '</div>';

                                        echo '<div style="display: flex; align-items: center; flex-direction: column; width: 75%;">';
                                            echo '<textarea name="content" placeholder="Válasz írása..."></textarea>';
                                        echo '</div>';

                                    echo '</div>';

                                echo '</form>';
                            }
                            
                        echo '</div>';

                    echo '</td>';

                echo '</tr>';
            echo '</table>';

            if (count($hibak) > 0) {
                foreach ($hibak as $hiba) {
                    echo $hiba;
                }
            }

        }
    }

?>