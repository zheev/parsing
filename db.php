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
    if(!file_exists(DB)){
        $db = new SQLite3(DB);
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
    }else{
        //если бд есть то просто подключ. к ней
        $db = new SQLite3(DB);
    }
}

/**
 * @param array $players
 */
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

function selectAllPlaeyrs()
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