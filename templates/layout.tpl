<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{block name=title}Blog{/block}</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <header class="site-header">
            <div class="container site-header__inner">
                <a class="site-header__logo" href="/">Blog</a>
            </div>
        </header>
        <main class="container">
            {block name=content}{/block}
        </main>
    </body>
</html>
