<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/css/bootstrap.min.css" rel="stylesheet" />
        <script src="/js/bootstrap.bundle.min.js"></script>
        <title>[CSMS] <?= $title ?? "" ?></title>
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Active</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#">Disabled</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container mt-3">
            <?= $content ?>
        </div> 

        <footer class="bg-body-tertiary text-center fixed-bottom">
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
                &copy; <?= date("Y") ?> Copyright:
                <a class="text-body" href="mailto:m.bocian@spttil.org.pl">Michał Bocian</a>
            </div>
        </footer>
    </body>
</html>