<? // WR-board v 1.6.0 lite // 06.08.10 �. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);

include "config.php";


function nospam() { global $max_key,$rand_key; // ������� ��������
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���: �������� ������ 24 ����
$stime=md5("$dopkod+$rand_key");// ���.���
echo'�������� ���: ';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //����� + ���� �� config.dbf + ��� ���������� ����� 24 ����
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6> (������� �����, ��������� �� ��������)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }



/***********************************************************************************
������� img_resize(): ��������� thumbnails
���������:
  $src             - ��� ��������� �����
  $dest            - ��� ������������� �����
  $width, $height  - ������ � ������ ������������� �����������, � ��������
�������������� ���������:
  $rgb             - ���� ����, �� ��������� - �����
  $quality         - �������� ������������� JPEG, �� ��������� - ������������ (100)
***********************************************************************************/
function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
  if (!file_exists($src)) return false;

  $size = getimagesize($src);

  if ($size === false) return false;

  // ���������� �������� ������ �� MIME-����������, ���������������
  // �������� getimagesize, � �������� ��������������� �������
  // imagecreatefrom-�������.
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


// ������� "����������� �����" - ��������� ��� �������
function addtop($brdskin) { global $wrbname, $wrbpass;
if (isset($_COOKIE['wrbcookies'])) {// ���� � ����� wrbcookies ����� ������� ���
$wrbc=$_COOKIE['wrbcookies']; $wrbc=htmlspecialchars($wrbc); 
$wrbc=stripslashes($wrbc); $wrbc=explode("|", $wrbc); $wrbname=$wrbc[0]; $wrbpass=$wrbc[1];} 
else {$wrbname=null; $wrbpass=null;}
echo'<TD align=right>';
if ($wrbname!=null) {print "<a href='tools.php?event=profile&pname=$wrbname'>��� �������</a>&nbsp;&nbsp;<a href='tools.php?event=clearcooke'>����� [<B>$wrbname</B>]</a>&nbsp;";}
else {print "<a href='tools.php?event=login'>���� � �������</a>&nbsp;|&nbsp;<a href='tools.php?event=reg'>�����������</a>&nbsp;";}
print"</TD></TR></TABLE></TD></TR></TABLE>
<TABLE cellPadding=0 cellSpacing=0 width=100%><TR><TD><IMG height=4 src='$brdskin/blank.gif'></TD></TR></TABLE>";
return true;}


function replacer ($text) { // ������� ������� ����
$text=str_replace("&#032;",' ',$text);
//$text=str_replace("&",'&amp;',$text); // �������������� ��� ������ ���� �� ����������� �����: ����������, ���������, ���������� � �.�.
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


//�������� ������� IP-������������ �� ���������� ���������� (���� bad_ip.dat)
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<noindex><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 10000);</script><center><br><br><B>������������ ������������ ��� ������ IP: $ip<br> ����������� ��������� ���������� �� ��������� �������:<br><br> <font color=red><B>$idt[1].</B></font><br><br>��� ��������� ������������� ����������,<br> � ��� ��������� ���������� ������������� ���������!</B></noindex>");
} while($i > "1");} unset($lines);}


// ������� ���������� ��������� //
if(isset($_GET['event'])) { if ($_GET['event'] =="add") {


if (!isset($_POST['rules'])) exit("$back. ��� ���������� <B>����������� � ���������.</B>");

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}


// �������� ������/������ �����. ����� �� �����, ����� ����� ���
if (isset($_POST['who'])) {$who=$_POST['who'];} else {$who=null;}
if (isset($_COOKIE['wrbcookies'])) { // ���� 1
    $wrfc=$_COOKIE['wrbcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc);
    $wrfc=explode("|", $wrfc);  $wrfname=$wrfc[0]; $wrfpass=$wrfc[1];
} else {$who=null; $wrfname=null; $wrfpass=null;}

$ok=null; if ($who!=null) { // ���� 2
if ($wrfname!=null & $wrfpass!=null) {
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (isset($rdt[1])) { $realname=strtolower($rdt[0]);
   if (strtolower($wrfname)===$realname & $wrfpass===$rdt[1]) {$ok="$i";}}
} while($i > "1");
if ($ok==null) {setcookie("wrbcookies","",time()); exit("������ ��� ������ � ����! <font color=red><B>�� �� ������� �������� ���������, ���������� ������ ��� ��� �����.</B></font> ��� ����� � ������ �� ������� � ���� ������, ���������� ����� �� ����� �����. ���� ������ ����������� - ���������� � �������������� �����.");}
}}

if (isset($_POST['name'])) {$name=$_POST['name'];} else {$name="";}
if (isset($_POST['email'])) { $email=replacer($_POST['email']); $email=str_replace("|","I",$email);
if ($mailmustbe==TRUE) { if (!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $email) or $email=="") exit("$back � ������� ���������� E-mail �����!</B></center>");}
$nameonly=$name; $name.="[email]".$email;}

$dtemp=explode("|", $_POST['rubrika']); if (!isset($dtemp[1])) exit("$back � �������� ���������!");
$katnumber=$dtemp[0]; $rname=$dtemp[2]; $katname=$dtemp[3]; $fid=$dtemp[1]; $days=$_POST['days'];
$katname.="[ktname]".$rname;
if (!ctype_digit($fid)) {exit("$back � �������� ���������!");}

if ($katnumber=="0") {exit("$back � �������� ���������!");}
if ($name == "" || strlen($name) > $maxname) {exit("$back ���� <B>��� ������, ��� ��������� $maxname ��������!</B></center>");}
$zag=$_POST['zag'];
if ($zag == "" || strlen($zag) > $maxzag) {exit("$back �� <B>�� ����� ��������� ����������, ��� �� ��������� $maxzag ��������!</B></center>");}
if (isset($_POST['type'])) {$type=$_POST['type'];} else {$type="";}
if ($type == "") {exit("$back � �������� ��� ���������� (<B>�����</B> ��� <B>�����������</B>).</B></center>");}
if ($type!="�" and $type!="�") {$type="�";}
$msg=$_POST['msg'];
if ($msg == "" || strlen($msg) > $maxmsg) {exit("$back ���� <B>�������� ������ ��� ��������� $maxmsg ��������.</B></center>");}

$newcityadd=FALSE;
if (isset($_POST['city'])) $city=$_POST['city'];
if (isset($_POST['newcity'])) {if (strlen($_POST['newcity'])>3) {$newcityadd=TRUE; $city=$_POST['newcity'];}}
if (isset($_POST['phone'])) {$phone=$_POST['phone'];} else {$phone="";}

if ($days>$maxdays or !ctype_digit($days)) {$days=$maxdays;}
$deldt=mktime()+$days*86400; // ��������� ���� �������� ����������
$msg=str_replace("|","I",$msg);
$zag=str_replace("|","I",$zag);
$today=mktime();

// ����������� ���� ��������� ����� $timer<10 - 10 ������ ������ �� �����
$timetek=time(); $timefile=filemtime("$datadir/$fid.dat"); 
$timer=$timetek-$timefile; // ������ ������� ������ ������� (� ��������) 
if ($timer<10 and $timer>0) {exit("$back ������� ���� ������� ����� $timer ������ �����.<br> ��������� ��� ��������� ������ � ��������� �������� ����������.");}

// ���������� ��� ��������� ���������� � �������������������
$flag="0"; $status="no"; $namesm=strtolower($name);
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
$rdt[0]=strtolower($rdt[0]);
if ($rdt[0]==$namesm) {$email="$rdt[2]"; $flag="yes";
if ($rdt[12]>0) {$vipdays=round(($rdt[12]-$today)/86400);} else {$vipdays="999";}
if ($rdt[10]==="vip" and $vipdays>0) {$status="vip";}}
} while($i > "1");

if (!isset($_COOKIE['wrbcookies']) and $flag=="yes") {exit("$back � ���������, �������� � ����� ������ ��� ��������������� �� ����� � <BR><B>�� �� ������ ������ ���������� ��� ����� ������.</B>");}

if ($antiflud!="0") {  // ������� �������� ����� 1 - �������� �� ������� � ������� �������
$linesn = file("$datadir/$fid.dat"); $in=count($linesn);
if ($in > 0) {
$lines=file("$datadir/$fid.dat"); $i=count($lines)-1; $itogo=$i; $dtf=explode("|",$lines[$i]);
$txtback="$dtf[0]|$dtf[1]|$dtf[2]|$dtf[3]|$dtf[4]|$dtf[5]|";
$txtflud="$katnumber|$katname|$name|$zag|$type|$msg|";
$txtflud=htmlspecialchars($txtflud); $txtflud=stripslashes($txtflud);
$txtflud=str_replace("\r\n","<br>",$txtflud);
if ($txtflud==$txtback) {exit("$back ������ ���������� ��� ��������� �� �����. ������� �� ����� ���������!");}}

// ����� 2 - �������� �� ������� ���������� � ��������� 10-20��
unset($lines); $lines=file("$datadir/newmsg.dat"); $max=count($lines); $i=$max-1;
if ($max > 0) { do { $dtf=explode("|",$lines[$i]);
$text1="$dtf[5]"; $text2="$msg"; $text2=replacer($text2);
if ($text1==$text2) {exit("$back ������ ���������� ��� ��������� �� �����. ������� �� ����� ���������!");}
$i--; } while($i > "1"); }
} //if $antiflud!=0

// ���� ���������� ��������� �� ������� ����� ����������
// ��������� ���� ���� � ������������ � ������
$allid=null; $records=file("$datadir/$fid.dat"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); $allid[$i]=$rd[10]; } while($i>0);
//natcasesort($allid); // ��������� �� �����������
$id=1000; $id="$fid$id";
do { $id++; if (is_file("$datadir/$fid$id.dat")) $id++; } while(in_array($id,$allid));
} else $id=$fid."1000"; // if ($i > 0)

//print"<PRE>"; print_r($allid); print "$id - $fid";


// ���� ��������, ��� ��� ��-�� ���� ������� �������� � ����������� �������
// ������� ��������� ��� ����������
//$add=null; $z=null; 
//do { $id=mt_rand(1000,9999); if ($fid<10) $add="0"; 
//if (!is_file("$datadir/$add$fid$id.dat") and strlen($id)==4) $z++;
//} while ($z<1); $id="$add$fid$id";

if (strlen($id)>8) exit("<B>$back. ����� ���������� ������ ���� ������. ����������� ������ ������� ��� ������� ������</B>");

$text="$katnumber|$katname|$name|$zag|$type|$msg|$date|$deldt|$fid|$status|$id|$today|$city|$phone||||||$rname|$ok|$ip||||||";

$foto=""; $fotoksize=""; $size[0]=""; $size[1]=""; $smallfoto="";

$text=htmlspecialchars($text);
$text=stripslashes($text);
$text=str_replace("\r\n","<br>",$text);

// ���������� ��������� �� ����� ������!!
$textdt=explode("|", $text);
$katnumber=$textdt[0];
$tdt=explode("[ktname]", $textdt[1]); $katname="$tdt[0]";
$name=$textdt[2]; $zag=$textdt[3]; $type=$textdt[4];
$msg=$textdt[5]; $date=$textdt[6]; $deldt=$textdt[7];
$fid=$textdt[8]; $status=$textdt[9]; $today=$textdt[11];
$city=$textdt[12]; $phone=$textdt[13]; $smallfoto=$textdt[14];
$foto=$textdt[15]; $fotoksize=$textdt[16];
if (!isset($email)) {$email="";}
// ������ ������ � ���� ����� ���������� - ����� ������� ������
$fp=fopen("$datadir/adminmail.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
flock ($fp,LOCK_UN);
fclose($fp);

// �������� ��������� ������
$lines=file("$datadir/adminmail.dat"); $i=count($lines); $aitogo=$i-1;
if ($i>$maxnewadmin) {

if ($sendmailadmin =="1")  { // �������� ��������� ������ �� ����
$headers=null;
$headers.="From: $name <$email>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

$deldate=date("d.m.Y",$deldt); // ������������ ���� �������� � ������������ ������

if (isset($nameonly)) {$name=$nameonly;} // ��� �� ���������� ����� ������� [email] ������ �����

// �������������� ������ ��� �������� �� ����� � ������ �� �����
if ($type=="�") {$sptype="�����";} else {$sptype="�����������";}
$msg=str_replace("\r\n", "<br>", $msg);

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$boardurl="http://$host$self";
$boardurl=str_replace("add.php", "index.php", $boardurl);
$boardadm=str_replace("index.php", "admin.php", $boardurl);


$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body><table border=0 align=center cellpadding=6 cellspacing=0>
<TR><TD colspan=3 align=center><h3>$aitogo ����� ����������</h3>$brdname (<a href='$boardurl'>$boardurl</a>)</TD></TR>
<TR><TD>";

do {$i--; $dt=explode("|", $lines[$i]); $msnum=$dt[0];

$zdt=explode("[ktname]",$dt[1]); $zdt2=explode("[email]",$dt[2]); if (!isset($zdt2[1])) $zdt2[1]="";
$dt[7]=date("d.m.Y",$dt[7]); // ����������� ���� �������� � ������������ ������

// �������� ��� ���������� � ���� ������
$allmsg.="<table border=1 align=center cellpadding=2 cellspacing=0 width=95% bordercolor='#DBDBDB'>
<tr><td colspan=2 align=center bgcolor='#E4E4E4'>�������: &nbsp;<B>$zdt[0]</B> >> <B>$zdt[1]</B> >> $dt[4]</td></tr>
<tr bgcolor='#F2F2F2'><td width=300>���: <B>$zdt2[0]</B></td><td width=70%>���������: <B>$dt[3]</B></td></tr>
<tr bgcolor='#F8F8F8'><td>�-����: <B>$zdt2[1]</B></td><td rowspan=5 valign=top>$dt[5]<br> <div align=left><a href='$boardurl?id=$dt[10]'>��������� >>></a></div></td></tr>
<tr bgcolor='#F8F8F8'><td>���� ������: $dt[6] �.</td></tr>
<tr bgcolor='#F2F2F2'><td>���� ��������: <B>$dt[7]</B> �.</td></tr>
<tr bgcolor='#F2F2F2'><td>������� (
<a href='$boardadm?event=topic&id=$dt[10]&topicrd=$i'>������������� *</a> / 
<a href='$boardadm?id=$dt[10]&msgtype=$dt[7]&topicxd=$i&page=1'>������� *</a>)</td></tr>
<tr bgcolor='#F2F2F2'><td>������� � ������� <a href='$boardurl?fid=$dt[8]'><B>$zdt[1]</B></a></td></tr>
</table><br>";

} while($i>"1");

$allmsg.="
* ��� �������������� / �������� ���������� ������� <a href='$boardadm'>� �������</a>, ���������������. 
�� �������� ���� ��������, ��������� � ��� ������ � �� ������� ������� ���������� ����� ������.<br>

<br><br>** ��� ��������� ������������� � ���������� ������� � ����� ����������. �������� �� ���� �� �����.
���� �� �������� ��� ������, ������ ��� ����� ������ � ������ �������������� � �������� �������� ��������� ���������� �� �����.</body></html>";

mail("$adminemail", "$brdname ($aitogo ����� ���������� �� ����� ����� ����������) �� ��������� �� $date $time", $allmsg, $headers); }

$fp=fopen("$datadir/adminmail.dat","w+");
flock ($fp,LOCK_EX);
fputs($fp,"");
flock ($fp,LOCK_UN);
fclose($fp);}
// ����� ����� �������� ������ ��������� �� ����


$fp=fopen("$datadir/$fid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ���� ��������� ������� � ���-�� ���������� � ���������
$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. �������� � ����� ������, ���� ������ ���� - ���������� � ��������������. <br><B>���� ������ ������������! ������� � ������� � �������� �������!</b>");
$i=count($lines); 

$itogo=$i; $ok=null;

do {$i--; $dt=explode("|",$lines[$i]);
$lines[$i]=$lines[$i];
if ($fid==$dt[0]) {$ok=1; if ($type=="�") {$dt[3]++;} else {$dt[2]++;} $lines[$i]="$fid|$dt[1]|$dt[2]|$dt[3]|\r\n";}
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

// ��������� ������ � ����������
$fp=fopen("$datadir/stat.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|$deldt|$type|\r\n");
flock ($fp,LOCK_UN);
fclose($fp);

if ($newcityadd==TRUE) { // ��������� ����� � ���� � �������� - city.dat (���� ����� ����)
$fp=fopen("$datadir/city.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"999|$city|\r\n");
flock ($fp,LOCK_UN);
fclose($fp); }

// ��������� � 10-20-�� ����� ����������
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


if (isset($_POST['idmsg'])) { // ���� ������� ��������� ����������
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



// �������� ������ � ����� ��������� � ���������� ����������
$headers=null; // ��������� ��� �������� �����
$headers.="From: �����-������������� <$adminemail>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

$deldate=date("d.m.Y",$deldt); // ������������ ���� �������� � ������������ ������

if (isset($nameonly)) {$name=$nameonly;} // ��� �� ���������� ����� ������� [email] ������ �����

// �������������� ������ ��� �������� �� ����� � ������ �� �����
if ($type=="�") {$sptype="�����";} else {$sptype="�����������";}
$msg=str_replace("\r\n", "<br>", $msg);

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"];
$boardurl="http://$host$self";
$boardurl=str_replace("add.php", "index.php", $boardurl);
$remurl=str_replace("index.php", "tools.php", $boardurl);

// �������� ��� ���������� � ���� ������
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<title>���� ���������� ���������</title>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body bgcolor='#EAEAEA'>
<table bordercolor=#FFFFFF border=1 align=center width='99%' cellpadding='0' cellspacing='0'><tr><td>
<table width='100%' cellpadding='4' cellspacing='0'><tr><td  bgcolor='#FFCACA' align=left>
����� ���������� \"<B><a href='$boardurl'>$boardurl</a></B>\"</td><td width='*' bgcolor='#FFCACA' align=right><B>$brdname</B>
</td></tr></table>
<table border=0 width='100%' cellpadding='4' cellspacing='0'><tr><td width='100%' bgcolor='#ffffff' align=center>
<br><h3 align='center'>���� ���������� ������� ���������</h3>
<table border=1 cellpadding='1' cellspacing='0' bordercolor=white BGCOLOR='#FFD0D0' WIDTH='99%'>
<tr><td>���� ���: <B>$name</B></td></Tr>
<tr><td>ID: <B>$id</B> &nbsp;&nbsp; ������������: &nbsp; <B>$rname</B> >> <B>$katname</B> >> $sptype</td></tr>
<TR><TD>��������� <B>$date �. - <small>$time</small></B> &nbsp;&nbsp;&nbsp; ���� ����������: <B>$days ��.</B> &nbsp;&nbsp;&nbsp;  ���� ��������: <B>$deldate</B> �.</td></tr>
<tr><td>�����: <B> $city </B> ���: <B> $phone</B></td></Tr>
<tr><td>E-mail: <B><a href='mailto:$email'>$email</a></B></td></tr>
<tr><td>��������� ����������: <B>$zag</B></td></Tr>
<tr><td bgcolor=white><Div Align='Justify'>$msg</Div></td></Tr>
</table>
<br><center><a href='$boardurl?id=$id'>����������� ����������</a><BR>
<a href='$boardurl?fid=$fid'>��������� � ������� <B>$katname</B></a><br><br>
</td></tr></table>
</td></tr></table>
<UL><A Href='$boardurl'>$boardurl</A> - $brdname<Br><A Href='$remurl?event=addrem'>��������� � ��������������� �����</A>";
$printmsg="$allmsg </body></html>";

$kompr=file_get_contents("$datadir/msg.html"); // ��������� ���������� ����� ������������ ����������� � ����������
$kompr.="<br><br>* ��� ��������� ������������� � ���������� ������� � ����� ����������. �������� �� ���� �� �����.</body></html>";
$allmsg.=$kompr;

if ($sendmail=="1") { // ���������� ������ ���� ��������� ��������
if (isset($email) & $flag=="yes") { mail("$email", "���������� ID-$id ($brdname)", $allmsg, $headers);} }

// C����� ������� ���� �� ����� ����� ���� �� ����� � �������
if (!isset($flag) and $onlyregistr==1) { if (isset($_COOKIE['wrbcookies'])) {setcookie("wrbcookies", "", time());}}

print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 2000);</script>$printmsg"; exit;
}
}
//} // if is_file($fid.dat)








if (!isset($_GET['event']) and !isset($_GET['fid'])) {  // ������� ��������

$rubrika="���������� ����������";
include "$topurl"; addtop($brdskin); // ���������� �����
if ($onlyregistr=="1" and !isset($wrbname))
{ print"<BR><BR><BR><BR><BR><center><font size=-1><B>��������� ����������!</B><BR><BR> 
�� ����� ����� ���������� ����������<BR><BR><B> ��� ����������� <font color=#FF0000> ���������!</B></font><BR><BR>
������������������ ����� �� <B><a href='tools.php?event=reg'>���� ������</a></B><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>"; }

else {
if (isset($_GET['fid'])) {$fid=$_GET['fid'];} else {$fid="";}
$today=mktime();

// �������� ���������� �� ������ ������ (������ �� ������)
$zag=null; $msg=null; $t1=null;$t2=null;
$name=null; $email=null; $city=null; $phone=null; $info=null; $addpole=null;



// ���� ������� ��������� ����������
if (isset($_GET['id'])) { $id=$_GET['id']; $stop=0; $num=null;
$info="<br><br>����������, ��������� ������������� ����������.<br> ���� ���� ���������� - ���������, �������� �����:</center><br> - ����� ����������;<br> - ������� �������� ���; <br>- ����������� � ���������;<br> - ������� ���������.<br><br>";
$lines=file("data/wait.dat"); $itogo=count($lines)-1; $i=$itogo; // ��������� ��� ���������� � ������
do {$dt=explode("|",$lines[$i]);
if ($dt[10]==$id) {$num=$i; $i="0";} $i--;
} while ($i>="0");

if ($num!=null) {
$dto=explode("|",$lines[$num]); $tdw=explode("[ktname]", $dto[1]);
$addpole="<input type=hidden name=idmsg value='$id'>";
$zag=$dto[3]; // ����
$msg=$dto[5]; // �����
if ($dto[4]=="�") {$t1="checked"; $t2="";} else {$t2="checked"; $t1="";} // ���
$city=$dto[12]; // �����
$phone=$dto[13]; // �������
if (stristr($dto[2],"[email]")) {$tdt=explode("[email]", $dto[2]); $name=$tdt[0]; $email=$tdt[1];} else {$name="$dt[2]"; $email="$dt[2]";}
$fid=$dto[8];} else (exit("<center><br><br>������ ���������� ��� ��� � ����.<br> ���������� ���������� ��� �����.<br> ��� ����� ��������� �� ������ <a href='add.php'>���������� ����� ����������</a>"));
} //����� ����� ��������� ����������



print"<center><TABLE class=bakfon cellPadding=2 cellSpacing=1>
<FORM action='add.php?event=add' method=post name=addForm enctype=\"multipart/form-data\">
<TBODY>
<TR class=row2><TD height=23 align=left colSpan=2><center><B>$rubrika</B>$info</TD></TR>";

echo'<tr class=row1><TD>���������:</TD><TD><SELECT name=rubrika class=maxiinput><option>�������� �������</option>\r\n';

// ���� ��������� ��� ��������� �� �����
$realbase="1"; if (is_file("$datadir/$datafile")) $lines=file("$datadir/$datafile");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);}}
if ($datasize<=0) exit("$back. �������� � ����� ������, ���� ������ ���� - ���������� � ��������������. <br><B>���� ������ ������������! ������� � ������� � �������� �������!</b>");
$imax=count($lines);

$i="0"; $r="0"; $cn=0;
do {$dt=explode("|", $lines[$i]);
if ($fid==$dt[0]) $fy="selected"; else $fy="";
if ($dt[1]!="R") print "<OPTION value=\"$i|$dt[0]|$r|$dt[1]|\"$fy>$r - $dt[1]</OPTION>\r\n";
else {$r=$dt[2]; if ($cn!=0) {echo'</optgroup>'; $cn=0;} $cn++; print "<optgroup label=' - $dt[2]'>";}
$i++;
} while($i < $imax);

print "</optgroup></SELECT></TD></TR>
<TR class=row2><TD>���� ����������:<FONT color=#ff0000>*</FONT><BR>(�� ����� $maxzag ��������)</TD>
<TD><INPUT name=zag class=maxiinput maxlength=$maxzag value=\"$zag\"></TD></TR>

<TR class=row1><TD>����� ����������:</TD>
<TD><TEXTAREA class=maxiinput name=msg style='HEIGHT: 200px; WIDTH: 370px'>$msg</TEXTAREA></TD></TR>

<TR class=row2><TD>��� ����������:<FONT color=#ff0000>*</FONT></TD>
<TD><INPUT name=type type=radio value='�' $t1><B><font color=#EE2200>�</font></B>���������� 
<INPUT name=type type=radio value='�' $t2><B><font color=#1414CD>�</font></B>���� </TD></TR>

<tr class=row1 height=23><td>�����:</td><TD><SELECT name=city style='FONT-SIZE: 14px; WIDTH: 200px'><OPTION value='0'> - - - - - ������ ���� - - - - -</OPTION>";
$slines = file("data/city.dat"); $smax = count($slines); $i="0"; do {$dts=explode("|",$slines[$i]);
print "<OPTION value=\"$dts[1]\">$dts[1]</OPTION>\r\n"; $i++; } while($i < $smax);
print "</SELECT>&nbsp; ������: <input type=text value='' name=newcity size=30 maxlength=40 class=maininput style='FONT-SIZE: 14px; WIDTH: 180px'><BR> * ���� ������ ������ ��� � ������ ������� ��� � ���� ������</TD></tr>

<TR class=row1 height=23><TD>���� ���:$addpole";

if (isset($wrbname)) {
print "<INPUT type=hidden name=who value='��'><INPUT type=hidden name=rules><input type=hidden name=name value='$wrbname'></TD><TD><B>$wrbname</B></td></tr>";
}  else  {
print "
<FONT color=#ff0000>*</FONT></TD><TD><INPUT type=hidden name=who value=''>
<INPUT name=name class=maxiinput value=\"$name\" maxlength=30>
<TR class=row2 height=23><TD>��� �-����:<FONT color=#ff0000>*</FONT></TD><TD><INPUT name=email class=maxiinput value=\"$email\" maxlength=30></td></tr>
<TR class=row2 height=23><TD>�������: <BR>(�� �������: (495) 344356)</TD><TD><INPUT name=phone value=\"$phone\" class=maxiinput maxlength=35></td></tr>
";}

echo'<TR class=row1><TD>���� �������� ����������:</TD>
<TD><SELECT name=days style="FONT-SIZE: 13px">
<OPTION value=7>7 ����</OPTION>
<OPTION value=14>14 ����</OPTION>
<OPTION selected value=30>30 ����</OPTION>';

if (isset($wrbname)) {
print"<OPTION value=60>60 ����</OPTION>
<OPTION value=$maxdays>$maxdays ����</OPTION>";}

echo '</SELECT></TD></TR>';

if ($antispam==TRUE and !isset($wrbname)) {print"<tr class=row1><td>��������</td><TD>"; nospam();} // �������� !

print"</TD></TR>";

if (!isset($wrbname)) {
print"<TR class=row2><TD colSpan=2><INPUT type=checkbox name=rules>� <B><A href='tools.php?event=about'>���������</A></B> ����������</TD></TR></TR>";
} // if !isset($wrfname)

echo'<TR class=row1><TD colspan=2 align=middle><INPUT class=longok type=submit value=���������></TD></TR></FORM></TBODY></TABLE>';
}
}




if (isset($_GET['fid']) and isset($_GET['id'])) {
$fid=$_GET['fid']; $id=$_GET['id'];

if (!ctype_digit($fid) or !ctype_digit($id)) {exit("$back. ������� ������. ������� ����� �� �����.");}

if (is_file("$datadir/$id.dat")) { $linesn = file("$datadir/$id.dat"); $in=count($linesn);
if ($in > 15) {exit("$back <B>����� 15 ������������</B> � ���������� ��������� ��������.</center>");}}

// ������� ���������� ���������
if(isset($_GET['add'])) {

if (!isset($_COOKIE['wrbcookies'])) {exit("<br><br><br><B><center>���������� �����������<br> ��������� ������ ������������������ ����������!!!</B></center><br><br><br>");}

if (isset($_POST['name'])) {$name=$_POST['name'];} else {$name="";}
if (strlen($name)<1 || strlen($name) > $maxname) {exit("$back ���� <B>��� ������, ��� ��������� $maxname ��������!</B></center>");}
$name=str_replace("|","I",$name);

if (isset($_POST['type'])) {$type=$_POST['type'];} else {$type="0";}
if (strlen($type)>2) {exit("$back. ������ ����� �������� ������ �� ���� ����!");}
if (!ctype_digit($type)) {exit("$back. ������� ������. ������� ����� �� �����.");}

$msg=$_POST['msg']; if ($msg=="" || strlen($msg) > $maxmsg) {exit("$back ��� <B>����������� ���� ��� ��������� $maxmsg ��������.</B></center>");}
$msg=str_replace("|","I",$msg);

if (isset($_POST['email'])) {$email=$_POST['email'];} else {$email="";}
$email=str_replace("|","I",$email);

$day=mktime();
$text="$name|$email|$msg|$day|$type|";
$text=htmlspecialchars($text);
$text=stripslashes($text);
$text=str_replace("\r\n","<br>",$text);

if ($antiflud=="1") {  // ������� �������� �����!
if (is_file("$datadir/$id.dat")) { // ��������� ���� �� ����� ����
$linesn = file("$datadir/$id.dat"); $in=count($linesn);
if ($in > 0) {
$lines=file("$datadir/$id.dat"); $i=count($lines)-1; $itogo=$i; $dtf=explode("|",$lines[$i]);
$txtback="$dtf[0]|$dtf[1]|$dtf[2]|$dtf[3]|$dtf[4]|";
if ($text==$txtback) {exit("$back ������ ����������� ��� ��������! ������� �� ����� ���������!");} }
}}

if (is_file("$datadir/$id.dat")) { $lines = file("$datadir/$id.dat"); $itogo=count($lines);} else {$itogo=0;}

$lines[$itogo]="$text\r\n"; $p=$itogo+1;

$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� �����
for ($i=0; $i<$p; $i++) {fputs($fp,"$lines[$i]");}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 1000);</script>"; exit;
}


$rubrika="���������� ����������� � ����������";

include "$topurl"; addtop($brdskin); // ���������� �����

if (isset($wrbname)) { print"<center><BR><BR><BR>
<FORM action='add.php?add=1&fid=$fid&id=$id' method=post name=addForm>
<TABLE class=bakfon cellPadding=2 cellSpacing=1><TBODY>
<TR class=toptable><TD height=23 align=middle colSpan=2><B>$rubrika</B></TD></TR>
<TR class=row1 height=23><TD>���� ���: <FONT color=#ff0000>*</FONT></TD><TD><INPUT name=name class=maininput style='FONT-SIZE: 14px; WIDTH: 300px' maxlength=30></td></tr>
<TR class=row2><TD>�����:</TD><TD><INPUT name=email class=maininput style='FONT-SIZE: 14px; WIDTH: 300px' maxlength=$maxzag></TD></TR>
<TR class=row1><TD>����� �����������: <FONT color=#ff0000>*</FONT></TD><TD><TEXTAREA class=maininput name=msg style='FONT-SIZE: 14px; HEIGHT: 100px; WIDTH: 300px'></TEXTAREA></TD></TR>
<TR class=row2><TD>������� �������� <BR> ����������:</TD><TD>&nbsp;&nbsp;&nbsp; <INPUT name=type type=radio value='1'>1&nbsp;&nbsp; <INPUT name=type type=radio value='2'>2&nbsp;&nbsp; <INPUT name=type type=radio value='3'>3&nbsp;&nbsp; <INPUT name=type type=radio value='4'>4&nbsp;&nbsp; <INPUT name=type type=radio value='5'>5</TD></TR>
<TR class=row1><TD colspan=2 height=32 align=middle><INPUT class=longok type=submit value=���������></TD></TR>
</TBODY></TABLE></FORM><BR><BR><BR>";
} else {echo'<br><br><br><B><center>���������� �����������<br> ��������� ������ ������������������ ����������!!!</B></center><br><br><br>';}


}


if (is_file("$brdskin/bottom.html")) include "$brdskin/bottom.html";
?>

<center><small>Powered by <a href="http://www.wr-script.ru" title="������ ����� ����������" class="copyright">WR-Board</a> &copy; 1.6 Lite<br></small></font></center>
</body></html>
