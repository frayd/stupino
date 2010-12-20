<? // WR-board v 1.6.0 lite // 06.08.10 г. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);

include "config.php";

// Функция "ПРОДОЛЖЕНИЕ ШАПКИ" - закрывает ВСЕ таблицы
function addtop($brdskin) { global $wrbname,$wrbpass,$datadir;
if (isset($_COOKIE['wrbcookies'])) {// ищем В КУКАХ wrbcookies чтобы вывести ИМЯ
$wrbc=$_COOKIE['wrbcookies']; $wrbc=htmlspecialchars($wrbc); 
$wrbc=stripslashes($wrbc); $wrbc=explode("|", $wrbc); $wrbname=$wrbc[0]; $wrbpass=$wrbc[1];} 
else {$wrbname=null; $wrbpass=null;}
echo'<TD align=right>';
if ($wrbname!=null) {print "<a href='tools.php?event=profile&pname=$wrbname'>Ваш Профиль</a>&nbsp;&nbsp;<a href='tools.php?event=clearcooke'>Выход [<B>$wrbname</B>]</a>&nbsp;";}
else {print "<a href='tools.php?event=login'>вход в систему</a>&nbsp;|&nbsp;<a href='tools.php?event=reg'>регистрация</a>&nbsp;";}
print"</TD></TR></TABLE></TD></TR></TABLE>
<TABLE cellPadding=0 cellSpacing=0 width=100%><TR><TD><IMG height=4 src='$brdskin/blank.gif'></TD></TR></TABLE>
<table cellspacing=0 cellpadding=0 width=98% border=0><tbody>
<tr><td valign=top align=center>" ;
if (is_file("$datadir/left.html")) include"$datadir/left.html";
echo'</td><td  width=100% align=center valign=top>';
return true;}

// Функция сортировки
function prcmp ($a, $b) {if ($a==$b) return 0; if ($a>$b) return -1; return 1;}


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


if (!is_file("$brdskin/top.html")) $topurl="$brdskin/top.html"; else $topurl="$brdskin/top.html";



if (!isset($_GET['id'])) {    // ГЛАВНАЯ СТРАНИЦА ДОСКИ (Левел 0)

$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору. <br><B>Файл РУБРИК несуществует! Зайдите в админку и создайте рубрики!</b>");
$i=count($lines); $imax=$i;

include "$topurl"; addtop($brdskin); // подключаем ШАПКУ

$imagefile=0; $rubitogo=0; $msgitogo=0; $itogo=0;
$record=array_fill(0, $imax,''); // создаём пустой массив под названия рубрик и разделов

// считаем количестов РУБРИК (чтобы картинку потом верную показывать)
do {$i--; $dt=explode("|",$lines[$i]); if ($dt[1]=="R") $imagefile++;} while($i>0); $i=$imax;

// формируем список "наоборот" (так как мы считаем количество объявлений в рубрике)
do {$i--; $dt=explode("|",$lines[$i]);


$fid="$dt[0]"; $url="index.php?id=$fid";

if ($dt[1]=="R" and $i>0) {$record[$i].='</TD></TR></TABLE><br>@endtable@';}

if ($dt[1]=="R") {
$record[$i].="<TABLE cellSpacing=0 cellPadding=0 width=250 border=0>
<TR>
<TD><IMG src='$brdskin/$imagefile.gif' border=0></TD>
<TD width=200 valign=middle><h4><B><a href='index.php?id=$fid&R'>$dt[2]</a> [$rubitogo]</B></h4></TD>
</TR><TR><TD colspan=3>
";
$rubitogo=0; $imagefile--;}


if ($dt[1]!="R") { $rubitogo=$rubitogo+$dt[2]+$dt[3]; $msgitogo=$dt[2]+$dt[3]; $itogo=$itogo+$msgitogo;

if ($msgitogo>"0") {$ok="have.gif"; $ok1="onmouseover=\"tover(this)\" onmouseout=\"tout(this)\""; 
$ok2="<A href='$url' style='text-decoration: none;'>$dt[1]</A>";
} else {$ok2="<font color=#808080>$dt[1]</font>"; $ok1=""; $ok="nohave.gif";}

if ($litemode==TRUE) { $record[$i].='<span style="line-height:20px">';
 $record[$i].="$ok2 "; if ($msgitogo>0) $record[$i].="[$msgitogo] "; else $record[$i].="&nbsp;\r\n";
 } else {
 $record[$i].="<TR onmouseover=\"trtover(this)\" onmouseout=\"trtout(this)\">
 <TD colspan=2 $ok1 height=20><img src='$brdskin/$ok'>&nbsp;$ok2"; 
 if ($msgitogo>0) $record[$i].=" [$msgitogo]"; else $record[$i].="&nbsp; </TD></TR>\r\n";
}  //if ($litemode==TRUE)
} // if $dt[1]!="R"
} while($i > 0);


// печатаем список "наоборот" (так как он снизу вверх сформирован)
$si=0; 

if (is_file("$datadir/mainreklama.html")) include"$datadir/mainreklama.html";
echo'<TABLE cellSpacing=0 cellPadding=0 width=800 border=0 align=center><TR><TD valign=top>';
do {
if (strstr($record[$i],"@endtable@")) { $si++;
if ($si==$colrub) {$chto="</TD></TR><TR><TD vAlign=top>\r\r\n"; $si=0;} else $chto="</TD><TD vAlign=top>\r\r\n";
$record[$i]=str_replace("@endtable@","$chto",$record[$i]); }

print"$record[$i]"; $i++; } while($i < $imax);
echo'</TD></TR></TABLE>
</TD></TR></TABLE>'; // закрываем таблицы с объявлениями

if ($realbase==FALSE) $text_base="<br><br><font color=red>Основной файл базы данных повреждён, доска работает на копии. Администратор! Зайди в админпанель и восстанови базу данных из копии, затем сделай пересчёт количества объявлений!</font>"; else $text_base="";

// закрываем центральную таблицу
print"<center>Всего объявлений в базе: <B>$itogo</B> $text_base</center><BR>";



// Вставляем РЕКЛАМУ СПРАВА
echo'<td valign=top>'; if (is_file("$datadir/right.html")) include"$datadir/right.html"; echo'</td></tr></tbody></table>';

// Выводим 10-20 последних объявлений
$shapka20="<TABLE align=center cellPadding=0 cellSpacing=0 width=99%>";
$shapka10="<TABLE align=center cellPadding=0 cellSpacing=0 width=99%>";
if (is_file("$datadir/newmsg.dat") and $showten>="1") { // проверяем есть ли такой файл
$linesn = file("$datadir/newmsg.dat"); $in=count($linesn);
if ($in > 0) {
$newdat=file("$datadir/newmsg.dat");
$in=count($newdat)-1; $iall=$in; $ia=$in+1;
echo'<TABLE cellPadding=2 cellSpacing=1 align=center width=98%>';

if ($showten>"10") {print "<TR class=toptable height=18><TD colspan=4 align=center><B> Новых объявлений: $ia</B> &nbsp;&nbsp;&nbsp; <font color='#0000FF'><strong>C</strong></font>-Спрос <font color='#FF0000'><strong>П</strong></font>-Предложение</TD></TR><TR><TD width=50% valign=top>$shapka20";}
//if ($showten>"10") {print "<TR class=toptable height=18><TD colspan=4 align=center><B>$ia новых объявлений:</B></TD></TR><TR><TD width=50% valign=top>$shapka20";}

   else {print "<TR><TD>&nbsp;</td><TD align=center><TABLE align=center cellPadding=3 cellSpacing=0 width=468><TR class=toptable height=18><TD colspan=4 align=center><B>Последние $ia объявлений:</B></TD></TR>";}

do {$dtn=explode("|", $newdat[$in]);
$tdt=explode("[ktname]", $dtn[1]);

if (!isset($tdt[1])) {$tdt[1]="";} // удалить ДЛЯ тех, кто не конвертирует БД

$url="index.php?id=$dtn[10]";
$dtn[5]=substr($dtn[5],0,150); // образаем сообщение до 150 символов
$dtn[5]=str_replace("<br>","\r\n",$dtn[5]);
$dtn[1]=str_replace("[ktname]"," --> ",$dtn[1]);

$dtn[7]=date("H:i",$dtn[7]);
$datemsg=substr($dtn[6],0,5);
if ($dtn[4]=="П") {$colorsp="#ff3333";} else {$colorsp="#1414CD";}
if (round($iall/2)==($in+1) & $showten>10) {print"</table></td><td valign=top>$shapka10";}
if ($dtn[9]=="vip") {$st1="<B>"; $st2="VIP-объявление \r\n";} else {$st1=""; $st2="";}
print"
<TR height=25 onmouseover=trtover(this) onmouseout=trtout(this)>
<TD><FONT color=$colorsp><B>$dtn[4]</B></FONT></TD>
<TD>$datemsg <small>$dtn[7]</small></TD>
<TD width=78%>$st1<A href='$url' style='text-decoration: none;' title='$dtn[5] \r\r\n $dtn[1]\r\r\n $st2\r\n размещено $dtn[6] г.'>$dtn[3]</A></TD>
<TD><IMG alt='перейти' border=0 src='$brdskin/go.gif'></TD>
</TR>";
$in--;
} while($in >"-1");
} echo'</table></td></tr></table>';
}

} // конец главной страницы







// СТРАНИЦА с объявлениями текущей рубрики(Левел 0+1)
if (isset($_GET['id']) and (strlen($_GET['id'])<=3) and isset($_GET['R'])) { $fid=$_GET['id'];

$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору. <br><B>Файл РУБРИК несуществует! Зайдите в админку и создайте рубрики!</b>");
$i=count($lines); 

include "$topurl"; addtop($brdskin); // подключаем ШАПКУ

$n="0"; $a1="-1"; $u=$i-1; $total="0"; $i="0"; $cm="0"; $si="0"; $flag=null; $itogos=0; $itogo="0"; $it=0;

echo'<TABLE class=bigmaintbl border=0 width=98% cellSpacing=10 cellPadding=1 align=center><TR><TD align=left vAlign=top>';

do {$a1++; $dt=explode("|", $lines[$a1]);

$url="index.php?id=$dt[0]";
if ($dt[1]=="R") $cm++;

if ($dt[1]=="R" and $dt[0]==$fid) { $flag++; $si++;
print"<TABLE cellSpacing=0 cellPadding=0 width=250 border=0><TR>
<TD><IMG src='$brdskin/$cm.gif' border=0></TD>
<TD width=200 valign=middle><h4><B>$dt[2]</B></h4></TD>
</TR><TR><TD colspan=3><TABLE cellSpacing=0 cellPadding=0 width=100% border=0>"; }

if ($dt[1]!="R" and $flag==1) $it=$dt[2]+$dt[3];

if ($dt[1]=="R" and $dt[0]>$fid) $flag=null;

if ($dt[1]!="R" and $dt[0]>$fid and $flag!=null)  {

$itogoo=$dt[2]+$dt[3]; if ($itogoo>5) $ob=$dt[0];
if (($dt[2]+$dt[3])>"0") {$ok="have.gif"; $ok1="onmouseover=\"tover(this)\" onmouseout=\"tout(this)\"";} 
else { 
if (is_file("$datadir/$fid.dat")) {$line=file("$datadir/$dt[0].dat"); $itek=count($line);} $ok1=""; $ok="nohave.gif"; }

if ($it>0) {$ok2="<A href='$url' style='text-decoration: none;'>$dt[1]</A>";} else {$ok2="<font color=#808080>$dt[1]</font>";}

print"<TR onmouseover=\"trtover(this)\" onmouseout=\"trtout(this)\">
<TD $ok1 height=20>
<img src='$brdskin/$ok'>$ok2</TD><TD width=20>"; if ($it>0) print"$it"; else print"&nbsp; "; print"</TD></TR>\r\n";}

$i++;
$itogo=$itogo+$it;
} while($a1 < $u);

// закрываем центральную таблицу
print"</TD></TR></TABLE></TD></TR></TABLE></td>";


$ivip="1";
if (isset($ob) and is_file("$datadir/$ob.dat")) { // проверяем есть ли такой файл
$lines=null; $lines=file("$datadir/$ob.dat"); $i=count($lines); $number=0;
if ($i>5) {$ii=5;
$lt=explode("|",$lines[0]); $tdt=explode("[ktname]", $lt[1]); 
print"<TD valign=top>";

print"<TABLE class=bakfon cellPadding=2 cellSpacing=1 width=98% align=center><TBODY>
<TR class=row1 height=28><TD colspan=6 class=main align=center><h3>Последние объявления: $tdt[1] --> $tdt[0]</h3></TD></TR>

<TR class=toptable align=center>
<TD><B>№</B></TD>
<TD><B>Т</B></TD>
<TD width=50%><B>Заголовок</B></TD>
<TD width=40%><B>Имя, дата, действует</B></TD></TR>";

do {$ii--; $dt=explode("|",$lines[$ii]);

$url="index.php?id=$dt[10]";

$deldate=date("d.m.Y",$dt[7]); // конверируем дату удаления в человеческий формат
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // Дата удаления
$dt[5]=str_replace("<br>", "\r\n", $dt[5]);
$dt[5]=substr($dt[5],0,200); $dt[5].="...";
$dt[6]=str_replace("200", "0", $dt[6]);

// приводим слово ДЕНЬ/ДНЯ/ДНЕЙ - к нужному типу
$dney="дней"; if ($deldays<="0") {$deldays=1;}
if ($deldays>20) {$ddays=substr($deldays,-1);} else {$ddays=$deldays;}
if ($ddays=="1") {$dney="день";}
if ($ddays=="2" or $ddays=="3" or $ddays=="4") {$dney="дня";}

if ($dt[9]=="vip") {print "<TR height=28 class=vip onmouseout=\"vipout(this)\" onmouseover=\"vipover(this)\">";}
    else {print "<TR height=28 class=row1 onmouseover=\"trtover(this)\" onmouseout=\"trtout(this)\">";}

if (stristr($dt[2],"[email]")) {$tdt=explode("[email]",$dt[2]); $usdat="<TD>$tdt[0]";} else {$usdat="<TD onmouseover=\"tover(this)\" onmouseout=\"tout(this)\"><A href='tools.php?event=profile&pname=$dt[2]'>$dt[2]</A>";}

$number++;
print"
<TD align=center><B>$number</B></TD>
<TD><B>$dt[4]</B></TD>
<TD onmouseover=\"tover(this)\" onmouseout=\"tout(this)\"><A href='$url' style='text-decoration: none;' title='$dt[5]'>";
print"$dt[3]</A></TD>$usdat, $dt[6], действует <B>$deldays</B> $dney</TD></TR>";
if ($dt[4]=="С") {$itogos++;}

} while($ii > 0);
print"</TD></tr></table></TD>";
}}

print"</tr></table><center>Всего объявлений в рубрике: <B>$itogo</B></center><BR>";
}








// СПИСОК ОБъЯВЛЕНИЙ (ЛЕВЕЛ 2)
if (isset($_GET['id']) and (strlen($_GET['id'])<=3) and !isset($_GET['R'])) { $fid=$_GET['id'];

$addbutton="<a href=\"add.php?id=$fid\">Добавить объявление в этот раздел</a>";

// Защиты
$deleted="$back. Файл рубрики НЕ существует! Возможно администратор удалил данную рубрику.";
if (!ctype_digit($fid)) exit(' <b>Попытка взлома. Хакерам здесь не место.</b>');

$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору. <br><B>Файл РУБРИК несуществует! Зайдите в админку и создайте рубрики!</b>");
$i=count($lines);

$imax=$i; if (($fid>999) or (strlen($fid)==0)) exit("$deleted");

if (!is_file("$datadir/$fid.dat")) exit("$deleted"); // проверяем есть ли такой файл

else {

$lines=file("$datadir/$fid.dat"); $itogo=count($lines); $maxi=$itogo-1; $n="0";

if ($itogo > 0) {

//            функция АВТОУДАЛЕНИЯ здесь!
$tekdate=mktime(); $i=$itogo; $newi="-1"; $pred="0"; $spros="0"; $todelete="0"; $itogos="0"; $old=0;

do {$i--; $dt=explode("|",$lines[$i]);
    if ($dt[4]!="П") {$itogos++;} // строка посчитывает кол-во объявлений Спрос/Предложение
    if ($dt[7]<$tekdate) {
    // Собираем в переменную $scribemass массив данных объявлений, где срок 
    // уже закончился и нужно выслать письмо с предложением продлить объявление
    $scribemass[$old]=$lines[$i]; $old++;
    $todelete++; if ($dt[4]=="П") {$pred++;} else {$spros++;}} else {$newi++; $newlines[$newi]=$lines[$i];}
} while($i>0); $old--; // нужно чтобы верные обяъвления отправить;


// $newlines - массив с данными, в которых уже нет просроченных объявлений
if (isset($newlines)) {$newitogo=count($newlines)-1;} else {$newitogo="0"; $newlines[0]="";}

// Если в БД были объявления, которые необходимо удалить, то удаляем ИХ ВСЕ!
// Блок используется для УДАЛЕНИЯ / АВТОУДАЛЕНИЯ выбранного ОБЪЯВЛЕНИЯ

if ($todelete>"0") {

// записываем в файл БД данные в которых уже НЕТ ПРОСРОЧЕННЫХ ОБЪЯВЛЕНИЙ
$fp=fopen("$datadir/$fid.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($i=0; $i <= $newitogo; $i++) {fputs($fp,$newlines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);

// БЛОК записывает ПРОСРОЧЕНЫЕ объявления в файл
$fp=fopen("$datadir/oldmess.dat","a+");
flock ($fp,LOCK_EX);
for ($i=0; $i <= $old; $i++) {fputs($fp,$scribemass[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);

// Блок вычитает единицу из кол-ва объявлений в рубрике - если разрешено
if (!isset($flagm1)) {$flagm1=1;}
if ($flagm1>"0") {
$lines = file("$datadir/$datafile"); $i=count($lines);
do {$i--; $dt=explode("|", $lines[$i]);
// находим в БД раздел, соответствующий разделу, в котором мы сейчас находимся
if ($fid==$dt[0]) {
$dt[2]=$dt[2]-$pred; if ($dt[2]<"0") {$dt[2]="0";}
$dt[3]=$dt[3]-$spros; if ($dt[3]<"0") {$dt[3]="0";}
if ($newitogo==0) {$dt[2]="0"; $dt[3]="0";}
$text="$fid|$dt[1]|$dt[2]|$dt[3]|";
$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($ii=0;$ii< sizeof($file);$ii++) {if ($i!=$ii) {fputs($fp,$file[$ii]);} else {fputs($fp,"$text\r\n");}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
}
} while($i > 0);
} // конец если разрешено отнимать единицу

// считываем данные раздела в память вновь - так как мы удалили просроченные
$lines = file("$datadir/$fid.dat");
$itogo=count($lines); $maxi=$itogo-1; $i=$itogo;

}  // if ($todelete > 0)
}  // if ($itogo > 0)
// ************* функция АВТОУДАЛЕНИЯ выше!


if ($itogo > 0) {$i=$itogo; $lt=explode("|",$lines[0]); $tdt=explode("[ktname]", $lt[1]); 

if (!isset($tdt[1])) {$tdt[1]="";} // удалить кто не использует конветер!

$razdel=$tdt[1]; $rubrika="$tdt[0] .:. $tdt[1]";

include "$topurl"; addtop($brdskin); // подключаем ШАПКУ

$rubrika="$tdt[0]";
// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) {$page=1;} else {$page=$_GET['page']; if (!ctype_digit($page)) {$page=1;} if ($page<1) $page=1;}

$ivip=0; $itogos=0;

print"<TABLE class=bakfon cellPadding=2 cellSpacing=1 width=98% align=center><TBODY>
<TR class=row1 height=28><TD colspan=6 class=main align=center><strong>$razdel</strong> <small>--></small> <strong>$rubrika</strong><br>";

if (is_file("$datadir/reklama.html")) include"$datadir/reklama.html";

print"</TD></TR>
<TR class=toptable align=center>
<TD><B>№</B></TD>
<TD><B>Т</B></TD>
<TD><B>Ф</B></TD>
<TD width=60%><B>Заголовок</B></TD>
<TD width=20%><B>Имя</B></TD>
<TD width=20%><B>размещено, действует</B></TD></TR>";


// БЛОК СОРТИРОВКИ
$p=$itogo; $ivip=0;

do {$p--; $dt=explode("|", $lines[$p]);
if ($dt[9]=="vip") {$ivip++;}
$newlines[$p]="$dt[9]|$dt[11]|$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[10]|$dt[12]|$dt[13]|$dt[14]|$dt[15]|$dt[16]|$dt[17]|$dt[18]|";
} while($p > 0);

usort($newlines,"prcmp");

$p=$itogo;
do {$p--; $dt=explode("|", $newlines[$p]);
  $lines[$p]="$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[0]|$dt[11]|$dt[1]|$dt[12]|$dt[13]|$dt[14]|$dt[15]|$dt[16]|$dt[17]|$dt[18]|\r\n";
} while($p > 0);
// КОНЕЦ сортировки


// Показываем QQ ОБЪЯВЛЕНИЙ
$maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) {$page=$maxpage;}

$fm=$qq*($page-1); if ($fm>$maxi) {$fm=$maxi-$qq;}
$lm=$fm+$qq; if ($lm>$maxi) {$lm=$maxi+1;}

do {$dt=explode("|", $lines[$fm]);
$fm++;
$url="index.php?id=$dt[10]";

if ($dt[4]=="П") $colorsp="#ff3333"; else $colorsp="#1414CD";

$deldate=date("d.m.Y",$dt[7]); // конверируем дату удаления в человеческий формат
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // Дата удаления
$dt[3]=str_replace("\r\n", "", $dt[3]);
$dt[3]=str_replace("<br>", "", $dt[3]);
$dt[5]=str_replace("<br>", "\r\n", $dt[5]);
if (strlen($dt[5])>300) {$dt[5]=substr($dt[5],0,300); $dt[5].="...";}
$dt[6]=str_replace("200", "0", $dt[6]);

// приводим слово ДЕНЬ/ДНЯ/ДНЕЙ - к нужному типу
$dney="дней"; if ($deldays=="0") {$deldays=1;}
if ($deldays>20) {$ddays=substr($deldays,-1);} else {$ddays=$deldays;}
if ($ddays=="1") {$dney="день";}
if ($ddays=="2" or $ddays=="3" or $ddays=="4") {$dney="дня";}

if ($dt[9]=="vip") {print "<TR height=28 class=vip onmouseout=\"vipout(this)\" onmouseover=\"vipover(this)\">";}
    else {print "<TR height=28 class=row1 onmouseover=\"trtover(this)\" onmouseout=\"trtout(this)\">";}

if (stristr($dt[2],"[email]")) {$tdt=explode("[email]",$dt[2]); $usdat="<TD>$tdt[0]";} else {$usdat="<TD onmouseover=\"tover(this)\" onmouseout=\"tout(this)\"><A href='tools.php?event=profile&pname=$dt[2]'>$dt[2]</A>";}

if (strlen($dt[14])<4) {$fotoznak="<img src='$brdskin/blank.gif'>";} else {$fotoznak="<A href='#' onclick=\"window.open('tools.php?event=viewfoto&foto=$dt[15]','$dt[10]','width=$dt[17],height=$dt[18],left=100,top=100,scrollbars=yes,resizable=yes')\"><img border=0 src='$brdskin/foto.gif'></a>";}
print"
<TD align=center><B>$fm</B></TD>
<TD><FONT color=$colorsp><B>$dt[4]</B></FONT></TD>
<TD>$fotoznak</TD>
<TD onmouseover=\"tover(this)\" onmouseout=\"tout(this)\"><A href='$url' style='text-decoration: none;' title='$dt[5]'>
<B>$dt[3]</B>
</A><br><br>$dt[5]</TD>$usdat</TD>
<TD align=center> $dt[6], действует <B>$deldays</B> $dney</TD></TR>";

if (($dt[9]=="vip") and ($ivip==1)) {echo'<TR height=15 class=small bgColor=#FFFFFF><TD colspan=6>&nbsp;</TD></TR>';}
$ivip--;

if ($dt[4]=="С") {$itogos++;}

} while($fm < $lm);

$itogop=$i-$itogos;



// выводим список доступных страниц
$maxi=$itogo-1; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;

$pageinfo='<TABLE cellPadding=0 cellSpacing=0 width=98% align=center><TBODY><TR><TD width=50%><div class=pgbutt>Страницы:&nbsp; ';

$addp="class=sel"; $addpage="";

if ($page>=4 and $maxpage>5) $pageinfo.="<a style=\"width:10px\" $addp href=index.php?id=$fid>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page==1) { $f1=$page+4; $f2=$page; }
if ($page==2) { $f1=$page+3; $f2=$page-1; }
if ($page==$maxpage) { $f1=$page; $f2=$page-4; }
if ($page==$maxpage-1) { $f1=$page+1; $f2=$page-3; }
if ($maxpage<4) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) {$pageinfo.="<B>$i</B> &nbsp;";
} else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a style=\"width:10px\" $addp href=index.php?id=$fid$addpage>$i</a> &nbsp;";}}
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a style=\"width:10px\" $addp href=index.php?id=$fid&page=$maxpage>$maxpage</a>";
echo("</TBODY></TABLE><BR> $pageinfo </b></span>&nbsp; <noindex><a rel=nofolow href='tools.php?id=$fid&page=$page'>Для_печати</a></noindex>");
} else {$rubrika="Объявлений в данной рубрике нет"; include "$topurl"; addtop($brdskin); print"<center><BR><BR><BR><BR><BR><font size=-1><B>Уважаемый посетитель!</B><BR><BR> В данном разделе в настоящее время объявлений нет.<BR><BR> Вы можете <B>$addbutton</B> или <BR><BR> перейти на главную страницу доски по <B><a href='index.php'>этой ссылке</a></B>.<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>";}
}
echo'</tr></table><BR>';

// Вставляем РЕКЛАМУ СПРАВА
echo'<td valign=top>'; if (is_file("$datadir/right.html")) include"$datadir/right.html"; echo'</td></tr></tbody></table>';

}






// ПОКАЗЫВАЕМ ТЕКУЩЕЕ ОБЪЯВЛЕНИЕ (ЛЕВЕЛ 3)
if (isset($_GET['id']) and strlen($_GET['id'])>=6) {

if (strlen($_GET['id'])==6) $fid=substr($_GET['id'],0,2); else $fid=substr($_GET['id'],0,3);

$error="Ошибка скрипта! Обратитесь к администратору. Свзяаться можно по ссылке Обратной связи на главной скрипта.";
$deleted="$back. Файл рубрики НЕ существует! Возможно администратор удалил данную рубрику.";
if (!isset($_GET['id'])) exit("$error");
if ($_GET['id']==="") exit("$error");
$id=$_GET['id'];

$ok=null; if (is_file("$datadir/$fid.dat")) { if (sizeof("$datadir/$fid.dat")>0) $lines=file("$datadir/$fid.dat");
$cy=count($lines)-1; $itogo=$cy; $i=$itogo; $number=null;
if ($cy>=0) {do {$dt=explode("|",$lines[$i]); if ($dt[10]==$id) {$ok=1; $number=$i;} $i--;} while ($i >= 0);}}

if ($ok==null) {$rubrika="объявление отсутствует"; 
ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,follow">',$topurl);
print"$topurl"; addtop($brdskin); print"<BR><BR><BR><BR><BR><center><font size=-1><B>Уважаемый посетитель!</B><BR><BR> 
Извините, но запрашиваемое Вами <B>объявление недоступно.</B><BR><BR>
Скорее всего, <B>закончился срок его показа</B>, и оно было удалено с доски.<BR><BR>
Вы можете <B><a href='index.php?id=$fid'>перейти в раздел</a></B> где было размещено объявление.<BR>
Возможно, Вы найдёте похожее объявление в этом разделе.<BR><BR>
<B>Перейти на главную</B> страницу доски можно по <B><a href='index.php'>этой ссылке</a></B><BR><BR><BR><BR><BR><BR><BR><BR><BR>";

} else {

$dt=explode("|",$lines[$number]);

// формируем содержимое тега title для страницы
$rub=$dt[3]; if (strlen($rub)>98) {$rub=substr($rub,0,98); $rub.="...";} 
$tdt=explode("[ktname]", $dt[1]); 

if (!isset($tdt[1])) {$tdt[1]="";} // удалить кто не использует конветер!

$razdel=$tdt[1]; $rubrika="$rub .:. $tdt[0] .:. $tdt[1]";

include "$topurl"; addtop($brdskin); // подключаем ШАПКУ
$rubrika=$tdt[0];
// считываем данные о пользователе, оставившем сообщение
$userline=file("$datadir/usersdat.php"); $i=count($userline); $usernum="";

// проходим по всем юзерам и сверяем данные
do {$i--; $rdt=explode("|", $userline[$i]);
if ($dt[2]==$rdt[0] and $dt[20]!="") $usernum="$i";
} while($i > "1");

if ($usernum!="") {$rdt = explode("|", $userline[$usernum]);} else {$rdt[0]="";$rdt[2]="";$rdt[3]="";$rdt[4]="";$rdt[5]="";$rdt[6]="";}

$deldate=date("d.m.Y",$dt[7]); // конверируем дату удаления в человеческий формат
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // через сколько дней будет удалено объявление
$dt[7]=date("H:i:s",$dt[7]);
$mstek=$number+1;
$numtek=$cy-$number+1;

$dney="дней"; // приводим слово ДЕНЬ/ДНЯ/ДНЕЙ - к нужному типу
if ($deldays>20) {$ddays=substr($deldays,-1);} else {$ddays=$deldays;}
if ($ddays=="1") {$dney="день";}
if ($ddays=="2" or $ddays=="3" or $ddays=="4") {$dney="дня";}

if ($dt[4]=="П") {$dt[4]="<font color=#EE2200>Предложение";} else {$dt[4]="<font color=#1414CD>Спрос";}

$foto="</tr>"; $tblwidth="600"; $tblheight="400"; $fwidth=$dt[17]+50; $fheigh=$dt[18]+50;
if (strlen($dt[14])>2) {$foto="<td rowspan=12 valign=bottom align=center>Прикреплено Фото: <BR>
<A href='#' onclick=\"window.open('tools.php?event=viewfoto&foto=$dt[15]','$id','width=$fwidth,height=$fheigh,left=100,top=100,scrollbars=yes,resizable=yes')\">
<img src='$fotodir/$dt[14]' border=0></a><BR> Размер: <B>$dt[16]</B> Кб.<BR> Разрешение: <B>$dt[17] x $dt[18]</B>.</font></b></td></tr>";} else {$foto=""; $tblwidth="500"; $tblheight="370";}

if ($usernum!="") {

$userinfo="<TR class=row2 height=23><TD>Организация:</TD><TD>$rdt[6] &nbsp;</TD></TR>
<TR class=row1 height=23><TD>Автор:</TD><TD><a href='tools.php?event=profile&pname=$dt[2]'>$dt[2]</a></TD></TR>
<TR class=row2 height=23><TD>E-mail:</TD><TD width=220><a href='#' onclick=\"window.open('tools.php?event=mailto&email=$rdt[2]&name=$rdt[0]&id=$id','email','width=600,height=300,left=170,top=100')\">Отправить письмо автору</A></TD></TR>
<TR class=row1 height=23><TD>Город:</TD><TD>$rdt[11] &nbsp;</TD></TR>
<TR class=row2 height=23><TD>URL:</TD><TD><a href='$rdt[3]' target='_blank'>$rdt[3]</a></TD></TR>
<TR class=row1 height=23><TD>Телефон:</TD><TD>$rdt[5] &nbsp;</TD></TR>";

}   else   {

$userinfo="<TR class=row2 height=23><TD>Автор:</TD><TD>";
if (stristr($dt[2],"[email]")) { $tdt=explode("[email]", $dt[2]); $userinfo.="$tdt[0]</TD></TR><TR class=row1 height=23><TD>E-mail:</TD><TD width=220><A href='#' onclick=\"window.open('tools.php?event=mailto&email=$tdt[1]&name=$tdt[0]&id=$id','email','width=600,height=300,left=100,top=100')\">Отправить письмо автору</A>";} else {$userinfo.="$dt[2]";}
if (!isset($dt[13])) {$dt[13]="";} if (!isset($dt[12])) {$dt[12]="";}
$userinfo.="</td></tr><TR class=row2 height=23><TD>Город:</TD><TD width=220>$dt[12]</td></tr><TR class=row1 height=23><TD>Телефон:</TD><TD width=220>$dt[13]</td></tr>";
}

if ($liteurl==TRUE) $dt[5]=preg_replace("#(\[url=([^\]]+)\](.*?)\[/url\])|(http://(www.)?[0-9a-z\.-]+\.[a-z]{2,6}[0-9a-z/\?=&\._-]*)#","<a href=\"$4\" >$4</a> ",$dt[5]);

print "<center><TABLE class=bakfon align=center cellPadding=3 cellSpacing=1 width=$tblwidth height=$tblheight><TBODY>
<TR class=row1 height=28 align=center><TD colspan=3><font style='FONT-SIZE: 15px;'><strong>$razdel</strong> <small>>></small> <strong>$rubrika</strong></font></TD></TR>
<TR HEIGHT=23><TD align=middle class=toptable colSpan=3 width='100%'><table width=100%><TR align=center><TD><B>$dt[3]</B></TD><TD width=20><B>$numtek</B></TD></TR></table></TD></TR>
<TR class=row1 height=23><TD width=140>Тип объявления:</TD><TD width=220><B>$dt[4]</B></TD>
$foto
<TR class=row2 height=23><TD>Дата опубликования:</TD><TD>$dt[6] &nbsp;<small>$dt[7]</small></TD></TR>
<TR class=row1 height=23><TD>Дата удаления:</TD><TD>$deldate (осталось <B>$deldays</B> $dney)</TD></TR>
$userinfo </TD></TR>
<TR class=row1 height=23><TD colSpan=2>Текст объявления:</TD></TR>
<TR class=row1><TD bgColor=#FFFFFF colSpan=2 width=500 vAlign=top><BR>$dt[5]<BR><BR></TD>
</TR></TBODY></TABLE>
<BR><table width=300><TR align=center><TD width=70>";

if ($number>0) {$last=$mstek-2; $dtlast=explode("|",$lines[$last]); print "<A href='index.php?id=$dtlast[10]'><IMG alt='Предыдущее объявление' border=0 src='$brdskin/forward.gif'></A>";}
    print "</td><td width=90><A href='index.php?id=$fid'><IMG alt='Вернуться в раздел $dt[1]' border=0 src='$brdskin/back.gif'></A></td><td width=80>";
if ($number<$cy) {$next=$mstek; $dtnext=explode("|",$lines[$next]); print "<A href='index.php?id=$dtnext[10]'><IMG alt='Следующее объявление' border=0 src='$brdskin/next.gif'></A>";}

echo'</td></tr></table>';

// Вставляем РЕКЛАМУ СПРАВА
echo'<td valign=top>'; if (is_file("$datadir/right.html")) include"$datadir/right.html"; echo'</td></tr></tbody></table>';

if (is_file("$datadir/$id.dat")) {

$rlines=file("$datadir/$id.dat"); $ri=count($rlines); $bals=0; $all=0;
echo'<BR><table class=bakfon align=center cellPadding=2 cellSpacing=1 width=560><TR><TD class=main align=center height=25 width=560 class=main colspan=3>Комментарии посетителей:</TD></TR>
<TR class=row1 height=20 align=center><TD><B>Имя, Емайл, Дата</TD><TD>Текст комментария</TD><TD>Оценка</TD></TR>';
do {$ri--; $edt=explode("|",$rlines[$ri]);
$edt[3]=date("d.m.Y H:i:s",$edt[3]);
if ($edt[4]!=0) {$bals=$bals+$edt[4]; $all++;} else {$edt[4]="-";}
print"<TR class=row1><TD><B>$edt[0]</B><BR>$edt[1]<BR>$edt[3]</TD><TD>$edt[2]</TD><TD align=center>$edt[4]</TD></TR>";
} while($ri>0);
if ($bals==0) {$itogobals="н/д";} else {$itogobals=round($bals*10/$all)/10;}
print "</TD></TR><TR class=row1><TD colspan=3 align=center height=28><font style='FONT-SIZE: 13px'>Важность объявления: &nbsp;&nbsp;<B>$itogobals</B> / 5</font></TD></TR></TABLE>";


}
if ($addrem=="1" and isset($_COOKIE['wrbcookies'])) print"<center><BR><font style='FONT-SIZE: 13px'><B><a href='add.php?id=$id'>Добавить комментарий</a></B></font></center><BR><BR>";

}

}


if (is_file("$brdskin/bottom.html")) include "$brdskin/bottom.html";

?>

<center><small>Powered by <a href="http://www.wr-script.ru" title="Скрипт доски объявлений" class="copyright">WR-Board</a> &copy; 1.6 Lite <br></small></font></center>
</body></html>
