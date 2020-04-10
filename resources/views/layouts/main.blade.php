<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    @include("includes.globals")

    @yield("page_head")

    <title>@yield("title") | {{ env('APP_NAME') }} - {{ env('ORG_NAME') }}</title>

</head>
<body>

@include("includes.nav")

<main id="app">

    @yield("body")

</main>

@yield("scripts")

@include("includes.footer")

</body>
</html>