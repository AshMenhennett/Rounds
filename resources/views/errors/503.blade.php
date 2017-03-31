<!DOCTYPE html>
<html>
    <head>
        <title>Be right back.</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato', sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }

            .go-back-link {
                font-size: 46px;
                color: #525151;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">We Will Be right back.</div>
                 <p><a href="#" class="go-back-link" onClick="window.history.back()">Go Back?</a></p>
            </div>
        </div>
    </body>
</html>
