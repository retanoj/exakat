<?php

if (!array_key_exists("i", $_GET) || !is_numeric($_GET["i"]))
    die("Index unspecified or non-numeric");
$index = (int) $_GET["i"];
    
$arch = RarArchive::open("example.rar");
if ($arch === FALSE)
    die("Cannot open example.rar");

$entries = $arch->getEntries();
if ($entries === FALSE)
    die("Cannot retrieve entries");

if (!array_key_exists($index, $entries))
    die("No such index: $index");

$orfilename = $entries[$index]->getName(); //UTF-8 encoded

$filesize = $entries[$index]->getUnpackedSize();

/* you could check HTTP_IF_MODIFIED_SINCE here and compare with
 * $entries[$index]->getFileTime(). You could also send a
 * "Last modified" header */

$fp = $entries[$index]->getStream();
if ($fp === FALSE)
    die("Cannot open file with index $index insided the archive.");

$arch->close(); //no longer needed; stream is independent

function detectUserAgent() {
    if (!array_key_exists('HTTP_USER_AGENT', $_SERVER))
        return "Other";
    
    $uas = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match("@Opera/@", $uas))
        return "Opera";
    if (preg_match("@Firefox/@", $uas))
        return "Firefox";
    if (preg_match("@Chrome/@", $uas))
        return "Chrome";
    if (preg_match("@MSIE ([0-9.]+);@", $uas, $matches)) {
        if (((float)$matches[1]) >= 7.0)
            return "IE";
    }
    
    return "Other";
}

/*
 * We have 3 options:
 * - For FF and Opera, which support RFC 2231, use that format.
 * - For IE and Chrome, use attwithfnrawpctenclong
 *   (http://greenbytes.de/tech/tc2231/#attwithfnrawpctenclong)
 * - For the others, convert to ISO-8859-1, if possible
 */
$formatRFC2231 = 'Content-Disposition: attachment; filename*=UTF-8\'\'%s';
$formatDef = 'Content-Disposition: attachment; filename="%s"';

switch (detectUserAgent()) {
    case "Opera":
    case "Firefox":
        $orfilename = rawurlencode($orfilename);
        $format = $formatRFC2231;
        break;

    case "IE":
    case "Chrome":
        $orfilename = rawurlencode($orfilename);
        $format = $formatDef;
        break;
    default:
        if (function_exists('iconv'))
            $orfilename =
                @iconv("UTF-8", "ISO-8859-1//TRANSLIT", $orfilename);
        $format = $formatDef;
}

header(sprintf($format, $orfilename));
//cannot send error messages from now on (headers already sent)

//replace by real content type, perhaps infering from the file extension
$contentType = "application/octet-stream";
header("Content-Type: $contentType");

header("Content-Transfer-Encoding: binary");

header("Content-Length: $filesize");

if ($_SERVER['REQUEST_METHOD'] == "HEAD")
    die();
    
while (!feof($fp)) {
    $s = @fread($fp, 8192);
    if ($s === false)
        break; //useless to send error messages
  
    echo $s;
}
?>