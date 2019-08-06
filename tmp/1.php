<?php
$city=1780; // Новосибирск(код Новосибирска с сайта, указанного ниже,
$cache_file=$_SERVER['DOCUMENT_ROOT']."/tmp/pogoda".$city.".cch"; //указываем путь к картинкам
if (file_exists( $cache_file ) && // Проверка существования кэш файла
	date('Y-m-d',filemtime($cache_file))==
	date("Y-m-d", mktime(0, 0, 0, date("m") , date("d"), date("Y")))){
   readfile($cache_file);
}else{ // создание кэш файла

$content=file_get_contents("http://meteo.infospace.ru/win/wcond/html/");//сайт,с которого взята информация о погоде
$start = strpos( $content, "r_form.ssi?id=" ) + 11;
$end   = strpos( $content, '"', $start );

$id = substr( $content, $start, $end - $start );
if ( substr( $id, strlen( $id ) - 1, 1 ) == '"' )
{
	$id = substr( $id, 0, strlen( $id ) - 1 );
}

$content=file_get_contents("http://meteo.infospace.ru/win/cities/html/city_r.sht?num=1780&id=140071365"); // получаем сведения с сайта

// название населенного пункта
if (preg_match('|<font color=#800000>(.*?)</font>|sei', $content, $arr)) $title = trim($arr[1]); // название города
   else $title='';

$tstart = strpos( $content, "<table BORDER=0 CELLPADDING=0 CELLSPACING=0 width=100%>" ) + 55;
$tend   = strpos( $content, "</TABLE>", $tstart ) + 8;
$tbl    = substr( $content, $tstart, $tend - $tstart );

$search_patterns  = array( // массив сведений в таблицу о погоде 
                "../../images/",
	"<font size=-1",
	"Дневная температура",
	"Ночная температура",
	"(при H =\x0a   \t\t\t\t90\x0a   \t\t\t\tм)"
);

$replace_patterns = array( // шапка
	"pogoda/",
	"<font size=1,5 face=\"Verdana\"",
	"Днем",
	"Ночью",
	""
);

$tbl =  "<h2>Прогноз погоды на сегодня в городе ".$title."</h2>\n". // отображение всех данных
	"<table border=2 cellpadding=1 cellspacing=1 width=500 height=190>\n".
	str_replace( $search_patterns, $replace_patterns, $tbl );
@file_put_contents($cache_file,$tbl);
echo $tbl;
}
?>
