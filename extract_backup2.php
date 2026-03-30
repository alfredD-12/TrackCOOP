<?php
$logFiles = [
    'C:/Users/Pierre Edriz/.gemini/antigravity/brain/e9539d4d-900c-411c-807b-e211d4aa4249/.system_generated/logs/overview.txt'
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
            preg_match_all("/File Path: `$f`.*?Total Lines: \d+\r?\nTotal Bytes: \d+\r?\nShowing lines (\d+) to (\d+)\r?\n(.*?)(?:The above content does NOT show|The above content shows)/s", $content, $matches);

            if (!empty($matches[3])) {
                // Find the one with highest line coverage or specific logic
                $bestMatchIndex = -1;
                $maxLines = -1;
                for ($i = 0; $i < count($matches[1]); $i++) {
                    $start = (int)$matches[1][$i];
                    if ($start === 1) {
                        $lines = (int)$matches[2][$i] - $start;
                        if ($lines > $maxLines) {
                            $maxLines = $lines;
                            $bestMatchIndex = $i;
                        }
                    }
                }

                if ($bestMatchIndex !== -1 && $maxLines > 400) {
                    $fileContent = $matches[3][$bestMatchIndex];
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
