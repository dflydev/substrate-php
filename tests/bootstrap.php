<?php

if ( file_exists('bootstrap-site-pre.php') ) require_once('bootstrap-site-pre.php');

$substratePackageRoot = dirname(dirname(__FILE__));

$classpath[] = $substratePackageRoot . '/configs';
$classpath[] = $substratePackageRoot . '/lib';

$relativeVendors = $substratePackageRoot . '/vendors';

if ( $dirHandle = opendir($relativeVendors) ) {
    $vendorPaths = array();
    while ( ($potentialVendorDir = readdir($dirHandle)) !== false ) {
        $potentialVendorPath = $relativeVendors . DIRECTORY_SEPARATOR . $potentialVendorDir;
        if ( ( preg_match('/^[^\.]/', $potentialVendorDir) ) and is_dir($potentialVendorPath) ) {
            $classpath[] = realpath($potentialVendorPath);
        }
    }
    closedir($dirHandle);
}

if ( file_exists('bootstrap-site-post.php') ) require_once('bootstrap-site-post.php');

?>
