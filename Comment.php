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

        public function __construct(string $author, string $content, string $answer_to, array $answers, array $liked_by) {
            $this->author = $author;
            $this->id = uniqid();
            $this->date = date('Y-m-d H:i:s');
            $this->content = $content;
            $this->answer_to = $answer_to;
            $this->answers = $answers;
            $this->liked_by = $liked_by;
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
    }
?>