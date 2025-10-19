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

    preg_match("/GET (.*) HTTP/", $request, $matches);
    $path = $matches[1];

    if ($path == '/' || $path == '/index.html') {
        $response = "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\n\r\n";
        $response .= "<h1>Server Running Successfully!</h1>";
    } else {
        $response = "HTTP/1.1 404 Not Found\r\nContent-Type: text/html\r\n\r\n";
        $response .= "<h1>404 Resource Not Found</h1>";
    }

    socket_write($client, $response);
    socket_close($client);
}
?>
