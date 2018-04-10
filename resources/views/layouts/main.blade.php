<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    @include("includes.globals")

    @yield("page_head")

    <title>@yield("title") | Naps - RIT Student Government</title>

</head>
<body>

@include("includes.nav")

<main id="app">

    @yield("body")

</main>

@include("includes.footer")

@yield("scripts")

</body>
</html>