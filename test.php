<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trumbowyg by Alex-D</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.23.0/ui/trumbowyg.min.css" integrity="sha512-iw/TO6rC/bRmSOiXlanoUCVdNrnJBCOufp2s3vhTPyP1Z0CtTSBNbEd5wIo8VJanpONGJSyPOZ5ZRjZ/ojmc7g==" crossorigin="anonymous" />
</head>
<body class="documentation-body">
<div class="main main-demo-inner">
    <section class="wrapper section">
        <h2 class="section-title">Simple</h2>

        <div class="feature">
            <h3>Basic usage</h3>
            <p>
                Only strong (bold), emphasis (italic), some align, image and link.
            </p>

            <div id="editor">
                <p style="text-align: center;">
                  <strong>Colllect</strong> &mdash; See <em>more</em> at <a href="http://getcollect.io/?ref=trumbowyg">http://colllect.io</a>
                </p>
                <p style="text-align: center;">
                    <img src="http://getcollect.io/images/install/ui.png" alt="Colllect" width="50%">
                </p>
            </div>

            <h4>The code</h4>
            <pre><code class="js-code-to-eval javascript">
$('#editor').trumbowyg({
    btns: [
        ['strong', 'em'],
        ['justifyLeft', 'justifyCenter'],
        ['insertImage', 'link']
    ]
});
            </code></pre>
        </div>

        <div class="feature">
            <h3>Setup</h3>

            <h4>In head tag</h4>
            <pre><code class="html loading-head">
            </code></pre>
            <h4>At the end of body</h4>
            <pre><code class="html loading-body">
&lt;!-- Import jQuery -->
&lt;script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">&lt;/script>
&lt;script>window.jQuery || document.write('&lt;script src="js/vendor/jquery-3.3.1.min.js">&lt;\/script>')&lt;/script>
            </code></pre>
        </div>
    </section>
</div>


<!-- Import jQuery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="../../js/vendor/jquery-3.3.1.min.js"><\/script>')</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.23.0/trumbowyg.min.js" integrity="sha512-sffB9/tXFFTwradcJHhojkhmrCj0hWeaz8M05Aaap5/vlYBfLx5Y7woKi6y0NrqVNgben6OIANTGGlojPTQGEw==" crossorigin="anonymous"></script>
</body>
</html>
