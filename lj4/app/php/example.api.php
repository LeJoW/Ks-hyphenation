<?php

const ESCAPE_KEY = "_cdk12332123443234554345665456776567887678998789009890cdk_";

function before(string $text): string
{
    $text = str_replace("=", ESCAPE_KEY, $text);
    $text = preg_split("/(\s)/", $text);
    $text = array_map(function ($word) {
        return !preg_match("/^\s+$/", $word) && $word !== ""
            ? preg_replace([
                "/([\wáàéèiíìóòúùýæœǽ]+('|’))/i",
                "/([^\wáàéèiíìóòúùýæœǽ]+)/i"
            ], ["", ""], $word)
            : "\n";
    }, $text);
    $text = join("", $text);
    $text = "$text\n";
    return $text;
}

function after(string $text, string $hyphed): string
{
    if (strlen($text) > 0) {
        $hyphed = str_replace("\n", " ", $hyphed);
        $hyphed = preg_split('/(' . ESCAPE_KEY . ')/', $hyphed, -1, PREG_SPLIT_DELIM_CAPTURE);
        array_walk($hyphed, function (string &$e, int $i) use ($text) {
            $out = "";
            $outStore = "";
            if ($e !== ESCAPE_KEY) {
                $lettersH = $e;
                $lettersO = preg_split("/(=)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
                $lettersO = $lettersO[$i];
                $idH = 0;
                $idO = 0;
                for (
                    $i = 0;
                    strlen($outStore) == 0 || strlen($outStore) < strlen($out);
                    $i++
                ) {
                    $outStore = $out;
                    $lH = isset($lettersH[$idH]) ? $lettersH[$idH] : "";
                    $lO = isset($lettersO[$idO]) ? $lettersO[$idO] : "";
                    if ($lO && strtolower($lO) === $lH) {
                        $idH++;
                        $idO++;
                        $out .= $lO;
                    } else if ($lH === "=") {
                        $idH++;
                        $out .= "-";
                    } else if ($lO) {
                        $idO++;
                        $out .= $lO;
                    }
                }
            } else {
                $out .= "=";
            }
            $e = $out;
        });
        $text = join("", $hyphed);
    } else {
        $text = "";
    }
    return $text;
}

if (isset($_GET['lang']) && isset($_GET['body'])) {
    $lang = $_GET['lang'];
    $content = $_GET['body'];
    $content = before($content);
    $pwd = __DIR__;
    switch ($lang) {
        case 'fr-FR':
            $dic = 'hyph_fr.dic';
            break;
        case '_fr-FR':
            $dic = 'hyph_fr.dic.1';
            break;
        case 'la-VA':
            $dic = 'hyph_la_VA.dic';
            break;
        case 'en-US':
            $dic = 'hyph_en_US.dic';
            break;
    }
    $text = `echo "$content" | $pwd/bin/hyph/example $pwd/bin/hyph/$dic /dev/stdin`;
    echo after($content, $text);
}
