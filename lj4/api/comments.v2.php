<?php

declare(strict_types=1);

include __DIR__ . '/parse.php';

class Comments
{
    protected $link;
    protected $key;
    protected $bdd;
    protected $OFFSET = 10000;

    public  function __construct(string $link)
    {
        try {
            $this->bdd = new PDO('mysql:host=localhost;dbname=einstein-s-power;charset=utf8', 'lj', 'lj', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (Exception $e) {
        }
        $this->link = $link;
        $this->key = preg_replace("#^[\S\s]+.edublogs.org/([\S\s]+)feed/?$#", "$1", $link);
    }

    public function getEdublogComments(bool $formated = false)
    {
        $edublog_comments = `wget '$this->link' -O /dev/stdout`;
        return $formated ? XMLtoArray($edublog_comments) : $edublog_comments;
    }

    public function getPrivateComments()
    {
        $open = $this->bdd->prepare('SELECT * FROM comments WHERE ref = :key');

        $open->execute(["key" => $this->key]);
        $other_comments = $open->fetchAll();
        $open->closeCursor();
        return array_map(function (array $comment) {
            return [
                "title" => "By: " . $comment["author"],
                "link" => "private#comment-" . ((int)$comment["id"] + $this->OFFSET),
                "dc:creator" => $comment["author"],
                "pubDate" => $comment["date"],
                "description" => $comment["content"],
                "content:encoded" => ($comment["reply"] ? "<p>In reply to href=\"link/#comment-" . $comment["reply"] . "\"\n" : "")
                    . $comment["content"]
            ];
        }, $other_comments);
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
            return true;
        }
        return false;
    }
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

if (isset($_GET['rss'])) {
    $link = $_GET['rss'];
    if (!empty($link) && preg_match("#^https://einsteinpower.edublogs.org#", $link)) {

        header('Content-Type: text/xml');

        $comments = new Comments($link);

        $other_comments = $comments->getPrivateComments();

        if (count($other_comments) === 0) {
            echo $comments->getEdublogComments();
        } else {
            $edublog_comments = $comments->getEdublogComments(true);

            $comments = &$edublog_comments["rss"]["channel"]["item"];

            if (isset($comments["title"])) {
                $comments = [$comments];
            }

            array_push($comments, ...$other_comments);

            include __DIR__ . '/comments.feed.php';
        }
    }
} elseif ($input = file_get_contents('php://input')) {
    $input = json_decode($input, true);
    if ($input && isset($input["comment"])) {
        $new_comment = $input['comment'];
        if (isset($new_comment["ref"])) {
            $ref = $new_comment["ref"];

            $comments = new Comments($new_comment["ref"]);

            echo json_encode([
                "success" => $comments->addPrivateComment($new_comment)
            ]);
        } else {
            echo json_encode([
                "success" => false
            ]);
        }
    }
}
