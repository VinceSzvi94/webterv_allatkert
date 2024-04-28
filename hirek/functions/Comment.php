<?php
    class Comment {

        private $author;
        private $id;
        private $date;
        private $content;
        private $answer_to;
        private $depth;
        private $answers;
        private $liked_by;
        private $deleted;

        public function __construct(string $author, string $content, $answer_to=null) {
            $this->author = $author;
            $this->id = uniqid();
            $this->date = date('Y-m-d H:i:s');
            $this->content = $content;
            $this->answer_to = $answer_to;
            if ($answer_to !== null) {
                $this->depth = $answer_to->depth + 1;
            }
            else { $this->depth = 0; }
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

        public function getAnswerTo() {
            return $this->answer_to;
        }

        public function getDepth(): int {
            return $this->depth;
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

        // dátum, szerző, id, mélység és hogy kinek volt a válasz nem módosítható

        public function setContent(string $content) {
            $this->content = $content;
        }

        // válaszokat egyesével addAnswer metódussal megadni!
        // public function setAnswers(array $answers) {
        //     $this->answers = $answers;
        // }

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

        public function addAnswer(string $author, string $content) {
            $answer = new Comment($author, $content, $this);
            $this->answers[] = $answer;
        }

        public function listAnswers(): array {
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

        public function countLikes(): int {
            return count($this->liked_by);
        }

        public function findRoot(): Comment {
            $root = $this;
            while ($root->getAnswerTo() !== "" && $root->getAnswerTo() !== null) {
                $root = $root->getAnswerTo();
            }
            return $root;
        }

        public function findChild(string $id) {
            $answers = $this->listAnswers();
            foreach ($answers as $answer) {
                if ($answer->getId() === $id) {
                    return $answer;
                }
            }
            return null;
        }

        public function isChild(string $id): bool {
            $child = $this->findChild($id);
            return $child !== null;
        }

        public function applyMethodOnChild(string $childId, string $methodName, ...$params) {
            // metódus alkalmazása alkommentre
            if ($this->isChild($childId)) {
                foreach ($this->answers as $answer) {

                    if ($answer->getId() === $childId) {
                        if (method_exists($answer, $methodName)) {
                            return call_user_func_array([$answer, $methodName], $params);
                        }
                        return null; // valamiért nem létezett a metódus
                    }
                    else if ($answer->isChild($childId)) {
                        return $answer->applyMethodOnChild($childId, $methodName, $params);
                    }
                    else { continue; }
                }
            }
            return null; // alkomment más kmmenthez tartozik
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