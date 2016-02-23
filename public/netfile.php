
<?php
$file = 'K:\New%20Database\Reports\DIRECT%20MAIL%20COUNT%20REQUEST%20MM.DOC';
$file = 'K:/New%20Database/Reports/DIRECT%20MAIL%20COUNT%20REQUEST%20MM.DOC';

$handle = fopen($file, 'r');

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}
else { echo "nope"; }
?>
