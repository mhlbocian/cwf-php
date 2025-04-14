<!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="<?= Framework\Url::Site("css/style.css", false) ?>"/>
        <title>Authentication Framework - tests site</title>
    </head>
    <body>
        <div id="page-container">
            <h1>Authentication Framework - tests site</h1>
            <hr/>
            <nav>
                <ul>
                    <?php foreach ($sites as $link => $name): ?>
                        <li><a href="<?= Framework\Url::Site("Authenticate/{$link}") ?>"><?= $name ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <hr/>
            <br/>
            <?php if (isset($content)): ?>
                <?= $content ?>
            <?php else: ?>
                <b>No content. When using for the first time and errors below are
                visible, try to create database schema first.</b>
            <?php endif; ?>
            <br/>&nbsp;
            <hr/>
            <nav style="text-align: center;">
                <ul>
                    <li><a href="<?= Framework\Url::Site() ?>">Main page</a></li>
                    <li><a href="<?= Framework\Url::Site("Authenticate") ?>">Auth index</a></li>
                </ul>
            </nav>
        </div>
    </body>
</html>