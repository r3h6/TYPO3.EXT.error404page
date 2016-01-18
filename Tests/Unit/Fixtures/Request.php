<?php

namespace Monogon\Page404\Http;

class Request
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function send()
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <title>Error 404</title>
</head>
<body>
<h1>Error 404</h1>
<p>Ther current url ###CURRENT_URL### can not be displayed.</p>
<p>Reson: ###REASON###</p>
</body>
</html>
        ';
    }
}
