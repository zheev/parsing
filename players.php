<?php
/**
 * Created by PhpStorm.
 * User: zheev
 * Date: 22.01.19
 * Time: 21:53
 */


if(!file_exists($_SERVER['PWD'].'/db.php') ||
    !file_exists($_SERVER['PWD'].'/lib.php') ||
    !file_exists($_SERVER['PWD'].'/telegram.php'))
{
    exit('Один из служебных файлов не подключен');
}

require $_SERVER['PWD'].'/lib.php';
require $_SERVER['PWD'].'/constants.php';
require $_SERVER['PWD'].'/db.php';

$html = getHtmlPlayers();

preg_match('/<section class="main">(.*?)<\/section>/ms', $html, $match);

$filterHtml = $match[1];

unset($match);

preg_match_all('/<div class="text">(.*?)<\/div>/ms', $filterHtml, $players);

$playersHtml = $players[1];

unset($players);

$playerFamaly = [];

foreach ($playersHtml as $player)
{
    $playerData = preg_replace('/\s{2,}/',' ', strip_tags($player));
    $playerFamaly[] = explode(' ', $playerData)[1].PHP_EOL;
}

//addPlayer($playerFamaly);

print_r(selectAllPlaeyrs());