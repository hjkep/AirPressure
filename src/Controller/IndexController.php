<?php

namespace AirPressure\Controller;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class IndexController {
    public function serve(Request $request, Response $response, $args): Response {
        $response->getBody()->write(file_get_contents(APP_DIR.'/index.html'));
        return $response;
    }
}