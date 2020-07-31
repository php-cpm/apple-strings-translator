<?php
$shebang = "#!/usr/bin/env php";
$chmod = true;
$main = 'cmd.php';
$filename = '../point.phar';
if (file_exists($filename)) unlink($filename);
$phar = new Phar($filename);
//$phar->buildFromDirectory(__DIR__.'/phar/', '/\.php$/');
$phar->buildFromDirectory(__DIR__ . 'package.php/');
$phar->compressFiles(Phar::GZ);
$phar->setAlias("point.phar");
$phar->setStub(($shebang ? $shebang . PHP_EOL : "") . $phar->createDefaultStub($main));
if ($chmod) {
    chmod($filename, fileperms($phar) | 0700);
}
