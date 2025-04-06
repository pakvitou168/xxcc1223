<html>

<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            position: relative;
        }

        img {
            width: 100%;
            position: absolute;
            top: 1232px;
            z-index: -1;
        }

        img.without-letterhead {
            display: none;
        }

        /* @font-face {
            font-family: 'Candara';
            src: url({{ public_path('fonts/Candara/Candara.ttf') }});
        } */

        p {
            font-family: Candara, Arial, Helvetica, sans-serif;
            font-size: 12pt;
            position: absolute;
            top: 1320px;
            right: 40px;
            z-index: 1;
            display: block;
            width: 100%;
            text-align: right;
        }
    </style>

    <script>
        function removeHeaderContent() {
            var vars = {};

            var x = document.location.search.substring(1).split('&');

            for (var i in x) {
                var z = x[i].split('=', 2);
                vars[z[0]] = unescape(z[1]);
            }
            var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];

            for (var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];

                if (vars['page'] == 1) { // If page is 1, set display to none
                    document.getElementById("documentNo").style.display = 'none';
                }
            }
        }
    </script>
</head>
<body onload="removeHeaderContent()">
    <img class="{{ !$hasLetterHead ? 'without-letterhead' : '' }}" alt=""
        src="{{ public_path('images/letterhead_header.png') }}">
    <p id="documentNo">{{ $documentNo }}</p>
</body>

</html>
