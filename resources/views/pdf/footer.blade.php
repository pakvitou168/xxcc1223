<html>
    <head>
        <style>
            body {
                margin: 0;
                padding: 0;
            }
            img {
                width: 100%;
                position: relative;
                top: 40px;
            }
            img.without-letterhead {
                display: none;
            }
        </style>
    </head>
    <body>
        <img class="{{ !$hasLetterHead ? 'without-letterhead' : '' }}" src="{{ public_path('images/letterhead_footer.png') }}">
    </body>
</html>