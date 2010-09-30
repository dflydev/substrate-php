<?php

$testRoot = dirname(__FILE__);
$substratePackageRoot = dirname($testRoot);

if ( file_exists($testRoot . '/bootstrap-site-pre.php') ) require_once($testRoot . '/bootstrap-site-pre.php');

// Substrate package library location
$classpath[] = $substratePackageRoot . '/lib';

// Substrate package vendor library root
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

if ( file_exists($testRoot . '/bootstrap-site-post.php') ) require_once($testRoot . '/bootstrap-site-post.php');

?>
