<? // WR-board v 1.6.0 lite // 06.08.10 г. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);

include "config.php";


function nospam() { global $max_key,$rand_key; // Функция АНТИСПАМ
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'Защитный код: ';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из config.dbf + код меняющийся кажые 24 часа
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6> (введите число, указанное на картинке)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }



/***********************************************************************************
Функция img_resize(): генерация thumbnails
Параметры:
  $src             - имя исходного файла
  $dest            - имя генерируемого файла
  $width, $height  - ширина и высота генерируемого изображения, в пикселях
Необязательные параметры:
  $rgb             - цвет фона, по умолчанию - белый
  $quality         - качество генерируемого JPEG, по умолчанию - максимальное (100)
***********************************************************************************/
function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
  if (!file_exists($src)) return false;

  $size = getimagesize($src);

  if ($size === false) return false;

  // Определяем исходный формат по MIME-информации, предоставленной
  // функцией getimagesize, и выбираем соответствующую формату
  // imagecreatefrom-функцию.
  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc = "imagecreatefrom" . $format;
  if (!function_exists($icfunc)) return false;

  $x_ratio = $width / $size[0];
  $y_ratio = $height / $size[1];

  $ratio       = min($x_ratio, $y_ratio);
  $use_x_ratio = ($x_ratio == $ratio);

  $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
  $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
  $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
  $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

  $isrc = $icfunc($src);
  $idest = imagecreatetruecolor($width, $height);

  imagefill($idest, 0, 0, $rgb);
  imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, 
    $new_width, $new_height, $size[0], $size[1]);

  imagejpeg($idest, $dest, $quality);

  imagedestroy($isrc);
  imagedestroy($idest);

  return true;}


// Функция "ПРОДОЛЖЕНИЕ ШАПКИ" - закрывает ВСЕ таблицы
function addtop($brdskin) { global $wrbname, $wrbpass;
if (isset($_COOKIE['wrbcookies'])) {// ищем В КУКАХ wrbcookies чтобы вывести ИМЯ
$wrbc=$_COOKIE['wrbcookies']; $wrbc=htmlspecialchars($wrbc); 
$wrbc=stripslashes($wrbc); $wrbc=explode("|", $wrbc); $wrbname=$wrbc[0]; $wrbpass=$wrbc[1];} 
else {$wrbname=null; $wrbpass=null;}
echo'<TD align=right>';
if ($wrbname!=null) {print "<a href='tools.php?event=profile&pname=$wrbname'>Ваш Профиль</a>&nbsp;&nbsp;<a href='tools.php?event=clearcooke'>Выход [<B>$wrbname</B>]</a>&nbsp;";}
else {print "<a href='tools.php?event=login'>вход в систему</a>&nbsp;|&nbsp;<a href='tools.php?event=reg'>регистрация</a>&nbsp;";}
print"</TD></TR></TABLE></TD></TR></TABLE>
<TABLE cellPadding=0 cellSpacing=0 width=100%><TR><TD><IMG height=4 src='$brdskin/blank.gif'></TD></TR></TABLE>";
return true;}


function replacer ($text) { // ФУНКЦИЯ очистки кода
$text=str_replace("&#032;",' ',$text);
//$text=str_replace("&",'&amp;',$text); // закоментируйте эту строку если вы используете языки: Украинский, Татарский, Башкирский и т.д.
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


if (!is_file("$brdskin/top.html")) $topurl="$brdskin/top.html"; else $topurl="$brdskin/top.html";


//Проверка ЗАПРЕТА IP-пользователя на добавление объявлений (файл bad_ip.dat)
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<noindex><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 10000);</script><center><br><br><B>Админитратор заблокировал для Вашего IP: $ip<br> возможность добавлять объявления по следующей причине:<br><br> <font color=red><B>$idt[1].</B></font><br><br>Вам разрешено просматривать объявления,<br> а вот ДОБАВЛЯТЬ ОБЪЯВЛЕНИЯ категорически ЗАПРЕЩЕНО!</B></noindex>");
} while($i > "1");} unset($lines);}


// Событие добавления сообщения //
if(isset($_GET['event'])) { if ($_GET['event'] =="add") {


if (!isset($_POST['rules'])) exit("$back. Вам необходимо <B>согласиться с правилами.</B>");

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");}


// проверка Логина/Пароля юзера. Может он хакер, тогда облом ему
if (isset($_POST['who'])) {$who=$_POST['who'];} else {$who=null;}
if (isset($_COOKIE['wrbcookies'])) { // Этап 1
    $wrfc=$_COOKIE['wrbcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc);
    $wrfc=explode("|", $wrfc);  $wrfname=$wrfc[0]; $wrfpass=$wrfc[1];
} else {$who=null; $wrfname=null; $wrfpass=null;}

$ok=null; if ($who!=null) { // Этап 2
if ($wrfname!=null & $wrfpass!=null) {
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (isset($rdt[1])) { $realname=strtolower($rdt[0]);
   if (strtolower($wrfname)===$realname & $wrfpass===$rdt[1]) {$ok="$i";}}
} while($i > "1");
if ($ok==null) {setcookie("wrbcookies","",time()); exit("Ошибка при работе с КУКИ! <font color=red><B>Вы не сможете оставить сообщение, попробуйте подать его как гость.</B></font> Ваш логин и пароль не найдены в базе данных, попробуйте зайти на доску вновь. Если ошибка повторяется - обратитесь к администратору доски.");}
}}

if (isset($_POST['name'])) {$name=$_POST['name'];} else {$name="";}
if (isset($_POST['email'])) { $email=replacer($_POST['email']); $email=str_replace("|","I",$email);
if ($mailmustbe==TRUE) { if (!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $email) or $email=="") exit("$back и введите корректный E-mail адрес!</B></center>");}
$nameonly=$name; $name.="[email]".$email;}

$dtemp=explode("|", $_POST['rubrika']); if (!isset($dtemp[1])) exit("$back и выбирете категорию!");
$katnumber=$dtemp[0]; $rname=$dtemp[2]; $katname=$dtemp[3]; $fid=$dtemp[1]; $days=$_POST['days'];
$katname.="[ktname]".$rname;
if (!ctype_digit($fid)) {exit("$back и выбирете категорию!");}

if ($katnumber=="0") {exit("$back и выбирете категорию!");}
if ($name == "" || strlen($name) > $maxname) {exit("$back Ваше <B>имя пустое, или превышает $maxname символов!</B></center>");}
$zag=$_POST['zag'];
if ($zag == "" || strlen($zag) > $maxzag) {exit("$back Вы <B>не ввели заголовок объявления, или он превышает $maxzag символов!</B></center>");}
if (isset($_POST['type'])) {$type=$_POST['type'];} else {$type="";}
if ($type == "") {exit("$back и выбирите тип объявления (<B>Спрос</B> или <B>Предложение</B>).</B></center>");}
if ($type!="С" and $type!="П") {$type="П";}
$msg=$_POST['msg'];
if ($msg == "" || strlen($msg) > $maxmsg) {exit("$back Ваше <B>описание пустое или превышает $maxmsg символов.</B></center>");}

$newcityadd=FALSE;
if (isset($_POST['city'])) $city=$_POST['city'];
if (isset($_POST['newcity'])) {if (strlen($_POST['newcity'])>3) {$newcityadd=TRUE; $city=$_POST['newcity'];}}
if (isset($_POST['phone'])) {$phone=$_POST['phone'];} else {$phone="";}

if ($days>$maxdays or !ctype_digit($days)) {$days=$maxdays;}
$deldt=mktime()+$days*86400; // формируем дату удаления объявления
$msg=str_replace("|","I",$msg);
$zag=str_replace("|","I",$zag);
$today=mktime();

// запрашивать дату изменения файла $timer<10 - 10 секунд защита от флуда
$timetek=time(); $timefile=filemtime("$datadir/$fid.dat"); 
$timer=$timetek-$timefile; // узнаем сколько прошло времени (в секундах) 
if ($timer<10 and $timer>0) {exit("$back рубрика была активна менее $timer секунд назад.<br> Подождите Ещё несколько секунд и повторите отправку объявления.");}

// Сравниваем имя подавшего объявления с зарегистрированными
$flag="0"; $status="no"; $namesm=strtolower($name);
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
$rdt[0]=strtolower($rdt[0]);
if ($rdt[0]==$namesm) {$email="$rdt[2]"; $flag="yes";
if ($rdt[12]>0) {$vipdays=round(($rdt[12]-$today)/86400);} else {$vipdays="999";}
if ($rdt[10]==="vip" and $vipdays>0) {$status="vip";}}
} while($i > "1");

if (!isset($_COOKIE['wrbcookies']) and $flag=="yes") {exit("$back к сожалению, участник с таким именем уже зарегистрирован на доске и <BR><B>Вы не можете подать объявление под таким именем.</B>");}

if ($antiflud!="0") {  // функция АНТИФЛУД часть 1 - проверка на наличие в текущей рубрике
$linesn = file("$datadir/$fid.dat"); $in=count($linesn);
if ($in > 0) {
$lines=file("$datadir/$fid.dat"); $i=count($lines)-1; $itogo=$i; $dtf=explode("|",$lines[$i]);
$txtback="$dtf[0]|$dtf[1]|$dtf[2]|$dtf[3]|$dtf[4]|$dtf[5]|";
$txtflud="$katnumber|$katname|$name|$zag|$type|$msg|";
$txtflud=htmlspecialchars($txtflud); $txtflud=stripslashes($txtflud);
$txtflud=str_replace("\r\n","<br>",$txtflud);
if ($txtflud==$txtback) {exit("$back Данное объявление уже размещено на доске. Флудить на доске запрещено!");}}

// часть 2 - проверка на наличие объявления в последней 10-20ке
unset($lines); $lines=file("$datadir/newmsg.dat"); $max=count($lines); $i=$max-1;
if ($max > 0) { do { $dtf=explode("|",$lines[$i]);
$text1="$dtf[5]"; $text2="$msg"; $text2=replacer($text2);
if ($text1==$text2) {exit("$back Данное объявление уже размещено на доске. Флудить на доске запрещено!");}
$i--; } while($i > "1"); }
} //if $antiflud!=0

// БЛОК ГЕНЕРИРУЕТ СЛЕДУЮЩИЙ ПО ПОРЯДКУ НОМЕР ОБЪЯВЛЕНИЮ
// считываем весь файл в объявлениями в память
$allid=null; $records=file("$datadir/$fid.dat"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); $allid[$i]=$rd[10]; } while($i>0);
//natcasesort($allid); // сортируем по возрастанию
$id=1000; $id="$fid$id";
do { $id++; if (is_file("$datadir/$fid$id.dat")) $id++; } while(in_array($id,$allid));
} else $id=$fid."1000"; // if ($i > 0)

//print"<PRE>"; print_r($allid); print "$id - $fid";


// блок отключен, так как из-за этой системы проблемы с индексацией страниц
// КОЛДУЕМ рандомный КОД объявления
//$add=null; $z=null; 
//do { $id=mt_rand(1000,9999); if ($fid<10) $add="0"; 
//if (!is_file("$datadir/$add$fid$id.dat") and strlen($id)==4) $z++;
//} while ($z<1); $id="$add$fid$id";

if (strlen($id)>8) exit("<B>$back. Номер объявления должен быть числом. Критическая ошибка скрипта или попытка взлома</B>");

$text="$katnumber|$katname|$name|$zag|$type|$msg|$date|$deldt|$fid|$status|$id|$today|$city|$phone||||||$rname|$ok|$ip||||||";

$foto=""; $fotoksize=""; $size[0]=""; $size[1]=""; $smallfoto="";

$text=htmlspecialchars($text);
$text=stripslashes($text);
$text=str_replace("\r\n","<br>",$text);

// Возвращаем ОЧИЩЕННЫЕ от тегов данные!!
$textdt=explode("|", $text);
$katnumber=$textdt[0];
$tdt=explode("[ktname]", $textdt[1]); $katname="$tdt[0]";
$name=$textdt[2]; $zag=$textdt[3]; $type=$textdt[4];
$msg=$textdt[5]; $date=$textdt[6]; $deldt=$textdt[7];
$fid=$textdt[8]; $status=$textdt[9]; $today=$textdt[11];
$city=$textdt[12]; $phone=$textdt[13]; $smallfoto=$textdt[14];
$foto=$textdt[15]; $fotoksize=$textdt[16];
if (!isset($email)) {$email="";}
// запись данных в файл НОВЫХ обяъвлений - будет мылится админу
$fp=fopen("$datadir/adminmail.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
flock ($fp,LOCK_UN);
fclose($fp);

// удаление последней строки
$lines=file("$datadir/adminmail.dat"); $i=count($lines); $aitogo=$i-1;
if ($i>$maxnewadmin) {

if ($sendmailadmin =="1")  { // отправка СООБЩЕНИЯ админу на мыло
$headers=null;
$headers.="From: $name <$email>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

$deldate=date("d.m.Y",$deldt); // конвертируем дату удаления в человеческий формат

if (isset($nameonly)) {$name=$nameonly;} // Что не отправлять юзеру СПЕЦКОД [email] вместо имени

// подготавливаем данные для отправки на емайл и вывода на экран
if ($type=="С") {$sptype="Спрос";} else {$sptype="Предложение";}
$msg=str_replace("\r\n", "<br>", $msg);

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$boardurl="http://$host$self";
$boardurl=str_replace("add.php", "index.php", $boardurl);
$boardadm=str_replace("index.php", "admin.php", $boardurl);


$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body><table border=0 align=center cellpadding=6 cellspacing=0>
<TR><TD colspan=3 align=center><h3>$aitogo новых объявлений</h3>$brdname (<a href='$boardurl'>$boardurl</a>)</TD></TR>
<TR><TD>";

do {$i--; $dt=explode("|", $lines[$i]); $msnum=$dt[0];

$zdt=explode("[ktname]",$dt[1]); $zdt2=explode("[email]",$dt[2]); if (!isset($zdt2[1])) $zdt2[1]="";
$dt[7]=date("d.m.Y",$dt[7]); // конверируем дату удаления в человеческий формат

// Собираем всю информацию в теле письма
$allmsg.="<table border=1 align=center cellpadding=2 cellspacing=0 width=95% bordercolor='#DBDBDB'>
<tr><td colspan=2 align=center bgcolor='#E4E4E4'>Рубрика: &nbsp;<B>$zdt[0]</B> >> <B>$zdt[1]</B> >> $dt[4]</td></tr>
<tr bgcolor='#F2F2F2'><td width=300>Имя: <B>$zdt2[0]</B></td><td width=70%>Заголовок: <B>$dt[3]</B></td></tr>
<tr bgcolor='#F8F8F8'><td>Е-майл: <B>$zdt2[1]</B></td><td rowspan=5 valign=top>$dt[5]<br> <div align=left><a href='$boardurl?id=$dt[10]'>Подробнее >>></a></div></td></tr>
<tr bgcolor='#F8F8F8'><td>Дата подачи: $dt[6] г.</td></tr>
<tr bgcolor='#F2F2F2'><td>Дата удаления: <B>$dt[7]</B> г.</td></tr>
<tr bgcolor='#F2F2F2'><td>Править (
<a href='$boardadm?event=topic&id=$dt[10]&topicrd=$i'>редактировать *</a> / 
<a href='$boardadm?id=$dt[10]&msgtype=$dt[7]&topicxd=$i&page=1'>удалить *</a>)</td></tr>
<tr bgcolor='#F2F2F2'><td>Перейти в рубрику <a href='$boardurl?fid=$dt[8]'><B>$zdt[1]</B></a></td></tr>
</table><br>";

} while($i>"1");

$allmsg.="
* для редактирования / удаления объявлений зайдите <a href='$boardadm'>в админку</a>, авторизируйтесь. 
Не закрывая окно браузера, перейдите в это письмо и Вы сможете править объявления прямо отсюда.<br>

<br><br>** Это сообщение сгенерировано и отправлено роботом с доски объявлений. Отвечать на него не нужно.
Если Вы получили это письмо, значит Ваш емайл указан в панели администратора и включена отправка последних объявлений на емайл.</body></html>";

mail("$adminemail", "$brdname ($aitogo новых объявлений на Вашей доске объявлений) по состоянию на $date $time", $allmsg, $headers); }

$fp=fopen("$datadir/adminmail.dat","w+");
flock ($fp,LOCK_EX);
fputs($fp,"");
flock ($fp,LOCK_UN);
fclose($fp);}
// Конец блока отправки админу сообщений на мыло


$fp=fopen("$datadir/$fid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Блок добавляет единицы к кол-ву объявлений в категории
$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору. <br><B>Файл РУБРИК несуществует! Зайдите в админку и создайте рубрики!</b>");
$i=count($lines); 

$itogo=$i; $ok=null;

do {$i--; $dt=explode("|",$lines[$i]);
$lines[$i]=$lines[$i];
if ($fid==$dt[0]) {$ok=1; if ($type=="С") {$dt[3]++;} else {$dt[2]++;} $lines[$i]="$fid|$dt[1]|$dt[2]|$dt[3]|\r\n";}
if ($ok!=null) {if ($dt[1]=="R") {$ok=null; $dt[3]++; $lines[$i]="$dt[0]|R|$dt[2]|$dt[3]|\r\n";}}
} while($i > 0);
$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($i=0;$i<$itogo;$i++) {fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// добавляем данные в СТАТИСТИКУ
$fp=fopen("$datadir/stat.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|$deldt|$type|\r\n");
flock ($fp,LOCK_UN);
fclose($fp);

if ($newcityadd==TRUE) { // добавляем город в файл с городами - city.dat (если введён свой)
$fp=fopen("$datadir/city.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"999|$city|\r\n");
flock ($fp,LOCK_UN);
fclose($fp); }

// добавляем в 10-20-ку новых объявлений
$newmessfile="$datadir/newmsg.dat";
if (is_file($newmessfile)) {
$file=file($newmessfile); $i=count($file);
if ($showten>"0" & $i<$showten) {
$fp=fopen("$newmessfile","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
flock ($fp,LOCK_UN);
fclose($fp);
}  else  {
$fp=fopen($newmessfile,"w");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($ii=1;$ii<$showten; $ii++) {fputs($fp,$file[$ii]);}
fputs($fp,"$text\r\n");
flock ($fp,LOCK_UN);
fclose($fp);}
}


if (isset($_POST['idmsg'])) { // Если выбрано ПРОДЛЕНИЕ объявления
unset($lines); $lines=file("$datadir/wait.dat");
$itogo=count($lines)-1; $i=$itogo; $k=null;
do {$dt=explode("|",$lines[$i]);
if ($dt[10]!=$id) {$newlines[$k]=$lines[$i]; $k++;} $i--;
} while ($i>="0");
$itogo=count($newlines);
$fp=fopen("$datadir/wait.dat","w");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($i=1;$i<$itogo; $i++) {fputs($fp,$newlines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);
}



// отправка админу и юзеру сообщения о добавлении объявления
$headers=null; // Настройки для отправки писем
$headers.="From: робот-администратор <$adminemail>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

$deldate=date("d.m.Y",$deldt); // конвертируем дату удаления в человеческий формат

if (isset($nameonly)) {$name=$nameonly;} // Что не отправлять юзеру СПЕЦКОД [email] вместо имени

// подготавливаем данные для отправки на емайл и вывода на экран
if ($type=="С") {$sptype="Спрос";} else {$sptype="Предложение";}
$msg=str_replace("\r\n", "<br>", $msg);

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$boardurl="http://$host$self";
$boardurl=str_replace("add.php", "index.php", $boardurl);
$remurl=str_replace("index.php", "tools.php", $boardurl);

// Собираем всю информацию в теле письма
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<title>Ваше объявление добавлено</title>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body bgcolor='#EAEAEA'>
<table bordercolor=#FFFFFF border=1 align=center width='99%' cellpadding='0' cellspacing='0'><tr><td>
<table width='100%' cellpadding='4' cellspacing='0'><tr><td  bgcolor='#FFCACA' align=left>
Доска объявлений \"<B><a href='$boardurl'>$boardurl</a></B>\"</td><td width='*' bgcolor='#FFCACA' align=right><B>$brdname</B>
</td></tr></table>
<table border=0 width='100%' cellpadding='4' cellspacing='0'><tr><td width='100%' bgcolor='#ffffff' align=center>
<br><h3 align='center'>Ваше объявление успешно добавлено</h3>
<table border=1 cellpadding='1' cellspacing='0' bordercolor=white BGCOLOR='#FFD0D0' WIDTH='99%'>
<tr><td>Ваше имя: <B>$name</B></td></Tr>
<tr><td>ID: <B>$id</B> &nbsp;&nbsp; Расположение: &nbsp; <B>$rname</B> >> <B>$katname</B> >> $sptype</td></tr>
<TR><TD>Добавлено <B>$date г. - <small>$time</small></B> &nbsp;&nbsp;&nbsp; Срок размещения: <B>$days дн.</B> &nbsp;&nbsp;&nbsp;  Дата удаления: <B>$deldate</B> г.</td></tr>
<tr><td>Город: <B> $city </B> Тел: <B> $phone</B></td></Tr>
<tr><td>E-mail: <B><a href='mailto:$email'>$email</a></B></td></tr>
<tr><td>Заголовок объявления: <B>$zag</B></td></Tr>
<tr><td bgcolor=white><Div Align='Justify'>$msg</Div></td></Tr>
</table>
<br><center><a href='$boardurl?id=$id'>Просмотреть объявление</a><BR>
<a href='$boardurl?fid=$fid'>Вернуться в рубрику <B>$katname</B></a><br><br>
</td></tr></table>
</td></tr></table>
<UL><A Href='$boardurl'>$boardurl</A> - $brdname<Br><A Href='$remurl?event=addrem'>Связаться с Администратором доски</A>";
$printmsg="$allmsg </body></html>";

$kompr=file_get_contents("$datadir/msg.html"); // Считываем содержимое файла комерческого предложения в переменную
$kompr.="<br><br>* Это сообщение сгенерировано и отправлено роботом с доски объявлений. Отвечать на него не нужно.</body></html>";
$allmsg.=$kompr;

if ($sendmail=="1") { // Отправляем ПИСЬМА если разрешена отправка
if (isset($email) & $flag=="yes") { mail("$email", "Объявление ID-$id ($brdname)", $allmsg, $headers);} }

// Cтрока удаляет КУКУ на компе юзера если он удалён в админке
if (!isset($flag) and $onlyregistr==1) { if (isset($_COOKIE['wrbcookies'])) {setcookie("wrbcookies", "", time());}}

print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 2000);</script>$printmsg"; exit;
}
}
//} // if is_file($fid.dat)








if (!isset($_GET['event']) and !isset($_GET['fid'])) {  // ГЛАВНАЯ СТРАНИЦА

$rubrika="Добавление объявления";
include "$topurl"; addtop($brdskin); // подключаем ШАПКУ
if ($onlyregistr=="1" and !isset($wrbname))
{ print"<BR><BR><BR><BR><BR><center><font size=-1><B>Уважаемые посетители!</B><BR><BR> 
На нашей доске размещение объявления<BR><BR><B> без регистрации <font color=#FF0000> запрещено!</B></font><BR><BR>
Зарегистрироваться можно по <B><a href='tools.php?event=reg'>этой ссылке</a></B><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>"; }

else {
if (isset($_GET['fid'])) {$fid=$_GET['fid'];} else {$fid="";}
$today=mktime();

// обнуляем переменные на всякий случай (защита от взлома)
$zag=null; $msg=null; $t1=null;$t2=null;
$name=null; $email=null; $city=null; $phone=null; $info=null; $addpole=null;



// Если выбрано ПРОДЛЕНИЕ объявления
if (isset($_GET['id'])) { $id=$_GET['id']; $stop=0; $num=null;
$info="<br><br>Пожалуйста, проверьте достоверность объявления.<br> Если есть неточности - поправьте, сделайте выбор:</center><br> - срока размещения;<br> - введите защитный код; <br>- согласитесь с правилами;<br> - нажмите сохранить.<br><br>";
$lines=file("data/wait.dat"); $itogo=count($lines)-1; $i=$itogo; // Считываем все объявления в память
do {$dt=explode("|",$lines[$i]);
if ($dt[10]==$id) {$num=$i; $i="0";} $i--;
} while ($i>="0");

if ($num!=null) {
$dto=explode("|",$lines[$num]); $tdw=explode("[ktname]", $dto[1]);
$addpole="<input type=hidden name=idmsg value='$id'>";
$zag=$dto[3]; // тема
$msg=$dto[5]; // текст
if ($dto[4]=="П") {$t1="checked"; $t2="";} else {$t2="checked"; $t1="";} // тип
$city=$dto[12]; // город
$phone=$dto[13]; // телефон
if (stristr($dto[2],"[email]")) {$tdt=explode("[email]", $dto[2]); $name=$tdt[0]; $email=$tdt[1];} else {$name="$dt[2]"; $email="$dt[2]";}
$fid=$dto[8];} else (exit("<center><br><br>Такого объявления уже нет в базе.<br> Рекомендую разместить его снова.<br> Для этого перейдите по ссылке <a href='add.php'>Разместить новое объявление</a>"));
} //конец блока ПРОДЛЕНИЕ объявления



print"<center><TABLE class=bakfon cellPadding=2 cellSpacing=1>
<FORM action='add.php?event=add' method=post name=addForm enctype=\"multipart/form-data\">
<TBODY>
<TR class=row2><TD height=23 align=left colSpan=2><center><B>$rubrika</B>$info</TD></TR>";

echo'<tr class=row1><TD>Категория:</TD><TD><SELECT name=rubrika class=maxiinput><option>Выберите рубрику</option>\r\n';

// Блок считывает все категории из файла
$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору. <br><B>Файл РУБРИК несуществует! Зайдите в админку и создайте рубрики!</b>");
$imax=count($lines);

$i="0"; $r="0"; $cn=0;
do {$dt=explode("|", $lines[$i]);
if ($fid==$dt[0]) $fy="selected"; else $fy="";
if ($dt[1]!="R") print "<OPTION value=\"$i|$dt[0]|$r|$dt[1]|\"$fy>$r - $dt[1]</OPTION>\r\n";
else {$r=$dt[2]; if ($cn!=0) {echo'</optgroup>'; $cn=0;} $cn++; print "<optgroup label=' - $dt[2]'>";}
$i++;
} while($i < $imax);

print "</optgroup></SELECT></TD></TR>
<TR class=row2><TD>Тема объявления:<FONT color=#ff0000>*</FONT><BR>(не более $maxzag символов)</TD>
<TD><INPUT name=zag class=maxiinput maxlength=$maxzag value=\"$zag\"></TD></TR>

<TR class=row1><TD>Текст объявления:</TD>
<TD><TEXTAREA class=maxiinput name=msg style='HEIGHT: 200px; WIDTH: 370px'>$msg</TEXTAREA></TD></TR>

<TR class=row2><TD>Тип объявления:<FONT color=#ff0000>*</FONT></TD>
<TD><INPUT name=type type=radio value='П' $t1><B><font color=#EE2200>П</font></B>редложение 
<INPUT name=type type=radio value='С' $t2><B><font color=#1414CD>С</font></B>прос </TD></TR>

<tr class=row1 height=23><td>Город:</td><TD><SELECT name=city style='FONT-SIZE: 14px; WIDTH: 200px'><OPTION value='0'> - - - - - Ввести свой - - - - -</OPTION>";
$slines = file("data/city.dat"); $smax = count($slines); $i="0"; do {$dts=explode("|",$slines[$i]);
print "<OPTION value=\"$dts[1]\">$dts[1]</OPTION>\r\n"; $i++; } while($i < $smax);
print "</SELECT>&nbsp; Другой: <input type=text value='' name=newcity size=30 maxlength=40 class=maininput style='FONT-SIZE: 14px; WIDTH: 180px'><BR> * если вашего города нет в списке введите его в поле справа</TD></tr>

<TR class=row1 height=23><TD>Ваше имя:$addpole";

if (isset($wrbname)) {
print "<INPUT type=hidden name=who value='да'><INPUT type=hidden name=rules><input type=hidden name=name value='$wrbname'></TD><TD><B>$wrbname</B></td></tr>";
}  else  {
print "
<FONT color=#ff0000>*</FONT></TD><TD><INPUT type=hidden name=who value=''>
<INPUT name=name class=maxiinput value=\"$name\" maxlength=30>
<TR class=row2 height=23><TD>Ваш Е-майл:<FONT color=#ff0000>*</FONT></TD><TD><INPUT name=email class=maxiinput value=\"$email\" maxlength=30></td></tr>
<TR class=row2 height=23><TD>Телефон: <BR>(по шаблону: (495) 344356)</TD><TD><INPUT name=phone value=\"$phone\" class=maxiinput maxlength=35></td></tr>
";}

echo'<TR class=row1><TD>Срок хранения объявления:</TD>
<TD><SELECT name=days style="FONT-SIZE: 13px">
<OPTION value=7>7 дней</OPTION>
<OPTION value=14>14 дней</OPTION>
<OPTION selected value=30>30 дней</OPTION>';

if (isset($wrbname)) {
print"<OPTION value=60>60 дней</OPTION>
<OPTION value=$maxdays>$maxdays дней</OPTION>";}

echo '</SELECT></TD></TR>';

if ($antispam==TRUE and !isset($wrbname)) {print"<tr class=row1><td>АНТИСПАМ</td><TD>"; nospam();} // АНТИСПАМ !

print"</TD></TR>";

if (!isset($wrbname)) {
print"<TR class=row2><TD colSpan=2><INPUT type=checkbox name=rules>С <B><A href='tools.php?event=about'>правилами</A></B> ознакомлен</TD></TR></TR>";
} // if !isset($wrfname)

echo'<TR class=row1><TD colspan=2 align=middle><INPUT class=longok type=submit value=Сохранить></TD></TR></FORM></TBODY></TABLE>';
}
}




if (isset($_GET['fid']) and isset($_GET['id'])) {
$fid=$_GET['fid']; $id=$_GET['id'];

if (!ctype_digit($fid) or !ctype_digit($id)) {exit("$back. Попытка взлома. Хакерам здесь не место.");}

if (is_file("$datadir/$id.dat")) { $linesn = file("$datadir/$id.dat"); $in=count($linesn);
if ($in > 15) {exit("$back <B>более 15 комментариев</B> к объявления добавлять запещено.</center>");}}

// Событие добавления сообщения
if(isset($_GET['add'])) {

if (!isset($_COOKIE['wrbcookies'])) {exit("<br><br><br><B><center>Добавление коментариев<br> разрешено только ЗАРЕГИСТРИРОВАННЫМ участникам!!!</B></center><br><br><br>");}

if (isset($_POST['name'])) {$name=$_POST['name'];} else {$name="";}
if (strlen($name)<1 || strlen($name) > $maxname) {exit("$back Ваше <B>имя пустое, или превышает $maxname символов!</B></center>");}
$name=str_replace("|","I",$name);

if (isset($_POST['type'])) {$type=$_POST['type'];} else {$type="0";}
if (strlen($type)>2) {exit("$back. Оценка может состоять только из двух цифр!");}
if (!ctype_digit($type)) {exit("$back. Попытка взлома. Хакерам здесь не место.");}

$msg=$_POST['msg']; if ($msg=="" || strlen($msg) > $maxmsg) {exit("$back Ваш <B>комментарий пуст или превышает $maxmsg символов.</B></center>");}
$msg=str_replace("|","I",$msg);

if (isset($_POST['email'])) {$email=$_POST['email'];} else {$email="";}
$email=str_replace("|","I",$email);

$day=mktime();
$text="$name|$email|$msg|$day|$type|";
$text=htmlspecialchars($text);
$text=stripslashes($text);
$text=str_replace("\r\n","<br>",$text);

if ($antiflud=="1") {  // функция АНТИФЛУД здесь!
if (is_file("$datadir/$id.dat")) { // проверяем есть ли такой файл
$linesn = file("$datadir/$id.dat"); $in=count($linesn);
if ($in > 0) {
$lines=file("$datadir/$id.dat"); $i=count($lines)-1; $itogo=$i; $dtf=explode("|",$lines[$i]);
$txtback="$dtf[0]|$dtf[1]|$dtf[2]|$dtf[3]|$dtf[4]|";
if ($text==$txtback) {exit("$back Данный комментарий уже размещён! Флудить на доске запрещено!");} }
}}

if (is_file("$datadir/$id.dat")) { $lines = file("$datadir/$id.dat"); $itogo=count($lines);} else {$itogo=0;}

$lines[$itogo]="$text\r\n"; $p=$itogo+1;

$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0; $i<$p; $i++) {fputs($fp,"$lines[$i]");}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 1000);</script>"; exit;
}


$rubrika="Добавление комментария к объявлению";

include "$topurl"; addtop($brdskin); // подключаем ШАПКУ

if (isset($wrbname)) { print"<center><BR><BR><BR>
<FORM action='add.php?add=1&fid=$fid&id=$id' method=post name=addForm>
<TABLE class=bakfon cellPadding=2 cellSpacing=1><TBODY>
<TR class=toptable><TD height=23 align=middle colSpan=2><B>$rubrika</B></TD></TR>
<TR class=row1 height=23><TD>Ваше имя: <FONT color=#ff0000>*</FONT></TD><TD><INPUT name=name class=maininput style='FONT-SIZE: 14px; WIDTH: 300px' maxlength=30></td></tr>
<TR class=row2><TD>Емайл:</TD><TD><INPUT name=email class=maininput style='FONT-SIZE: 14px; WIDTH: 300px' maxlength=$maxzag></TD></TR>
<TR class=row1><TD>Текст комментария: <FONT color=#ff0000>*</FONT></TD><TD><TEXTAREA class=maininput name=msg style='FONT-SIZE: 14px; HEIGHT: 100px; WIDTH: 300px'></TEXTAREA></TD></TR>
<TR class=row2><TD>Оценить важность <BR> объявления:</TD><TD>&nbsp;&nbsp;&nbsp; <INPUT name=type type=radio value='1'>1&nbsp;&nbsp; <INPUT name=type type=radio value='2'>2&nbsp;&nbsp; <INPUT name=type type=radio value='3'>3&nbsp;&nbsp; <INPUT name=type type=radio value='4'>4&nbsp;&nbsp; <INPUT name=type type=radio value='5'>5</TD></TR>
<TR class=row1><TD colspan=2 height=32 align=middle><INPUT class=longok type=submit value=Сохранить></TD></TR>
</TBODY></TABLE></FORM><BR><BR><BR>";
} else {echo'<br><br><br><B><center>Добавление коментариев<br> разрешено только ЗАРЕГИСТРИРОВАННЫМ участникам!!!</B></center><br><br><br>';}


}


if (is_file("$brdskin/bottom.html")) include "$brdskin/bottom.html";
?>

<center><small>Powered by <a href="http://www.wr-script.ru" title="Скрипт доски объявлений" class="copyright">WR-Board</a> &copy; 1.6 Lite<br></small></font></center>
</body></html>
