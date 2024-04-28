<?php
    class Comment {

        private $author;
        private $id;
        private $date;
        private $content;
        private $answer_to;
        private $answers;
        private $liked_by;
        private $deleted;

        public function __construct(string $author, string $content, string $answer_to) {
            $this->author = $author;
            $this->id = uniqid();
            $this->date = date('Y-m-d H:i:s');
            $this->content = $content;
            $this->answer_to = $answer_to;
            $this->answers = [];
            $this->liked_by = [];
            $this->deleted = false;
        }

        // getterek és setterek
        public function getAuthor(): string {
            return $this->author;
        }

        public function getId(): string {
            return $this->id;
        }

        public function getDate(): string {
            return $this->date;
        }

        public function getContent(): string {
            return $this->content;
        }

        public function getAnswerTo(): string {
            return $this->answer_to;
        }

        public function getAnswers(): array {
            return $this->answers;
        }

        public function getLikedBy(): array {
            return $this->liked_by;
        }

        public function isDeleted(): bool {
            return $this->deleted;
        }

        // szerző, dátum és id nem módosítható

        public function setContent(string $content) {
            $this->content = $content;
        }

        public function setAnswerTo(string $answer_to) {
            $this->answer_to = $answer_to;
        }

        public function setAnswers(array $answers) {
            $this->answers = $answers;
        }

        public function setLikedBy(array $liked_by) {
            $this->liked_by = $liked_by;
        }

        public function delete() {
            $this->deleted = true;
        }

        public function like(string $username) {
            if (!in_array($username, $this->liked_by)) {
                $this->liked_by[] = $username;
            }
        }

        public function unlike(string $username) {
            $key = array_search($username, $this->liked_by);
            if ($key !== false) {
                unset($this->liked_by[$key]);
            }
        }

        public function addAnswer(Comment $answer) {
            $this->answers[] = $answer;
        }

        public function listAnswers():array {
            // válaszok rekurzív bejárása
            $answers = [];
            $reversed_answers = array_reverse($this->answers); // a legfrissebb válaszokat legelől listázza
            foreach ($reversed_answers as $answer) {
                if (count($answer->getAnswers()) > 0) {
                    $answers = array_merge($answers, $answer->listAnswers());
                }
                else { $answers[] = $answer; }
            }
            return $answers;
        }

        public function toArray(): array {
            $comment_as_array = array(
                "author" => $this->author,
                "id" => $this->id,
                "date" => $this->date,
                "content" => $this->content,
                "answer_to" => $this->answer_to,
                "answers" => $this->answers,
                "liked_by" => $this->liked_by,
                "deleted" => $this->deleted
            );
            return $comment_as_array;
        }

        public function __toString(): string {
            return $this->author . " (" . $this->date . "): " . $this->content;
        }
    }
?>