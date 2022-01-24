<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex" />
    <meta name="description" content="Welcome to the Eureka Platform" />

    <title>Eureka</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="icon" href="{{ asset('images/icon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}" />
    <meta name="application-name"         content="Eureka" />
    <meta name="msapplication-TileColor"  content="#f8f9fa" />
    <meta name="msapplication-TileImage"  content="{{ asset('images/icon.png') }}" />
    <meta property="og:type"              content="website" />
    <meta property="og:site_name"         content="Eureka" />
    <meta property="og:url"               content="{{ config('app.url', 'https://http://eureka-app.com') }}" />
    <meta property="og:title"             content="Eureka" />
    <meta property="og:description"       content="Welcome to the Eureka Platform" />
    <meta property="og:image"             content="{{ asset('images/icon.png') }}" />
    <meta property="og:image:alt"         content="Eureka logo" />
    <meta name="twitter:card"             content="summary" />
    <meta name="twitter:url"              content="{{ config('app.url', 'https://http://eureka-app.com') }}" />
    <meta name="twitter:title"            content="Eureka" />
    <meta name="twitter:description"      content="Welcome to the Eureka Platform" />
    <meta name="twitter:image"            content="{{ asset('images/icon.png') }}" />
  </head>
  <body>
    <div id="root"></div>
    <script src="{{ asset('js/app.js') }}" ></script>
  </body>
</html>
