<?php
#has to be ANSI to generate ANSI files >>
ini_set('display_errors', 1);
echo getcwd();
if (isset($argv)) {#cli selft tests
    $stdin = file_get_contents('mails/stdin.log');#die($stdin);
    preg_match('~Subject:(.*)From:~is', $stdin, $m);
    $subject = str_replace('=?utf-8?B', '', trim($m[1]));#d�but
    $subject = str_replace('?=', '', $subject);    #fin
    $subject = base64_decode($subject);
    $s = preg_replace('~[^a-z]+|\-~i', '-', strtolower($subject));
    die("\n" . $s);
}
#sendmail_path="php /home/tests/mail2disk.php"
chdir(__DIR__);
$date = date('Y/m/d-H:i:s');
#php73 $td/mail2disk.php mails/mail-20190130123420.txt
#php73 $td/mail2disk.php mails/a.html
if (0 && isset($argv[1])) {#cela  bloquerait-il le stdin ?
    $f = $argv[1];
    if (!is_file($f)) {
        die('nf:' . $f);
    }
    $x = file_get_contents($f);#chr(61).chr(13).chr(10)
#$xs=str_split($x);foreach($xs as $v){echo"\n$v:".ord($v);}die;

    $f2 = str_replace('.txt', '.html', $f);
    file_put_contents($f2, replace($x));
    die($f2);
}

$input = $stdin = file_get_contents('php://stdin');
#file_put_contents('mails/stdin.log',$stdin);

preg_match('~Subject:(.*)From:~is', $stdin, $m);
if ($m[1]) {
    $subject = str_replace('=?utf-8?B', '', trim($m[1]));#début
    $subject = str_replace('?=', '', $subject);    #fin
    $subject = base64_decode($subject);
    $accents = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'];
    $subject = strtr($subject, $accents);
    $s = trim(preg_replace('~[^a-z0-9]+|\-+~i', '-', strtolower($subject)), '- ');
}


$input = $date . '<hr>' . $input;
$lm = 'mails/0.lastmail';
#=\n/>
if (1) {
    #$input .= date('YmdHis');
    $filename = 'mails/mail-' . gmdate('YmdHis') . '-' . $s . '.txt';
    $retry = 0;
    while (is_file($filename)) {
        $retry++;
        $filename = 'mails/mail-' . gmdate('YmdHis') . '-' . $retry . '-' . $s . '.txt';
    }

    $input = str_replace($m[0], " - $s - ", $input);

    file_put_contents($filename, $input);
    file_put_contents(str_replace('.txt', '.html', $filename), '<html><head><meta charset="UTF-8"></head><body>' . replace($input));
}

file_put_contents($lm . '.txt', $input);
file_put_contents($lm . '.html', '<html><head><meta charset="UTF-8"></head><body>' . replace($input));


$c = replace($input);
file_put_contents('mails/0.lastmailcorrected.html', "\xEF\xBB\xBF" . utf8_encode($c));#iconv("CP1257","UTF-8", $c)
echo "\n" . $s;
return;

function replace($c)
{
    $c = str_ireplace(["\r", '=3D"', '=3D=', '=0D=0A', '=0A'], ["\r", '="', '=', "\n", "\n"], $c);
    $c = preg_replace("~\n+~", "\n", $c);
#"#
    $c = str_ireplace(["=\n", "=0A\s", "=0D\s"], ['', "\n", "\n"], $c);
    $c = str_ireplace(["=\n/>"], ["/>"], $c);
    $c = str_ireplace(["=\"\n=", "=\n"], ['="', '='], $c);
    $c = str_ireplace(["=\"\n=", "=\n"], ['="', '='], $c);
    $c = str_ireplace(["=C2=B0"], ['°'], $c);
    $a = ['=C2=A0=E2=82=AC' => '€', '=C3=A9' => 'é', '=C3=A8' => 'è', '=C3=A0' => 'à', '=C3=B4' => 'ô', '=C3=89' => 'é', '=0A' => ',', '=0D' => '', '=0A' => '', '=09' => ' ', '=C3=A7' => 'ç', '=E2=82=AC' => '€', '=C3=AA' => 'Ê', '=C3=8A' => 'Ê', '=C3=A0' => 'à', '=C3=A9' => 'é', '=C2=A0=C2=A3' => '£', '=C3=A9' => 'é'];
    $c = str_ireplace(array_keys($a), array_values($a), $c);
    $ln = chr(61) . chr(13) . chr(10);
    return str_replace($ln, '', $c);
}

return;
