<?php

    include 'header.php';

    require_once('../mydb_pdo.php');

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    /******** GET PLAYS ********/
    $sql = "SELECT playdata.id, playdata.name, playdata.file, category.name AS cat, category.id AS catid,
    playdata.thumbsup, playdata.thumbsdown, playdata.rated, playdata.rate, playdata.ratecount, playdata.comments, playdata.movements, playdata.created_on, users.id AS userid, playdata.id, users.name AS user, playdata.tags FROM playdata JOIN users ON (users.id = playdata.userid) JOIN category ON (category.id = playdata.catid)  WHERE playdata.`private`='0' AND playdata.copied=0
    AND playdata.featured = 1
    ";

    // Initialize statement for limited fetch
    $st = $conn->prepare( $sql );

    $st->execute();
    $plays = array();

    while ( $row = $st->fetch() )
    {
        $plays[] = $row;
    }
    /******** END GET PLAYS ********/

    /******** GET FEATURED USERS ********/
    $sql = "SELECT * FROM users WHERE featured = 1";

    $st = $conn->prepare($sql);
    $st->execute();

    $feat_users = array();

    while ( $row = $st->fetch() )
    {
        $feat_users[] = $row;

    }
    /******** END GET FEATURED USERS ********/

    // echo json_encode($feat_users);
    // echo $sql;
    // echo $play_count[0];
    $conn = null;
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Manage Featured - Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="../css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/bp-main.css?version=1.2">
    </head>

    <body class="pb-4" style="background-color: #f1f1f1">
        <nav class="navbar navbar-expand-xl navbar-light bg-light mb-4">
            <div class="container">
                <a class="navbar-brand text-secondary" href="/playbook/admin/index.php">admin home</a>
            </div>
        </nav>

        <div class="container">
            <h2>Manage Featured Items</h2>
            <hr>
            <div class="row">
                <div class="col-md-6">

                    <form class="form" action="add_featured.php" method="POST">
                        <input type="hidden" name="type" value="featured_user">
                        <input id="feat_user_id" type="hidden" name="id" value="">

                        <div class="input-group">
                            <input id="search-user" type="text" class="form-control search-featured" name="user_name"  autocomplete="off" data-type="featured_user" placeholder="add contributor" required/>

                            <span class="input-group-btn">
                                <button id="submit-user" class="btn btn-success" type="submit" disabled><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </form>
                    <div id="user-results-view" aria-labelledby="dLabel" class="results-view">
                        <ul id="user-results-list" class="list-unstyled">

                        </ul>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            Featured Contributors (<?= count($feat_users) ?> total)
                        </div>
                        <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>name</th>
                                        <th>manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($feat_users as $key => $feat_user) { ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $feat_user['email'] ?></td>
                                            <td>
                                                <form class="form" action="remove_featured.php" method="POST">
                                                    <input type="hidden" name="type" value="featured_user">
                                                    <input type="hidden" name="id" value="<?= $feat_user['id'] ?>">
                                                    <button type="submit" class="btn btn-danger"><i class="fa fa-minus"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php ++$i; } ?>
                                </tbody>
                            </table>
                    </div>
                </div>

                <div class="col-md-6">

                    <form class="form" action="add_featured.php" method="POST">
                        <input type="hidden" name="type" value="featured_play">
                        <input id="feat_play_id" type="hidden" name="id" value="">

                        <div class="input-group">
                            <input id="search-play" type="text" class="form-control search-featured" name="play_name"  autocomplete="off" data-type="featured_play" placeholder="add play" required/>

                            <span class="input-group-btn">
                                <button id="submit-play" class="btn btn-success" type="submit" disabled><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </form>
                    <div id="play-results-view" aria-labelledby="dLabel" class="results-view">
                        <ul id="play-results-list" class="list-unstyled">

                        </ul>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            Featured Plays (<?= count($plays) ?> total)
                        </div>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>name</th>
                                    <th>manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($plays as $key => $play) { ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $play['name'] ?></td>
                                        <td>
                                            <form class="form" action="remove_featured.php" method="POST">
                                                <input type="hidden" name="type" value="featured_play">
                                                <input type="hidden" name="id" value="<?= $play['id'] ?>">
                                                <button type="submit" class="btn btn-danger"><i class="fa fa-minus"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php ++$i; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </body>

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <!-- <script src="js/vendor/retina.js"></script> -->

    <script>
        $('.search-featured').on('input', function()
        {
            console.log('search-featured input changed! ', $(this).val());

            var type = $(this).attr('data-type');

            var $submitBtn = type === 'featured_user' ? $('#submit-user') : $('#submit-play');
            $($submitBtn).attr('disabled', true);

            var $resultsView = type === 'featured_user' ? $('#user-results-view') : $('#play-results-view');
            var $resultsList = $resultsView.find('ul');

            var $otherResultsView = type !== 'featured_user' ? $('#user-results-view') : $('#play-results-view');
            $($otherResultsView).removeClass('visible');

            console.log('$resultsView? ', $resultsView);
            console.log('resultsList? ', $resultsList);
            console.log('search for type: ', type);

            if ($(this).val() != '')
            {
                // hide errors
                // toggleElement('hide', $('#customer-info .text-danger'));
                // showStatusMessage('#customer-info', 'success');

                var searchString = $(this).val();

                console.log('searchString? ', searchString);

                // Send the data using post
                var posting = $.post( 'search_featured.php', {searchString: searchString, type: type},
                    function( data )
                    {
                        console.log('Posted data? ', data);

                        // if data returned no errors
                        if (data.success)
                        {
                            console.log('search success data: ', data);

                            var results = data.results;
                            var type = data.type;

                            if (results.length > 0)
                            {
                                $resultsList.html('');

                                $.each(results, function(index, result)
                                {
                                    console.log('type? ', type);
                                    $string = '';
                                    if (type == 'featured_user')
                                    {
                                        $string = result.email;
                                        //$string = result.name == '' ? '<strong>' + result.email + '</strong>'  : '<strong>' + result.name + '</strong> - ' + result.email;
                                    } else {
                                        $string = result.name;
                                    }
                                    $resultsList.append('<li class="search-li" data-id="' + result.id + '" data-type="' + type + '">' + $string + '</li>');
                                });

                                // show results view
                                $($resultsView).addClass('visible');

                            } else {

                                console.log('No results found!');
                            }


                        } else {
                            console.log('featured search error : ', data.error);
                        }
                    } ,'json' );

            } else {

                console.log('input is empty!');

                // hide results view
                var $resultsView = type === 'featured_user' ? $('#user-results-view') : $('#play-results-view');

                $($resultsView).removeClass('visible');

                // reset inputs
                // $('#appt-customer-id').val('');
                // $('#appt-pet-select').html('');

                // show error message
                // showStatusMessage('#customer-info', 'error');
            }
        });

        $(document).on("click", ".search-li", function(e)
        {
            console.log('search result clicked! ', e);

            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');

            console.log('search result clicked id? ' + id + ' type? ' + type);

            var $resultsView = type === 'featured_user' ? $('#user-results-view') : $('#play-results-view');

            var $idInput = type === 'featured_user' ? $('#feat_user_id') : $('#feat_play_id');
            var $searchInput = type === 'featured_user' ? $('#search-user') : $('#search-play');

            console.log('this html? ', $(this).html());

            // update the search input with the selected name
            $($searchInput).val($(this).html());

            // update the inputId with the selected items id
            $($idInput).val(id);

            // hide results view
            $($resultsView).removeClass('visible');

            var $submitBtn = type === 'featured_user' ? $('#submit-user') : $('#submit-play');
            $($submitBtn).attr('disabled', false);

        });
    </script>

</html>
