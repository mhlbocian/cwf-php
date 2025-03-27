<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?= Framework\Url::Local("css/style.css") ?>" />
        <title>CWF-PHP: <?= $title ?? "Untitled page" ?></title>
    </head>
    <body>
        <div id="page-container">
            <header>
                <h1>CWF-PHP Framework</h1>
            </header>
            <hr />
            <nav>
                <ul>
                    <?php foreach ($menu as $item): ?>
                        <?php if (!is_null($item["url"])) : ?>
                            <li>
                                <a href="<?= $item["url"] ?>">
                                    <?= $item["description"] ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li><b><?= $item["description"] ?></b></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <hr />
            <h3><?= $title ?? "Untitled page" ?></h3>
            <?= $content ?? "<p>No content.</p>" ?>
            <hr />
            <footer>
                &copy; <?= date("Y") ?> <?= APPNAME ?> <i>(<?= APPDES ?>)</i> ver. <?= APPVER ?>
                &bullet; visit: <a href="https://github.com/mhlbocian/cwf-php" target="_blank">github</a>
            </footer>
        </div>
    </body>
</html>