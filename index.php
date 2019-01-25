<?php
/**
 * Created by PhpStorm.
 * User: zheev
 * Date: 12.01.19
 * Time: 23:09
 */


if( !file_exists(__dir__.'/db.php') ||
    !file_exists(__dir__.'/lib.php') ||
    !file_exists(__dir__.'/telegram.php'))
{
    exit('Один из служебных файлов не подключен');
}

if(!file_exists(__dir__.'/constants.php'))
{
    exit('Файл с константами \'constants.php\' в корне сайта не создан');
}

require_once __dir__.'/constants.php';
require_once __dir__.'/db.php';
require_once __dir__.'/lib.php';
require_once __dir__.'/telegram.php';


$html = getHtml();

if(strlen($html) <= 0)
{
    exit('Не удалось получить html-код с сайта');
}
writeLog(date('d').'.'.date('m'). '.'.date('Y').' '.date('G').' Скрипт запущен');

// получение статей
preg_match('/<ul class="mostabs mostabs-tile">(.*?)<\/ul>/ms', $html, $match);

$articlesListHtml =  $match[1];

unset($match);

preg_match_all('/<a class="moslist__title" href="(.*?)">(.*?)<\/a>/', $articlesListHtml, $match);

//приведём к общему виду, чтобы проще было работать

$articlesList = [$match[1], $match[2]];

//получение новостей

preg_match('/<nav class="l-col_3 page-nav">(.*?)<\/nav>/ms', $html, $mathes);

preg_match_all('/<div class="news-box_1">(\s*)(<strong>|[^<strong>])<a class="news-box_1-title" href="(.*?)">(.*?)<\/a>(<\/strong>|[^<\/strong>])(.*?)<\/div>/ms', $mathes[0], $arLink);

$dataList = [array_merge($articlesList[0],$arLink[3]), array_merge($articlesList[1], $arLink[4])];

unset($arLink);

unset($articlesList);

//работа с данными

$arTrueLink = [];

fillArray($dataList, $arTrueLink);

$urlsFromDb = selectAllUrls();

foreach ($arTrueLink as $url)
{
    if(count($urlsFromDb) > 0){
        if(!in_array($url, $urlsFromDb))
        {
            sendMessage($url);
            writeLog(date('d').'.'.date('m'). '.'.date('Y').' '.date('G').' Отправлена новость '.$url);
        }
    }else{

        sendMessage($url);
        writeLog(date('d').'.'.date('m'). '.'.date('Y').' '.date('G').' Отправлена новость '.$url);

    }

}

unset($urlsFromDb);

addUrl($arTrueLink);

