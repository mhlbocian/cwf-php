<?php

use Framework\Url;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?= Url::Local("css/style.css") ?>" />
        <title>[CWF] <?= $title ?? "" ?></title>
    </head>
    <body>
        <div id="page-container">
            <header>
                <h1>Custom Web Framwework for PHP</h1>
            </header>
            <hr />
            <nav>
                <ul>
                    <?php foreach ($menu as $item): ?>
                        <?php if (!is_null($item[0])) : ?>
                            <li><a href="<?= $item[0] ?>"><?= $item[1] ?></a></li>
                        <?php else: ?>
                            <li><b><?= $item[1] ?></b></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <hr />
            <section>
                <article>
                    <h3><?= $title ?? "" ?></h3>
                    <?= $content ?? "" ?>
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