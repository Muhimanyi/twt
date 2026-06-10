<?php
$files = ['fr.json', 'en.json', 'sw.json', 'ln.json'];
foreach ($files as $f) {
    $path = __DIR__ . '/lang/' . $f;
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    if ($data === null) {
        echo "$f: JSON ERROR - " . json_last_error_msg() . "\n";
    } else {
        $count = count($data);
        $sample = array_slice($data, 0, 3, true);
        echo "$f: Valid JSON, $count keys\n";
        if (isset($data['nav.dashboard'])) echo "  nav.dashboard = {$data['nav.dashboard']}\n";
        if (isset($data['engins.identification'])) echo "  engins.identification = {$data['engins.identification']}\n";
        if (isset($data['conducteurs.form.father_name'])) echo "  conducteurs.form.father_name = {$data['conducteurs.form.father_name']}\n";
        // Check for nested objects
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                echo "  WARNING: Nested array found at key '$k'\n";
            }
        }
    }
    echo "\n";
}
