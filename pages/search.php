<div class="row">
    <div class="col-md-12">
    <?php
        if(isset($_GET['q'])){
            $input = $purifier->purify(htmlentities($_GET['q']));

            echo'<h1>Search results for "' . $input . '"</h1><hr />';

            echo $search->sQuery($input);
        }
    ?>
    </div>
</div>