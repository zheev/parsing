<?php
/**
 * Created by PhpStorm.
 * User: zheev
 * Date: 13.01.19
 * Time: 21:42
 */

//Функция для получения html с нужной страницы. Работаем с помощью cUrl
function getHtml()
{
    $ch = curl_init('http://sport.business-gazeta.ru/razdel/484');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $html = curl_exec($ch);
    curl_close($ch);

    return $html;
}

//Функция для заполнения массива необходимыми урлами.
function fillArray($array, &$arTrueLink)
{
    $count = count($array[0]);

    for($i = 0; $i <= $count-1; $i++)
    {
        if(preg_match('/(Рубин|Кудряшов)/ms',$array[1][$i])
        && !preg_match('/((Все трансферы РПЛ))/ms', $array[1][$i]))
        {
            $arTrueLink[] = $array[0][$i];
        }
    }

}

function deleteHtmlTags($html)
{
    return ($html ? preg_replace('/<((\/)|.*?)>/m', '', $html): '');
}

function expressionArticle($html)
{

    if($html)
    {
        preg_match_all('/<article class="article">(.*?)<\/article>/ms', $html, $match);

        $text = preg_replace('/<ul class="share">(.*?)<\/ul>/ms', '',$match[0]);

        $text = preg_replace('/<div class="article-foot">(.*?)<\/div>/ms', '',$text[0]);

        $text = preg_replace('/(&nbsp;)/', ' ',$text);

        $text = preg_replace('/(&hellip;)/', '...',$text);

        preg_match_all('/<img src="(.*?)".+?>/m', $text, $photo);

        $text = preg_replace('/<strong>(.*?)<\/strong>/m','*$1*',$text);

        $text = preg_replace('/<p>(.*?)<\/p>/ms',"\t\t $1 \n\n",$text);

        $text = preg_replace('/\s{2,}/',' ',$text);

        $text = preg_replace('/<h1>(.*?)<\/h1>/m',"\n\n *$1* \n\n",$text);

        $text = preg_replace('/<a href="(.*?)" .+?>(.*?)<\/a>/',"[$2]($1) \n\n",$text);

        if($text)
        {
            return ['text' => deleteHtmlTags($text), 'photo' => $photo[1]];
        }
    }

}

function getTextNote($url)
{

    if($url)
    {
        $ch = curl_init('http://sport.business-gazeta.ru'.$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);

        return expressionArticle($html);

    }

}

function getPhoto($url)
{
    if(!preg_match('/http/', $url))
    {
        $url = 'http:'.$url;
    }

    $image = file_get_contents($url);

    if($image){

        if(!file_exists($_SERVER['PWD'].'/tmp/'))
        {
            mkdir($_SERVER['PWD'].'/tmp/', 755);
        }

        $new_name = $_SERVER['PWD'].'/tmp/'.time().'.jpg';

        file_put_contents($new_name, $image);
    }

    return ['url' => $url, 'filePath' => (isset($new_name)?$new_name:'')];

}