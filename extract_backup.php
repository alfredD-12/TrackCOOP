<?php
$logFiles = [
    'C:\\Users\\Pierre Edriz\\.gemini\\antigravity\\brain\\c9773415-03d2-4d43-bd80-a050237c4197\\.system_generated\\logs\\overview.txt',
    'C:\\Users\\Pierre Edriz\\.gemini\\antigravity\\brain\\e9539d4d-900c-411c-807b-e211d4aa4249\\.system_generated\\logs\\overview.txt'
];

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        
        $filesToFind = [
            'file:///c:/xampp/htdocs/TRACKCOOP/admin/admin_dashboard.php',
            'file:///c:/xampp/htdocs/TRACKCOOP/bookkeeper/bookkeeper_dashboard.php'
        ];

        foreach ($filesToFind as $f) {
            echo "Searching for $f in $logFile...\n";
            preg_match_all("/File Path: `$f`.*?Total Lines: \d+\r?\nTotal Bytes: \d+\r?\nShowing lines 1 to (\d+)\r?\n(.*?)(?:The above content does NOT show|The above content shows)/s", $content, $matches);

            if (!empty($matches[2])) {
                // Find the one with the maximum lines shown, representing the full file
                $bestMatchIndex = -1;
                $maxLines = -1;
                for ($i = 0; $i < count($matches[1]); $i++) {
                    if ((int)$matches[1][$i] > $maxLines) {
                        $maxLines = (int)$matches[1][$i];
                        $bestMatchIndex = $i;
                    }
                }

                if ($bestMatchIndex !== -1 && $maxLines > 500) { // Should be a near full file
                    $fileContent = $matches[2][$bestMatchIndex];
                    // Clean up Line Headers: "1: <?php" -> "<?php"
                    $cleaned = preg_replace("/^\d+: /m", "", $fileContent);
                    $cleaned = str_replace("The following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>. Please note that any changes targeting the original code should remove the line number, colon, and leading space.\n", "", $cleaned);
                    
                    $saveName = basename($f) . ".bak";
                    file_put_contents($saveName, trim($cleaned));
                    echo "=> Saved backup for $f as $saveName with $maxLines lines.\n";
                }
            }
        }
    } else {
        echo "Log file not found: $logFile\n";
    }
}
?>
