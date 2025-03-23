<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/style.css" />
        <title>[CWF] <?= $title ?? "" ?></title>
    </head>
    <body>
        <div id="page-container">
            <header>
                <h1>Custom Web Framwework for PHP</h1>
            </header>
            <hr />
            <section>
                <article>
                    <h3><?= $page ?? "" ?></h3>
                    <?= $content ?? "" ?>
                    <p style="text-align: right;">
                        <a href="<?= $link ?? "/" ?>">Switch page</a>
                    </p>
                </article>
            </section>
            <hr />
            <footer>
                <p>
                    <b>Application info:</b> <?= APPNAME ?>
                    <i>(<?= APPDES ?>)</i> &bullet; Version: <?= APPVER ?>
                </p>
                <p>
                    &copy; <?= date("Y") ?> CWF by
                    <a href="mailto:mhl.bocian@gmail.com">Michał Bocian</a>
                </p>
            </footer>
        </div>
    </body>
</html>