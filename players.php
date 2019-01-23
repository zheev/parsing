<?php
/**
 * Created by PhpStorm.
 * User: zheev
 * Date: 22.01.19
 * Time: 21:53
 */


if( !file_exists($_SERVER['PWD'].'/db.php') ||
    !file_exists($_SERVER['PWD'].'/lib.php') ||
    !file_exists($_SERVER['PWD'].'/constants.php'))
{
    exit('Один из служебных файлов не подключен');
}

require $_SERVER['PWD'].'/lib.php';
require $_SERVER['PWD'].'/constants.php';
require $_SERVER['PWD'].'/db.php';

//получим html код списка игроков и тренеров
$html = getHtmlPlayers();
//убираем лишнее из html кода, осавляем общий блок со списком
preg_match('/<section class="main">(.*?)<\/section>/ms', $html, $match);
//запишем в переменную html код. Чтобы очистить массив и не хранить в памяти
$filterHtml = $match[1];
//удаляем массив
unset($match);
// получаем имя и фамилию игроков
preg_match_all('/<div class="text">(.*?)<\/div>/ms', $filterHtml, $players);
//запишем в переменную, чтобы очистить массив
$playersHtml = $players[1];
//удаляем массив
unset($players);
//обяъявим переменную  как массив
$playerFamaly = [];
//циклом проходим по всем полученным игрокам и тренерам
foreach ($playersHtml as $player)
{
    //удаляем повторяющие пробелы и заменяем их на 1
    $playerData = preg_replace('/\s{2,}/',' ', strip_tags($player));
    //строку делим на пробелы и записываем фамилии
    $playerFamaly[] = explode(' ', $playerData)[1].PHP_EOL;
}

//запишем фаимилии в бд
addPlayer($playerFamaly);