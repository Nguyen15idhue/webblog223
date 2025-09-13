<?php
/**
 * Hostname Configuration Checker
 * 
 * This script checks if the webblog223.test domain is properly configured
 */

echo "<h1>Hostname Configuration Checker</h1>";

// Check if the domain can be resolved
echo "<h2>Domain Resolution Check</h2>";
$domain = 'webblog223.test';
$ip = gethostbyname($domain);

if ($ip === $domain) {
    echo "<p style='color:red'>❌ The domain '$domain' could not be resolved. It's not properly configured in your hosts file.</p>";
    echo "<p>You need to add the following line to your hosts file (C:\\Windows\\System32\\drivers\\etc\\hosts):</p>";
    echo "<pre>127.0.0.1 $domain</pre>";
} else {
    echo "<p style='color:green'>✅ The domain '$domain' resolves to IP: $ip</p>";
}

// Show server variables
echo "<h2>Server Variables</h2>";
echo "<table border='1' style='border-collapse:collapse'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";
echo "<tr><td>HTTP_HOST</td><td>" . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>SERVER_NAME</td><td>" . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>REQUEST_URI</td><td>" . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>DOCUMENT_ROOT</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</td></tr>";
echo "<tr><td>PHP_SELF</td><td>" . ($_SERVER['PHP_SELF'] ?? 'Not set') . "</td></tr>";
echo "</table>";

// Test URL generation
echo "<h2>URL Generation Test</h2>";
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_url = $protocol . $host;
$api_url = $protocol . $host . '/backend';

echo "<p>Protocol: $protocol</p>";
echo "<p>Host: $host</p>";
echo "<p>Base URL: $base_url</p>";
echo "<p>API URL: $api_url</p>";

// Display current directory structure
echo "<h2>Directory Structure</h2>";
$root_dir = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__FILE__);
echo "<p>Root directory: $root_dir</p>";

function listDir($dir, $depth = 0) {
    $output = '';
    if ($depth > 3) return $output; // Limit depth to avoid huge output
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == "." || $file == "..") continue;
        
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        $isDir = is_dir($path);
        
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
        $output .= "$indent" . ($isDir ? "📁" : "📄") . " $file<br>";
        
        if ($isDir) {
            $output .= listDir($path, $depth + 1);
        }
    }
    return $output;
}

echo "<div style='font-family:monospace'>";
echo listDir($root_dir);
echo "</div>";

// Instructions for fixing host configuration
echo "<h2>How to Fix Host Configuration</h2>";
echo "<ol>";
echo "<li>Open Notepad as Administrator</li>";
echo "<li>Open the file: C:\\Windows\\System32\\drivers\\etc\\hosts</li>";
echo "<li>Add this line at the end of the file: <code>127.0.0.1 webblog223.test</code></li>";
echo "<li>Save the file</li>";
echo "<li>Restart your browser</li>";
echo "</ol>";

// Test connections
echo "<h2>Connection Tests</h2>";
echo "<p>Testing connection to backend API...</p>";

function testUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    curl_close($ch);
    
    echo "<div style='margin-bottom:10px; padding:10px; border:1px solid #ccc'>";
    echo "<strong>URL:</strong> $url<br>";
    echo "<strong>Status:</strong> " . ($info['http_code'] ? $info['http_code'] : 'No response') . "<br>";
    
    if ($error) {
        echo "<strong>Error:</strong> $error ($errno)<br>";
    }
    
    if ($response) {
        echo "<strong>Response:</strong> <pre style='max-height:200px;overflow:auto'>" . htmlspecialchars($response) . "</pre>";
    }
    
    echo "</div>";
}

testUrl('http://webblog223.test');
testUrl('http://webblog223.test/backend');
testUrl('http://webblog223.test/backend/auth/register');
testUrl('http://localhost');

echo "<p>Tests completed.</p>";
