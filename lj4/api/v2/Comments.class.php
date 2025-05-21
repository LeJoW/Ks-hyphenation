<?php

declare(strict_types=1);

class Comments
{
    protected $link;
    protected $key;
    protected $bdd;
    protected $OFFSET = 10000;

    public function __construct(string $link)
    {
        try {
            $this->bdd = new PDO('mysql:host=localhost;dbname=einstein-s-power;charset=utf8', 'lj', 'lj', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (Exception $e) {
        }
        $this->link = $link;
        $this->key = preg_replace("#^[\S\s]+.edublogs.org/([\S\s]+)feed/?$#", "$1", $link);
    }

    protected function getEdublogComments()
    {
        $edublog_comments = `wget '$this->link' -O /dev/stdout`;
        $edublog_comments = XMLtoArray($edublog_comments);
        $comments = &$edublog_comments["rss"]["channel"]["item"];
        $comments = $comments ? $comments : [];
        if (isset($comments["title"])) {
            $comments = [$comments];
        }
        return array_map(function ($comment) {
            return [
                "author" => $comment["dc:creator"],
                "date" => $comment["pubDate"],
                "link" => $comment["link"],
                "content" => $comment["content:encoded"]
            ];
        }, $comments);
    }

    protected function getPrivateComments()
    {
        $open = $this->bdd->prepare('SELECT * FROM comments WHERE ref = :key');

        $open->execute(["key" => $this->key]);
        $other_comments = $open->fetchAll();
        $open->closeCursor();
        return array_map(function (array $comment) {
            return [
                "author" => $comment["author"],
                "date" => $comment["date"],
                "link" => "private#comment-" . ((int)$comment["id"] + $this->OFFSET),
                "content" => ($comment["reply"] ? "<p>In reply to href=\"link/#comment-" . $comment["reply"] . "\"\n" : "")
                    . nl2br($comment["content"])
            ];
        }, $other_comments);
    }

    public function getComments()
    {
        $edublog = $this->getEdublogComments();
        $private = $this->getPrivateComments();

        return array_merge($edublog, $private);
    }

    public function getPrivateCommentsCount(): int
    {
        $open = $this->bdd->prepare('SELECT COUNT(*) AS nbr FROM comments WHERE ref = :key');

        $open->execute(["key" => $this->key]);
        $nbr = $open->fetchAll();
        $open->closeCursor();

        return (int)$nbr[0]["nbr"] ?? 0;
    }

    public function addPrivateComment(array $comment): bool
    {
        if (
            isset($comment["author"]) && !empty($comment["author"])
            && isset($comment["content"]) && !empty($comment["content"])
            && isset($comment["ref"]) && !empty($comment["ref"])
        ) {
            $open = $this->bdd->prepare('INSERT INTO comments(author, ref, content, date, reply)
    VALUES(:author, :ref, :content, NOW(), :reply)');
            $reply = (int)$comment["reply"];
            $open->execute([
                "author" => $comment["author"],
                "ref" => $this->key,
                "content" => $comment["content"],
                "reply" => $reply
            ]);
            $open->closeCursor();
            return true;
        }
        return false;
    }
}
