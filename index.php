<?php
require_once 'twitter_api.php';
require_once 'regexp.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <title>Document</title>
    <style>
        p{
            text-align: left;
        }
        .date{
            text-align: center;
        }
        .single-tweet{
            padding-left: 10px;
            text-align:center;
            width:500px;
            border: 1px solid black;
            border-radius: 10px;
            margin: auto;
        }
        img{
            border-radius: 20px ;
        }
    </style>
</head>
<body>
<div>

</div>
    <div id="refresh">
        <?php for($i=0;$i<count($names);$i++):?>
            <div class="single-tweet">
                <?php if(preg_match('/^RT/',$texts[$i])):?>
                    <img src="<?=$rt_image[$i]?>">
                    <a href="https://twitter.com/<?=$rt_name[$i]?>"><?=$rt_screen_name[$i]?></a>
                    <span>@<?=$rt_name[$i]?></span>
                    <p><?=link_it(twitter_it($texts[$i]))?></p>
                    <p class="date"><?=substr($created_at[$i],0,20)?></p>
                    <a href="https://twitter.com/<?=$names[$i]?>/status/<?=$source[$i]?>">View source</a>
                <?php else:?>
                    <img src="<?=$images[$i]?>">
                    <a href="https://twitter.com/<?=$screen_names[$i]?>"><?=$names[$i]?></a>
                    <span>@<?=$screen_names[$i]?></span>
                    <p><?=link_it(twitter_it($texts[$i]))?></p>
                    <p class="date"><?=substr($created_at[$i],0,20)?></p>
                    <a href="https://twitter.com/<?=$names[$i]?>/status/<?=$source[$i]?>">View source</a>
                <?php endif;?>

            </div>
        <br>
        <?php endfor; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>
