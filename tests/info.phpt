--TEST--
phpinfo() displays zstd info
--SKIPIF--
--FILE--
<?php
$ref = new ReflectionExtension('zstd');
ob_start();
$ref->info();
$info = ob_get_contents();
ob_end_clean();

// skip uninteresting lines
$lines = [];
foreach(explode("\n", $info) as $line) {
    if ($line && $line !== 'zstd') {
        $lines[] = $line;
    }
}

$verNum = '(([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{1,2}))';

if (count($lines) >= 4) {
    echo preg_match('/^Extension\sversion\s\=\>\s'.$verNum.'$/', $lines[0]) ? "Ext version OK\n" : "Fail\n";

    $libZstd = '(bundled|external)';
    $hasApcu = null;

    if (file_exists($configH = dirname(__DIR__) . '/config.h')) {
        $configH = file_get_contents($configH);
        $libZstd = preg_match('/define\sHAVE_BUNDLED_ZSTD\s1/', $configH) ? 'bundled' : 'external';
        $hasApcu = preg_match('/define\sHAVE_APCU_SUPPORT\s1/', $configH) ? true : false;
    }

    echo preg_match('/^Zstd\slibrary\s\=\>\s'.$libZstd.'$/', $lines[1]) ? "Bundled/external zstd OK\n" : "Fail\n";

    echo preg_match('/^Zstd\sinterface\sversion\s\=\>\s'.$verNum.'$/', $lines[2]) ? "Zstd version OK\n" : "Fail\n";

    if ($hasApcu === null) {
        echo "Apcu OK\n"; // just assume it is okay
    } else if ($hasApcu) {
        if (substr($lines[3], 0, 18) !== 'APCu serializer =>') {
            echo "Fail\n";
        } else {
            $fail = '';
            $value = substr($lines[3], 19);

            if (!extension_loaded('apcu')) {
                if ($value !== 'APCu extension not loaded') {
                    $fail .= 'should be not loaded ';
                }
            } else if (!in_array($value, ['zstd active', 'zstd inactive'])) {
                $fail .= 'active/inactive mismatch ';
            }

            if (
                !isset($lines[4])
                ||
                !preg_match('/^APCu\sserializer\sinterface\sversion\s\=\>\s([0-9])/', $lines[4])
            ) {
                $fail .= 'ABI ';
            }

            echo !$fail ? "Apcu OK\n" : ("Fail: " . $fail . "");
        }
    } else {
        echo ($lines[3] == 'APCu serializer support => not built') ? "Apcu OK\n" : "Fail: not built\n";
    }

    if (PHP_VERSION_ID >= 80000) {
        $search = 'Built-in output compression exclusions => ';
        $mimeIndex = null;
        // could be 4 or 5 depending on apcu
        foreach([4, 5] as $index) {
            if (isset($lines[$index]) && substr($lines[$index], 0, 42) == $search) {
                $mimeIndex = $index;
            }
        }
        if ($mimeIndex) {
            $types = explode(', ', substr($lines[$mimeIndex], 42));
            $invalidTypes = [];
            foreach($types as $type) {
                if (!preg_match('/^([a-z]+)\/(([a-z0-9\-\.]+)|\*)$/', $type)) {
                    $invalidTypes[] = $type;
                }
            }
            if ($invalidTypes) {
                echo "Fail: " . implode(", ", $invalidTypes) . "\n";
            } else {
                echo "MIMEs OK\n";
            }
        } else {
            echo "Fail\n";
        }
    } else {
        echo "MIMEs OK\n";
    }
}
--EXPECTF--
Ext version OK
Bundled/external zstd OK
Zstd version OK
Apcu OK
MIMEs OK
