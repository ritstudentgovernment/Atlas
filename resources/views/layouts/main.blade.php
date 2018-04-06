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

<div id="wrapper">

    @yield("body")

</div>

@include("includes.footer")

</body>
</html>