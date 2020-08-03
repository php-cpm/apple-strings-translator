<?php
$shebang = "#!/usr/bin/env php";
$chmod = true;
$main = 'cmd.php';
$filename = '../asting.phar';
if (file_exists($filename)) unlink($filename);
$phar = new Phar($filename);
//$phar->buildFromDirectory(__DIR__.'/phar/', '/\.php$/');
$phar->buildFromDirectory(__DIR__ . '/');
$phar->compressFiles(Phar::GZ);
$phar->setAlias("asting.phar");
$phar->setStub(($shebang ? $shebang . PHP_EOL : "") . $phar->createDefaultStub($main));
if ($chmod) {
    chmod($filename, fileperms($phar) | 0700);
}
