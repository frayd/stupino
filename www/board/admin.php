<? // WR-board v 1.6.1 LUX // 06.08.10 �. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);

include "config.php";

$skey="657567"; // !!! ��������� ���� !!! 
// ��������� �� ���� � ��� ��� ������� ������� :-)
// !!! ����� ����� - ������ �������������� � ���������� ���������� ����������!
// ��� ��������� ������ ������ �������������� ������ � 104
// �������� ���������� ��� � config.php � ���������� $password � $moderpass

// �����������
$adminname="admin|moder|"; // ��� �������������� � ����� ���� | ��� ���������� � � ����� |
$adminpass=$password;


function nospam() { global $max_key,$rand_key; // ������� ��������
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���: �������� ������ 24 ����
$stime=md5("$dopkod+$rand_key");// ���.���
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //����� + ���� �� config.dbf + ��� ���������� ����� 24 ����
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6>
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }


function replacer ($text) { // ������� ������� ����
$text=str_replace("&#032;",' ',$text);
$text=str_replace("&",'&amp;',$text); // �������������� ��� ������ ���� �� ����������� �����: ����������, ���������, ���������� � �.�.
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


// ������ ����� - ������� ����
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrforumm","",time()-3600); Header("Location: index.php"); exit; } }

if (isset($_COOKIE['wrforumm'])) { // ������� ���/������ �� ���� � �������� � ������ �����
$text=$_COOKIE['wrforumm'];
$text=str_replace("\r\n","",$text); $text=str_replace(" ","",$text); // �������� ���������� ������� 
if (strlen($text)>60) {exit("������� ������ - ����� ���������� ���� ������ �������!");}
$text=replacer($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];
$adminname=explode("|",$adminname);

if ($name1!=$adminname[0] and $name1!=$adminname[1] or $pass1!=$adminpass) 
{sleep(1); setcookie("wrforumm", "0", time()-3600); Header("Location: admin.php"); exit;} // ������� �������� ����!!!

} else { // ���� ���� ���� ����


if (isset($_POST['name']) & isset($_POST['pass'])) { // ���� ���� ���������� �� ����� ����� ������
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // �������� ���������� ������� 
if (strlen($text)<4) {exit("$back �� �� ����� ��� ��� ������!");}
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$qq=md5("$pass+$skey"); print"$qq"; exit; // ������������� ��� ��������� MD5 ������ ������!

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}


// ������� �������� ���/������ � �������� � ������ �����
$adminname=explode("|",$adminname);
// �������������� ������������� ����
if ($name==$adminname[0] & md5("$pass+$skey")==$adminpass) 
{$tektime=time(); $wrforumm="$adminname[0]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}
// ���������� ������������� ����
if ($name==$adminname[1] & md5("$pass+$skey")==$moderpass) 
{$tektime=time(); $wrforumm="$adminname[1]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}

exit("$back ��� ������ <B>��������</B>!</center>");

} else { // ���� ���� ������, �� ������� ����� ����� ������

echo "<html><head><META HTTP-EQUIV='Pragma' CONTENT='no-cache'><META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'><META content='text/html; charset=windows-1251' http-equiv=Content-Type><style>input, textarea {font-family:Verdana; font-size:12px; text-decoration:none; color:#000000; cursor:default; background-color:#FFFFFF; border-style:solid; border-width:1px; border-color:#000000;}</style></head>
<BR><BR><BR><center>
<table border=#C0C0C0 border=1  cellpadding=3 cellspacing=0 bordercolor=#959595>
<form action='admin.php' method=POST name=pswrd>
<TR><TD bgcolor=#C0C0C0 align=center>����������������� �����</TD></TR>
<TR><TD align=right>������� �����: <input size=17 name=name value=''></TD></TR>
<TR><TD align=right>������� ������: <input type=password size=17 name=pass></TD></TR>";

if ($antispam==TRUE and !isset($wrbname)) {print"<tr class=row1><td align=right>�������� ���: "; nospam();} // �������� !

print"<TR><TD align=center><input type=submit style='WIDTH: 120px; height:20px;' value='�����'>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT></TD></TR></table>
<BR><BR><center><font size=-2><small>Powered by <a href=\"http://www.wr-script.ru\" title=\"������ ����� ����������\" class='copyright'>WR-Board</a> &copy;<br></small></font></center></body></html>";
exit;}

} // ����������� ��������!


// ������ ����� - ������� ����
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrforumm","",time()-3600); Header("Location: index.php"); exit; } }


$gbc=$_COOKIE['wrforumm']; $gbc=explode("|", $gbc); $gbname=$gbc[0];$gbpass=$gbc[1];$gbtime=$gbc[2];


// ���������� IP-����� � ���
if (isset($_GET['badip']))  {
if (isset($_POST['ip'])) {$ip=$_POST['ip']; $badtext=$_POST['text'];}
if (isset($_GET['ip_get'])) {$ip=$_GET['ip_get']; $badtext="�� ���������� ������������� ���������� �� �����! �� ����!!!";}
$text="$ip|$badtext|"; $text=stripslashes($text); $text=htmlspecialchars($text); $text=str_replace("\r\n", "<br>", $text);
$fp=fopen("$datadir/bad_ip.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }



// �������� ����� �� ����
if (isset($_GET['delip']))  { $xd=$_GET['delip'];
$file=file("$datadir/bad_ip.dat"); $dt=explode("|",$file[$xd]); 
$fp=fopen("$datadir/bad_ip.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }




// ������ �������/���������� � �����
if (isset($_GET['savebiginfo']))  { if (isset($_POST['text'])) $text=$_POST['text'];
//$text=str_replace("\r\n", "<br>", $text);
if (isset($_POST['chto'])) $chto=replacer($_POST['chto']);
$editfile="$datadir/mainreklama.html"; // ������� ����
if ($chto=="1") $editfile="$datadir/left.html"; // ����� ����
if ($chto=="2") $editfile="$datadir/right.html"; // ������ ����
if ($chto=="3") $editfile="$datadir/reklama.html"; // ������ ����
if ($chto=="4") $editfile="$datadir/msg.html"; // ������ ����
$fp=fopen("$editfile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$text");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }





// ���������� ������ � city.dat
if (isset($_GET['newcity']))  {
if (isset($_POST['city'])) {$city=replacer($_POST['city']); $top=replacer($_POST['top']);}
$key=mt_rand(0,999); $text="$key|$city|\r\n";
$lines=file_get_contents("$datadir/city.dat"); // ���������� ����� ��������� � ����������
$fp=fopen("$datadir/city.dat","w");
flock ($fp,LOCK_EX);
if ($top==TRUE) $text="$text$lines"; else $text="$lines$text";//���� ����� - top=TRUE - � ������
fputs($fp,"$text");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=editcity"); exit; }





// �������� ������ �� ����� city.dat
if (isset($_GET['deletecity']))  {

if (isset($_GET['page'])) $page=replacer($_GET['page']); else $page=1;
$first=replacer($_POST['first']); $last=replacer($_POST['last']);
$delnum=""; $i=0;

do {$dd="del$first"; if (isset($_POST["$dd"])) { $delnum[$i]=$first; $i++;} $first++;} while ($first<=$last);
$itogodel=count($delnum); $newi=0; if ($delnum=="") exit("�������� ����� ������ ������ ����������!");

$file=file("$datadir/city.dat"); $itogo=sizeof($file); $lines=""; $delyes="0";
for ($i=0; $i<$itogo; $i++) { // ���� �� ����� � �������
for ($p=0; $p<$itogodel; $p++) {if ($i==$delnum[$p]) $delyes=1;} // ���� �� ������� ��� ��������
// ���� ��� ����� �� �������� ������ - ��������� ����� ������ �������, ����� - ���
if ($delyes!=1) {$lines[$newi]=$file[$i]; $newi++;} else $delyes="0"; }

// ����� ����� ������ � ����
$newitogo=count($lines); 
$fp=fopen("$datadir/city.dat","w");
flock ($fp,LOCK_EX);
// ���� ��� ���������� �� ��������, ����� ������ ������ ���� ������ :-))
if (isset($lines[0])) {for ($i=0; $i<$newitogo; $i++) fputs($fp,$lines[$i]);} else fputs($fp,"");
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=editcity"); exit; }





// ���� �������� �������
if (isset($_GET['xd']))  { $xd=$_GET['xd'];
// ���� ���� � ������������ � ������� ���
$file=file("$datadir/$datafile"); $dt=explode("|",$file[$xd]); 
if (is_file("$datadir/$dt[0].dat")) {unlink ("$datadir/$dt[0].dat");}
// ������� ������, ��������������� ������ ������� � ��
$fp=fopen("$datadir/$datafile","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }



// ���� �������� ���������� � ����������
if (isset($_GET['remxd']))  {
$id=$_GET['id']; $flname=$_GET['flname']; $remxd=$_GET['remxd']; $page=$_GET['page'];
$file=file("$datadir/$flname.dat");
// ������� ������ � �����������
$fp=fopen("$datadir/$flname.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) { if ($i==$remxd) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
if (count($file)==0) {unlink ("$datadir/$flname.dat");}
Header("Location: admin.php?event=topic&id=$id&page=$page"); exit;}



// ���� �������� ���������� �� 10-�� ���������
if (isset($_GET['tenxd']))  { $tenxd=$_GET['tenxd'];

$first=$_POST['first']; $last=$_POST['last'];
$delnum=""; $i=0; $spros="0"; $predl="0";

do {$dd="del$first"; if (isset($_POST["$dd"])) { $delnum[$i]=$first; $i++;} $first++; } while ($first<=$last);
$itogodel=count($delnum); $newi=0; 
if ($delnum=="") {exit("�������� ����� ������ ������ ����������!");}
$file=file("$datadir/newmsg.dat"); $itogo=sizeof($file); $lines=""; $delyes="0";
for ($i=0; $i<$itogo; $i++) { // ���� �� ����� � �������
for ($p=0; $p<$itogodel; $p++) {if ($i==$delnum[$p]) {$delyes=1;}} // ���� �� ������� ��� ��������
// ���� ��� ����� �� �������� ������ - ��������� ����� ������ �������, ����� - ���
if ($delyes!=1) {$lines[$newi]=$file[$i]; $newi++;} else {$delyes="0";} }

// ����� ����� ������ � ����
$newitogo=count($lines); 
$fp=fopen("$datadir/newmsg.dat","w");
flock ($fp,LOCK_EX);
// ���� ��� ���������� �� ��������, ����� ������ ������ ���� ������ :-))
if (isset($lines[0])) {for ($i=0; $i<$newitogo; $i++) {fputs($fp,$lines[$i]);}} else {fputs($fp,"");}
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit;}



// ���� �������� ��������� ����������
if (isset($_GET['deletemsg'])) {

$id=$_GET['id']; if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page=1;}
$first=$_POST['first']; $last=$_POST['last'];
$delnum=""; $i=0; $spros="0"; $predl="0";

do {$dd="del$first";
if (isset($_POST["$dd"])) { $delnum[$i]=$first; if ($_POST["$dd"]=="�") {$predl++;} else {$spros++;} $i++;}
$first++;
} while ($first<=$last);

$itogodel=count($delnum); $newi=0; 
if ($delnum=="") {exit("�������� ����� ������ ������ ����������!");}
$file=file("$datadir/$id.dat"); $itogo=sizeof($file); $lines=""; $delyes="0";
for ($i=0; $i<$itogo; $i++) { // ���� �� ����� � �������
for ($p=0; $p<$itogodel; $p++) {if ($i==$delnum[$p]) {$delyes=1;}} // ���� �� ������� ��� ��������
// ���� ��� ����� �� �������� ������ - ��������� ����� ������ �������, ����� - ���
if ($delyes!=1) {$lines[$newi]=$file[$i]; $newi++;} else {$delyes="0";} }

// ����� ����� ������ � ����
$newitogo=count($lines); 
$fp=fopen("$datadir/$id.dat","w");
flock ($fp,LOCK_EX);
// ���� ��� ���������� �� ��������, ����� ������ ������ ���� ������ :-))
if (isset($lines[0])) {for ($i=0; $i<$newitogo; $i++) {fputs($fp,$lines[$i]);}} else {fputs($fp,"");}
flock ($fp,LOCK_UN);
fclose($fp);

// ���� �������� �������� ���������� �� ���-�� ���������� � �������
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
ftruncate ($fp,0);//������� ���������� �����
for ($ii=0;$ii< sizeof($file);$ii++) 
 { if ($fnomer!=$ii) {fputs($fp,$file[$ii]);} else {fputs($fp,"$text\r\n");} }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=topic&id=$id&page=$page"); exit;}





// ���� �������� ��������� �����
if(isset($_GET['xduser'])) {
if ($_GET['xduser'] =="") {exit("��������� ����-�������� :-( ���������� ����� ;-)");}
$xduser=$_GET['xduser']-1; if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page=1;}
$file=file("$datadir/usersdat.php"); $i=count($file);
if ($xduser<"1") {exit("$back. 1-�� ������ �������� ��������! Ÿ <B>������ �������!</B>");}
if ($i<"3") {exit("$back. ���������� �������� ���� �� <B>������</B> ���������!");}
// ������� ������ � ����������
$fp=fopen("$datadir/usersdat.php","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xduser) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=userwho&page=$page"); exit; }



// ���� ����������� �����/���� ������� ��� ������
if(isset($_GET['movetopic'])) { if ($_GET['movetopic'] !="") {
$move1=$_GET['movetopic']; $where=$_GET['where']; 
if ($move1=="0" or $move1=="1") {exit("$back. ��������� ���������� ����� ������ ������!");}
if ($where=="0") {$where="-1";}
$move2=$move1-$where;
$file=file("$datadir/boardbase.dat"); $imax=sizeof($file);
if (($move2>=$imax) or ($move2<"0")) {exit("$back. ���� ���� �������!");}
$data1=$file[$move1]; $data2=$file[$move2];
$fp=fopen("$datadir/boardbase.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
// ������ ������� ��� �������� �������
for ($i=0; $i<$imax; $i++) {if ($move1==$i) {fputs($fp,$data2);} else  {if ($move2==$i) {fputs($fp,$data1);} else {fputs($fp,$file[$i]);}}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }}



// ���� �����ר�� ���-�� ������ � ����������
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
if ($dtt[4]=="�") {$itogop++;} else {$itogos++;}
} while ($it<$itmax);
}
}

if ($dt[1]=="R") {$lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|\r\n";} else {$lines[$i]="$dt[0]|$dt[1]|$itogop|$itogos|\r\n";}
} while($i < $countmf);

// ��������� ���������� ������ � ���-�� ����������
$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) {fputs($fp,$lines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);
exit("<center><BR>�� ������� �����������.<BR><BR><h3>$back</h3></center>"); }
}



// ���������� �������
if (isset($_GET['newrubrika']))  { $ftype=$_POST['ftype']; $zag=$_POST['zag'];
if (strlen($zag)<3) {exit("$back. ���� ���������� ������ ��������� <B> ����� 3 �������� </B>!");}

// ��������� �� ����� ���� ���������� ����� ������� � ��������� +1
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



// ���� ������� - �������������� ���������� � �������
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
$deldt=mktime()+$days*86400; // ��������� ���� �������� ����������
$msg=str_replace("|","I",$msg);

$text="$rubrn|$rubka|$name|$zag|$type|$msg|$date|$deldt|$id|$vip|$key|$today|$gorod|$phone|$smallfoto|$foto|$fotoksize|$size0|$size1|||";

// �������� ������ ������� �� ���� �������� ������
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

print "<BR><BR><BR><center><B>����� ���������� ������� ������</B><BR><BR><BR><BR><BR><script language='Javascript'>function reload() {location = \"admin.php?event=topic&id=$id\"}; setTimeout('reload()', 1000);</script>"; exit;

}  else   { // if ($newru[0]==$rubrn) // ���� ���������� ����������� ���������� � ������ �������

$topicxd=$fnomer; $idold=$id; // ���������� ��������� ���������� �������

$deldt=mktime()+$days*86400; // ��������� ���� �������� ����������
$msg=str_replace("|"," ",$msg);
$id=$newru[1];
$text="$newru[0]|$newru[3][ktname]$newru[2]|$name|$zag|$type|$msg|$date|$deldt|$newru[1]|$vip|$key|$today|$gorod|$phone|$smallfoto|$foto|$fotoksize|$size0|$size1|$newru[2]||";

// �������� ������ ������� �� ���� �������� ������
$text=stripslashes($text);
$text=htmlspecialchars($text);
$text=str_replace("\r\n", "<br>", $text);

$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ������ ������� � 10-20 ��������� ����������
$lines=file("$datadir/newmsg.dat"); $itogo=count($lines);
for ($i=0; $i<$itogo; $i++) { // ���� �� ����� � �������
$dt=explode("|",$lines[$i]);
if ($dt[10]==$key) {$lines[$i]="$text\r\n";}}
$itogo=count($lines); // ���������� ���-�� ����� ����� ��������
$fp=fopen("$datadir/newmsg.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i<$itogo; $i++) {fputs($fp,$lines[$i]);}
flock ($fp,LOCK_UN);
fclose($fp);

// ������� ������, ��������������� �������� ���������� � ������ �������
$file=file("$datadir/$idold.dat");
$fp=fopen("$datadir/$idold.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) { if ($i==$topicxd) {unset($file[$i]);} }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);

// ������������ ���-�� ����� � ����������
$lines=null; $lines=file("$datadir/$datafile"); $itogo=count($lines); $i=$itogo; $ok1=null; $ok2=null;
do {$i--; $dt=explode("|",$lines[$i]);
$lines[$i]=$lines[$i];
if ($newru[1]==$dt[0]) {$ok=1; if ($type=="�") {$dt[3]++;} else {$dt[2]++;} $lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|\r\n";}
if ($ok1!=null) {if ($dt[1]=="R") {$ok1=null; $dt[3]++; $lines[$i]="$dt[0]|R|$dt[2]|$dt[3]|\r\n";}}
if ($id==$dt[0]) {$ok=1; if ($type=="�") {$dt[3]--;} else {$dt[2]--;} if ($dt[3]<0) $dt[3]=0; if ($dt[2]<0) $dt[2]=0; $lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|\r\n";}
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

print "<BR><BR><BR><center><B>������� � ����� ���������� ������� ��������<BR><BR><BR></B><script language='Javascript'>function reload() {location = \"admin.php?event=topic&id=$id\"}; setTimeout('reload()', 1000);</script>";
exit; }
}
}



// �������������� ���� ��� ������� (��������� ���� !!!)
if (isset($_GET['event']))  {

if (($_GET['event']=="add") or ($_GET['event'] =="addlink"))  {

// ���� ������� - �������������� ������. $fnomer - ����� ������, ������� ���������� ��������.
if (isset($_GET['rd']))  { $rd=$_GET['rd']; $fnomer=$_POST['fnomer'];
$zag=$_POST['zag']; $spros=$_POST['spros']; $predl=$_POST['predl']; $idtopic=$_POST['idtopic'];

$text="$idtopic|$zag|$spros|$predl|";
$text=str_replace("\r\n", "", $text);

$file=file("$datadir/$datafile");
$fp=fopen("$datadir/$datafile","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ����������
for ($i=0;$i< sizeof($file);$i++) {if ($fnomer!=$i) {fputs($fp,$file[$i]);} else {fputs($fp,"$text\r\n");}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ������� ����� �������� ������� � ������ ������ ����� � ������������
$linesrdt=file("$datadir/$idtopic.dat");
$fp=fopen("$datadir/$idtopic.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ����������
for ($i=0;$i< sizeof($linesrdt);$i++) {$drdt = explode("|", $linesrdt[$i]); $text1="$drdt[0]|$zag|$drdt[2]|$drdt[3]|$drdt[4]|$drdt[5]|$drdt[6]|$drdt[7]|$drdt[8]|$drdt[9]|$drdt[10]|$drdt[11]|$drdt[12]|$drdt[13]|$drdt[14]|$drdt[15]|$drdt[16]|$drdt[17]|$drdt[18]|$drdt[19]|$drdt[20]|"; $text1=str_replace("\r\n", "", $text1); fputs($fp,"$text1\r\n");}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }
}



// ��������� ������������
if(isset($_GET['event'])) { if ($_GET['event']=="activate") {

$key=$_GET['key']; $email=$_GET['email']; $page=$_GET['page'];

// ������ �� ������ �� ����� � ������
if (strlen($key)<6 or strlen($key)>6 or !ctype_digit($key)) {exit("$back �� �������� ��� ����� �����. ���� ����� ��������� ������ 6 ����.");}
$email=stripslashes($email); $email=htmlspecialchars($email);
$email=str_replace("|","I",$email); $email=str_replace("\r\n","<br>",$email);
if (strlen($key)>30) {exit("������ ��� ����� ������");}

// ���� ����� � ����� ������� � ������. ���� ���� - ������ ������ �� ������ ����
$email=strtolower($email); unset($fnomer); unset($ok);
$lines=file("$datadir/usersdat.php"); $ui=count($lines); $i=$ui;
do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[0]=strtolower($rdt[3]);
if ($rdt[2]===$email and $rdt[12]===$key) {$name=$rdt[0]; $pass=$rdt[1]; $fnomer=$i;}
if ($rdt[2]===$email and $rdt[10]==="ok") {$ok="1";}
} while($i > 1);

if (isset($fnomer)) { // ���������� ������ ����� � ��
$i=$ui; $dt=explode("|", $lines[$fnomer]);
$txtdat="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|ok|$dt[11]|||||";
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i<=(sizeof($lines)-1);$i++) {if ($i==$fnomer) {fputs($fp,"$txtdat\r\n");} else {fputs($fp,$lines[$i]);}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp); }
if (!isset($fnomer) and !isset($ok)) {exit("$back �� �������� � ���� �������������� ����� ��� ������.</center>");}
if (isset($ok)) {$add="������ ������������ �����";} else {$add="$name, ������������ ������� ���������������.";}

print"<html><head><link rel='stylesheet' href='$brdskin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"admin.php?event=userwho&page=$page\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
�������, <B>$add</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� �������� � ����������� ������.<BR><BR>
<B><a href='admin.php?event=userwho&page=$page'>������� �����, ���� �� ������ ������ �����</a></B></td></tr></table></td></tr></table></center></body></html>";
exit;}
}


}  // if isset($event)




$shapka="<html><head>
<title>����������� - $brdname</title>
<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'>
<META content='text/html; charset=windows-1251' http-equiv=Content-Type>
<LINK href='$brdskin/style.css' rel=stylesheet type=text/css>
</head><body topMargin=5 bgcolor=#F3F3F3><center>
<table width=100% cellpadding=1 cellspacing=0 border=1 bordercolor=#666666>
<TR height=30><TD align=center style='FONT-SIZE: 13px; FONT-WEIGHT: bold'>
<b><a href='admin.php'>�������</font></a> :: 
<a href='admin.php?event=revolushion'>�����������</a> :: 
<a href='admin.php?event=makecopy'>������� ����� ��</a> :: 
<a href='admin.php?event=restore'>������������ �� �����</a> ::
<a href='admin.php?event=config'>���������</a> :: <br>
<a href='admin.php?event=userwho'>���������</a> :: 
<a href='admin.php?event=blockip'>IP-����������</a> :: 
<a href='admin.php?event=editcity'>���������� / �������� �������</a> :: 
<br>�������������� ��������� ������: <a href='admin.php?event=editinfo&chto=0'>�� ������� ��������</a> :: 
<a href='admin.php?event=editinfo&chto=1'>������ �����</a> :: 
<a href='admin.php?event=editinfo&chto=2'>������� �����</a> :: 
<a href='admin.php?event=editinfo&chto=3'>���� � ������ ����������</a> :: 
<a href='admin.php?event=clearcooke'>�����</a></b>
</td></tr><tr><td width=100%>";


// ����� �������� - ������ �� �������
if(!isset($_GET['event'])) {

// ������� ��� ������� �� ������� ��������
if (!is_file("$datadir/$datafile")) {$add1="<center><h3>���� $datadir/boardbase.dat �� ����������! ������ �������������� �� �����!!! ���� ����������� ��������� ���� �� ������!</h3>"; $stop=1; $lines=file("$datadir/copy.dat"); $data1size = sizeof($lines); $i=count($lines); }
else {$lines=file("$datadir/$datafile"); $data1size = sizeof($lines); $i=count($lines); $add1="";}

$toper="
<BR><TABLE align=center cellPadding=2 cellSpacing=1 width=98%>
<TR  align=center class=smallest bgColor=#cccccc><TD width=5%><B>� �/�</B></TD><TD width=80%><B>�������</B></TD><TD width=5%><B>�����</B></TD><TD colspan=4 width=15%><B>��������</B></TD></TR>";

if (is_file("$datadir/copy.dat")) {
if (count(file("$datadir/copy.dat"))<1) {$a2="<font color=red size=+1>�� ���� ����� ����! ������ ������������!</font><br> (�������� ����� �������, ���� �� ��������� �����������)";} else {$a2="";}
$a1=round((mktime()-filemtime("$datadir/copy.dat"))/86400); if ($a1<1) $a1="�������</font>, ��� ���� ���!"; else $a1.="</font> ���� �����.";
$add="<br><center>����� ���� ������� <font color=red size=+1>".$a1." $a2</center>"; } else {$add="";}

print"$shapka $add1 $add<TABLE cellPadding=2 cellSpacing=0 width=100%><tr height=25 align=center><TD width=50%>";
if (isset($stop)) {exit("���������� ������ ����������� ����������!!!");} else {print"$toper";}
if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page="0";}
$a1="0";

if ($i>0) {
do {$dt = explode("|", $lines[$a1]);

$halfrubsize=round($data1size/2); // ���������� ���-�� ������ � ������ �������
if ($a1==$halfrubsize) {print "</table></td><td align=center width=50%>$toper";}
$a1++;
$numpp=$a1-1;

if ($dt[1]!="R") {$kolvo=$dt[2]+$dt[3]; $add="<UL><a href=\"admin.php?event=topic&id=$dt[0]\">$dt[1]</a>";} else {$kolvo=""; $add="<B>$dt[2]</B>";}


print"<tr align=center>
<td><font size=-1>$a1</font></td>
<td align=left>$add</td>
<td><font size=-1>$kolvo</font></td>
<td width=10 bgcolor=#A6D2FF><B><a href='admin.php?movetopic=$numpp&where=1'>��.</a></B></td>
<td width=10 bgcolor=#DEB369><B><a href='admin.php?movetopic=$numpp&where=0'>��.</a></B></td><td bgcolor=#00E600>";
if ($dt[1]!="R") {print"<B><a href='admin.php?rd=$numpp'>.P.</a></B>";} else {echo'&nbsp;';}
print"</td><td width=5% bgcolor=#FF6C6C><B><a href='admin.php?xd=$numpp'>.X.</a></B>
</td></tr>";
} while($a1 < $i);
} else {echo'<br><center><h3>���� �������� �� ����! �������� �������, ���� ������������ �� ����� (���� �� � ������ ������...)</h3></center>';}

echo'</table></tr></td></table>';


// ���� ������� ����� .P. - �������������� �������, �� ���� ��� � ������� � �����
if (isset($_GET['rd'])) { $rd=$_GET['rd']; $dt = explode("|", $lines[$rd]);

print "<BR><center><table><tr><td valign=top><B>�������</td><td>
<form action='admin.php?event=add&rd=$rd' method=post name=REPLIER>
<input type=text value=\"$dt[1]\" name=zag size=50><br><br>
<input type=hidden name=spros value=\"$dt[2]\">
<input type=hidden name=predl value=\"$dt[3]\">
<input type=hidden name=idtopic value=\"$dt[0]\">
<input type=hidden name=fnomer value=\"$rd\">
<center><input type=submit  value='�������� �������'></form>
</td></tr></table>
<SCRIPT language=JavaScript>document.REPLIER.zag.focus();</SCRIPT><BR></td></tr></table>"; 
} else {
print "<center><BR><form action=?newrubrika=add method=post name=REPLIER>
��������: <input type=radio name=ftype value='razdel'> ������ &nbsp;&nbsp; <input type=radio name=ftype value=''checked> <B>�������</B>  &nbsp;&nbsp;&nbsp;<input type=text name=zag size=40> <input type=submit value='��������'></form>
<SCRIPT language=JavaScript>document.REPLIER.zag.focus();</SCRIPT>";


// ������� 10-20 ��������� ����������
$shapka20="<TABLE align=center border=1 bordercolor='#E1E1E1' cellPadding=3 cellSpacing=0 width=100%>";
if (is_file("$datadir/newmsg.dat")) { // ��������� ���� �� ����� ����
$linesn = file("$datadir/newmsg.dat"); $in=count($linesn); $first=0; $last=$in;
if ($in > 0) {
$newdat=file("$datadir/newmsg.dat");
$in=count($newdat)-1; $iall=$in; $ia=$in+1;
print"<FORM action='admin.php?pswrd=$password&tenxd=$in' method=POST name=delform>
<TABLE cellPadding=2 cellSpacing=1 align=center width='98%'>
<TR bgColor=#cccccc height=18><TD colspan=4 align=center><B>��������� $ia ����������:</B></TD></TR>
<TR><TD valign=top> $shapka20";

do {$dtn=explode("|", $newdat[$in]);
$url="index.php?fid=$dtn[8]&id=$dtn[10]";

$dtn[5]=substr($dtn[5],0,150); // �������� ��������� �� 150 ��������
$dtn[5]=str_replace("<br>","\r\n",$dtn[5]);
$dtn[7]=date("H:i",$dtn[7]);
if ($dtn[4]=="�") {$colorsp="#ff3333";} else {$colorsp="#1414CD";}
if (round($iall/2)==($in+1)) {print"</table></td><td valign=top width=50%>$shapka20";}
if ($dtn[9]=="vip") {$st1="<B>"; $st2="VIP-���������� \r\n";} else {$st1=""; $st2="";}
print"
<TR height=20>
<td width=5% bgcolor=#FF6C6C><B>
<input type=checkbox name='del$in' value=''"; if (isset($_GET['chekall'])) {echo'CHECKED';} print"></B></td>
</td>
<TD><FONT color=$colorsp><B>$dtn[4]</B></FONT></TD>
<TD>$dtn[7]</TD>
<TD width=100%>$st1<A href='$url' title='$dtn[5] \r\r\n $st2 ��������� $dtn[6] �.'>$dtn[3]</A></TD>
</TR>";
$in--;
} while($in >"-1");
print"</table></td></tr></table>

<table border=0><TR><TD valign=top>
<input type=hidden name=first value='$first'><input type=hidden name=last value='$last'><INPUT type=submit value='������� ��������� ����������'></FORM>
</TD><TD>
<FORM action='admin.php?chekall' method=POST name=delform><INPUT type=submit value='�������� ��'></FORM>
</TD><TD>
<FORM action='admin.php' method=POST name=delform><INPUT type=submit value='����� �������'></FORM>
</TD></TR></TABLE>";
}
}

echo'<div align=left>&nbsp; �������� ��� ������: <BR>
&nbsp; <B>��.</B> - ����������� <B>�����</B>;<BR>
&nbsp; <B>��.</B> - ����������� <B>����</B>;<BR>
&nbsp; <B>.�.</B> - <B>�������������</B>;<BR>
&nbsp; <B>.�.</B> - <B>�������</B>.<BR><BR>
</td></tr></table>'; }


}  // if !isset($event')




// �������� ���������� � ������� �������

else  {
if ($_GET['event'] == "topic") {
if (!isset($_GET['id'])) {exit("ID - ������ �����. ����� ���� �� �����!"); } else {$id=$_GET['id'];}

if (is_file("$datadir/$id.dat")) { // ��������� ���� �� ����� ����
$lines = file("$datadir/$id.dat"); $i=count($lines); $maxi=$i-1;
if ($i > 0) {

// ������� qq ������ � ������� �������
$dtsize=sizeof($lines); 
$itogos="0"; // ����� ���������� - �����

// �� �� �� �� ������������ --> ������ ��������. ����, �� ����� �������� - �������� � ����� ������ ;-)

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) {$page=1;} else {$page=$_GET['page']; if (!ctype_digit($page)) {$page=1;} if ($page<1) $page=1;}

$fm=$maxi-$qq*($page-1); if ($fm<"0") {$fm=$qq;}
$lm=$fm-$qq; if ($lm<"0") {$lm="-1";}

print"$shapka <TABLE cellPadding=2 cellSpacing=0 width=100%><tr height=25 align=center><TD width=100%>";

$dtt=explode("|",$lines[0]);
$tdt=explode("[ktname]", $dtt[1]); $razdel=$tdt[1]; $rubrika=$tdt[0];

print"<BR><h3>$razdel --> $rubrika</h3><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1 width=98% align=center><TBODY>
<TR class=small align=center bgColor=#cccccc>
<TD><small><B>� �/�</B></small></TD>
<TD>&nbsp;</TD>
<TD width=2%><B>�</B></TD>
<TD width=2%><B>�</B></TD>
<TD width=60%><B>��������� / ����� ����������</B></TD>
<TD width=13%><B>��� / IP / �������� �� IP</B></TD>
<TD width=20%><B><small>��������� / ���� ��������</small></B></TD>
<FORM action='admin.php?deletemsg&id=$id&page=$page' method=POST name=delform>
</TR>";

$last=$fm; // ����� �������� ������

do {$dt=explode("|",$lines[$fm]);

$deldate=date("d.m.Y",$dt[7]);  // ����������� ���� �������� � ������������ ������
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // ����� ������� ���� ����� ������� ����������

if ($dt[4]=="�") {$colorsp="#ff3333";} else {$colorsp="#1414CD";}

$numpp=$fm+1;
$numanti=$i-$numpp+1;
$stroka=substr($dt[5],0,$msglength); // �������� ������ � ����������
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
<td align=right>$u_profile <br>$dt[21] <a href='admin.php?badip&ip_get=$dt[21]'><B><font color=red>��� �� IP</font><B></a></td>
<TD><small>$dt[6]</small><br><small>����� <B>$deldays</B> ���� ($deldate)</small></TD>
</TR>";

// ���� ���� ���������� � ���������� - ������� ���
if (is_file("$datadir/$dt[10].dat")) { print"<TR class=small bgColor=$addvip><TD>&nbsp;</TD><TD>&nbsp;</TD><TD colspan=7>";
$klines = file("$datadir/$dt[10].dat"); $ik=count($klines);
for ($z=0;$z<sizeof($klines);$z++) {$dtk=explode("|",$klines[$z]); print "
<table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?id=$id&flname=$dt[10]&remxd=$z&page=$page'>.X.</a></B></td><td> 
 ���: <B>$dtk[0]</B> �����: <B>$dtk[1]</B> ����������: <B>$dtk[2]</B> ������: $dtk[4]</td></tr></table>";}
echo'</TD></TR>'; }

if ($dt[4]=="�") {$itogos++;}
$fm--;
} while($lm < $fm);
$itogop=$i-$itogos;
$first=$lm; // ��������� ��������

// ������� ������ ��������� �������
print "</TBODY></TABLE></TD></TR></TABLE>
<BR><center><TABLE cellPadding=0 cellSpacing=0 border=0 width=98%><TR height=40>
</TD><TD width=50% colspan=2>����� ����������: <B>$i</B>. �� ���: ����� - <B>$itogos</B> ����������� - <B>$itogop</B>.</TD></TR>
<TR><TD>
<input type=hidden name=first value='$first'><input type=hidden name=last value='$last'><INPUT type=submit value='������� ��������� ����������'></FORM>
</TD><TD>
<FORM action='admin.php?event=topic&id=$id&page=$page&chekall' method=POST name=delform><INPUT type=submit value='�������� ��'></FORM>
</TD><TD>
<FORM action='admin.php?event=topic&id=$id&page=$page' method=POST name=delform><INPUT type=submit value='����� �������'></FORM>
</TD></TR>";

if ($i>$qq) { // ������� ������ ��������� �������
echo'<TD align=left width=50%><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;��������: ';

// ������� ������ �������
$maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) {$page=$maxpage;}
if ($page>=4 and $maxpage>5) print "<a href=admin.php?event=topic&id=$id&page=1>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page<=2) {$f1=5; $f2=1;} if ($page>=$maxpage-1) {$f1=$maxpage; $f2=$page-3;} if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) {print "<B>$i</B> &nbsp;";} else {print "<a href=admin.php?event=topic&id=$id&page=$i>$i</a> &nbsp;";}}
if ($page<=$maxpage-3 and $maxpage>5) print "... <a href=admin.php?event=topic&id=$id&page=$maxpage>$maxpage</a>";
}
} else {print "$shapka <center><font size=2><BR><BR>���������� � ���� ������� ���.<BR><BR><a href='add.php'>�������� ����������</a><BR><BR><a href='admin.php'>���������</a><BR><BR><BR>";}

// ������� ������ ��������� �������
echo'</font></center></td></tr></table><center>';



// ���� ������� ����� .P. - �������������� ����������
if (isset($_GET['topicrd'])) {

$topicrd=$_GET['topicrd'];
// ���� ���������� ��� �������������� � ������� ��� � �����
$lines = file("$datadir/$id.dat");
$a1=$topicrd+1;
$u=$a1+1;
do {$a1--;  $dt = explode("|", $lines[$a1]); $dt[5]=str_replace("<br>", "\r\n", $dt[5]);} while($a1 > $u);

$deldate=date("d.m.Y",$dt[7]);  // ����������� ���� �������� � ������������ ������
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // ����� ������� ���� ����� ������� ����������

print"<center><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1>
<FORM action='admin.php?event=rdmsgintopic&topicrd=$topicrd' method=post name=addForm>
<TBODY>
<TR><TD align=middle bgColor=#cccccc colSpan=2>������������� ����������</TD>
</TR><TR>";

print "<TD bgColor=#eeeeee>���� ���:</TD><TD bgColor=#eeeeee><B>$dt[2]</B>
</td></tr><tr><TD bgColor=#eeeeee>���������:</TD><TD bgColor=#eeeeee><SELECT name=newrubrika style='FONT-SIZE: 13px; WIDTH: 280px'>";

// ���� ��������� ��� ��������� �� �����
$tdt=explode("[ktname]", $dt[1]);
$lines=file("$datadir/$datafile"); $imax=count($lines); $i="0"; $r="0"; $cn=0;
do {$dtt=explode("|", $lines[$i]);
if ($dt[8]==$dtt[0]) {$fy="selected";} else {$fy="";} 
if ($dtt[1]!="R") {print "<OPTION value=\"$i|$dtt[0]|$r|$dtt[1]|\"$fy> - $r - $dtt[1]</OPTION>\r\n";}
else {$r=$dtt[2]; if ($cn!=0) {echo'</optgroup>'; $cn=0;} $cn++; print "<optgroup label=' - $dtt[2]'>";}
$i++;
} while($i < $imax);

print "</optgroup></SELECT></TD></TR>
<TR><TD bgColor=#ffffff>���� ����������:<FONT color=#ff0000>*</FONT><BR>(�� ����� 100 ��������)</TD>
<TD bgColor=#ffffff><INPUT name=zag value=\"$dt[3]\" style='FONT-SIZE: 14px; WIDTH: 300px'></TD></TR>

<TR><TD bgColor=#eeeeee>��� ����������:<FONT color=#ff0000>*</FONT></TD>
<TD bgColor=#eeeeee>";

if ($dt[4]=="�") {print "<INPUT name=type type=radio value='�'checked>����� <INPUT name=type type=radio value='�'>�����������";}
else {print "<INPUT name=type type=radio value='�'>����� <INPUT name=type type=radio value='�'checked>����������� ";}

print "</TD></TR>
<TR><TD bgColor=#ffffff name=msg>����� ����������:</TD>
<TD bgColor=#ffffff><TEXTAREA name=msg style='FONT-SIZE: 14px; HEIGHT: 200px; WIDTH: 300px'>$dt[5]</TEXTAREA></TD></TR>

<TR><TD bgColor=#eeeeee>���� �������� ����������:</TD>
<TD bgColor=#eeeeee><SELECT name=days style='FONT-SIZE: 12px'>
<OPTION value=$deldays>��� $deldays ����</OPTION>
<OPTION value=10>7 ����</OPTION>
<OPTION value=15>14 ����</OPTION>
<OPTION value=30>30 ����</OPTION>
<OPTION value=60>60 ����</OPTION>
<OPTION value=90>90 ����</OPTION>
<OPTION value=365>365 ����</OPTION></SELECT>
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

<TR><TD colspan=2 bgColor=#eeeeee align=middle><INPUT style='FONT-SIZE: 10px; HEIGHT: 20px; WIDTH: 100px' type=submit value=��������></TD></TR>

</FORM></TBODY></TABLE>
<SCRIPT language=JavaScript>document.addForm.msg.focus();</SCRIPT><BR>";
}
}
}



// ������� ����� ��
if ($_GET['event']=="makecopy")  {
if (is_file("$datadir/$datafile")) {$lines=file("$datadir/$datafile");}
if (!isset($lines)) {$datasize=0;} else {$datasize=sizeof($lines);}
if ($datasize<=0) {exit("�������� � ����� ������ - ���� ����������. ������ = 0!");}
if (copy("$datadir/$datafile", "$datadir/copy.dat")) {print "<center><BR>����� ���� ������ �������.<BR><BR><h3>$back</h3></center>";} else {print"������ �������� ����� ���� ������. ���������� ������� ������� ���� copy.dat � ����� $datadir � ��������� ��� ����� �� ������ - 666 ��� ������ ����� 777 � ��������� �������� �������� �����!";}
exit; }



// ������������ �� ����� ��
if ($_GET['event']=="restore")  {
if (is_file("$datadir/copy.dat")) {$lines=file("$datadir/copy.dat");}
if (!isset($lines)) {$datasize=0;} else {$datasize=sizeof($lines);}
if ($datasize<=0) {exit("�������� � ������ ���� ������ - ��� ����������. �������������� ����������!");}
if (copy("$datadir/copy.dat", "$datadir/$datafile")) {print "<center><BR>�� ������������� �� �����.<BR><BR><h3>$back</h3></center>";} else {print"������ �������������� �� ����� ���� ������. ���������� ������� ������ copy.dat � mainforum.dat � ����� $datadir ��������� ����� �� ������ - 666 ��� ������ ����� 777 � ��������� �������� ��������������!";}
exit; }



// �������� ���� �������������
if ($_GET['event']=="userwho")  {
$userlines=file("$datadir/usersdat.php");
$ui=count($userlines)-1; $uq="25"; // �� ������� ������� �������� ������ ����������
$t1="#FFFFFF"; $t2="#EEEEEE";

print"$shapka<BR><table border=1 width=98% align=center cellpadding=1 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB align=center>
<td>� �/�</td>
<td><B>.�.</B></td>
<td><B>.X.</B></td>
<td><B>���</B></td>
<td><B>���� ���-��</B></td>
<td><B>E-mail</B></td>
<td><B>WWW</B></td>
<td><B>�����������</B></td>
<td><B>IP / ��������</B></td>
<td><B>������ / VIP + -</B></td>
</tr>";

if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page="1";}
if (!ctype_digit($page)) {$page=1;}
if ($page=="0") {$page="1";} else {$page=abs($page);}

$maxpage=ceil(($ui+1)/$uq); if ($page>$maxpage) {$page=$maxpage;}

$i=1+$uq*($page-1); if ($i>$ui) {$i=$ui-$uq;}
  $lm=$i+$uq; if ($lm>$ui) {$lm=$ui+1;} 

do {$tdt=explode("|",$userlines[$i]); $i++; $npp=$i-1;

if ($tdt[10]=="ok") {$user1="<font color=#AAAAAA>�������</font>"; $user2="<input type=text name=addvip value='30' style='width: 30px' size=18 maxlength=3><input type=submit name=submit value=' + ' style='width: 20px'>";
} else {
if ($tdt[12]>0) {$tek=mktime(); $vipdays=round(($tdt[12]-$tek)/86400); $vipdays.=" ��.";} else {$vipdays="������";}
$user1="<font color=red><B>VIP</B></font> - $vipdays";
if ($vipdays<0) {$user1="<font color=#AAAAAA>�������</font>"; $vipdays="���� ����";}
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
print"<td class=$t1 colspan=9><B>[<a href='admin.php?event=activate&email=$tdt[2]&key=$tdt[12]&page=$page'>������������</a>]. ������� ������ �� ������������  � $tdt[9]. </B>
(�����: <B>$tdt[2]</B> ����: <B>$tdt[12]</B>)"; 
} else {
print"
<td><a href=\"mailto:$tdt[2]\">$tdt[2]</a> &nbsp;</td>
<td><a href=\"$tdt[3]\">$tdt[3]</a> &nbsp;</td>
<td>$tdt[6] &nbsp;</td>

<form action='admin.php?badip' method=POST><td align=right>$tdt[8]
<input type=hidden name=ip value='$tdt[8]'>
<input type=hidden name=text value='�� ���������� ������������� ���������� �� �����! �� ����!!!'>
<input type=submit value='���'></form></td>

<form action='admin.php?event=userstatus&page=$page' method=post><td align=right>$user1 <input type=hidden name=usernum value='$i'><input type=hidden name=status value='$tdt[10]'>
$user2</td></form></tr>";
}
$t3=$t2; $t2=$t1; $t1=$t3;
} while ($i<$lm);

// ������� ������ �������
if ($page>$maxpage) {$page=$maxpage;}
echo'</table><BR><table width=100%><TR><TD>��������:&nbsp; ';
if ($page>=4 and $maxpage>5) print "<a href=admin.php?event=userwho&page=1>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page<=2) {$f1=5; $f2=1;} if ($page>=$maxpage-1) {$f1=$maxpage; $f2=$page-3;} if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) {print "<B>$i</B> &nbsp;";} else {print "<a href=admin.php?event=userwho&page=$i>$i</a> &nbsp;";}}
if ($page<=$maxpage-3 and $maxpage>5) print "... <a href=admin.php?event=userwho&page=$maxpage>$maxpage</a>";

print "</TD><TD align=right>����� ���������������� ���������� - <B>$ui</B></TD></TR></TABLE><br>";
}



// �������������� ������� ������������ ���������������
if ($_GET['event'] =="profile")  { 
if (!isset($_GET['pname'])) {exit("������� ������.");}
$pname=urldecode($_GET['pname']); // ����������� ��� ������������, ��������� �� GET-�������.
$lines=file("$datadir/usersdat.php"); $i=count($lines); $use="0";

do {$i--; $rdt=explode("|", $lines[$i]);
if (isset($rdt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������
if (strlen($rdt[13])=="6" and ctype_digit($rdt[13])) {$rdt[13]="<B><font color=red>�������� ���������</font></B>";}
if ($pname===$rdt[0])  {
print"$shapka";
if ($rdt[10]=="ok") {$user1="<font color=#AAAAAA>�������</font>";
} else {
if ($rdt[12]>0) {$tek=mktime(); $vipdays=round(($rdt[12]-$tek)/86400); $vipdays.=" ��. ��������";} else {$vipdays="������";}
$user1="<font color=red><B>VIP-������</B></font>* ($vipdays)";
$user2="* ��� ����������� ���� ���������� ������ ����������� ������ �������� � ���������� ������ ������.";
if ($vipdays<0) {$user1="<font color=#AAAAAA>�������</font> (���� ����)"; $user2="";}}

print "<BR><center><TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action='admin.php?event=reregistr' method=post>
<TBODY><TR class=toptable><TD align=middle colSpan=2><B>��������������� ����������</B></TD></TR>
<TR class=row1 height=25><TD>���:</TD><TD><B>$rdt[0]</B></TD></TR>
<TR class=row2 height=25><TD>������:</TD><TD>$user1</TD></TR>
<TR class=row1><TD>������:<FONT color=#ff0000>*</FONT><BR>(�� ����� 15 ��������)</TD><TD><INPUT name=password class=maxiinput value='$rdt[1]' type=password></TD></TR>
<TR class=row2><TD>E-mail:<FONT color=#ff0000>*</FONT></TD><TD><INPUT name=email class=maxiinput value='$rdt[2]'></TD></TR>
<TR class=row1><TD>�����:</TD><TD><INPUT name=gorod class=maxiinput value='$rdt[11]'></TD></TR>
<TR class=row2><TD>URL:</TD><TD><INPUT name=url class=maxiinput value='$rdt[3]'></TD></TR>
<TR class=row1><TD>ICQ:</TD><TD><INPUT name=icq class=maxiinput value='$rdt[4]'></TD></TR>
<TR class=row2><TD>�������:</TD><TD><INPUT name=phone class=maxiinput value='$rdt[5]'></TD></TR>
<TR class=row1><TD>�����������:</TD><TD><INPUT name=company class=maxiinput value='$rdt[6]'></TD></TR>
<TR class=row2><TD>������� � ����:</TD><TD><TEXTAREA name=about class=maxiinput style='HEIGHT: 70px'>$rdt[7]</TEXTAREA></TD></TR>
<TR class=row1><TD height=30 colspan=2><center><INPUT type=submit class=longok value='��������� ���������'></TD></TR></TBODY></TABLE>
<input type=hidden name=login value='$rdt[0]'>
<input type=hidden name=oldpass value='$rdt[1]'></FORM>"; $use="1"; $i=0;
}
} // if
} while($i > "1");

// �� ������ ����� ���, ��������, ��� ����� ������ ��� ���� ��
if ($use!="1") { echo'<br><br>������������ ��� �����, ���� ������ ���� ������. �������������� ����������.'; }
} // $event=="profile"



if ($_GET['event'] =="reregistr") { // ��������������� (��������� ������ ����� �������)
$login=$_POST['login']; // ����� �����
$oldpass=$_POST['oldpass']; // ������ ������
$password=$_POST['password']; // ����� ������
$email=$_POST['email']; $email=strtolower($email);
$gorod=$_POST['gorod'];
$url=$_POST['url'];
$icq=$_POST['icq'];
$phone=$_POST['phone'];
$company=$_POST['company'];
$about=$_POST['about'];
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����

if ($login==="" || strlen($login)>$maxname) {exit("$back ���� ��� ������, ��� ��������� $maxname ��������!</B></center>");}
if ($password==="" || strlen($password)>15) {exit("$back �� �� ����� ������!</B></center>");}

$lines=file("$datadir/usersdat.php");
$i = count($lines);

// �������� ������/������� ������
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (strtolower($login)===strtolower($rdt[0]) & $oldpass===$rdt[1]) {$ok="$i";} // ���� ����� �����/������
   else { if ($email===$rdt[2]) {$bademail="1"; } } // ����� � ������ ��� ���� ����� �����?
} while($i > "1");
if (!isset($ok)) {exit("$back ��� ����� ����� /������ / ����� �� ��������� �� � ����� �� ��. <BR><BR>
����� ������������ ������ <font color=red><B>���������</B></font><BR><BR>
<font color=red><B>������ ������� ��� ������� ������ - ���������� � ��������������!</B></font>");}
if (isset($bademail)) {exit("$back. �������� � ������� <B>$email ��� ���������������</B> �� �����! <BR>��������, ��� ����� ������������� � �� - ���������� � ��������������!</center>");}

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
$textdt=explode("|", $text); // ���������� ��������� �� ����� ������!!
$login=$textdt[0]; $password=$textdt[1]; $email=$textdt[2]; $url=$textdt[3];
$icq=$textdt[4]; $phone=$textdt[5]; $company=$textdt[6]; $about=$textdt[7];
$ip=$textdt[8]; $date=$textdt[9]; $status=$textdt[10]; $gorod=$textdt[11];

$file=file("$datadir/usersdat.php");
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i< sizeof($file);$i++) {if ($ok!=$i) {fputs($fp,$file[$i]);} else {fputs($fp,"$text\r\n");}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<html><body><script language='Javascript'>function reload() {location = \"admin.php?event=userwho\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#888888 align=center valign=center width=60%><tr><td><center>
<B>$login</B>, ������ ������� ��������. <BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� ��������.<BR>
<B><a href='admin.php?event=userwho'>������� �����, ���� �� ������ ������ �����</a></B></td></tr></table></td></tr></table></center></body></html>";
exit;}




if ($_GET['event']=="blockip") { // - ���������� �� IP

print"$shapka";
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines); $itogo=$i;
if ($i>0) {

print"<BR><table border=1 width=98% align=center cellpadding=3 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB height=25 align=center>
<td width=20><B>.X.</B></td>
<td width=150><B>IP</B></td>
<td><B>������������</B></td>
</tr>";
do {$i--; $idt=explode("|", $lines[$i]);
   print"<TR bgcolor=#F7F7F7><td width=10 align=center><table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?delip=$i'>.X.</a></B></td></tr></table></td><td>$idt[0]</td><td>$idt[1]</td></tr>";
} while($i > "0");
} else print"<br><br><H2 align=center>��������������� IP-������ �����������</H2><br>";
}
print"</table><br><CENTER><form action='admin.php?badip' method=POST>
������ IP �������! &nbsp; <input type=text style='FONT-SIZE: 14px; WIDTH: 110px' maxlength=15 name=ip> ������������: <input type=text style='FONT-SIZE: 14px; WIDTH: 200px' maxlength=50 name=text> 
<input type=submit value=' �������� '></form>*������� IP ���������, �� ������� ������ ������� � ������ ��������.
<BR>����� �������� ������������� - <B>$itogo</B><BR><BR></td></tr></table>"; }




if ($_GET['event']=="userstatus") { // ��VIP������ ����� � ���VIP������
if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page=1;}
$status=$_POST['status']; // ������� ������ VIP/ok
$usernum=$_POST['usernum']-1; // ���������� ����� ����� � ��
$addvip=$_POST['addvip']; // ���-�� ���� �� ������� ������� ����� VIP
if ($addvip!='' and !ctype_digit($addvip)) {exit("�� ������ ������ ���-�� ���� �� ������� ���� ���������� VIP-������!");}
if ($usernum<"1") {exit("$back. ������! - ������ ������ ������ �������!");}
if ($status!="vip") {$status="vip"; $dayx="";} else {$status="ok"; $dayx="";}
if ($addvip>0) {$dayx=$addvip*86400+mktime();}

$lines=file("$datadir/usersdat.php"); $imax=count($lines);
$dt=explode("|", $lines[$usernum]);
$userline="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$status|$dt[11]|$dayx|||\r\n";

$headers=null; // ��������� ��� �������� �����
$headers.="From: ������������� <".$adminemail.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// �������� ��� ���������� � ���� ������
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; // ��������� ��� ������� 
$boardurl="http://$host$self";
$boardurl=str_replace("admin.php", "index.php", $boardurl);

$allmsg="<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head><body>������������, $dt[0].<br><br>";

if ($status=="vip") {$st="� �������� �� VIP"; $allmsg.="� ������������ ��� <font color=red><B>� $date �. � ������� $time �� $addvip ����<br>
���������� ��� ������ �� VIP-������������.</B></font><br><br>
��� ��������� �������� ����� ����������� <B>������ ����������</B><br>
�� ������ �������� � ������ �������� ������ �������.<br>�� ��������� �����";

} else {$st="� VIP �� �������"; $allmsg.="� ������������ ��� <font color=#C0C0C0><B>� $date �. � ������� $time<br>
���������� ��� ������ � VIP-������������ �� �������.</B></font><br>";}

$allmsg.=" ���� ���������� ����� ���������� � ����� �������.<br><br>
������� �� ������� �������� �����: <a href='$boardurl'>$boardurl</a><br><br>
<small>* $dt[0], ��� ��������� ���������� ��� �� �������������� ����� ����������<BR>
<B>$brdname</B>. �������� �� ���� �� �����.<br></small><BR><BR></body></html>";

mail("$dt[2]", "��������� ������ ������� ($st) �� ����� ���������� ($brdname)", $allmsg, $headers);

// ������ ������ �����
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





if ($_GET['event'] =="editcity") { // �������������� �������

$record=file("$datadir/city.dat"); $imax=count($record); $i=0; $first=0; $last=$imax;

print"$shapka";

if ($imax>=0) { // ���� ���� ������ � ����� �������
print"<BR><table border=1 width=98% align=center cellpadding=3 cellspacing=0 bordercolor=#DDDDDD class=forumline><tr bgcolor=#BBBBBB height=25 align=center>
<td width=20><B>.X.</B></td>
<td width=50><B>�</B></td>
<td><B>�������� ������</B></td></tr>
<FORM action='admin.php?deletecity' method=POST name=delform>";

do {$dt=explode("|",$record[$i]);
print"<TR bgcolor=#F7F7F7><td width=10 bgcolor=#FF2244><B><input type=checkbox name='del$i' value='$dt[1]'";
if (isset($_GET['chekall'])) echo'CHECKED';
print"></B></td><TD>$dt[0]</TD><TD>$dt[1]</TD></TR>";
$i++;
} while($i < $imax);

print"</table>
<TABLE><TR><TD colspan=4>
<input type=hidden name=first value='$first'><input type=hidden name=last value='$last'><INPUT type=submit value='������� ��������� ������'></FORM>
</TD><TD>
<FORM action='admin.php?event=editcity&chekall' method=POST name=delform><INPUT type=submit value='�������� ��'></FORM>
</TD><TD>
<FORM action='admin.php?event=editcity' method=POST name=delform><INPUT type=submit value='����� �������'></FORM>
</table>
</table><br>* ���� � �������� city.dat �� ������ ������ ������������� � ��������!";
} else echo'<br><br><h2 align=center>���� ������� ���� - �������� �����.</h2>'; // if $imax>=0

print "<center><BR><form action=?newcity method=post name=REPLIER>
�������� �����: <input type=radio name=top value='1'> � ������ &nbsp;&nbsp; 
<input type=radio name=top value='0'checked> <B>� �����</B>  &nbsp;&nbsp;&nbsp;<input type=text name=city size=40> <input type=submit value='��������'></form>
<SCRIPT language=JavaScript>document.REPLIER.city.focus();</SCRIPT>";
} //if $event==editcity




// ����� �������������� ����������/��������������� ����� �� ������� �������� (���� mainreklama.html)
if ($_GET['event'] =="editinfo") {
if (isset($_GET['chto'])) $chto=replacer($_GET['chto']);
$editfile="$datadir/mainreklama.html"; // ������� ����
if ($chto=="1") $editfile="$datadir/left.html"; // ����� ����
if ($chto=="2") $editfile="$datadir/right.html"; // ������ ����
if ($chto=="3") $editfile="$datadir/reklama.html"; // ������ ����
if ($chto=="4") $editfile="$datadir/msg.html"; // ������ ����

$text=file_get_contents("$editfile"); // ���������� ����� ��������� �
$text=str_replace("<br>", "\r\n", $text);
print"$shapka <center><br><BR>���������� ����� <B>mainreklama.html</B>, ������� ������������ �� ������� �������� �����<BR><br>
<form action='admin.php?savebiginfo' method=post name=REPLIER>
<textarea rows=10 cols=80 name=text>$text</textarea><br><br>
<input type=hidden name=chto value='$chto'>
<input type=submit value='�������� � ���������'><BR></TABLE>"; }





if ($_GET['event'] =="config") { // ���������������� - ����� ��������

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
<td><B>����������</B></td>
<td><B>��������</B></td></tr>
<form action='admin.php?event=confignext' method=post name=REPLIER>
<tr><td width=350>��� ������� (������������ <B>� title</B>)</td><td width=420><input type=text value='$brdname' name=brdname maxlength=70 size=55></tr></td>
<tr><td>����� � ��������� ����� (������������ <B>������ �� ��������</B>)</td><td><input type=text value='$brdmaintext' name=brdmaintext  maxlength=150 size=55></tr></td>
<tr><td>�������� ����������� ����� ������� ��������?</td><td><input type=radio name=litemode value=\"1\"$lm1> ��&nbsp; <input type=radio name=litemode value=\"0\"$lm2> ��� (������� ������������ ������, � �� � �������)</tr></td>
<tr><td>��������� &quot;������&quot; ���������</td><td><input type=radio name=sendmail value=\"1\"$m1> ��&nbsp; <input type=radio name=sendmail value=\"0\"$m2> ���</tr></td>
<tr><td>����� ������ / �������� ��������� / �� �������?</td><td><input type=text value='$adminemail' name=adminemail maxlength=45 size=25> <input type=radio name=sendmailadmin value=\"1\"$ma1> ��&nbsp; <input type=radio name=sendmailadmin value=\"0\"$ma2> ��� &nbsp; <input type=text class=post value='$maxnewadmin' name=maxnewadmin size=1 maxlength=2> (�� 1 �� 99)</tr></td>
<tr><td>����������� ������� ����� ��� ������ ���������� �� ������������������ �������������?</td><td><input type=radio name=mailmustbe value=\"1\"$mn1> ��&nbsp; <input type=radio name=mailmustbe value=\"0\"$mn2> ���</tr></td>
<tr><td>������ ������ / ���������� *</td><td><input name=password type=hidden value='$password'><input class=post type=text value='�����' maxlength=10 name=newpassword size=15> &nbsp; / &nbsp;&nbsp; <input name=moderpass type=hidden value='$moderpass'><input class=post type=text value='�����' maxlength=10 name=newmoderpass size=15> (����������� � ������)</td></tr>
<tr><td>��������� ���������� ����� ������ <B>������������������ ����������?</B></td><td><input type=radio name=onlyregistr value=\"1\"$or1> ��&nbsp;&nbsp; <input type=radio name=onlyregistr value=\"0\"$or2> ��� - ����� ����</td></tr>
<tr><td class=row1>������������� �������� / ����� ����</td><td class=row2><input type=radio name=antispam value=\"1\"$as1> ��&nbsp;&nbsp; <input type=radio name=antispam value=\"0\"$as2> ��� &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text class=post value='$max_key' name=max_key size=4 maxlength=1> (�� 1 �� 9) ����</td></tr>
<tr><td class=row1>������������� �������� / �������� �����</td><td class=row2><input type=radio name=antiflud value=\"1\"$af1> ��&nbsp;&nbsp; <input type=radio name=antiflud value=\"0\"$af2> ��� &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text class=post value='$fludtime' name=fludtime size=4 maxlength=2> (�� 1 �� 20) ������.</td></tr>
<tr><td>��������� ��������� �����������?</B></td><td><input type=radio name=addrem value=\"1\"$a1> ��&nbsp;&nbsp; <input type=radio name=addrem value=\"0\"$a2> ���</td></tr>
<tr><td><font color=red>��������� ����������� ���� � ���������� / ���� �� �����? **</font></B></td><td rowspan=3><input type=hidden name=fotoadd value=\"0\">&nbsp;&nbsp; <input type=hidden value='$fotodir' name=fotodir maxlength=30 size=15>** �������� ������ � ����-������ ����� ����������:<br>��������� 450 ���, <a href='http://www.wr-script.ru/by.html'>������� ������������ �� ���� ��������</a> <input type=hidden name=fotoaddany value=\"0\"><input type=hidden value='$max_file_size' name=max_file_size maxlength=7 size=10></tr></td>
<tr><td><font color=red>���� ����� ��������� ��� ��� ������ ������������������?</font></B></td></tr>
<tr><td><font color=red>����. ������ ����������� ���� � ������</font></td></tr>
<tr><td><B>�����!**</B>��� ������������ �������������� ���-�� ���������� / +1 ��� ���������� ���������� � ���-��</td><td><input type=radio name=flagm1 value=\"1\"$sm1> ��&nbsp; <input type=radio name=flagm1 value=\"0\"$sm2> ��� &nbsp;&nbsp; <input type=radio name=flagm2 value=\"1\"$sf1> ��&nbsp; <input type=radio name=flagm2 value=\"0\"$sf2> ��� - ������ �����? ������ ��� 2 ����!</tr></td>
<tr><td>���������� ����� ���������� �� �������</td><td><input type=radio name=showten value=\"0\"$st1> ���&nbsp; <input type=radio name=showten value='10'$st2> 10-��&nbsp; <input type=radio name=showten value='20'$st3> 20-��</tr></td>
<tr><td>���-�� ������������ �������� ���������� <B>� �������</B></td><td><input type=text value='$msglength' maxlength=3 name=msglength size=10></tr></td>
<tr><td>������������ ��������� ������ ������������ �� ������?</B></td><td><input type=radio name=useactkey value=\"1\"$u1> ��&nbsp;&nbsp; <input type=radio name=useactkey value=\"0\"$u2> ���</td></tr>
<tr><td>����. ����� ���� ���������� / ����� ������������ / ������ ����������</td><td><input type=text value='$maxzag' name=maxzag maxlength=2 size=10> .:. <input type=text value='$maxname' maxlength=2 name=maxname size=10> .:. <input type=text value='$maxmsg' maxlength=4 name=maxmsg size=10></tr></td>
<tr><td>����. ���� ������ ����������</td><td><input type=text value='$maxdays' maxlength=3 name=maxdays size=10></tr></td>
<tr><td>���-�� �������� � ��������� �� ������� �������� / ���������� �� �������� � �������� ����������</td><td>
<input type=text value='$colrub' maxlength=1 name=colrub size=10> &nbsp; .:. &nbsp;&nbsp; <input type=text value='$qq' maxlength=2 name=qq size=10></tr></td>
<tr><td>������ ������ � ������ <B>���������</B>?</td><td><input type=radio name=liteurl value=\"1\"$lu1> ��&nbsp;&nbsp; <input type=radio name=liteurl value=\"0\"$lu2> ���</td></tr>
<tr><td>������������� ���� �� ����� � ������� ����� </td><td><input type=text value='$datadir' maxlength=20 name=datadir size=20> &nbsp; &nbsp; �� ���������: &quot;<B><U>./data</U></B>&quot;.</tr></td>

<tr><td>����</td><td><select class=input name=brdskin>
<option value=\"$brdskin\">�������</option>
<option value='skin-red' style='color: #FFFFFF; background: #FF0000'>�������</option>
<option value='skin-orange' style='color: #FFFFFF; background: #FF8000'>���������</option>
<option value='skin-green' style='color: #FFFFFF; background: #008000'>������</option>
</select></nobr></tr></td>
<tr><td colspan=2><BR><center><input type=submit value='��������� ������������'>
<input type=hidden name=datafile value=$datafile>
</form></td></tr></table>
<center><br>* ���� ������ �������� ������ - ������� ����� <B>'�����'</B> � ������� ����� ������.<br> ���������� ������������ ������ ����� �/��� �����.<br><br>
** <B>��� �������� > 200 ������</B> (���������� ����������� � ������ IP) � �����<br>�/��� ��� ������ '������� ������� ��������' ���������� <B>��� ������������� � '���'</B>!
</td></tr></table>"; }




if ($_GET['event'] =="confignext")  {  // ���������������� ��� 2 - ���������� ������
// ��������� ����� ������ ������/����������
if (strlen($_POST['newpassword'])<1 or strlen($_POST['newmoderpass'])<1) {exit("$back ����������� ����� ������ ������� 1 ������!");}
if ($_POST['newpassword']!="�����") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}
if ($_POST['newmoderpass']!="�����") {$pass=trim($_POST['newmoderpass']); $_POST['moderpass']=md5("$pass+$skey");}

// ������ �� ������. ��������, ��� � ������� ������ ���������� �������...
$fd=stripslashes($_POST['brdmaintext']); $fd=str_replace("\\","/",$fd); $fd=str_replace("?>","? >",$fd); $fd=str_replace("\"","'",$fd); $brdmaintext=str_replace("\r\n","<br>",$fd);

mt_srand(time()+(double)microtime()*1000000); $rand_key=mt_rand(1000,9999); // ���������� ��������� ����� ��� �����������

$configdata="<? // WR-board v 1.6.1 LUX // 06.08.10 �. // Miha-ingener@yandex.ru\r\r\n".
"$"."brdname=\"".$_POST['brdname']."\"; // ��� ������� ������������ � ���� TITLE � ���������\r\n".
"$"."brdmaintext=\"".$_POST['brdmaintext']."\"; // �����, ����������� ����� ������ ����� ����������\r\n".
"$"."password=\"".$_POST['password']."\"; // ������ ������ ���������� md5()\r\n".
"$"."moderpass=\"".$_POST['moderpass']."\"; // ������ ���������� ���������� md5()\r\n".
"$"."litemode=\"".$_POST['litemode']."\"; // �������� ����������� ����� ������� ��������\r\n".
"$"."sendmail=\"".$_POST['sendmail']."\"; // ��������/��������� ������� �������� ����� ���������\r\n".
"$"."sendmailadmin=\"".$_POST['sendmailadmin']."\"; // ���������� ��������� � ������ ������������ ������?\r\n".
"$"."maxnewadmin=\"".$_POST['maxnewadmin']."\"; // �� ����� ���������� ������ ������?\r\n".
"$"."adminemail=\"".$_POST['adminemail']."\"; // ����� ������\r\r\n".
"$"."fotoadd=\"".$_POST['fotoadd']."\"; // ��������� ����������� ���� � ����������?\r\n".
"$"."fotoaddany=\"".$_POST['fotoaddany']."\"; // ���� ����� ��������� ��� ��� ������ ������������������?\r\n".
"$"."fotodir=\"".$_POST['fotodir']."\"; // ������� ���� ����� ������� ����\r\n".
"$"."max_file_size=\"".$_POST['max_file_size']."\"; // ������������ ������ ��������� � ������\r\r\n".
"$"."mailmustbe=\"".$_POST['mailmustbe']."\"; // ���/���� ������������� ���������� ������ ��� ������ ����������\r\n".
"$"."flagm1=\"".$_POST['flagm1']."\"; // ��� ������������ �������������� ���-�� ���������� � ������� 1/0\r\n".
"$"."flagm2=\"".$_POST['flagm2']."\"; // +1 ��� ���������� ���������� � ���-�� � ������� 1/0\r\n".
"$"."antispam=\"".$_POST['antispam']."\"; // ������������� ��������\r\n".
"$"."antiflud=\"".$_POST['antiflud']."\"; // �������� ���/���� - 1/0\r\n".
"$"."fludtime=\"".$_POST['fludtime']."\"; // ��������-�����\r\n".
"$"."useactkey=\"".$_POST['useactkey']."\"; // ������������ ��������� �� ������? 1/0 - ��/���\r\n".
"$"."max_key=\"".$_POST['max_key']."\"; // ���-�� �������� � ���� �����������\r\n".
"$"."rand_key=\"".$rand_key."\"; // ��������� ����� ��� �����������\r\n".
"$"."showten=\"".$_POST['showten']."\"; // ���������� 10-�� ����� ���������� ��� �������\r\n".
"$"."onlyregistr=\"".$_POST['onlyregistr']."\"; // �������� ���������� ����� ������ ������������������ ����������?\r\n".
"$"."msglength=\"".$_POST['msglength']."\"; // ���-�� ������������ �������� ���������� � �������\r\n".
"$"."maxzag=\"".$_POST['maxzag']."\"; // ������������ ���-�� �������� � ���� ����������\r\n".
"$"."maxname=\"".$_POST['maxname']."\"; // ������������ ���-�� �������� � �����\r\n".
"$"."maxmsg=\"".$_POST['maxmsg']."\"; // ������������ ���������� �������� � ������ ����������\r\n".
"$"."maxdays=\"".$_POST['maxdays']."\"; // ������������ ���������� ���� ������ ����������\r\n".
"$"."liteurl=\"".$_POST['liteurl']."\";// ������������ ���? 1/0\r\n".
"$"."qq=\"".$_POST['qq']."\"; // ���-�� ������������ ���������� �� ������ ��������\r\n".
"$"."colrub=\"".$_POST['colrub']."\"; // ���-�� �������� � ��������� �� ������� ��������\r\n".
"$"."brdskin=\"".$_POST['brdskin']."\"; // ������� �������� ����\r\n".
"$"."addrem=\"".$_POST['addrem']."\"; // ��������� ��������� �����������?\r\r\n".
"$"."date=date(\"d.m.Y\"); // �����.�����.���\r\n".
"$"."time=date(\"H:i:s\"); // ����:������:�������\r\n".
"$"."datadir=\"".$_POST['datadir']."\"; // ����� � ������� �����\r\n".
"$"."datafile=\"".$_POST['datafile']."\"; // ��� ����� ���� ������\r\n".
"$"."back=\"<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'></head><body><center>��������� <a href='javascript:history.back(1)'><B>�����</B></a>\"; // ������� ������\r\n".
"$"."rubrika=\"\"; // ��������� ����������.\r\n?>";

// ������ ���� �� �����!!!
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



print"<BR><small>������� <b>$date</b></small>";

?>
</td></tr></table></td></tr></table>
<center><small>Powered by <a href="http://www.wr-script.ru" title="������ ����� ����������" class="copyright">WR-Board</a> &copy;<br></small></font></center>
</body></html>
