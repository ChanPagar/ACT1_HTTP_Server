<?php
$ip = '127.0.0.1';
$port = 8080;

$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($server, $ip, $port);
socket_listen($server);

echo "Server listening on http://$ip:$port ...\n";

while (true) {
    $client = socket_accept($server);
    $request = socket_read($client, 1024);

    preg_match('/^(GET) (.*?) HTTP\/1\.[01]/', $request, $matches);
    $method = $matches[1] ?? '';
    $path = $matches[2] ?? '';

    if ($method !== 'GET') {
        $response = "HTTP/1.1 400 Bad Request\r\nContent-Type: text/html\r\n\r\n";
        $response .= "<html><body><h1>400 Bad Request</h1><p>Only GET method is supported.</p></body></html>";
        socket_write($client, $response);
        socket_close($client);
        continue;
    }

    if ($path === '/' || $path === '/index.html') {
        $response = "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\n\r\n";
        $response .= <<<HTML
<html>
<head>
  <title>PHP Socket Server</title>
  <style>
    body { font-family: Arial; background-color: #f0f8ff; text-align: center; padding-top: 50px; }
    h1 { color: #0077cc; }
    p { font-size: 18px; }
  </style>
</head>
<body>
  <h1>Server Running Successfully!</h1>
  <p>This page was served by a <strong>simple PHP Socket HTTP Server</strong>.</p>
  <p>The server returned the HTTP status code: <strong>200 OK</strong>.</p>
  <p>Try visiting a nonexistent path to see the <strong>404 response</strong>.</p>
</body>
</html>
HTML;
    } else {
        $response = "HTTP/1.1 404 Not Found\r\nContent-Type: text/html\r\n\r\n";
        $response .= <<<HTML
<html>
<head>
  <title>404 Not Found</title>
  <style>
    body { font-family: Arial; background-color: #ffe6e6; text-align: center; padding-top: 50px; }
    .box { background: white; padding: 30px; border-radius: 10px; display: inline-block; }
    h1 { color: #cc0000; }
    p { font-size: 18px; }
  </style>
</head>
<body>
  <div class="box">
    <h1>404<br>⚠️ Resource Not Found</h1>
    <p>We couldn't find the page you were looking for on this server.</p>
    <p>The server returned the HTTP status code: <strong>404 Not Found</strong>.</p>
    <p><a href="/">Return to homepage</a></p>
  </div>
</body>
</html>
HTML;
    }

    socket_write($client, $response);
    socket_close($client);
}
?>
