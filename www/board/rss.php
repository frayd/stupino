<? // WR-board v 1.6.1 LUX // 06.08.10 г. // Miha-ingener@yandex.ru
   // Скрипт для отображения последних тем и сообщений в формате RSS-новостей

include ("config.php");

// Получение адреса страницы
$rss="http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
$url=str_replace("rss.php","index.php",$rss);

if (!isset($_GET['whatisthis'])) {

$brdname=strip_tags($brdname);
$brdmaintext=strip_tags($brdmaintext);

// Заголовок RSS

echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
<rss version=\"2.0\">
 <channel>
   <title>$brdname</title>
   <link>$url</link>
   <description>$brdmaintext</description>
   <language>Russian</language>
   <copyright>WR-Script.RU</copyright>
   <managingEditor>$adminemail</managingEditor>
   <webMaster>$adminemail</webMaster>
   <generator>WR-Board 1.5 RSS-module</generator>
   <lastBuildDate>$date $time</lastBuildDate>
";

//18|Квартиры[ktname]Недвижимость|Альберт|as-dom.moy.su|П|ПРОДАЮ УЧАСТКИ|13.10.2009|1258022009|19|no|199283|1255430009|Влакиквказ|+7 (918)-829-99-96||||||Недвижимость||

// Чтение новостей и их вывод на экран
$lines=file("$datadir/newmsg.dat");
$itogo=sizeof($lines); $x=$itogo-1;
do { $dt=explode("|",$lines[$x]);

// конвертируем дату в формат ленты RSS    
$xdate=date("r",$dt[7]);

$dtt=explode("[ktname]",$dt[1]);

$rubrika=$dtt[0];
$razdel=$dtt[1];

$zag=$dt[3];
$dtz=explode("[email]",$dt[2]); $name=$dtz[0];

$msg=$dt[5];
$msg=str_replace("&","&amp;",$msg);
$msg=str_replace("<","&lt;",$msg);
$msg=str_replace(">","&gt;",$msg);
$msg=str_replace('\"','"',$msg);
$fid=$dt[8];
$id=$dt[10];

echo "
<item>
 <title>$zag</title>
  <link>$url?id=$id</link>
   <description>В &lt;b&gt;разделе:&lt;/b&gt; &lt;a href=\"$url?id=$fid\"&gt; $razdel&lt;/a&gt; рубрике: $rubrika &lt;b&gt;$name&lt;/b&gt; пишет: &lt;br&gt;&lt;br&gt; $msg &lt;br&gt;&lt;br&gt;</description>
   <author>$name</author>
  <comments>$url?id=$id</comments>
 <pubDate>$xdate</pubDate>
</item>
";
$x--;
} while ($x>=0); // end do ... while

echo "
 </channel>
</rss>";

} else { // Страница не поддерживает RSS, поэтому выводим информацию об RSS 

echo '<html>
<head>
<meta http-equiv="Content-Type"
content="text/html; charset=windows-1251">
<title>Что такое RSS</title>
</head>
<body bgcolor="#FFFFFF">
<p><font size="5">Что такое RSS?</font></p>
<p><strong>RSS</strong> - это сокращение от <b>R</b>eally
<b>S</b>imple <b>S</b>yndication, что в переводе
на русский звучит, как Действительно Простая… Синдикация?
Хотя, скорее, Действительно Простое Синдицирование, - так более
правильно, но не более понятно. </p>
<p>Смотрим словарь: </p>
<blockquote>
    <p>Syndicate - 1) агентство печати,  приобретающее информацию, статьи
    и т. п. и продающее их различным  газетам для одновременной
    публикации, (сущ.) 2) приобретать  информацию и пр. (гл.)</p>
</blockquote>
<p>Итого, <strong>RSS</strong> - это Простое
Приобретение Информации.</p>
<p><strong>RSS </strong>- это разновидность
XML, формат, специально придуманный для того, чтобы легко и быстро
делиться контентом. Изначально придуманный Netscape для их портала
Netcenter, он быстро завоевал популярность и стал черезвычайно широко использоваться.</p>
<p>В настоящее время <strong>RSS</strong>
наиболее популярен для передачи лент новостей прямо на рабочий стол
конечного пользователя, что существенно экономит траффик как
самого пользователя (не надо каждый раз загружать WEB-страницу), так и
хостера.</p> 
<p>Для работы с <b>RSS</b> на этой доске объявлений вам
необходима специальная программа для чтения <b>RSS</b>-файлов. Мы рекомендуем
вам установить простую в использовании, компактную и
бесплатную программу <a  href="http://www.google.ru/search?hl=ru&amp;q=Abilon+RSS&amp;lr=">Abilon</a>
настроив которую вы будете в режиме on-line получать всю информацию о
новых темах и сообщениях на нашей доске объявлений и всегда будете в курсе
событий.</p>
<p>Адрес <b>RSS</b>-колонки доски объявлений - <b>'.$rss.'</b></p>
</body>
</html>
'; }

?>
