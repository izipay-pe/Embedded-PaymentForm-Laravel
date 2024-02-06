<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/styles/dracula.min.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/highlight.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });
        });
    </script>
    <style>
        body{
            background-color: #FF2D46;
        }
        h1, h2, h3, a{ color: #ffffff;}
    </style>
</head>

<body>
    <div class="container">
        <h1>Transaction paid !</h1>
        <h3>Raw POST data received:</h3>
        <pre><code class="json">{{ $dataPost }}</code></pre>

        <h3>Parsed POST["kr-answer"] data:</h3>
        <pre><code class="json">{{ $formAnswer }}</code></pre>
    </div>

    
    <h1><a href="/">Back to home</a></h1>

</body>

</html>
