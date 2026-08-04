--TEST--
zstd.output_compression_exclude_types exact match
--SKIPIF--
<?php
include (dirname(__FILE__) . '/ob_skipif.inc');
?>
--INI--
zstd.output_compression=1
--ENV--
HTTP_ACCEPT_ENCODING=zstd
--GET--
ob=020
--FILE--
<?php
header('Content-Type: application/pdf');
echo "hi\n";
?>
--EXPECT--
hi
