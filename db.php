<?php
/**
 * Created by PhpStorm.
 * User: zheev
 * Date: 13.01.19
 * Time: 22:03
 */

/**
 * @param $db
 */
function db_connect(&$db)
{
    $isExistsDB = true;

    /**
     *  Проиверим есть ли база, что бы дважды не писать
     *  создание соединения
     */
    if(!file_exists(__dir__.'/'.DB)){
        $isExistsDB = false;
    }

    $db = new SQLite3(__dir__.'/'.DB);

    if(!$isExistsDB){
        $createLinkTable="CREATE TABLE links(
            id INTEGER PRIMARY KEY,
            url TEXT UNIQUE
        )";
        $db->query($createLinkTable);

        $createListPlayers = "CREATE TABLE players(
            id INTEGER PRIMARY KEY,
            family TEXT UNIQUE
        )";

        $db->query($createListPlayers);
    }
}

/**
 * @param array $players
 */
//Добавляем игроков
function addPlayer($players = [])
{
    db_connect($db);

    foreach ($players as $player)
    {
        $arPlay[] = '("'.$player.'")';
    }

    $sql = "insert or ignore into players (family) values ".implode(',', $arPlay);

    $db->query($sql);

    $db->close();
}

function selectAllPlayers()
{
    db_connect($db);

    $sql = 'select * from `players`';

    $result = $db->query($sql);

    $data = [];

    while($d = $result->fetchArray(SQLITE3_ASSOC))
    {
        $data[] = $d['family'];
    }

    return ($data ? $data : []);
}

/**
 * @param array $urls
 */

function addUrl($urls = [])
{
    db_connect($db);

    foreach ($urls as $url)
    {
        $arUrl[] = '("'.$url.'")';
    }

    $sql = 'insert or ignore into links (url) values '.implode(',', $arUrl);

    $db->query($sql);

    $db->close();
}

/**
 * @return Array
 */
function selectAllUrls()
{
    db_connect($db);

    $sql = 'select * from `links`';

    $result = $db->query($sql);

    $data = [];

    while($d = $result->fetchArray(SQLITE3_ASSOC))
    {
        $data[] = $d['url'];
    }

    return ($data ? $data : []);
}