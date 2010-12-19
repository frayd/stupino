<? // WR-board v 1.6.1 LUX // 06.08.10 г. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);

include "config.php";

$skey="657567"; // !!! Секретный ключ !!! 
// Поменяйте на свой и фиг кто вскроет админку :-)
// !!! ПОСЛЕ СМЕНЫ - пароли администратора и модератора становятся ошибочными!
// для получения нового пароля разкоменируйте строку № 104
// вставьте полученный код в config.php В ПЕРЕМЕННЫЕ $password и $moderpass

// Авторизация
$adminname="admin|moder|"; // Имя администратора и через знак | имя модератора и в конце |
$adminpass=$password;


function nospam() { global $max_key,$rand_key; // Функция АНТИСПАМ
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из config.dbf + код меняющийся кажые 24 часа
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6>
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }


function replacer ($text) { // ФУНКЦИЯ очистки кода
$text=str_replace("&#032;",' ',$text);
$text=str_replace("&",'&amp;',$text); // закоментируйте эту строку если вы используете языки: Украинский, Татарский, Башкирский и т.д.
$text=str_replace(">",'&gt;',$text);
$text=str_replace("<",'&lt;',$text);
$text=str_replace("\"",'&quot;',$text);
$text=preg_replace("/\n\n/",'<p>',$text);
$text=preg_replace("/\n/",'<br>',$text);
$text=preg_replace("/\\\$/",'&#036;',$text);
$text=preg_replace("/\r/",'',$text);
$text=preg_replace("/\\\/",'&#092;',$text);
$text=str_replace("\r\n","<br> ",$text);
$text=str_replace("\n\n",'<p>',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


// Выбран ВЫХОД - очищаем куки
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrforumm","",time()-3600); Header("Location: index.php"); exit; } }

if (isset($_COOKIE['wrforumm'])) { // Сверяем имя/пароль из КУКИ с заданным в конфиг файле
$text=$_COOKIE['wrforumm'];
$text=str_replace("\r\n","",$text); $text=str_replace(" ","",$text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)>60) {exit("Попытка взлома - длина переменной куки сильно большая!");}
$text=replacer($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];
$adminname=explode("|",$adminname);

if ($name1!=$adminname[0] and $name1!=$adminname[1] or $pass1!=$adminpass) 
{sleep(1); setcookie("wrforumm", "0", time()-3600); Header("Location: admin.php"); exit;} // убаваем НЕВЕРНУЮ КУКУ!!!

} else { // ЕСЛИ ваще нету КУКИ


if (isset($_POST['name']) & isset($_POST['pass'])) { // Если есть переменные из формы ввода пароля
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)<4) {exit("$back Вы не ввели имя или пароль!");}
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$qq=md5("$pass+$skey"); print"$qq"; exit; // РАЗБЛОКИРУЙТЕ для получения MD5 своего пароля!

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");}


// Сверяем введённое имя/пароль с заданным в конфиг файле
$adminname=explode("|",$adminname);
// АДМИНИСТРАТОРУ присваиваются куки
if ($name==$adminname[0] & md5("$pass+$skey")==$adminpass) 
{$tektime=time(); $wrforumm="$adminname[0]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}
// МОДЕРАТОРУ присваиваются куки
if ($name==$adminname[1] & md5("$pass+$skey")==$moderpass) 
{$tektime=time(); $wrforumm="$adminname[1]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}

exit("$back Ваш данные <B>ОШИБОЧНЫ</B>!</center>");

} else { // если нету данных, то выводим ФОРМУ ввода пароля

echo "<html><head><META HTTP-EQUIV='Pragma' CONTENT='no-cache'><META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'><META content='text/html; charset=windows-1251' http-equiv=Content-Type><style>input, textarea {font-family:Verdana; font-size:12px; text-decoration:none; color:#000000; cursor:default; background-color:#FFFFFF; border-style:solid; border-width:1px; border-color:#000000;}</style></head>
<BR><BR><BR><center>
<table border=#C0C0C0 border=1  cellpadding=3 cellspacing=0 bordercolor=#959595>
<form action='admin.php' method=POST name=pswrd>
<TR><TD bgcolor=#C0C0C0 align=center>Администрирование доски</TD></TR>
<TR><TD align=right>Введите логин: <input size=17 name=name value=''></TD></TR>
<TR><TD align=right>Введите пароль: <input type=password size=17 name=pass></TD></TR>";

if ($antispam==TRUE and !isset($wrbname)) {print"<tr class=row1><td align=right>Защитный код: "; nospam();} // АНТИСПАМ !

print"<TR><TD align=center><input type=submit style='WIDTH: 120px; height:20px;' value='Войти'>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT></TD></TR></table>
<BR><BR><center><font size=-2><small>Powered by <a href=\"http://www.wr-script.ru\" title=\"Скрипт доски объявлений\" class='copyright'>WR-Board</a> &copy;<br></small></font></center></body></html>";
exit;}

} // АВТОРИЗАЦИЯ ПРОЙДЕНА!


// Выбран ВЫХОД - очищаем куки
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrforumm","",time()-3600); Header("Location: index.php"); exit; } }


$gbc=$_COOKIE['wrforumm']; $gbc=explode("|", $gbc); $gbname=$gbc[0];$gbpass=$gbc[1];$gbtime=$gbc[2];


// Добавление IP-юзера в БАН
if (isset($_GET['badip']))  {
if (isset($_POST['ip'])) {$ip=$_POST['ip']; $badtext=$_POST['text'];}
if (isset($_GET['ip_get'])) {$ip=$_GET['ip_get']; $badtext="За добавление нежелательных объявлений на доску! ЗА СПАМ!!!";}
$text="$ip|$badtext|"; $text=stripslashes($text); $text=htmlspecialchars($text); $text=str_replace("\r\n", "<br>", $text);
$fp=fopen("$datadir/bad_ip.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }



// Удаления юзера из БАНА
if (isset($_GET['delip']))  { $xd=$_GET['delip'];
$file=file("$datadir/bad_ip.dat"); $dt=explode("|",$file[$xd]); 
$fp=fopen("$datadir/bad_ip.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }




// ЗАПИСЬ рекламы/объявления в ФАЙЛы
if (isset($_GET['savebiginfo']))  { if (isset($_POST['text'])) $text=$_POST['text'];
//$text=str_replace("\r\n", "<br>", $text);
if (isset($_POST['chto'])) $chto=replacer($_POST['chto']);
$editfile="$datadir/mainreklama.html"; // главный файл
if ($chto=="1") $editfile="$datadir/left.html"; // левый блок
if ($chto=="2") $editfile="$datadir/right.html"; // правый блок
if ($chto=="3") $editfile="$datadir/reklama.html"; // правый блок
if ($chto=="4") $editfile="$datadir/msg.html"; // правый блок
$fp=fopen("$editfile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$text");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }





// Добавление ГОРОДА в city.dat
if (isset($_GET['newcity']))  {
if (isset($_POST['city'])) {$city=replacer($_POST['city']); $top=replacer($_POST['top']);}
$key=mt_rand(0,999); $text="$key|$city|\r\n";
$lines=file_get_contents("$datadir/city.dat"); // содержимое файла считываем в переменную
$fp=fopen("$datadir/city.dat","w");
flock ($fp,LOCK_EX);
if ($top==TRUE) $text="$text$lines"; else $text="$lines$text";//куда пишем - top=TRUE - в начало
fputs($fp,"$text");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=editcity"); exit; }





// Удаления ГОРОДА из файла city.dat
if (isset($_GET['deletecity']))  {

if (isset($_GET['page'])) $page=replacer($_GET['page']); else $page=1;
$first=replacer($_POST['first']); $last=replacer($_POST['last']);
$delnum=""; $i=0;

do {$dd="del$first"; if (isset($_POST["$dd"])) { $delnum[$i]=$first; $i++;} $first++;} while ($first<=$last);
$itogodel=count($delnum); $newi=0; if ($delnum=="") exit("Сделайте выбор хотябы одного объявления!");

$file=file("$datadir/city.dat"); $itogo=sizeof($file); $lines=""; $delyes="0";
for ($i=0; $i<$itogo; $i++) { // цикл по файлу с данными
for ($p=0; $p<$itogodel; $p++) {if ($i==$delnum[$p]) $delyes=1;} // цикл по строкам для удаления
// если нет метки на удаление записи - формируем новую строку массива, иначе - нет
if ($delyes!=1) {$lines[$newi]=$file[$i]; $newi++;} else $delyes="0"; }

// пишем новый массив в файл
$newitogo=count($lines); 
$fp=fopen("$datadir/city.dat","w");
flock ($fp,LOCK_EX);
// если все объявления на удаление, тогда ничего впутим туда НИЧЕГО :-))
if (isset($lines[0])) {for ($i=0; $i<$newitogo; $i++) fputs($fp,$lines[$i]);} else fputs($fp,"");
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=editcity"); exit; }





// Блок удаления РУБРИКИ
if (isset($_GET['xd']))  { $xd=$_GET['xd'];
// ищем файл с объявлениями и удаляем его
$file=file("$datadir/$datafile"); $dt=explode("|",$file[$xd]); 
if (is_file("$datadir/$dt[0].dat")) {unlink ("$datadir/$dt[0].dat");}
// удаляем строку, соответствующую данной рубрике в БД
$fp=fopen("$datadir/$datafile","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }



// Блок УДАЛЕНИЯ КОМЕНТАРИЯ к объявлению
if (isset($_GET['remxd']))  {
$id=$_GET['id']; $flname=$_GET['flname']; $remxd=$_GET['remxd']; $page=$_GET['page'];
$file=file("$datadir/$flname.dat");
// удаляем строку с коментарием
$fp=fopen("$datadir/$flname.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) { if ($i==$remxd) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
if (count($file)==0) {unlink ("$datadir/$flname.dat");}
Header("Location: admin.php?event=topic&id=$id&page=$page"); exit;}



// Блок УДАЛЕНИЯ объявления из 10-КИ последних
if (isset($_GET['tenxd']))  { $tenxd=$_GET['tenxd'];

$first=$_POST['first']; $last=$_POST['last'];
$delnum=""; $i=0; $spros="0"; $predl="0";

do {$dd="del$first"; if (isset($_POST["$dd"])) { $delnum[$i]=$first; $i++;} $first++; } while ($first<=$last);
$itogodel=count($delnum); $newi=0; 
if ($delnum=="") {exit("Сделайте выбор хотябы одного объявления!");}
$file=file("$datadir/newmsg.dat"); $itogo=sizeof($file); $lines=""; $delyes="0";
for ($i=0; $i<$itogo; $i++) { // цикл по файлу с данными
for ($p=0; $p<$itogodel; $p++) {if ($i==$delnum[$p]) {$delyes=1;}} // цикл по строкам для удаления
// если нет метки на удаление записи - формируем новую строку массива, иначе - нет
if ($delyes!=1) {$lines[$newi]=$file[$i]; $newi++;} else {$delyes="0";} }

// пишем новый массив в файл
$newitogo=count($lines); 
$fp=fopen("$datadir/newmsg.dat","w");
flock ($fp,LOCK_EX);
// если все объявления на удаление, тогда ничего впутим туда НИЧЕГО :-))
if (isset($lines[0])) {for ($i=0; $i<$newitogo; $i++) {fputs($fp,$lines[$i]);}} else {fputs($fp,"");}
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit;}



// Блок УДАЛЕНИЯ выбранных ОБЪЯВЛЕНИЙ
if (isset($_GET['deletemsg'])) {

$id=$_GET['id']; if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page=1;}
$first=$_POST['first']; $last=$_POST['last'];
$delnum=""; $i=0; $spros="0"; $predl="0";

do {$dd="del$first";
if (isset($_POST["$dd"])) { $delnum[$i]=$first; if ($_POST["$dd"]=="П") {$predl++;} else {$spros++;} $i++;}
$first++;
} while ($first<=$last);

$itogodel=count($delnum); $newi=0; 
if ($delnum=="") {exit("Сделайте выбор хотябы одного объявления!");}
$file=file("$datadir/$id.dat"); $itogo=sizeof($file); $lines=""; $delyes="0";
for ($i=0; $i<$itogo; $i++) { // цикл по файлу с данными
for ($p=0; $p<$itogodel; $p++) {if ($i==$delnum[$p]) {$delyes=1;}} // цикл по строкам для удаления
// если нет метки на удаление записи - формируем новую строку массива, иначе - нет
if ($delyes!=1) {$lines[$newi]=$file[$i]; $newi++;} else {$delyes="0";} }

// пишем новый массив в файл
$newitogo=count($lines); 
$fp=fopen("$datadir/$id.dat","w");
flock ($fp,LOCK_EX);
// если все объявления на удаление, тогда ничего впутим туда НИЧЕГО :-))
if (isset($lines[0])) {for ($i=0; $i<$newitogo; $i++) {fputs($fp,$lines[$i]);}} else {fputs($fp,"");}
flock ($fp,LOCK_UN);
fclose($fp);

// Блок вычитает удалённые объявления из кол-ва объявлений в рубрике
$mlines=file("$datadir/$datafile"); $i=count($mlines);
do {$i--; $dt=explode("|",$mlines[$i]);
if ($id==$dt[0]) {$fnomer=$i; $dt[3]=$dt[3]-$predl; $dt[2]=$dt[2]-$spros; 
if (!isset($lines[0])) {$dt[2]=0; $dt[3]=0;} 
if ($dt[2]<0) $dt[2]=0; if ($dt[3]<0) $dt[3]=0;
$text="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$id|";}
} while($i > 0);

$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($ii=0;$ii< sizeof($file);$ii++) 
 { if ($fnomer!=$ii) {fputs($fp,$file[$ii]);} else {fputs($fp,"$text\r\n");} }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=topic&id=$id&page=$page"); exit;}





// Блок удаления УЧАСТНИКА ДОСКИ
if(isset($_GET['xduser'])) {
if ($_GET['xduser'] =="") {exit("произошёл глюк-переглюк :-( Вертайтесь назад ;-)");}
$xduser=$_GET['xduser']-1; if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page=1;}
$file=file("$datadir/usersdat.php"); $i=count($file);
if ($xduser<"1") {exit("$back. 1-ая строка является защитной! Её <B>НЕЛЬЗЯ УДАЛЯТЬ!</B>");}
if ($i<"3") {exit("$back. Необходимо оставить хотя бы <B>ОДНОГО</B> участника!");}
// удаляем строку с участником
$fp=fopen("$datadir/usersdat.php","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xduser) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=userwho&page=$page"); exit; }



// Блок ПЕРЕМЕЩЕНИЯ ВВЕРХ/ВНИЗ РАЗДЕЛА или ТОПИКА
if(isset($_GET['movetopic'])) { if ($_GET['movetopic'] !="") {
$move1=$_GET['movetopic']; $where=$_GET['where']; 
if ($move1=="0" or $move1=="1") {exit("$back. Запрещено перемещать самый первый раздел!");}
if ($where=="0") {$where="-1";}
$move2=$move1-$where;
$file=file("$datadir/boardbase.dat"); $imax=sizeof($file);
if (($move2>=$imax) or ($move2<"0")) {exit("$back. НИЗЯ туда двигать!");}
$data1=$file[$move1]; $data2=$file[$move2];
$fp=fopen("$datadir/boardbase.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
// Меняем местами два соседних раздела
for ($i=0; $i<$imax; $i++) {if ($move1==$i) {fputs($fp,$data2);} else  {if ($move2==$i) {fputs($fp,$data1);} else {fputs($fp,$file[$i]);}}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }}



// Блок ПЕРЕСЧЁТА кол-ва рубрик и объявлений
if (isset($_GET['event'])) { if ($_GET['event']=="revolushion") {
$lines = file("$datadir/$datafile");
$countmf=count($lines)-1;
$n="0";$i="-1";$u=$countmf-1;$k="0";$it=0;

do {$i++; $dt=explode("|", $lines[$i]);
$fid=$dt[0]; $itogos="0";$itogo="0";$itogop="0";

if (is_file("$datadir/$fid.dat")) {
$msglines=file("$datadir/$fid.dat");
if (count($msglines)>0) {

$itogo=count($msglines); $it="-1"; $itmax=$itogo-1;
do {$it++; $dtt = explode("|", $msglines[$it]);
if ($dtt[4]=="П") {$itogop++;} else {$itogos++;}
} while ($it<$itmax);
}
}

if ($dt[1]=="R") {$lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|\r\n";} else {$lines[$i]="$dt[0]|$dt[1]|$itogop|$itogos|\r\n";}
} while($i < $countmf);

// сохраняем обновлённые данные о кол-ве объявлений
$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) {fputs($fp,$lines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);
exit("<center><BR>Всё успешно пересчитано.<BR><BR><h3>$back</h3></center>"); }
}



// Добавление РУБРИКИ
if (isset($_GET['newrubrika']))  { $ftype=$_POST['ftype']; $zag=$_POST['zag'];
if (strlen($zag)<3) {exit("$back. Тема объявления должна содержать <B> более 3 символов </B>!");}

// пробегаем по файлу ищем наибольшей номер рубрики и добавляем +1
$fid="0"; if (is_file("$datadir/$datafile")) { $lines=file("$datadir/$datafile"); $imax = count($lines); $i=0; do {$dt = explode("|", $lines[$i]); if ($fid<$dt[0]) {$fid=$dt[0];} $i++;} while($i < $imax); $fid++;}

$zag=str_replace("|"," ",$zag);
if ($ftype=="") {$text="$fid|$zag|0|0|";} else {$text="$fid|R|$zag|0|";}
$text=stripslashes($text);
$text=htmlspecialchars($text);
$text=str_replace("\r\n", "<br>", $text);

$fp=fopen("$datadir/$datafile","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
$fp=fopen("$datadir/$fid.dat","a+");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }



// Если выбрано - редактирование ОБЪЯВЛЕНИЯ в рубрике
if (isset($_GET['event'])) {
if ($_GET['event']=="rdmsgintopic") {

$rubrn=$_POST['rubrn'];
$rubka=$_POST['rubka'];
$name=$_POST['name'];
$id=$_POST['id'];
$fnomer=$_POST['fnomer'];
$newrubrika=$_POST['newrubrika'];
$days=$_POST['days'];
$msg=$_POST['msg'];
$zag=$_POST['zag'];
$type=$_POST['type'];
$vip=$_POST['vip'];
$key=$_POST['key'];
$today=$_POST['today'];
$gorod=$_POST['gorod'];
$phone=$_POST['phone'];
$smallfoto=$_POST['smallfoto'];
$foto=$_POST['foto'];
$fotoksize=$_POST['fotoksize'];
$size0=$_POST['size0'];
$size1=$_POST['size1'];
$newru = explode("|",$newrubrika);

if ($newru[0]==$rubrn)  {
$deldt=mktime()+$days*86400; // формируем дату удаления объявления
$msg=str_replace("|","I",$msg);

$text="$rubrn|$rubka|$name|$zag|$type|$msg|$date|$deldt|$id|$vip|$key|$today|$gorod|$phone|$smallfoto|$foto|$fotoksize|$size0|$size1|||";

// обрезаем лишние символы во всех введённых данных
$text=stripslashes($text);
$text=htmlspecialchars($text);
$text=str_replace("\r\n", "<br>", $text);

$file=file("$datadir/$id.dat");
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0); 
for ($i=0;$i< sizeof($file);$i++) 
{   if ($fnomer!=$i) {fputs($fp,$file[$i]);} else {fputs($fp,"$text\r\n");}    }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<BR><BR><BR><center><B>текст объявления успешно изменён</B><BR><BR><BR><BR><BR><script language='Javascript'>function reload() {location = \"admin.php?event=topic&id=$id\"}; setTimeout('reload()', 1000);</script>"; exit;

}  else   { // if ($newru[0]==$rubrn) // если необходимо ПЕРЕМЕСТИТЬ ОБЪЯВЛЕНИЕ в другую рубрику

$topicxd=$fnomer; $idold=$id; // запоминаем параметры предыдущей рубрики

$deldt=mktime()+$days*86400; // формируем дату удаления объявления
$msg=str_replace("|"," ",$msg);
$id=$newru[1];
$text="$newru[0]|$newru[3][ktname]$newru[2]|$name|$zag|$type|$msg|$date|$deldt|$newru[1]|$vip|$key|$today|$gorod|$phone|$smallfoto|$foto|$fotoksize|$size0|$size1|$newru[2]||";

// обрезаем лишние символы во всех введённых данных
$text=stripslashes($text);
$text=htmlspecialchars($text);
$text=str_replace("\r\n", "<br>", $text);

$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// меняем рубрику в 10-20 последних объявлений
$lines=file("$datadir/newmsg.dat"); $itogo=count($lines);
for ($i=0; $i<$itogo; $i++) { // цикл по файлу с данными
$dt=explode("|",$lines[$i]);
if ($dt[10]==$key) {$lines[$i]="$text\r\n";}}
$itogo=count($lines); // определяем кол-во строк после удаления
$fp=fopen("$datadir/newmsg.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i<$itogo; $i++) {fputs($fp,$lines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);

// удаляем строку, соответствующую текущему объявлению в старой рубрике
$file=file("$datadir/$idold.dat");
$fp=fopen("$datadir/$idold.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) { if ($i==$topicxd) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);

// Корректируем кол-во объяв в категориях
$lines=null; $lines=file("$datadir/$datafile"); $itogo=count($lines); $i=$itogo; $ok1=null; $ok2=null;
do {$i--; $dt=explode("|",$lines[$i]);
$lines[$i]=$lines[$i];
if ($newru[1]==$dt[0]) {$ok=1; if ($type=="С") {$dt[3]++;} else {$dt[2]++;} $lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|\r\n";}
if ($ok1!=null) {if ($dt[1]=="R") {$ok1=null; $dt[3]++; $lines[$i]="$dt[0]|R|$dt[2]|$dt[3]|\r\n";}}
if ($id==$dt[0]) {$ok=1; if ($type=="С") {$dt[3]--;} else {$dt[2]--;} if ($dt[3]<0) $dt[3]=0; if ($dt[2]<0) $dt[2]=0; $lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|\r\n";}
if ($ok2!=null) {if ($dt[1]=="R") {$ok2=null; $dt[3]--; if ($dt[3]<0) $dt[3]=0; $lines[$i]="$dt[0]|R|$dt[2]|$dt[3]|\r\n";}}
} while($i > 0);
$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","w");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($i=0;$i<$itogo;$i++) {fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<BR><BR><BR><center><B>рубрика и текст объявления успешно изменены<BR><BR><BR></B><script language='Javascript'>function reload() {location = \"admin.php?event=topic&id=$id\"}; setTimeout('reload()', 1000);</script>";
exit; }
}
}



// РЕДАКТИРОВАНИЕ ТЕМЫ или РУБРИКИ (проверить блок !!!)
if (isset($_GET['event']))  {

if (($_GET['event']=="add") or ($_GET['event'] =="addlink"))  {

// если выбрано - редактирование РУБРИК. $fnomer - номер ячейки, которую необходимо заменить.
if (isset($_GET['rd']))  { $rd=$_GET['rd']; $fnomer=$_POST['fnomer'];
$zag=$_POST['zag']; $spros=$_POST['spros']; $predl=$_POST['predl']; $idtopic=$_POST['idtopic'];

$text="$idtopic|$zag|$spros|$predl|";
$text=str_replace("\r\n", "", $text);

$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ
for ($i=0;$i< sizeof($file);$i++) {if ($fnomer!=$i) {fputs($fp,$file[$i]);} else {fputs($fp,"$text\r\n");}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Заносим новое название рубрики в каждую строку файла с объявлениями
$linesrdt=file("$datadir/$idtopic.dat");
$fp=fopen("$datadir/$idtopic.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ
for ($i=0;$i< sizeof($linesrdt);$i++) {$drdt = explode("|", $linesrdt[$i]); $text1="$drdt[0]|$zag|$drdt[2]|$drdt[3]|$drdt[4]|$drdt[5]|$drdt[6]|$drdt[7]|$drdt[8]|$drdt[9]|$drdt[10]|$drdt[11]|$drdt[12]|$drdt[13]|$drdt[14]|$drdt[15]|$drdt[16]|$drdt[17]|$drdt[18]|$drdt[19]|$drdt[20]|"; $text1=str_replace("\r\n", "", $text1); fputs($fp,"$text1\r\n");}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }
}



// АКТИВАЦИЯ пользователя
if(isset($_GET['event'])) { if ($_GET['event']=="activate") {

$key=$_GET['key']; $email=$_GET['email']; $page=$_GET['page'];

// защиты от взлома по ключу и емайлу
if (strlen($key)<6 or strlen($key)>6 or !ctype_digit($key)) {exit("$back Вы ошиблись при вводе ключа. Ключ может содержать только 6 цифр.");}
$email=stripslashes($email); $email=htmlspecialchars($email);
$email=str_replace("|","I",$email); $email=str_replace("\r\n","<br>",$email);
if (strlen($key)>30) {exit("Ошибка при вводе емайла");}

// Ищем юзера с таким емайлом и ключом. Если есть - меняем статус на пустое поле
$email=strtolower($email); unset($fnomer); unset($ok);
$lines=file("$datadir/usersdat.php"); $ui=count($lines); $i=$ui;
do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[0]=strtolower($rdt[3]);
if ($rdt[2]===$email and $rdt[12]===$key) {$name=$rdt[0]; $pass=$rdt[1]; $fnomer=$i;}
if ($rdt[2]===$email and $rdt[10]==="ok") {$ok="1";}
} while($i > 1);

if (isset($fnomer)) { // обновление строки юзера в БД
$i=$ui; $dt=explode("|", $lines[$fnomer]);
$txtdat="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|ok|$dt[11]|||||";
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i<=(sizeof($lines)-1);$i++) {if ($i==$fnomer) {fputs($fp,"$txtdat\r\n");} else {fputs($fp,$lines[$i]);}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp); }
if (!isset($fnomer) and !isset($ok)) {exit("$back Вы ошиблись в воде активационного ключа или емайла.</center>");}
if (isset($ok)) {$add="Запись активирована ранее";} else {$add="$name, Пользователь успешно зарегистрирован.";}

print"<html><head><link rel='stylesheet' href='$brdskin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"admin.php?event=userwho&page=$page\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$add</B>.<BR><BR>Через несколько секунд Вы будете автоматически перемещены на страницу с участниками форума.<BR><BR>
<B><a href='admin.php?event=userwho&page=$page'>Нажмите здесь, если не хотите больше ждать</a></B></td></tr></table></td></tr></table></center></body></html>";
exit;}
}


}  // if isset($event)




$shapka="<html><head>
<title>Админпанель - $brdname</title>
<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META content='text/html; charset=windows-1251' http-equiv=Content-Type>
<LINK href='$brdskin/style.css' rel=stylesheet type=text/css>
</head><body topMargin=5 bgcolor=#F3F3F3><center>
<table width=100% cellpadding=1 cellspacing=0 border=1 bordercolor=#666666>
<TR height=30><TD align=center style='FONT-SIZE: 13px; FONT-WEIGHT: bold'>
<b><a href='admin.php'>Главная</font></a> :: 
<a href='admin.php?event=revolushion'>Пересчитать</a> :: 
<a href='admin.php?event=makecopy'>Сделать копию БД</a> :: 
<a href='admin.php?event=restore'>Восстановить из копии</a> ::
<a href='admin.php?event=config'>Настройки</a> :: <br>
<a href='admin.php?event=userwho'>Участники</a> :: 
<a href='admin.php?event=blockip'>IP-Блокировка</a> :: 
<a href='admin.php?event=editcity'>Добавление / удаление городов</a> :: 
<br>Редактирование рекламных блоков: <a href='admin.php?event=editinfo&chto=0'>на главной странице</a> :: 
<a href='admin.php?event=editinfo&chto=1'>левого блока</a> :: 
<a href='admin.php?event=editinfo&chto=2'>правого блока</a> :: 
<a href='admin.php?event=editinfo&chto=3'>блок в списке объявлений</a> :: 
<a href='admin.php?event=clearcooke'>Выход</a></b>
</td></tr><tr><td width=100%>";


// Общие действия - ничего не выбрано
if(!isset($_GET['event'])) {

// Выводим все рубрики на текущей странице
if (!is_file("$datadir/$datafile")) {$add1="<center><h3>файл $datadir/boardbase.dat НЕ существует! СРОЧНО восстановитесь из копии!!! Либо перезалейте указанный файл из архива!</h3>"; $stop=1; $lines=file("$datadir/copy.dat"); $data1size = sizeof($lines); $i=count($lines); }
else {$lines=file("$datadir/$datafile"); $data1size = sizeof($lines); $i=count($lines); $add1="";}

$toper="
<BR><TABLE align=center cellPadding=2 cellSpacing=1 width=98%>
<TR  align=center class=smallest bgColor=#cccccc><TD width=5%><B>№ п/п</B></TD><TD width=80%><B>Разделы</B></TD><TD width=5%><B>Итого</B></TD><TD colspan=4 width=15%><B>Операции</B></TD></TR>";

if (is_file("$datadir/copy.dat")) {
if (count(file("$datadir/copy.dat"))<1) {$a2="<font color=red size=+1>НО файл копии ПУСТ! Срочно пересоздайте!</font><br> (смотрите права доступа, если эо сообщение повторяется)";} else {$a2="";}
$a1=round((mktime()-filemtime("$datadir/copy.dat"))/86400); if ($a1<1) $a1="сегодня</font>, это есть гуд!"; else $a1.="</font> дней назад.";
$add="<br><center>Копия была создана <font color=red size=+1>".$a1." $a2</center>"; } else {$add="";}

print"$shapka $add1 $add<TABLE cellPadding=2 cellSpacing=0 width=100%><tr height=25 align=center><TD width=50%>";
if (isset($stop)) {exit("Дальнейшая работа админпанели НЕВОЗМОЖНА!!!");} else {print"$toper";}
if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page="0";}
$a1="0";

if ($i>0) {
do {$dt = explode("|", $lines[$a1]);

$halfrubsize=round($data1size/2); // определяем кол-во рубрик в каждом столбце
if ($a1==$halfrubsize) {print "</table></td><td align=center width=50%>$toper";}
$a1++;
$numpp=$a1-1;

if ($dt[1]!="R") {$kolvo=$dt[2]+$dt[3]; $add="<UL><a href=\"admin.php?event=topic&id=$dt[0]\">$dt[1]</a>";} else {$kolvo=""; $add="<B>$dt[2]</B>";}


print"<tr align=center>
<td><font size=-1>$a1</font></td>
<td align=left>$add</td>
<td><font size=-1>$kolvo</font></td>
<td width=10 bgcolor=#A6D2FF><B><a href='admin.php?movetopic=$numpp&where=1'>Вв.</a></B></td>
<td width=10 bgcolor=#DEB369><B><a href='admin.php?movetopic=$numpp&where=0'>Нз.</a></B></td><td bgcolor=#00E600>";
if ($dt[1]!="R") {print"<B><a href='admin.php?rd=$numpp'>.P.</a></B>";} else {echo'&nbsp;';}
print"</td><td width=5% bgcolor=#FF6C6C><B><a href='admin.php?xd=$numpp'>.X.</a></B>
</td></tr>";
} while($a1 < $i);
} else {echo'<br><center><h3>файл основной БД пуст! добавьте рубрики, либо восстановите из копии (если вы её делали вообще...)</h3></center>';}

echo'</table></tr></td></table>';


// если выбрана метка .P. - редактирование рубрики, то ищем его и выводим в форму
if (isset($_GET['rd'])) { $rd=$_GET['rd']; $dt = explode("|", $lines[$rd]);

print "<BR><center><table><tr><td valign=top><B>Рубрика</td><td>
<form action='admin.php?event=add&rd=$rd' method=post name=REPLIER>
<input type=text value=\"$dt[1]\" name=zag size=50><br><br>
<input type=hidden name=spros value=\"$dt[2]\">
<input type=hidden name=predl value=\"$dt[3]\">
<input type=hidden name=idtopic value=\"$dt[0]\">
<input type=hidden name=fnomer value=\"$rd\">
<center><input type=submit  value='Изменить рубрику'></form>
</td></tr></table>
<SCRIPT language=JavaScript>document.REPLIER.zag.focus();</SCRIPT><BR></td></tr></table>"; 
} else {
print "<center><BR><form action=?newrubrika=add method=post name=REPLIER>
Добавить: <input type=radio name=ftype value='razdel'> Раздел &nbsp;&nbsp; <input type=radio name=ftype value=''checked> <B>Рубрику</B>  &nbsp;&nbsp;&nbsp;<input type=text name=zag size=40> <input type=submit value='Добавить'></form>
<SCRIPT language=JavaScript>document.REPLIER.zag.focus();</SCRIPT>";


// Выводим 10-20 последних объявлений
$shapka20="<TABLE align=center border=1 bordercolor='#E1E1E1' cellPadding=3 cellSpacing=0 width=100%>";
if (is_file("$datadir/newmsg.dat")) { // проверяем есть ли такой файл
$linesn = file("$datadir/newmsg.dat"); $in=count($linesn); $first=0; $last=$in;
if ($in > 0) {
$newdat=file("$datadir/newmsg.dat");
$in=count($newdat)-1; $iall=$in; $ia=$in+1;
print"<FORM action='admin.php?pswrd=$password&tenxd=$in' method=POST name=delform>
<TABLE cellPadding=2 cellSpacing=1 align=center width='98%'>
<TR bgColor=#cccccc height=18><TD colspan=4 align=center><B>Последние $ia объявлений:</B></TD></TR>
<TR><TD valign=top> $shapka20";

do {$dtn=explode("|", $newdat[$in]);
$url="index.php?fid=$dtn[8]&id=$dtn[10]";

$dtn[5]=substr($dtn[5],0,150); // образаем сообщение до 150 символов
$dtn[5]=str_replace("<br>","\r\n",$dtn[5]);
$dtn[7]=date("H:i",$dtn[7]);
if ($dtn[4]=="П") {$colorsp="#ff3333";} else {$colorsp="#1414CD";}
if (round($iall/2)==($in+1)) {print"</table></td><td valign=top width=50%>$shapka20";}
if ($dtn[9]=="vip") {$st1="<B>"; $st2="VIP-объявление \r\n";} else {$st1=""; $st2="";}
print"
<TR height=20>
<td width=5% bgcolor=#FF6C6C><B>
<input type=checkbox name='del$in' value=''"; if (isset($_GET['chekall'])) {echo'CHECKED';} print"></B></td>
</td>
<TD><FONT color=$colorsp><B>$dtn[4]</B></FONT></TD>
<TD>$dtn[7]</TD>
<TD width=100%>$st1<A href='$url' title='$dtn[5] \r\r\n $st2 размещено $dtn[6] г.'>$dtn[3]</A></TD>
</TR>";
$in--;
} while($in >"-1");
print"</table></td></tr></table>

<table border=0><TR><TD valign=top>
<input type=hidden name=first value='$first'><input type=hidden name=last value='$last'><INPUT type=submit value='Удалить выбранные объявления'></FORM>
</TD><TD>
<FORM action='admin.php?chekall' method=POST name=delform><INPUT type=submit value='Пометить всё'></FORM>
</TD><TD>
<FORM action='admin.php' method=POST name=delform><INPUT type=submit value='Снять пометку'></FORM>
</TD></TR></TABLE>";
}
}

echo'<div align=left>&nbsp; Операции для рубрик: <BR>
&nbsp; <B>Вв.</B> - переместить <B>ВВЕРХ</B>;<BR>
&nbsp; <B>Нз.</B> - переместить <B>ВНИЗ</B>;<BR>
&nbsp; <B>.Р.</B> - <B>РЕДАКТИРОВАТЬ</B>;<BR>
&nbsp; <B>.Х.</B> - <B>УДАЛИТЬ</B>.<BR><BR>
</td></tr></table>'; }


}  // if !isset($event')




// ПРОСМОТР объявлений в текущей рубрике

else  {
if ($_GET['event'] == "topic") {
if (!isset($_GET['id'])) {exit("ID - только число. Буквы быть не может!"); } else {$id=$_GET['id'];}

if (is_file("$datadir/$id.dat")) { // проверяем есть ли такой файл
$lines = file("$datadir/$id.dat"); $i=count($lines); $maxi=$i-1;
if ($i > 0) {

// Выводим qq ссылок в текущей рубрике
$dtsize=sizeof($lines); 
$itogos="0"; // итого объявлений - спрос

// чё то не то Недвижимость --> Услуги маклеров. Эххх, не успел доделать - исправлю в новой версии ;-)

// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) {$page=1;} else {$page=$_GET['page']; if (!ctype_digit($page)) {$page=1;} if ($page<1) $page=1;}

$fm=$maxi-$qq*($page-1); if ($fm<"0") {$fm=$qq;}
$lm=$fm-$qq; if ($lm<"0") {$lm="-1";}

print"$shapka <TABLE cellPadding=2 cellSpacing=0 width=100%><tr height=25 align=center><TD width=100%>";

$dtt=explode("|",$lines[0]);
$tdt=explode("[ktname]", $dtt[1]); $razdel=$tdt[1]; $rubrika=$tdt[0];

print"<BR><h3>$razdel --> $rubrika</h3><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1 width=98% align=center><TBODY>
<TR class=small align=center bgColor=#cccccc>
<TD><small><B>№ п/п</B></small></TD>
<TD>&nbsp;</TD>
<TD width=2%><B>Р</B></TD>
<TD width=2%><B>Х</B></TD>
<TD width=60%><B>Заголовок / часть объявления</B></TD>
<TD width=13%><B>Имя / IP / Забанить по IP</B></TD>
<TD width=20%><B><small>Размещено / Дата удаления</small></B></TD>
<FORM action='admin.php?deletemsg&id=$id&page=$page' method=POST name=delform>
</TR>";

$last=$fm; // какое значение первое

do {$dt=explode("|",$lines[$fm]);

$deldate=date("d.m.Y",$dt[7]);  // конверируем дату удаления в человеческий формат
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // через сколько дней будет удалено объявление

if ($dt[4]=="П") {$colorsp="#ff3333";} else {$colorsp="#1414CD";}

$numpp=$fm+1;
$numanti=$i-$numpp+1;
$stroka=substr($dt[5],0,$msglength); // обрезаем лишнее у объявления
$dt5itog=strlen($dt[5]);
if ($dt[9]=="vip") {$addvip="#FFB9B9";} else {$addvip="#FFFFFF";}

if (strlen($dt[20])>1) $u_profile="<A class=listlink href='tools.php?event=profile&pname=$dt[2]'><small>$dt[2]</small></A>"; else $u_profile="$dt[2]";
$u_profile=str_replace("[email]",", ",$u_profile);
print "<TR height=28 class=small bgColor=$addvip>
<TD><B>$numanti</B></TD>
<TD><FONT color=$colorsp><B>$dt[4]</B></FONT></TD>
<td width=10 bgcolor=#22FF44><B><a href='admin.php?event=topic&id=$id&topicrd=$fm'>.P.</a></B></td>

<td width=10  bgcolor=#FF2244><B><input type=checkbox name='del$fm' value='$dt[4]'"; if (isset($_GET['chekall'])) {echo'CHECKED';} print"></B></td>
<TD><B><A style='text-decoration: none;' class=listlink href='index.php?fid=$id&id=$dt[10]'>$dt[3]</A></B><BR><small>$stroka [$dt5itog]</small></TD>
<td align=right>$u_profile <br>$dt[21] <a href='admin.php?badip&ip_get=$dt[21]'><B><font color=red>БАН по IP</font><B></a></td>
<TD><small>$dt[6]</small><br><small>через <B>$deldays</B> дней ($deldate)</small></TD>
</TR>";

// если есть коментарий к объявлению - выводим все
if (is_file("$datadir/$dt[10].dat")) { print"<TR class=small bgColor=$addvip><TD>&nbsp;</TD><TD>&nbsp;</TD><TD colspan=7>";
$klines = file("$datadir/$dt[10].dat"); $ik=count($klines);
for ($z=0;$z<sizeof($klines);$z++) {$dtk=explode("|",$klines[$z]); print "
<table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?id=$id&flname=$dt[10]&remxd=$z&page=$page'>.X.</a></B></td><td> 
 Имя: <B>$dtk[0]</B> Емайл: <B>$dtk[1]</B> Коментарий: <B>$dtk[2]</B> Оценка: $dtk[4]</td></tr></table>";}
echo'</TD></TR>'; }

if ($dt[4]=="С") {$itogos++;}
$fm--;
} while($lm < $fm);
$itogop=$i-$itogos;
$first=$lm; // последнее значение

// выводим список доступных страниц
print "</TBODY></TABLE></TD></TR></TABLE>
<BR><center><TABLE cellPadding=0 cellSpacing=0 border=0 width=98%><TR height=40>
</TD><TD width=50% colspan=2>Всего объявлений: <B>$i</B>. Из них: Спрос - <B>$itogos</B> Предложение - <B>$itogop</B>.</TD></TR>
<TR><TD>
<input type=hidden name=first value='$first'><input type=hidden name=last value='$last'><INPUT type=submit value='Удалить выбранные объявления'></FORM>
</TD><TD>
<FORM action='admin.php?event=topic&id=$id&page=$page&chekall' method=POST name=delform><INPUT type=submit value='Пометить всё'></FORM>
</TD><TD>
<FORM action='admin.php?event=topic&id=$id&page=$page' method=POST name=delform><INPUT type=submit value='Снять пометку'></FORM>
</TD></TR>";

if ($i>$qq) { // выводим список доступных страниц
echo'<TD align=left width=50%><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Страницы: ';

// выводим СПИСОК СТРАНИЦ
$maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) {$page=$maxpage;}
if ($page>=4 and $maxpage>5) print "<a href=admin.php?event=topic&id=$id&page=1>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page<=2) {$f1=5; $f2=1;} if ($page>=$maxpage-1) {$f1=$maxpage; $f2=$page-3;} if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) {print "<B>$i</B> &nbsp;";} else {print "<a href=admin.php?event=topic&id=$id&page=$i>$i</a> &nbsp;";}}
if ($page<=$maxpage-3 and $maxpage>5) print "... <a href=admin.php?event=topic&id=$id&page=$maxpage>$maxpage</a>";
}
} else {print "$shapka <center><font size=2><BR><BR>Объявлений в этой рубрике нет.<BR><BR><a href='add.php'>Добавить объявление</a><BR><BR><a href='admin.php'>Вернуться</a><BR><BR><BR>";}

// выводим список доступных страниц
echo'</font></center></td></tr></table><center>';



// если выбрана метка .P. - редактирование объявления
if (isset($_GET['topicrd'])) {

$topicrd=$_GET['topicrd'];
// ищем объявление для редактирования и выводим его в форму
$lines = file("$datadir/$id.dat");
$a1=$topicrd+1;
$u=$a1+1;
do {$a1--;  $dt = explode("|", $lines[$a1]); $dt[5]=str_replace("<br>", "\r\n", $dt[5]);} while($a1 > $u);

$deldate=date("d.m.Y",$dt[7]);  // конверируем дату удаления в человеческий формат
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // через сколько дней будет удалено объявление

print"<center><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1>
<FORM action='admin.php?event=rdmsgintopic&topicrd=$topicrd' method=post name=addForm>
<TBODY>
<TR><TD align=middle bgColor=#cccccc colSpan=2>Редактировать объявление</TD>
</TR><TR>";

print "<TD bgColor=#eeeeee>Ваше имя:</TD><TD bgColor=#eeeeee><B>$dt[2]</B>
</td></tr><tr><TD bgColor=#eeeeee>Категория:</TD><TD bgColor=#eeeeee><SELECT name=newrubrika style='FONT-SIZE: 13px; WIDTH: 280px'>";

// Блок считывает все категории из файла
$tdt=explode("[ktname]", $dt[1]);
$lines=file("$datadir/$datafile"); $imax=count($lines); $i="0"; $r="0"; $cn=0;
do {$dtt=explode("|", $lines[$i]);
if ($dt[8]==$dtt[0]) {$fy="selected";} else {$fy="";} 
if ($dtt[1]!="R") {print "<OPTION value=\"$i|$dtt[0]|$r|$dtt[1]|\"$fy> - $r - $dtt[1]</OPTION>\r\n";}
else {$r=$dtt[2]; if ($cn!=0) {echo'</optgroup>'; $cn=0;} $cn++; print "<optgroup label=' - $dtt[2]'>";}
$i++;
} while($i < $imax);

print "</optgroup></SELECT></TD></TR>
<TR><TD bgColor=#ffffff>Тема объявления:<FONT color=#ff0000>*</FONT><BR>(не более 100 символов)</TD>
<TD bgColor=#ffffff><INPUT name=zag value=\"$dt[3]\" style='FONT-SIZE: 14px; WIDTH: 300px'></TD></TR>

<TR><TD bgColor=#eeeeee>Тип объявления:<FONT color=#ff0000>*</FONT></TD>
<TD bgColor=#eeeeee>";

if ($dt[4]=="С") {print "<INPUT name=type type=radio value='С'checked>Спрос <INPUT name=type type=radio value='П'>Предложение";}
else {print "<INPUT name=type type=radio value='С'>Спрос <INPUT name=type type=radio value='П'checked>Предложение ";}

print "</TD></TR>
<TR><TD bgColor=#ffffff name=msg>Текст объявления:</TD>
<TD bgColor=#ffffff><TEXTAREA name=msg style='FONT-SIZE: 14px; HEIGHT: 200px; WIDTH: 300px'>$dt[5]</TEXTAREA></TD></TR>

<TR><TD bgColor=#eeeeee>Срок хранения объявления:</TD>
<TD bgColor=#eeeeee><SELECT name=days style='FONT-SIZE: 12px'>
<OPTION value=$deldays>ещё $deldays дней</OPTION>
<OPTION value=10>7 дней</OPTION>
<OPTION value=15>14 дней</OPTION>
<OPTION value=30>30 дней</OPTION>
<OPTION value=60>60 дней</OPTION>
<OPTION value=90>90 дней</OPTION>
<OPTION value=365>365 дней</OPTION></SELECT>
</TD></TR>
<BR><input type=hidden name=rubrn value=\"$dt[0]\">
<input type=hidden name=rubka value=\"$dt[1]\">
<input type=hidden name=name value=\"$dt[2]\">
<input type=hidden name=id value=\"$id\">
<input type=hidden name=fnomer value=\"$topicrd\">
<input type=hidden name=vip value=\"$dt[9]\">
<input type=hidden name=key value=\"$dt[10]\">
<input type=hidden name=today value=\"$dt[11]\">
<input type=hidden name=gorod value=\"$dt[12]\">
<input type=hidden name=phone value=\"$dt[13]\">
<input type=hidden name=smallfoto value=\"$dt[14]\">
<input type=hidden name=foto value=\"$dt[15]\">
<input type=hidden name=fotoksize value=\"$dt[16]\">
<input type=hidden name=size0 value=\"$dt[17]\">
<input type=hidden name=size1 value=\"$dt[18]\">

<TR><TD colspan=2 bgColor=#eeeeee align=middle><INPUT style='FONT-SIZE: 10px; HEIGHT: 20px; WIDTH: 100px' type=submit value=Изменить></TD></TR>

</FORM></TBODY></TABLE>
<SCRIPT language=JavaScript>document.addForm.msg.focus();</SCRIPT><BR>";
}
}
}



// Сделать копию БД
if ($_GET['event']=="makecopy")  {
if (is_file("$datadir/$datafile")) {$lines=file("$datadir/$datafile");}
if (!isset($lines)) {$datasize=0;} else {$datasize=sizeof($lines);}
if ($datasize<=0) {exit("Проблемы с Базой данных - база повреждена. Размер = 0!");}
if (copy("$datadir/$datafile", "$datadir/copy.dat")) {print "<center><BR>Копия база данных создана.<BR><BR><h3>$back</h3></center>";} else {print"Ошибка создания копии БАЗЫ Данных. Попробуйте создать вручную файл copy.dat в папке $datadir и выставить ему права на ЗАПИСЬ - 666 или полные права 777 и повторите операцию создания копии!";}
exit; }



// Восстановить из копии БД
if ($_GET['event']=="restore")  {
if (is_file("$datadir/copy.dat")) {$lines=file("$datadir/copy.dat");}
if (!isset($lines)) {$datasize=0;} else {$datasize=sizeof($lines);}
if ($datasize<=0) {exit("Проблемы с копией базы данных - она повреждена. Восстановление невозможно!");}
if (copy("$datadir/copy.dat", "$datadir/$datafile")) {print "<center><BR>БД восстановлена из копии.<BR><BR><h3>$back</h3></center>";} else {print"Ошибка восстановления из копии БАЗЫ Данных. Попробуйте вручную файлам copy.dat и mainforum.dat в папке $datadir выставить права на ЗАПИСЬ - 666 или полные права 777 и повторите операцию восстановления!";}
exit; }



// ПРОСМОТР всех пользователей
if ($_GET['event']=="userwho")  {
$userlines=file("$datadir/usersdat.php");
$ui=count($userlines)-1; $uq="25"; // По сколько человек выводить список участников
$t1="#FFFFFF"; $t2="#EEEEEE";

print"$shapka<BR><table border=1 width=98% align=center cellpadding=1 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB align=center>
<td>№ п/п</td>
<td><B>.Р.</B></td>
<td><B>.X.</B></td>
<td><B>Имя</B></td>
<td><B>Дата рег-ии</B></td>
<td><B>E-mail</B></td>
<td><B>WWW</B></td>
<td><B>Организация</B></td>
<td><B>IP / Забанить</B></td>
<td><B>Статус / VIP + -</B></td>
</tr>";

if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page="1";}
if (!ctype_digit($page)) {$page=1;}
if ($page=="0") {$page="1";} else {$page=abs($page);}

$maxpage=ceil(($ui+1)/$uq); if ($page>$maxpage) {$page=$maxpage;}

$i=1+$uq*($page-1); if ($i>$ui) {$i=$ui-$uq;}
  $lm=$i+$uq; if ($lm>$ui) {$lm=$ui+1;} 

do {$tdt=explode("|",$userlines[$i]); $i++; $npp=$i-1;

if ($tdt[10]=="ok") {$user1="<font color=#AAAAAA>обычный</font>"; $user2="<input type=text name=addvip value='30' style='width: 30px' size=18 maxlength=3><input type=submit name=submit value=' + ' style='width: 20px'>";
} else {
if ($tdt[12]>0) {$tek=mktime(); $vipdays=round(($tdt[12]-$tek)/86400); $vipdays.=" дн.";} else {$vipdays="всегда";}
$user1="<font color=red><B>VIP</B></font> - $vipdays";
if ($vipdays<0) {$user1="<font color=#AAAAAA>обычный</font>"; $vipdays="срок истёк";}
$user2="<input type=hidden name=addvip value=''><input type=submit name=submit value=' - ' style='width: 20px'>";
}

print"<tr height=30 bgcolor=$t1>
<td>$npp</td>
<td>

<table cellpadding=0 cellspacing=0><tr><td width=10 bgcolor=#00FF00><B><a href='admin.php?event=profile&pname=$tdt[0]'>.P.</a></B></td></tr></table>

</td><td align=center>

<table cellpadding=0 cellspacing=0><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?xduser=$i&page=$page'>.X.</a></B></td></tr></table>

</td>
<td><a href=\"tools.php?event=profile&pname=$tdt[0]\">$tdt[0]</a></td>
<td>$tdt[9]</td>";


if ($tdt[10]=="no" and ctype_digit($tdt[12])) {
print"<td class=$t1 colspan=9><B>[<a href='admin.php?event=activate&email=$tdt[2]&key=$tdt[12]&page=$page'>Активировать</a>]. Учётная запись не активирована  с $tdt[9]. </B>
(емайл: <B>$tdt[2]</B> ключ: <B>$tdt[12]</B>)"; 
} else {
print"
<td><a href=\"mailto:$tdt[2]\">$tdt[2]</a> &nbsp;</td>
<td><a href=\"$tdt[3]\">$tdt[3]</a> &nbsp;</td>
<td>$tdt[6] &nbsp;</td>

<form action='admin.php?badip' method=POST><td align=right>$tdt[8]
<input type=hidden name=ip value='$tdt[8]'>
<input type=hidden name=text value='За добавление нежелательных объявлений на доску! ЗА СПАМ!!!'>
<input type=submit value='БАН'></form></td>

<form action='admin.php?event=userstatus&page=$page' method=post><td align=right>$user1 <input type=hidden name=usernum value='$i'><input type=hidden name=status value='$tdt[10]'>
$user2</td></form></tr>";
}
$t3=$t2; $t2=$t1; $t1=$t3;
} while ($i<$lm);

// выводим СПИСОК СТРАНИЦ
if ($page>$maxpage) {$page=$maxpage;}
echo'</table><BR><table width=100%><TR><TD>Страницы:&nbsp; ';
if ($page>=4 and $maxpage>5) print "<a href=admin.php?event=userwho&page=1>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page<=2) {$f1=5; $f2=1;} if ($page>=$maxpage-1) {$f1=$maxpage; $f2=$page-3;} if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) {print "<B>$i</B> &nbsp;";} else {print "<a href=admin.php?event=userwho&page=$i>$i</a> &nbsp;";}}
if ($page<=$maxpage-3 and $maxpage>5) print "... <a href=admin.php?event=userwho&page=$maxpage>$maxpage</a>";

print "</TD><TD align=right>Всего зарегистрировано участников - <B>$ui</B></TD></TR></TABLE><br>";
}



// Редактирование профиля пользователя администратором
if ($_GET['event'] =="profile")  { 
if (!isset($_GET['pname'])) {exit("Попытка взлома.");}
$pname=urldecode($_GET['pname']); // РАСКОДИРУЕМ имя пользователя, пришедшее из GET-запроса.
$lines=file("$datadir/usersdat.php"); $i=count($lines); $use="0";

do {$i--; $rdt=explode("|", $lines[$i]);
if (isset($rdt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим
if (strlen($rdt[13])=="6" and ctype_digit($rdt[13])) {$rdt[13]="<B><font color=red>ожидание активации</font></B>";}
if ($pname===$rdt[0])  {
print"$shapka";
if ($rdt[10]=="ok") {$user1="<font color=#AAAAAA>обычный</font>";
} else {
if ($rdt[12]>0) {$tek=mktime(); $vipdays=round(($rdt[12]-$tek)/86400); $vipdays.=" дн. осталось";} else {$vipdays="всегда";}
$user1="<font color=red><B>VIP-статус</B></font>* ($vipdays)";
$user2="* Все добавленные Вами объявления всегда размещаются вверху страницы и выделяются другим цветом.";
if ($vipdays<0) {$user1="<font color=#AAAAAA>обычный</font> (срок истёк)"; $user2="";}}

print "<BR><center><TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action='admin.php?event=reregistr' method=post>
<TBODY><TR class=toptable><TD align=middle colSpan=2><B>Регистрационная информация</B></TD></TR>
<TR class=row1 height=25><TD>Имя:</TD><TD><B>$rdt[0]</B></TD></TR>
<TR class=row2 height=25><TD>Статус:</TD><TD>$user1</TD></TR>
<TR class=row1><TD>Пароль:<FONT color=#ff0000>*</FONT><BR>(не более 15 символов)</TD><TD><INPUT name=password class=maxiinput value='$rdt[1]' type=password></TD></TR>
<TR class=row2><TD>E-mail:<FONT color=#ff0000>*</FONT></TD><TD><INPUT name=email class=maxiinput value='$rdt[2]'></TD></TR>
<TR class=row1><TD>Город:</TD><TD><INPUT name=gorod class=maxiinput value='$rdt[11]'></TD></TR>
<TR class=row2><TD>URL:</TD><TD><INPUT name=url class=maxiinput value='$rdt[3]'></TD></TR>
<TR class=row1><TD>ICQ:</TD><TD><INPUT name=icq class=maxiinput value='$rdt[4]'></TD></TR>
<TR class=row2><TD>Телефон:</TD><TD><INPUT name=phone class=maxiinput value='$rdt[5]'></TD></TR>
<TR class=row1><TD>Организация:</TD><TD><INPUT name=company class=maxiinput value='$rdt[6]'></TD></TR>
<TR class=row2><TD>Коротко о себе:</TD><TD><TEXTAREA name=about class=maxiinput style='HEIGHT: 70px'>$rdt[7]</TEXTAREA></TD></TR>
<TR class=row1><TD height=30 colspan=2><center><INPUT type=submit class=longok value='Сохранить изменения'></TD></TR></TBODY></TABLE>
<input type=hidden name=login value='$rdt[0]'>
<input type=hidden name=oldpass value='$rdt[1]'></FORM>"; $use="1"; $i=0;
}
} // if
} while($i > "1");

// БД такого ЮЗЕРА НЕТ, например, его админ удалил или сбой БД
if ($use!="1") { echo'<br><br>Пользователь уже удалён, либо ошибка базы данных. Редактирование невозможно.'; }
} // $event=="profile"



if ($_GET['event'] =="reregistr") { // ПЕРЕрегистрация (изменение данных юзера админом)
$login=$_POST['login']; // Логин юзера
$oldpass=$_POST['oldpass']; // Старый пароль
$password=$_POST['password']; // Новый пароль
$email=$_POST['email']; $email=strtolower($email);
$gorod=$_POST['gorod'];
$url=$_POST['url'];
$icq=$_POST['icq'];
$phone=$_POST['phone'];
$company=$_POST['company'];
$about=$_POST['about'];
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера

if ($login==="" || strlen($login)>$maxname) {exit("$back ваше имя пустое, или превышает $maxname символов!</B></center>");}
if ($password==="" || strlen($password)>15) {exit("$back вы не ввели пароль!</B></center>");}

$lines=file("$datadir/usersdat.php");
$i = count($lines);

// проверка Логина/Старого пароля
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (strtolower($login)===strtolower($rdt[0]) & $oldpass===$rdt[1]) {$ok="$i";} // Ищем юзера логин/пароль
   else { if ($email===$rdt[2]) {$bademail="1"; } } // Вдруг у когото уже есть такой емайл?
} while($i > "1");
if (!isset($ok)) {exit("$back Ваш новый логин /пароль / Емайл не совпадает НИ с одним из БД. <BR><BR>
Смена электронного адреса <font color=red><B>Запрещена</B></font><BR><BR>
<font color=red><B>Ошибка скрипта или попытка взлома - обратитесь к администратору!</B></font>");}
if (isset($bademail)) {exit("$back. Участник с емайлом <B>$email уже зарегистрирован</B> на доске! <BR>Возможно, Ваш емайл продублирован в БД - обратитесь к администратору!</center>");}

$udt=explode("|",$lines[$ok]); $status=$udt[10]; $dayx=$udt[12];
$login=str_replace("|","I",$login);
$password=str_replace("|","I",$password);
$email=str_replace("|","I",$email);
$url=str_replace("|","I",$url);
$icq=str_replace("|","I",$icq);
$phone=str_replace("|","I",$phone);
$company=str_replace("|","I",$company);
$about=str_replace("|","I",$about);
$gorod=str_replace("|","I",$gorod);
$text="$login|$password|$email|$url|$icq|$phone|$company|$about|$ip|$date|$status|$gorod|$dayx|||";
$text=replacer($text);
$textdt=explode("|", $text); // Возвращаем ОЧИЩЕННЫЕ от тегов данные!!
$login=$textdt[0]; $password=$textdt[1]; $email=$textdt[2]; $url=$textdt[3];
$icq=$textdt[4]; $phone=$textdt[5]; $company=$textdt[6]; $about=$textdt[7];
$ip=$textdt[8]; $date=$textdt[9]; $status=$textdt[10]; $gorod=$textdt[11];

$file=file("$datadir/usersdat.php");
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i< sizeof($file);$i++) {if ($ok!=$i) {fputs($fp,$file[$i]);} else {fputs($fp,"$text\r\n");}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<html><body><script language='Javascript'>function reload() {location = \"admin.php?event=userwho\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#888888 align=center valign=center width=60%><tr><td><center>
<B>$login</B>, Данные успешно изменены. <BR>Через несколько секунд Вы будете автоматически перемещены на главную страницу.<BR>
<B><a href='admin.php?event=userwho'>Нажмите здесь, если не хотите больше ждать</a></B></td></tr></table></td></tr></table></center></body></html>";
exit;}




if ($_GET['event']=="blockip") { // - БЛОКИРОВКА по IP

print"$shapka";
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines); $itogo=$i;
if ($i>0) {

print"<BR><table border=1 width=98% align=center cellpadding=3 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB height=25 align=center>
<td width=20><B>.X.</B></td>
<td width=150><B>IP</B></td>
<td><B>Формулировка</B></td>
</tr>";
do {$i--; $idt=explode("|", $lines[$i]);
   print"<TR bgcolor=#F7F7F7><td width=10 align=center><table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?delip=$i'>.X.</a></B></td></tr></table></td><td>$idt[0]</td><td>$idt[1]</td></tr>";
} while($i > "0");
} else print"<br><br><H2 align=center>Заблокированные IP-адреса отсутствуют</H2><br>";
}
print"</table><br><CENTER><form action='admin.php?badip' method=POST>
Добавь IP НЕдруга! &nbsp; <input type=text style='FONT-SIZE: 14px; WIDTH: 110px' maxlength=15 name=ip> Формулировка: <input type=text style='FONT-SIZE: 14px; WIDTH: 200px' maxlength=50 name=text> 
<input type=submit value=' добавить '></form>*вводите IP аккуратно, не ставьте лишних ноликов и всякий пробелов.
<BR>Всего заБАНено пользователей - <B>$itogo</B><BR><BR></td></tr></table>"; }




if ($_GET['event']=="userstatus") { // заVIPование юзера и разVIPование
if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page=1;}
$status=$_POST['status']; // текущий статус VIP/ok
$usernum=$_POST['usernum']-1; // порядковый номер юзера в БД
$addvip=$_POST['addvip']; // кол-во дней на которые сделать юзера VIP
if ($addvip!='' and !ctype_digit($addvip)) {exit("Вы должны ввести кол-во дней на которые юзер становится VIP-юзером!");}
if ($usernum<"1") {exit("$back. Ошибка! - первую строку нельзя удалять!");}
if ($status!="vip") {$status="vip"; $dayx="";} else {$status="ok"; $dayx="";}
if ($addvip>0) {$dayx=$addvip*86400+mktime();}

$lines=file("$datadir/usersdat.php"); $imax=count($lines);
$dt=explode("|", $lines[$usernum]);
$userline="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$status|$dt[11]|$dayx|||\r\n";

$headers=null; // Настройки для отправки писем
$headers.="From: Администратор <".$adminemail.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// Собираем всю информацию в теле письма
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; // считываем урл скрипта 
$boardurl="http://$host$self";
$boardurl=str_replace("admin.php", "index.php", $boardurl);

$allmsg="<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head><body>Здравствуйте, $dt[0].<br><br>";

if ($status=="vip") {$st="с обычного на VIP"; $allmsg.="С сегодняшнего дня <font color=red><B>с $date г. с времени $time на $addvip дней<br>
изменяется Ваш статус на VIP-пользователь.</B></font><br><br>
Это изменение позволит Вашим объявлениям <B>всегда находиться</B><br>
на первой странице в первых строчках каждой рубрики.<br>По окончании срока";

} else {$st="с VIP на сбычный"; $allmsg.="С сегодняшнего дня <font color=#C0C0C0><B>с $date г. с времени $time<br>
изменяется Ваш статус с VIP-пользователь на обычный.</B></font><br>";}

$allmsg.=" Ваши объявления будут подаваться в общем порядке.<br><br>
Перейти на главную страницу доски: <a href='$boardurl'>$boardurl</a><br><br>
<small>* $dt[0], это сообщение отправлено вам от администратора доски объявлений<BR>
<B>$brdname</B>. Отвечать на него не нужно.<br></small><BR><BR></body></html>";

mail("$dt[2]", "Изменение Вашего статуса ($st) на доске объявлений ($brdname)", $allmsg, $headers);

// меняем статус юзеру
$file=file("$datadir/usersdat.php");
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($i=0; $i<$imax; $i++) {
if ($i==$usernum) {fputs($fp,"$userline");} else {fputs($fp,"$lines[$i]");}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=userwho&page=$page"); exit; }





if ($_GET['event'] =="editcity") { // РЕДАКТИРОВАНИЕ ГОРОДОВ

$record=file("$datadir/city.dat"); $imax=count($record); $i=0; $first=0; $last=$imax;

print"$shapka";

if ($imax>=0) { // если есть данные в файле ГОРОДОВ
print"<BR><table border=1 width=98% align=center cellpadding=3 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB height=25 align=center>
<td width=20><B>.X.</B></td>
<td width=50><B>№</B></td>
<td><B>Название города</B></td></tr>
<FORM action='admin.php?deletecity' method=POST name=delform>";

do {$dt=explode("|",$record[$i]);
print"<TR bgcolor=#F7F7F7><td width=10 bgcolor=#FF2244><B><input type=checkbox name='del$i' value='$dt[1]'";
if (isset($_GET['chekall'])) echo'CHECKED';
print"></B></td><TD>$dt[0]</TD><TD>$dt[1]</TD></TR>";
$i++;
} while($i < $imax);

print"</table>
<TABLE><TR><TD colspan=4>
<input type=hidden name=first value='$first'><input type=hidden name=last value='$last'><INPUT type=submit value='Удалить выбранные города'></FORM>
</TD><TD>
<FORM action='admin.php?event=editcity&chekall' method=POST name=delform><INPUT type=submit value='Пометить всё'></FORM>
</TD><TD>
<FORM action='admin.php?event=editcity' method=POST name=delform><INPUT type=submit value='Снять пометку'></FORM>
</table>
</table><br>* Файл с городами city.dat Вы всегда можете редактировать в блокноте!";
} else echo'<br><br><h2 align=center>Файл городов пуст - добавьте новые.</h2>'; // if $imax>=0

print "<center><BR><form action=?newcity method=post name=REPLIER>
Добавить город: <input type=radio name=top value='1'> в начало &nbsp;&nbsp; 
<input type=radio name=top value='0'checked> <B>в конец</B>  &nbsp;&nbsp;&nbsp;<input type=text name=city size=40> <input type=submit value='Добавить'></form>
<SCRIPT language=JavaScript>document.REPLIER.city.focus();</SCRIPT>";
} //if $event==editcity




// ФОРМА редактирования рекламного/информационного блока на главной странице (файл mainreklama.html)
if ($_GET['event'] =="editinfo") {
if (isset($_GET['chto'])) $chto=replacer($_GET['chto']);
$editfile="$datadir/mainreklama.html"; // главный файл
if ($chto=="1") $editfile="$datadir/left.html"; // левый блок
if ($chto=="2") $editfile="$datadir/right.html"; // правый блок
if ($chto=="3") $editfile="$datadir/reklama.html"; // правый блок
if ($chto=="4") $editfile="$datadir/msg.html"; // правый блок

$text=file_get_contents("$editfile"); // содержимое файла считываем в
$text=str_replace("<br>", "\r\n", $text);
print"$shapka <center><br><BR>Содержимое файла <B>mainreklama.html</B>, который отображается на главной странице доски<BR><br>
<form action='admin.php?savebiginfo' method=post name=REPLIER>
<textarea rows=10 cols=80 name=text>$text</textarea><br><br>
<input type=hidden name=chto value='$chto'>
<input type=submit value='Изменить и сохранить'><BR></TABLE>"; }





if ($_GET['event'] =="config") { // КОНФИГУРИРОВАНИЕ - выбор настроек

if ($litemode==TRUE) {$lm1="checked"; $lm2="";} else {$lm2="checked"; $lm1="";}
if ($sendmail==TRUE) {$m1="checked"; $m2="";} else {$m2="checked"; $m1="";}
if ($sendmailadmin==TRUE) {$ma1="checked"; $ma2="";} else {$ma2="checked"; $ma1="";}
if ($flagm1==TRUE) {$sm1="checked"; $sm2="";} else {$sm2="checked"; $sm1="";}
if ($flagm2==TRUE) {$sf1="checked"; $sf2="";} else {$sf2="checked"; $sf1="";}
if ($antispam==TRUE) {$as1="checked"; $as2="";} else {$as2="checked"; $as1="";}
if ($antiflud==TRUE) {$af1="checked"; $af2="";} else {$af2="checked"; $af1="";}
if ($useactkey==TRUE) {$u1="checked"; $u2="";} else {$u2="checked"; $u1="";}
if ($onlyregistr==TRUE) {$or1="checked"; $or2="";} else {$or2="checked"; $or1="";}
if ($addrem==TRUE) {$a1="checked"; $a2="";} else {$a2="checked"; $a1="";}
if ($fotoadd==TRUE) {$fa1="checked"; $fa2="";} else {$fa2="checked"; $fa1="";}
if ($fotoaddany==TRUE) {$faa1="checked"; $faa2="";} else {$faa2="checked"; $faa1="";}
if ($liteurl==TRUE) {$lu1="checked"; $lu2="";} else {$lu2="checked"; $lu1="";}
if ($mailmustbe==TRUE) {$mn1="checked"; $mn2="";} else {$mn2="checked"; $mn1="";}
if ($showten>"10") {$st1=""; $st2=""; $st3="checked";} if ($showten=="10") {$st1=""; $st2="checked"; $st3="";} if ($showten<"10") {$st1="checked"; $st2=""; $st3="";}

print "$shapka
<BR><table border=1 width=780 align=center cellpadding=1 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB align=center>
<td><B>Переменная</B></td>
<td><B>Значение</B></td></tr>
<form action='admin.php?event=confignext' method=post name=REPLIER>
<tr><td width=350>Имя скрипта (отображается <B>в title</B>)</td><td width=420><input type=text value='$brdname' name=brdname maxlength=70 size=55></tr></td>
<tr><td>Текст в заголовке доски (отображается <B>справа от логотипа</B>)</td><td><input type=text value='$brdmaintext' name=brdmaintext  maxlength=150 size=55></tr></td>
<tr><td>Включить облегчённый режим главной страницы?</td><td><input type=radio name=litemode value=\"1\"$lm1> да&nbsp; <input type=radio name=litemode value=\"0\"$lm2> нет (разделы показываются подряд, а не в столбик)</tr></td>
<tr><td>Разрешить &quot;мылить&quot; сообщения</td><td><input type=radio name=sendmail value=\"1\"$m1> да&nbsp; <input type=radio name=sendmail value=\"0\"$m2> нет</tr></td>
<tr><td>Емайл админа / отсылать сообщения / по сколько?</td><td><input type=text value='$adminemail' name=adminemail maxlength=45 size=25> <input type=radio name=sendmailadmin value=\"1\"$ma1> да&nbsp; <input type=radio name=sendmailadmin value=\"0\"$ma2> нет &nbsp; <input type=text class=post value='$maxnewadmin' name=maxnewadmin size=1 maxlength=2> (от 1 до 99)</tr></td>
<tr><td>Обязательно вводить емайл при подаче объявления не зарегистрированным пользователям?</td><td><input type=radio name=mailmustbe value=\"1\"$mn1> да&nbsp; <input type=radio name=mailmustbe value=\"0\"$mn2> нет</tr></td>
<tr><td>Пароль админа / модератора *</td><td><input name=password type=hidden value='$password'><input class=post type=text value='скрыт' maxlength=10 name=newpassword size=15> &nbsp; / &nbsp;&nbsp; <input name=moderpass type=hidden value='$moderpass'><input class=post type=text value='скрыт' maxlength=10 name=newmoderpass size=15> (зашифрованы и скрыты)</td></tr>
<tr><td>Размещать объявление можно только <B>зарегистрированным участникам?</B></td><td><input type=radio name=onlyregistr value=\"1\"$or1> да&nbsp;&nbsp; <input type=radio name=onlyregistr value=\"0\"$or2> нет - можно всем</td></tr>
<tr><td class=row1>Задействовать АНТИСПАМ / длина кода</td><td class=row2><input type=radio name=antispam value=\"1\"$as1> да&nbsp;&nbsp; <input type=radio name=antispam value=\"0\"$as2> нет &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text class=post value='$max_key' name=max_key size=4 maxlength=1> (от 1 до 9) цифр</td></tr>
<tr><td class=row1>Задействовать АНТИФЛУД / защитное время</td><td class=row2><input type=radio name=antiflud value=\"1\"$af1> да&nbsp;&nbsp; <input type=radio name=antiflud value=\"0\"$af2> нет &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text class=post value='$fludtime' name=fludtime size=4 maxlength=2> (от 1 до 20) секунд.</td></tr>
<tr><td>Разрешить добавлять комментарий?</B></td><td><input type=radio name=addrem value=\"1\"$a1> да&nbsp;&nbsp; <input type=radio name=addrem value=\"0\"$a2> нет</td></tr>
<tr><td><font color=red>Разрешить прикреплять ФОТО к объявлению / путь до папки? **</font></B></td><td rowspan=3><input type=hidden name=fotoadd value=\"0\">&nbsp;&nbsp; <input type=hidden value='$fotodir' name=fotodir maxlength=30 size=15>** Доступно только в ЛЮКС-версии доски объявлений:<br>Стоимость 450 руб, <a href='http://www.wr-script.ru/by.html'>Условия приобретения на этой странице</a> <input type=hidden name=fotoaddany value=\"0\"><input type=hidden value='$max_file_size' name=max_file_size maxlength=7 size=10></tr></td>
<tr><td><font color=red>ФОТО могут добавлять все или только зарегистрированные?</font></B></td></tr>
<tr><td><font color=red>Макс. размер заружаемого фото в байтах</font></td></tr>
<tr><td><B>ВАЖНО!**</B>при автоудалении корректировать кол-во объявлений / +1 при добавлении объявления к кол-ву</td><td><input type=radio name=flagm1 value=\"1\"$sm1> да&nbsp; <input type=radio name=flagm1 value=\"0\"$sm2> нет &nbsp;&nbsp; <input type=radio name=flagm2 value=\"1\"$sf1> да&nbsp; <input type=radio name=flagm2 value=\"0\"$sf2> нет - Глючит доска? Выбери НЕТ 2 раза!</tr></td>
<tr><td>Показывать новые объявления на главной</td><td><input type=radio name=showten value=\"0\"$st1> нет&nbsp; <input type=radio name=showten value='10'$st2> 10-ку&nbsp; <input type=radio name=showten value='20'$st3> 20-ку</tr></td>
<tr><td>Кол-во отображаемых символов объявления <B>в админке</B></td><td><input type=text value='$msglength' maxlength=3 name=msglength size=10></tr></td>
<tr><td>Использовать активацию нового пользователя по емайлу?</B></td><td><input type=radio name=useactkey value=\"1\"$u1> да&nbsp;&nbsp; <input type=radio name=useactkey value=\"0\"$u2> нет</td></tr>
<tr><td>Макс. длина темы объявления / имени добавляющего / текста объявления</td><td><input type=text value='$maxzag' name=maxzag maxlength=2 size=10> .:. <input type=text value='$maxname' maxlength=2 name=maxname size=10> .:. <input type=text value='$maxmsg' maxlength=4 name=maxmsg size=10></tr></td>
<tr><td>Макс. срок показа объявления</td><td><input type=text value='$maxdays' maxlength=3 name=maxdays size=10></tr></td>
<tr><td>Кол-во столбцов с рубриками на главной странице / Объявлений на страницу с перечнем объявлений</td><td>
<input type=text value='$colrub' maxlength=1 name=colrub size=10> &nbsp; .:. &nbsp;&nbsp; <input type=text value='$qq' maxlength=2 name=qq size=10></tr></td>
<tr><td>Делать ссылки в тексте <B>активными</B>?</td><td><input type=radio name=liteurl value=\"1\"$lu1> да&nbsp;&nbsp; <input type=radio name=liteurl value=\"0\"$lu2> нет</td></tr>
<tr><td>Относительный путь до папки с данными доски </td><td><input type=text value='$datadir' maxlength=20 name=datadir size=20> &nbsp; &nbsp; По умолчанию: &quot;<B><U>./data</U></B>&quot;.</tr></td>

<tr><td>Скин</td><td><select class=input name=brdskin>
<option value=\"$brdskin\">Текущий</option>
<option value='skin-red' style='color: #FFFFFF; background: #FF0000'>Красный</option>
<option value='skin-orange' style='color: #FFFFFF; background: #FF8000'>Оранжевый</option>
<option value='skin-green' style='color: #FFFFFF; background: #008000'>Зелёный</option>
</select></nobr></tr></td>
<tr><td colspan=2><BR><center><input type=submit value='Сохранить конфигурацию'>
<input type=hidden name=datafile value=$datafile>
</form></td></tr></table>
<center><br>* Если хотите изменить пароль - сотрите слово <B>'скрыт'</B> и введите новый пароль.<br> Рекомендую использовать только буквы и/или цифры.<br><br>
** <B>При нагрузке > 200 хостов</B> (уникальных посетителей с разным IP) в сутки<br>и/или при частом 'падении главной страницы' установите <B>ОБА переключателя в 'НЕТ'</B>!
</td></tr></table>"; }




if ($_GET['event'] =="confignext")  {  // КОНФИГУРИРОВАНИЕ ШАГ 2 - сохранение данных
// обработка полей пароль админа/модератора
if (strlen($_POST['newpassword'])<1 or strlen($_POST['newmoderpass'])<1) {exit("$back разрешается длина пароля МИНИМУМ 1 символ!");}
if ($_POST['newpassword']!="скрыт") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}
if ($_POST['newmoderpass']!="скрыт") {$pass=trim($_POST['newmoderpass']); $_POST['moderpass']=md5("$pass+$skey");}

// защита от дурака. Дожились, уже в админке защиту приходится ставить...
$fd=stripslashes($_POST['brdmaintext']); $fd=str_replace("\\","/",$fd); $fd=str_replace("?>","? >",$fd); $fd=str_replace("\"","'",$fd); $brdmaintext=str_replace("\r\n","<br>",$fd);

mt_srand(time()+(double)microtime()*1000000); $rand_key=mt_rand(1000,9999); // Генерируем случайное число для цифрозащиты

$configdata="<? // WR-board v 1.6.1 LUX // 06.08.10 г. // Miha-ingener@yandex.ru\r\r\n".
"$"."brdname=\"".$_POST['brdname']."\"; // Имя скрипта отображается в теге TITLE и заголовке\r\n".
"$"."brdmaintext=\"".$_POST['brdmaintext']."\"; // Текст, выводящийся перед формой ввода объявления\r\n".
"$"."password=\"".$_POST['password']."\"; // Пароль админа защифрован md5()\r\n".
"$"."moderpass=\"".$_POST['moderpass']."\"; // Пароль модератора защифрован md5()\r\n".
"$"."litemode=\"".$_POST['litemode']."\"; // Включить облегчённый режим главной страницы\r\n".
"$"."sendmail=\"".$_POST['sendmail']."\"; // Включить/выключить функцию отправки ЛЮБЫХ сообщений\r\n".
"$"."sendmailadmin=\"".$_POST['sendmailadmin']."\"; // Отправлять сообщения с новыми объявлениями админу?\r\n".
"$"."maxnewadmin=\"".$_POST['maxnewadmin']."\"; // По скока объявлений мылить админу?\r\n".
"$"."adminemail=\"".$_POST['adminemail']."\"; // Емайл админа\r\r\n".
"$"."fotoadd=\"".$_POST['fotoadd']."\"; // Разрешить прикреплять ФОТО к объявлению?\r\n".
"$"."fotoaddany=\"".$_POST['fotoaddany']."\"; // ФОТО могут добавлять все или только зарегистрированные?\r\n".
"$"."fotodir=\"".$_POST['fotodir']."\"; // Каталог куда будет закачан файл\r\n".
"$"."max_file_size=\"".$_POST['max_file_size']."\"; // максимальный размер фотофайла в байтах\r\r\n".
"$"."mailmustbe=\"".$_POST['mailmustbe']."\"; // Вкл/выкл обязательного заполнения емайла при подаче объявления\r\n".
"$"."flagm1=\"".$_POST['flagm1']."\"; // при автоудалении корректировать кол-во объявлений в рубрике 1/0\r\n".
"$"."flagm2=\"".$_POST['flagm2']."\"; // +1 при добавлении объявления к кол-ву в рубрике 1/0\r\n".
"$"."antispam=\"".$_POST['antispam']."\"; // Задействовать АНТИСПАМ\r\n".
"$"."antiflud=\"".$_POST['antiflud']."\"; // АНТИФЛУД вкл/выкл - 1/0\r\n".
"$"."fludtime=\"".$_POST['fludtime']."\"; // Антифлуд-время\r\n".
"$"."useactkey=\"".$_POST['useactkey']."\"; // Использовать активацию по емайлу? 1/0 - да/нет\r\n".
"$"."max_key=\"".$_POST['max_key']."\"; // Кол-во символов в коде ЦИФРОЗАЩИТЫ\r\n".
"$"."rand_key=\"".$rand_key."\"; // Случайное число для цифрозащиты\r\n".
"$"."showten=\"".$_POST['showten']."\"; // Показывать 10-ку новых объявлений нав главной\r\n".
"$"."onlyregistr=\"".$_POST['onlyregistr']."\"; // Подавать объявление можно только зарегистрированным участникам?\r\n".
"$"."msglength=\"".$_POST['msglength']."\"; // Кол-во отображаемых символов объявления в админке\r\n".
"$"."maxzag=\"".$_POST['maxzag']."\"; // Максимальное кол-во символов в теме объявления\r\n".
"$"."maxname=\"".$_POST['maxname']."\"; // Максимальное кол-во символов в имени\r\n".
"$"."maxmsg=\"".$_POST['maxmsg']."\"; // Максимальное количество символов в тексте объявления\r\n".
"$"."maxdays=\"".$_POST['maxdays']."\"; // Максимальное количество дней показа объявления\r\n".
"$"."liteurl=\"".$_POST['liteurl']."\";// Подсвечивать УРЛ? 1/0\r\n".
"$"."qq=\"".$_POST['qq']."\"; // Кол-во отображаемых объявлений на каждой странице\r\n".
"$"."colrub=\"".$_POST['colrub']."\"; // Кол-во столбцов с рубриками на главной странице\r\n".
"$"."brdskin=\"".$_POST['brdskin']."\"; // Текущий цветовой СКИН\r\n".
"$"."addrem=\"".$_POST['addrem']."\"; // разрешить добавлять комментарий?\r\r\n".
"$"."date=date(\"d.m.Y\"); // число.месяц.год\r\n".
"$"."time=date(\"H:i:s\"); // часы:минуты:секунды\r\n".
"$"."datadir=\"".$_POST['datadir']."\"; // Папка с данными доски\r\n".
"$"."datafile=\"".$_POST['datafile']."\"; // Имя файла базы данных\r\n".
"$"."back=\"<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'></head><body><center>Вернитесь <a href='javascript:history.back(1)'><B>назад</B></a>\"; // Удобная строка\r\n".
"$"."rubrika=\"\"; // временная переменная.\r\n?>";

// меняем куки на новые!!!
$tektime=time(); $wrforumm="$adminname[0]|".$_POST['password']."|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php");

$file=file("config.php");
$fp=fopen("config.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,$configdata);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }



} // if isset $event



print"<BR><small>Сегодня <b>$date</b></small>";

?>
</td></tr></table></td></tr></table>
<center><small>Powered by <a href="http://www.wr-script.ru" title="Скрипт доски объявлений" class="copyright">WR-Board</a> &copy;<br></small></font></center>
</body></html>
