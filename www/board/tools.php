<? // WR-board v 1.6.1 LUX // 06.08.10 �. // Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);

include "config.php";

$addstyle="style='font-family: Verdana; font-size: 12px; text-decoration: none; color: #000000; cursor: default; background-color: #FFFFFF; border-style: solid; border-width: 1px; border-color: #000000;'";
$shapka="<html><head><META http-equiv=Content-Type content='text/html; charset=windows-1251'></head><body>";

$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; // ��������� ��� ������� 
$boardurl="http://$host$self";
$boardurl=str_replace("tools.php", "index.php", $boardurl);

// ������� ����������
function prcmp ($a, $b) {if ($a==$b) return 0; if ($a>$b) return -1; return 1;}


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
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6> (������� �����, ��������� �� ��������)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }



if (!is_file("$brdskin/top.html")) {$topurl="$brdskin/top.html";} else {$topurl="$brdskin/top.html";}



// ��� ������� � ����� tools.php ����������� ������ ��� ������� ���������� $event
if(isset($_GET['event'])) {


if ($_GET['event']=="login") {
include "$topurl"; addtop($brdskin); // ���������� �����

print"<BR><BR><center>
<TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action='tools.php?event=regenter' method=post>
<TR class=toptable><TD align=middle colSpan=2><B>���� � �������</B></TD></TR>
<TR class=row1><TD>���:</TD><TD><INPUT name=name class=miniinput></TD></TR>
<TR class=row2><TD>������:</TD><TD><INPUT type=password name=pass class=miniinput></TD></TR>
<TR class=row1><TD colspan=2><center><INPUT type=submit class=longok value=�����></TD></TR></TABLE></FORM>";

print "<BR><BR><BR>
<TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action='tools.php?event=givmepassword' method=post>
<TR class=toptable><TD align=middle colSpan=3><B>������ ������?</B></TD></TR>
<TR class=row1><TD> <B>������� �����</B> - ��� � ������ �����<BR> ������� ��� �� ����������� �����.</TD>
<TD><INPUT name=myemail class=maininput></TD>
<TD><INPUT type=submit class=longok value='�������� ������'></TD></TR></TABLE></FORM><BR><BR>";
}


if ($_GET['event']=="regenter") { // �������� ������/������ � ���� �� �����

if (isset($_POST['name']) and isset($_POST['pass'])) {
 $name=strtolower($_POST['name']); $pass=$_POST['pass'];
 $name=str_replace("|","I",$name); $text="$name|$pass|";
 if (strlen($text)<4) {exit("$back �� �� ����� ��� ��� ������? ���� ���/������ ������� �������!");}
 $text=replacer($text); $dt=explode("|", $text);
} else {exit("$back �� ������� ��� �/��� ������!");}

$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   $emfile=strtolower($rdt[0]);
   if ($name==$emfile & $pass==$rdt[1]) { if ($rdt[10]=="no") exit("$back. ���� ������� ������ �� ������������!<br> ��������� �� <a href='tools.php?event=reg3'>���� ������ ��� ���������.</a> ���� ������ ��� �� �����.");
   $regenter="$i"; $tektime=time();
   $wrbcookies="$rdt[0]|$rdt[1]|$tektime|$tektime|";
   setcookie("wrbcookies", $wrbcookies, time()+1728000); }
} while($i > "1");
if (!isset($regenter)) {exit("$back. ��� ������ <B>��������</B>!</center>");}
Header("Location: index.php"); exit; }



// ������� ����, ���� ������ �����
if ($_GET['event']=="clearcooke") { setcookie("wrbcookies", "", time()); print "<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 0);</script>"; exit;}



if ($_GET['event']=="viewfoto") {$foto=$_GET['foto']; // �������� ���� �������
exit("<html><head><title>����</title></head><body><center><img src='$fotodir/$foto'></body></html>");}




if ($_GET['event'] =="about") { // ������� ������ � ������
include "$topurl"; addtop($brdskin); // ���������� �����
include"$datadir/pravila.html"; }




if ($_GET['event'] =="givmepassword") { // �������� �������� ������ �� ����

if ($sendmail=="0") {exit("$back.<B>������� �������� ����� ������������� ���������������!");}

// ��������������� ����� � ������ �������
$myemail=strtolower($_POST['myemail']);
$lines=file("$datadir/usersdat.php");
$i = count($lines);
$regenter="";
do {$i--; $rdt=explode("|", $lines[$i]);
// �������� �� ���� ������������� � ������� ������
if ($myemail==$rdt[2]) {$myname=$rdt[0]; $mypassword=$rdt[1];}
} while($i > "1");

// �������� ������������ ��� ����� � ������ �� ����
if (isset($myname)) {
$headers=null; // ��������� ��� �������� �����
$headers.="From: <".$adminemail.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/plan; charset=windows-1251";

// �������� ��� ���������� � ���� ������
$allmsg=$brdname.' (������ �����������)'.chr(13).chr(10).
        '�� ��������� ��� � ������ ������� � ����� ���������� �� ������: '.$boardurl.chr(13).chr(10).chr(13).chr(10).
        '���� ���: '.$myname.chr(13).chr(10).
        '��� ������: '.$mypassword.chr(13).chr(10).chr(13).chr(10).chr(13).chr(10).
        '��� ������ ������������� �������, �������� �� ���� �� �����.'.chr(13).chr(10);

// ���������� ������ ������� �� �������� ;-)
mail("$myemail", "$brdname (��������������� ������)", $allmsg, $headers);

// ���� ���� �������� � �������� �������
$msgtoopr="<B>$myname</B>, �� ��� ����������� ����� ������� ��������� � ������ � ������� ������� � ����� ����������.";
}

// ���� ��� ������ ������ � ��
else {$msgtoopr="<B>��������� � ����� �������</B><BR> �� ����� ���������� <B>�� ����������������!</B>";}

print "<html><body><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 1500);</script>
<BR><BR><BR><center><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 width=300><tr><td><center>
$msgtoopr ����� ��������� ������ �� ������ ������������� ���������� �� ������� ��������.
���� ����� �� ����������, ������� <B><a href='index.php'>�����</a></B>.</td></tr></table></center><BR><BR><BR></body></html>";
exit;
}






// �������� ��������� �����
if ($_GET['event']=="mailto") {
if ($sendmail!="1") {print"$back. <center><B>��������, �� ������� �������� ����� ������������� ���������������!<BR><BR><BR><a href='' onClick='self.close()'>������� ����</b></a></center>"; exit;}

$uemail=$_GET['email'];
$uname=$_GET['name'];
if (isset($_GET['id'])) $id=$_GET['id']; $fid=substr($_GET['id'],0,3);

print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
<title>����������� ��������� ������ ����������</title></head><body topMargin=5>
<center><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1>
<FORM action='tools.php?event=mailtogo' method=post>
<TBODY><TR><TD align=middle bgColor=#cccccc colSpan=2>�������� ��������� <B>$uname</B></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; ���� ���:<FONT color=#ff0000>*</FONT> <INPUT name=name style='FONT-SIZE: 14px; WIDTH: 150px'>

� E-mail:<FONT color=#ff0000>*</FONT> <INPUT name=email style='FONT-SIZE: 14px; WIDTH: 180px'></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; ���������:<FONT color=#ff0000>*</FONT><br>
<TEXTAREA name=msg style='FONT-SIZE: 14px; HEIGHT: 150px; WIDTH: 494px'></TEXTAREA></TD></TR>

<INPUT type=hidden name=uemail value=$uemail><INPUT type=hidden name=uname value=$uname>";

if ($antispam==TRUE and !isset($wrbname)) {print"<tr><td bgColor=#FFFFFF>"; nospam();} // �������� !

if (isset($_GET['id'])) {print"<INPUT type=hidden name=id value=$id><INPUT type=hidden name=fid value=$fid>";}

echo'<TR><TD bgColor=#FFFFFF colspan=2><center><INPUT type=submit value=���������></TD></TR></TBODY></TABLE></FORM>'; exit;
}


// ��� 2 �������� ��������� ������������ �����
if ($_GET['event']=="mailtogo")  {
$name=$_POST['name'];
$email=$_POST['email'];
$msg=$_POST['msg'];

if (isset($_POST['id'])) $id=$_POST['id']; $fid=substr($_POST['id'],0,3);
$uname=$_POST['uname'];
$uemail=$_POST['uemail'];

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}

if (!eregi("^([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)$", $email) and strlen($email) < 30 and $email != "") {exit("$back � ������� ���������� E-mail �����!</B></center>");}
if (!eregi("^([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)$", $uemail) and strlen($uemail) < 30 and $uemail != "") {exit("$back � ������������ ����� �������������� E-mail �����!</B></center>");}
if ($name=="") {exit("$back �� �� ����� ��� ���!</B></center>");}
if ($msg=="") {exit("$back �� �� ����� ���������!</B></center>");}

$text="$name|$msg|$uname|";
$text=htmlspecialchars($text);
$text=stripslashes($text);
$text=str_replace("\r\n","<br>",$text);
$exd=explode("|",$text); $name=$exd[0]; $msg=$exd[1]; $uname=$exd[2];

$headers=null; // ��������� ��� �������� �����
$headers.="From: ������������� <".$adminemail.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// �������� ��� ���������� � ���� ������
if (isset($_POST['id'])) $apurl="?id=$id"; else $apurl="tools.php?event=profile&pname=$uname";
$boardurl=str_replace("tools.php", "", $boardurl);

$allmsg="<html><head>
<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
</head><body>
<BR><BR><center>$uname, ��� ��������� ���������� ��� �� ���������� ����� ���������� <BR><B>$brdname</B><BR><BR>
<table cellspacing=0 width=700 bgcolor=#C0C0C0><tr><td><table cellpadding=6 cellspacing=2 width='100%'>
<tr bgcolor=#F7F7F7><td width=130 height=24>���</td><td>$name</td></tr>
<tr bgcolor=#F7F7F7><td>E-mail:</td><td><font size='-1'>$email</td></tr>
<tr bgcolor=#F7F7F7><td> ���������:</td><td><BR>$msg<BR></td></tr>
<tr bgcolor=#F7F7F7><td>���� �������� ���������:</td><td>$time - <B>$date �.</B></td></tr>
<tr bgcolor=#F7F7F7><td>���������� �� ��������:</td><td><font size=-1><a href='$boardurl$apurl'>$boardurl$apurl</a></font></td></tr>
<tr bgcolor=#F7F7F7><td>������� �� ������� ��������:</td><td><a href='$boardurl'>$boardurl</a></td></tr>
</table></td></tr></table></center><BR><BR>* ������ ������ ������������� � ���������� �������, �������� �� ���� �� �����.
</body></html>";

mail("$uemail", "����� �� ���� ���������� �� ($brdname) �� $name ", $allmsg, $headers);
print "<div align=center><BR><BR><BR>���� ��������� <B>�������</B> ����������.<BR><BR><BR><a href='' onClick='self.close()'><b>������� ����</b></a></div>";
exit; }







if ($_GET['event'] =="reg") {

// ������ �� �������-������������� �� �������
if (isset($_COOKIE['wrbcookies'])) exit("<B>��� ���������� ����� �� �������� �������, � ����� ���� ����������������!</B></font>");

// ��������� IP-���������� �� ������� � ����. ���� ���� - ��������!
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<noindex><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 10000);</script><center><br><br><B>������������ ������������ ��� ������ IP: $ip<br> ����������� ���������������� � ��������� ���������� �� ��������� �������:<br><br> <font color=red><B>$idt[1].</B></font><br><br>��� ��������� ������������� ����������,<br> � ��� ��������� ���������� � ���������������� ������������� ���������!</B></noindex>");
} while($i > "1");} unset($lines);}


include "$topurl"; addtop($brdskin); // ���������� �����

print "<BR><center><TABLE class=bakfon cellPadding=2 cellSpacing=1>
<FORM action='tools.php?event=reguser' method=post>
<TBODY><TR height=25 class=toptable><TD align=middle colSpan=2><B>�����������</B></TD></TR>
<TR class=row2><TD>���:<FONT color=#ff0000>*</FONT></TD><TD><INPUT name=login class=maxiinput maxlength=25> 
<TR class=row2><TD>E-mail:<FONT color=#ff0000>*</FONT></TD><TD><INPUT name=email class=maxiinput maxlength=40></TD></TR>
<TR class=row1><TD>�����:</TD><TD><INPUT name=gorod class=maxiinput maxlength=60></TD></TR>
<TR class=row1><TD>URL:</TD><TD><INPUT name=url class=maxiinput maxlength=40 value='http://'></TD></TR>
<TR class=row2><TD>�������:</TD><TD><INPUT name=phone class=maininput maxlength=20>&nbsp;
ICQ: <INPUT name=icq class=maininput maxlength=15></TD></TR>
<TR class=row2><TD>�����������:</TD><TD><INPUT name=company class=maxiinput maxlength=50></TD></TR>
<TR class=row1><TD>������� � ����:</TD><TD><TEXTAREA name=about class=maxiinput></TEXTAREA></TD>";

if ($antispam==TRUE and !isset($wrbname)) {print"<tr class=row1><td>�������� ���</td><TD>"; nospam();} // �������� !

print"<TR class=row2><TD colSpan=2><INPUT type=checkbox name=rules>� <B><A href='tools.php?event=about'>���������</A></B> ����������</TD></TR></TR>
<TR class=row1 height=36><TD colspan=2><center><INPUT type=submit value=������������������></TD></TR></TBODY></TABLE>
</FORM>
* ������ ��� ����� ������ �� �����.";
}



if ($_GET['event'] =="reguser") { //����������� - ��� 2 ���������� ������

// ������� ������ �� �������-�������������
if (isset($_COOKIE['wrbcookies'])) exit("<B>��� ���������� ����� �� �������� �������, � ����� ���� ����������������!</B></font>");

// ��������� IP-���������� �� ������� � ����. ���� ���� - ��������!
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<noindex><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 10000);</script><center><br><br><B>������������ ������������ ��� ������ IP: $ip<br> ����������� ���������������� � ��������� ���������� �� ��������� �������:<br><br> <font color=red><B>$idt[1].</B></font><br><br>��� ��������� ������������� ����������,<br> � ��� ��������� ���������� � ���������������� ������������� ���������!</B></noindex>");
} while($i > "1");} unset($lines);}

if (isset($_POST['login'])) $login=replacer($_POST['login']); else $login="";
$email=replacer($_POST['email']); $email=strtolower($email);
$gorod=replacer($_POST['gorod']);
$url=replacer($_POST['url']);
$icq=replacer($_POST['icq']);
$phone=replacer($_POST['phone']);
$company=replacer($_POST['company']);
$about=replacer($_POST['about']);
if (!isset($_POST['rules'])) exit("$back. ��� ���������� <B>����������� � ���������.</B>");
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
$status="ok";

$login=str_replace("|","I",$login);
$password=str_replace("|","I",$password);
$email=str_replace("|","I",$email);
$gorod=str_replace("|","I",$gorod);
$url=str_replace("|","I",$url);
$icq=str_replace("|","I",$icq);
$phone=str_replace("|","I",$phone);
$company=str_replace("|","I",$company);
$about=str_replace("|","I",$about);

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}

if (preg_match("/[^(\\w)|(\\x7F-\\xFF)|(\\-)]/",$login)) exit("$back ���� ��� �������� ����������� �������. ��������� ������� � ���������� �����, ����� � �������������!!.");
if ($login=="" or strlen($login)>$maxname) exit("$back ���� ��� ������, ��� ��������� $maxname ��������!</B></center>");
if (!eregi("^([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)$",$email) and strlen($email)<6 and $email != "") exit("$back � ������� ���������� E-mail �����!</B></center>");

// ���������� ����� ������ �����
$len=8; // ���������� �������� � ����� ������
$base='ABCDEFGHKLMNPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
$max=strlen($base)-1; $password=''; mt_srand((double)microtime()*1000000);
while (strlen($password)<$len) $password.=$base{mt_rand(0,$max)};

// ���������� ��������� ��� ���������
$z=1; do {$key=mt_rand(100000,999999); if (strlen($key)==6) {$z++;} } while ($z<1);
if ($useactkey!="1") {$key="";}

$text="$login|$password|$email|$url|$icq|$phone|$company|$about|$ip|$date|no|$gorod|$key|||";

// ��������, ����� ����� ���� ��� ����?
$loginsm=strtolower($login);
$lines=file("$datadir/usersdat.php"); $i = count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
$rdt[0]=strtolower($rdt[0]);
if ($rdt[0]===$loginsm) {$bad="1"; $er="�������";}
if ($rdt[2]===$email) {$bad="1"; $er="�������";}
} while($i > "1");
if (isset($bad)) {exit("$back. �������� � ����� <B>$er ��� ���������������</B>!</center>");}

$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

$burl=str_replace("index.php", "tools.php", $boardurl);

if ($sendmail==TRUE) { // ����� ����� ������ �����������

$headers=null; // ��������� ��� �������� �����
$headers.="From: ������������� <".$adminemail.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// �������� ��� ���������� � ���� ������
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body><center><h4>����� ���������� \"<B><a href='$boardurl'>$brdname</a></B>\"</h4>
<table border=1 cellpadding=6 cellspacing=0 width=550 bordercolor='#DBDBDB'>
<tr><td colspan=2 align=center bgcolor='#E4E4E4'><B>��������������� ������</B></td></tr>
<tr bgcolor='#F2F2F2'><td width=130>���� ���:</td><td width=420><B>$login</B></td></tr>
<tr bgcolor='#F8F8F8'><td>������:</td><td><B>$password</B></td></tr>
<tr bgcolor='#F2F2F2'><td>E-mail:</td><td><B>$email</B></td></tr>
<tr bgcolor='#F2F2F2'><td>���� ���������:</td><td><B>$key</B></td></tr>
<tr bgcolor='#F8F8F8'><td>�����:</td><td>$gorod &nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>URL:</td><td>$url &nbsp;</td></tr>
<tr bgcolor='#F8F8F8'><td>ICQ:</td><td>$icq &nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>�����������:</td><td>$company &nbsp;</td></tr>
<tr bgcolor='#F8F8F8'><td>�������:</td><td>$phone &nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>� ����:</td><td>$about &nbsp;</td></tr>
<tr bgcolor='#F8F8F8'><td>���� �����������:</td><td><small>$time</small> - $date �.&nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>��� IP-�����:</td><td>$ip &nbsp;</td></tr></table><BR>";

if ($useactkey=="1") {$allmsg.="<center>��� ���������� <B><font color=red>����������� ����������� �� �����,<br> ��� ����� <a href='$burl?event=reg3&email=$email&key=$key'>������� �� ���� ������</a></font></B><BR><BR>";
} else { $allmsg.="<center>�� <B><font color=navy>������� ����������������.<br><br>";}

$allmsg.="<a href='$boardurl'>������� �� ����� ����������</a><BR><BR><BR>
* ��� ��������� ������������� � ���������� ������� � ����� ����������. �������� �� ���� �� �����.</body></html>";

// ���������� ������ ������� �� �������� ;-)
mail("$email", "=?windows-1251?B?" . base64_encode("$brdname (������������� �����������)") . "?=", $allmsg, $headers);
if ($sendmailadmin!="0") {mail("$adminemail", "����� ������������ ����� ($brdname)", $allmsg, $headers);}
} // if ($sendmail!=0)

if ($useactkey==FALSE) { $tektime=time(); $wrbcookies="$login|$password|$tektime|0|"; setcookie("wrbcookies", $wrbcookies, time()+1728000);
print"<html><head><link rel='stylesheet' href='$brdskin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
<B>$login, �� ������� ����������������</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� �������� �����.<BR><BR>
<B><a href='index.php'>������� �����, ���� �� ������ ������ �����</a></B></td></tr></table></td></tr></table></center></body></html>"; exit;}

print"<html><head><link rel='stylesheet' href='$brdskin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"tools.php?event=reg3\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
<B>$login, �� ��������� ���� ����� ��� ������ ��� �������������.
��� ���� ����� ������������������ - ������� ��� �� ��������, ���� ��������� �� ������ - ��������� � ������</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� �������� ������������� �����������.<BR><BR>
<B><a href='tools.php?event=reg3'>������� �����, ���� �� ������ ������ �����</a></B></td></tr></table></td></tr></table></center></body></html>";
exit;}









// ����������� ��� 3 - ���� ����� ���� ������������� �� ������
if ($_GET['event']=="reg3") {

if (isset($_GET['email']) and isset($_GET['key'])) {$key=replacer($_GET['key']); $email=replacer($_GET['email']);} else {

include "$topurl"; addtop($brdskin); // ���������� �����

print"<center><span class=maintitle>������������� �����������</span><br>
<br><form action='tools.php' method=GET>
<input type=hidden name=event value='reg3'>
<table cellpadding=3 cellspacing=1 width=200 class=bakfon><tr>
<th colspan=2 height=25 valign=middle>���� ������ � �������������� �����</th>
</tr><tr class=row1><td><span class=gen>����� e-mail:</span><br><span class=gensmall></span></td><td class=row2><input type=text class=post style='width: 200px' name=email size=25 maxlength=50></td>
</tr><tr class=row2><td><span class=gen>������������� ����:</span><br><span class=gensmall></span></td><td class=row2><input type=text class=post style='width: 200px' name=key size=25 maxlength=6></td></tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit value='����������� �����������' class=pgbutt></td>
</tr></table></form>* ��� ��������� �������� <B>���������� ������� �� ������ � ������!</B><br>
** ���� ��� ������ ��� �� �����, ��������� ��� �����������.<br>
���� �� �� �������� ������, ���������� � �������������� ����� ����� �������� �����, <br>
������� ���� ����� � ����� � ��������� ������������ ���� ������� ������.<br><br><br>"; if (is_file("$brdskin/bottom.html")) include "$brdskin/bottom.html"; exit; }

// ������ �� ������ �� ����� � ������
if (strlen($key)<6 or strlen($key)>6 or !ctype_digit($key)) exit("$back. �� �������� ��� ����� �����. ���� ����� ��������� ������ 6 ����.");
if (strlen($email)>50) exit("������ ��� ����� ������");

// ���� ����� � ����� ������� � ������. ���� ���� - ������ ������ �� ������ ����.
$fnomer=null; $email=strtolower($email); unset($fnomer); unset($ok);
$lines=file("$datadir/usersdat.php"); $ui=count($lines); $i=$ui;
do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[2]=strtolower($rdt[2]);
if ($rdt[2]===$email and $rdt[12]===$key) {$name=$rdt[0]; $pass=$rdt[1]; $fnomer=$i;}
if ($rdt[2]===$email and $rdt[12]==="") $ok="1";
} while($i > 1);
if (isset($fnomer)) {
// ���������� ������ ����� � ��
$i=$ui; $dt=explode("|", $lines[$fnomer]);
$txtdat="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|ok|$dt[11]|$dt[12]||||";
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i<=(sizeof($lines)-1);$i++) {if ($i==$fnomer) {fputs($fp,"$txtdat\r\n");} else {fputs($fp,$lines[$i]);}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
// ������������� ����
$tektime=time(); $wrbcookies="$name|$pass|$tektime|0|";
setcookie("wrbcookies", $wrbcookies, time()+1728000);
}
if (!isset($fnomer) and !isset($ok)) exit("$back. �� �������� � ���� �������������� ����� ��� ������.</center>");
if (isset($ok)) $add="���� ������ ��� ������������"; else $add="$name, �� ������� ���������������� � ������������";

print"<html><head><link rel='stylesheet' href='$brdskin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
�������, <B>$add</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� �������� �����.<BR><BR>
<B><a href='index.php'>������� �����, ���� �� ������ ������ �����</a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }






if ($_GET['event'] =="reregistr") { // ��������������� - ��� 2 ���������� ������
$login=replacer($_POST['login']); // ����� �����
$oldpass=replacer($_POST['oldpass']); // ������ ������
$password=replacer($_POST['password']); // ����� ������
$email=replacer($_POST['email']); $email=strtolower($email);
$gorod=replacer($_POST['gorod']);
$url=replacer($_POST['url']);
$icq=replacer($_POST['icq']);
$phone=replacer($_POST['phone']);
$company=replacer($_POST['company']);
$about=replacer($_POST['about']);
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����

$login=str_replace("|","I",$login);
$password=str_replace("|","I",$password);
$email=str_replace("|","I",$email);
$url=str_replace("|","I",$url);
$icq=str_replace("|","I",$icq);
$phone=str_replace("|","I",$phone);
$company=str_replace("|","I",$company);
$about=str_replace("|","I",$about);
$gorod=str_replace("|","I",$gorod);

if ($login==="" || strlen($login)>$maxname) exit("$back ���� ��� ������, ��� ��������� $maxname ��������!</B></center>");
if ($password==="" || strlen($password)>15) exit("$back �� �� ����� ������!</B></center>");

// �������� ������/������� ������
$lines=file("$datadir/usersdat.php"); $i=count($lines);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (strtolower($login)===strtolower($rdt[0]) & $oldpass===$rdt[1]) {$ok="$i";} // ���� ����� �����/������
   else { if ($email===$rdt[2]) $bademail="1"; } // ����� � ������ ��� ���� ����� �����?
} while($i > "1");
if (!isset($ok)) {setcookie("wrbcookies", "", time());
exit("$back ��� ����� ����� /������ / ����� �� ��������� �� � ����� �� ��. <BR><BR>
����� ������������ ������ <font color=red><B>���������</B></font><BR><BR>
<font color=red><B>������ ������� ��� ������� ������ - ���������� � ��������������!</B></font>");}
if (isset($bademail)) {exit("$back. �������� � ������� <B>$email ��� ���������������</B> �� �����! <BR>��������, ��� ����� ������������� � �� - ���������� � ��������������!</center>");}

$udt=explode("|",$lines[$ok]); $status=$udt[10]; $dayx=$udt[12];

$text="$login|$password|$email|$url|$icq|$phone|$company|$about|$ip|$date|$status|$gorod|$dayx|||";

$file=file("$datadir/usersdat.php");
$fp=fopen("$datadir/usersdat.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i< sizeof($file);$i++) { if ($ok!=$i) fputs($fp,$file[$i]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ������������� ����
$tektime=time(); $wrbcookies="$login|$password|$tektime|0|";
setcookie("wrbcookies", $wrbcookies, time()+1728000);

if ($sendmail==TRUE) { // �������� ����� ��������������� ������

$headers=null; // ��������� ��� �������� �����
$headers.="From: ������������� <".$adminemail.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// �������� ��� ���������� � ���� ������
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body><center><h4>����� ���������� \"<B><a href='$boardurl'>$brdname</a></B>\"</h4>
<table border=1 cellpadding=6 cellspacing=0 width=550 bordercolor='#DBDBDB'>
<tr><td colspan=2 align=center bgcolor='#E4E4E4'><B>��������������� ������</B></td></tr>
<tr bgcolor='#F2F2F2'><td width=130>���� ���:</td><td width=420><B>$login</B></td></tr>
<tr bgcolor='#F8F8F8'><td>������:</td><td><B>$password</B></td></tr>
<tr bgcolor='#F2F2F2'><td>E-mail:</td><td><B>$email</B></td></tr>
<tr bgcolor='#F8F8F8'><td>�����:</td><td>$gorod &nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>URL:</td><td>$url &nbsp;</td></tr>
<tr bgcolor='#F8F8F8'><td>ICQ:</td><td>$icq &nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>�����������:</td><td>$company &nbsp;</td></tr>
<tr bgcolor='#F8F8F8'><td>�������:</td><td>$phone &nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>� ����:</td><td>$about &nbsp;</td></tr>
<tr bgcolor='#F8F8F8'><td>���� �����������:</td><td><small>$time</small> - $date �.&nbsp;</td></tr>
<tr bgcolor='#F2F2F2'><td>��� IP-�����:</td><td>$ip &nbsp;</td></tr>
</table><BR><center>�� <B><font color=navy>������� ������������������</font></B><BR><BR>
<a href='$boardurl'>������� �� ����� ����������</a><BR><BR><BR>
* ��� ��������� ������������� � ���������� ������� � ����� ����������. �������� �� ���� �� �����.</body></html>";

mail("$email", "������ ��������������� ($brdname)", $allmsg, $headers); // ����� ������
}

print "<html><body><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#888888 align=center valign=center width=60%><tr><td><center>
<B>$login</B>, ���� ������ ������� ��������. <BR>�� ��� ����������� ����� ������� ������ ���������������. <BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� ��������.<BR>
<B><a href='index.php'>������� �����, ���� �� ������ ������ �����</a></B></td></tr></table></td></tr></table></center></body></html>";
exit;
}






if ($_GET['event']=="who") {  // �������� ���� ���������� �����

ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,nofollow">',$topurl);
print"$topurl"; addtop($brdskin); // ���������� �����

// ���� ��������� - �� �������
if (!isset($wrbname)) exit("<br><br><br><br><table class=bakfon align=center width=700><tr><th class=thHead colspan=4 height=25>������ ���������</th></tr><tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>��� ��������� ������ ������������� ���������� ������������������.</B></center><BR><BR>$back<BR><BR></td></table><br>");

$uq="25"; // �� ������� ������� �������� ������ ����������

$t1="row1"; $lines=file("$datadir/usersdat.php"); $maxi=count($lines)-1;

echo'<table width=98% cellpadding=2 cellspacing=1 align=center class=bakfon><tr class=toptable> 
<th height=25 width=20>�</th>
<th><small>���</small></th>
<th><small>�� �� �-����</small></th>
<th><small>���������������</small></th>
<th><small>������</small></th>
<th><small>����</small></th>
<th><small>�������</small></th>
<th><small>�����������</small></th>
<th><small>�����</small></th>
</tr>';

if ($maxi<"1") {print"<TR><TD class=$t1 colspan=8 align=center>���������� �� ����������������</TD></TR>";
} else {

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) {$page=1;} else {$page=$_GET['page']; if (!ctype_digit($page)) {$page=1;} if ($page<1) $page=1;}

$maxpage=ceil(($maxi+1)/$uq); if ($page>$maxpage) $page=$maxpage;

$fm=$uq*($page-1); if ($fm>$maxi) {$fm=$maxi-$uq;}
$lm=$fm+$uq; if ($lm>$maxi) {$lm=$maxi+1;}

do {$dt=explode("|", $lines[$fm]);

$fm++; $num=$fm-1;

if (isset($dt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������

$codename=urlencode($dt[0]); // �������� ��� � ����������, ��� ��������� ���������� �������� ����� ����� GET-������.
if (isset($wrbname)) {$wbn="<a href=\"tools.php?event=profile&pname=$codename&page=$page\">$dt[0]</a>";
$mls="<A href='#' onclick=\"window.open('tools.php?event=mailto&email=$dt[3]&name=$dt[0]','email','width=520,height=300,left=170,top=100')\">��������_������</A>";} else {$wbn="$dt[0]"; $mls="�������������";}

if (strlen($dt[13])=="6" and ctype_digit($dt[13])) {$dt[13]="<B><font color=red>�������� ���������</font></B>";}
if ($dt[6]=="�������") {$add="polm.gif";} else {$add="polg.gif";}

if ($dt[10]!="vip") {$dt[10]="-";}
print"<tr height=25 class=$t1>
<td>$num</td>
<td><b>$wbn</b></td>
<td align=center>$mls</td>
<td align=center>$dt[9]</td>
<td align=center>$dt[10]</td>
<td align=center><a href='$dt[3]'>$dt[3]</a></td>
<td><small>$dt[5]</small></td>
<td>$dt[6]</td>
<td><small>$dt[11]</small></td>
</tr>";
if ($t1=="row1") $t1="row2"; else $t1="row1";

} // ���� ������� ����������

} while($fm < $lm);

} // ����� ���� ���� userdat.php ����

// ������� ������ �������
$pageinfo="<TD><table width=100%><tr><td align=right colspan=3><span class=nav>��������:&nbsp; ";
if ($page>=4 and $maxpage>5) $pageinfo.="<a href=tools.php?event=who&page=1>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page<=2) {$f1=5; $f2=1;} if ($page>=$maxpage-1) {$f1=$maxpage; $f2=$page-3;} if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; else $pageinfo.="<a href=tools.php?event=who&page=$i>$i</a> &nbsp;";}
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=tools.php?event=who&page=$maxpage>$maxpage</a>";

print"</table><BR> $pageinfo </b></span></td>
</TD><TD align=right>����� ���������������� ���������� - <B>$maxi</B></TD></TR></TABLE><BR>";}



if ($_GET['event'] =="profile")  { 
if (!isset($_GET['pname'])) {exit("������� ������.");}

ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,nofollow">',$topurl);
print"$topurl"; addtop($brdskin); // ���������� �����

$pname=urldecode($_GET['pname']); // ����������� ��� ������������, ��������� �� GET-�������.
$lines=file("$datadir/usersdat.php");
$i = count($lines); $use="0";

do {$i--; $rdt=explode("|", $lines[$i]);

if (isset($rdt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������

if (strlen($rdt[13])=="6" and ctype_digit($rdt[13])) {$rdt[13]="<B><font color=red>�������� ���������</font></B>";}

if ($pname===$rdt[0])  {

$user2="<br>* VIP-������ ��������� �������� ���������� ���������� ����� ����������. <br>����� VIP-���������� ������ ����������� �� ������ �������� <br>�������, � ������� ��� ���������. ��� ������ �������,<br> ��������� ������� � �������������� ����� ����� �������� �����.";
if ($rdt[10]=="ok") {$user1="<font color=#AAAAAA>�������</font>";
} else {
if ($rdt[12]>0) {$tek=mktime(); $vipdays=round(($rdt[12]-$tek)/86400); $vipdays.=" ��. ��������";} else {$vipdays="������";}
$user1="<font color=red><B>VIP-������</B></font>* ($vipdays)";
$user2="* ��� ����������� ���� ���������� ������ ����������� ������ �������� � ���������� ������ ������.";
if ($vipdays<0) {$user1="<font color=#AAAAAA>�������</font> (���� ����)"; $user2="";}}

if (isset($wrbname) & isset($wrbpass))  {$wrbname=replacer($wrbname); $wrbpass=replacer($wrbpass);


if ($wrbname===$rdt[0] & $wrbpass===$rdt[1])  {

print "<BR><center><TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action='tools.php?event=reregistr' method=post>
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
$user2
<input type=hidden name=login value='$rdt[0]'>
<input type=hidden name=oldpass value='$rdt[1]'></FORM>"; $use="1"; }


if ($use!="1") {
print "<BR><center><TABLE class=bakfon width=500 cellPadding=6 cellSpacing=1>
<FORM action='tools.php?event=reguser' method=post>
<TR class=toptable><TD align=middle colSpan=2><B>��������������� ����������</B></TD></TR>
<TR class=row1><TD width=30%>���:</TD><TD>$rdt[0]</td></tr>
<TR class=row2><TD>�����:</TD><TD>$rdt[11]</TD></TR>
<TR class=row1><TD>�����:</TD><TD><A href='#' onclick=\"window.open('tools.php?event=mailto&email=$rdt[3]&name=$rdt[0]','email','width=520,height=300,left=170,top=100')\">��������_������</A></td></tr>
<TR class=row2><TD>�������� ���������:</TD><td><a href='$rdt[3]'>$rdt[3]</a></td></tr>
<TR class=row1><TD>ICQ:</TD><td>$rdt[4]</td></tr>
<TR class=row2><TD>�������:</TD><TD>$rdt[5]</td></tr>
<TR class=row1><TD>�����������:</TD><TD>$rdt[6]</td></tr>
<TR class=row2><TD>�������������:</TD><TD>$rdt[7]</td></tr>
</table><BR><BR>"; $use="1";}
}
}
} // if
} while($i > "1");

if (!isset($wrbname)) {exit("<BR><BR><font size=+1><center>������ ������������������ ��������� ����� ����� ������������� ������ �������!");}

// �� ������ ����� ���, ��������, ��� ����� ������ ��� ���� ��
if ($use!="1") {
echo'<BR><BR><BR><BR><center><font size=-1><B>��������� ����������!</B><BR><BR> 
��������, �� �������� � ����� - <B>������� �� ����� �� ���������������.</B><BR><BR>
������ �����, <B>��� ������ �������������</B>.<BR><BR>
<B>������� �� �������</B> �������� ����� ����� �� <B><a href="index.php">���� ������</a></B>
<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>'; }
} // $event=="profile"






if ($_GET['event']=="find") { // ����� ����������

setlocale(LC_ALL,'ru_RU.CP1251'); // ! ��������� ������ �������, ���������� � ���������� � � �������� �������


include "$topurl"; addtop($brdskin); // ���������� �����

$minfindme="2"; // ����������� ���-�� ��������, � ��������� �����
$time=explode(' ', microtime()); $start_time=$time[1]+$time[0]; // ��������� ��������� ����� ������� ������

if (!isset($withregistr)) {$withregistr="0";}

$ftype=$_POST['ftype'];
if (isset($_POST['withregistr'])) {$withregistr="1";} else {$withregistr="0";}
$gdefinder=$_POST['gdefinder'];

// ��������� $findme �� �����
$findme=$_POST['findme'];
$findme=stripslashes($findme);
$findmeword=explode(" ",$findme);
$wordsitogo=count($findmeword);
$findme=trim($findme); // �������� ���������� ������� 
if ($findme == "" || strlen($findme) < $minfindme) {exit("$back ��� ������ ����, ��� ����� $minfindme ��������!</B>");}
// ��������� ���� � ������ ������� � ���������� ����� ������ � �����������

$lines = file("$datadir/$datafile"); $i=count($lines);
// ������ ���� - ������� ���-�� ���
$number="0";
do {$i--; $dt=explode("|", $lines[$i]);
$forumsid[$i]=$dt[0];
} while($i > "0");


$ii=count($forumsid);
// ������ ���� - �������� ���������������� ��������� � ����
do {$ii--;
$fid=$forumsid[$ii];

if (is_file("$datadir/$fid.dat")) {$file=file("$datadir/$fid.dat");}

 if ((is_file("$datadir/$fid.dat")) && (sizeof($file)>"0"))
 {
 $iii=count($file); // $iii-���-�� ��������� � ���� $fid.dat";
 $lines = file("$datadir/$fid.dat");

do {$iii--;
    $dt = explode("|", $lines[$iii]);

if ($gdefinder=="0") {$msgmass=array($dt[2],$dt[3],$dt[5]); $gi="3"; $add="�� <B>�����, �����, ���������</B> ";}
if ($gdefinder=="1") {$msgmass=array($dt[5]); $gi="1"; $add="� <B>�����</B> ";}
if ($gdefinder=="2") {$msgmass=array($dt[3],$dt[5]); $gi="2"; $add="�� <B>����� � ���������</B> ";}
if ($gdefinder=="3") {$msgmass=array($dt[2]); $gi="1"; $add="� <B>�����</B> ";}
if ($gdefinder=="4") {$msgmass=array($dt[3]); $gi="1"; $add="� <B>���������</B> ";}

// ���� �� ������ ������ (0,1,2,3,4)
do {$gi--;

$msg=$dt[5];
$msdat=$msgmass[$gi];

$stroka="0"; $wi=$wordsitogo;
// ���� �� ������� ����� ������� !
do {$wi--;



// ���� ������� ������
if ($withregistr!="1") // ������������������� ����� - c����� "i" ����� ������������ ������������ ������� - /
   {
    if ($ftype=="2") 
        {
        if (stristr($msdat,$findme))  // ����� �� "���� ����� �������" ��� ����� ��������
            { 
             $stroka++;
             $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
            }
        }
     else {
           $str1=strtolower($msdat);  
           $str2=strtolower($findmeword[$wi]); 
           if ($str2!="" and strlen($str2) >= $minfindme)
              {
               if (stristr($str1,$str2)) // ����� ��� ����� �������� ��� ������ ������ ��������
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }
        }

else  // if ($withregistr!="1")
   {
    if ($ftype=="2")
       {
        if (strstr($msdat,$findme))  // ����� �� "���� ����� �������" C ����� ��������
           {
            $stroka++;
            $msg=eregi_replace($findme," <b><u>$findme</u></b> ",$msg);
           }
       }
     else {
           if ($msdat!="" and strlen($findmeword[$wi]) >= $minfindme)
              {
               if (strstr($msdat,$findmeword[$wi]))  // ����� � ������ �������� ��� ������ ������ ��������
                  {
                   $stroka++;
                   $msg=eregi_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }

   } // if ($withregistr!="1")



} while($wi > "0");  // ����� ����� �� ������� ����� �������



// �������������� �������������� ���������, � ���� ��������� ������������� �������� - ������� ���
if ($ftype=="0") { if ($stroka==$wordsitogo) {$printflag="1";} }
if ($ftype=="1") { if ($stroka>"0") {$printflag="1";} }
if ($ftype=="2") { if ($stroka==$wordsitogo) {$printflag="1";} }



if (!isset($printflag)) {$printflag="0";}
    if ($printflag=="1")
       {$msg=str_replace("<br>", " &nbsp;&nbsp;", $msg); // �������� � ��������� <br> �� ���� ��������


if (strlen($msg)>150)
{
 $ma=strpos($msg,"<b>"); if ($ma > 50) {$ma=$ma-50;} else {$ma=0;}
 $mb=strrpos($msg,">b/<"); if (($mb+50) > strlen($msg)) {$mb=strlen($msg);} else {$mb=$mb+50;}
 $msgtowrite="..."; $msgtowrite.=substr($msg,$ma,$mb); $msgtowrite.="...";
}
else {$msgtowrite=$msg;}

if (!isset($m)) {print "<small><BR>�� ������� '<U><B>$findme</B></U>' � ���$add �������: <HR size=+2 width=99% color=navy><table width=100%><TR class=small bgColor=#cccccc><TD><B>�</B></TD><TD><B>���</B></TD><TD width=35%><B>���������</B></TD><TD><B>�����</B></TD><TD width=*><B>����� ����������</B></TD></TR>"; $m="1"; }
$number++;
$msgnumber=$iii;
print "<TR height=25 class=small bgColor=#FFFFFF onmouseover=trtover(this) onmouseout=trtout(this)>
<TD align=center><B>$number</B></TD>
<TD><FONT color=#ff3333><B>$dt[4]</B></FONT></TD>
<TD onmouseover=tover(this) onclick=\"LmUp('index.php?id=$dt[10]')\" onmouseout=tout(this)><A class=listlink href='index.php?id=$dt[10]'>$dt[3]</A></TD>
<TD onmouseover=tover(this) onmouseout=tout(this) onclick=\"LmUp('tools.php?event=profile&pname=$dt[2]')\"><A class=listlink href='tools.php?event=profile&pname=$dt[2]'>$dt[2]</A></TD>
<TD onclick=\"LmUp('index.php?id=$dt[10]')\">$msgtowrite</TD></TR>";
$printflag="0";
       }


} while($gi > "0"); // ����� ����� �� ����� ������

} while($iii >= "1");

 } // if ((is_file("$fid.dat")) && (sizeof("$fid.dat")>0))


} while($ii > "0");


if (!isset($m)) {echo'<table width=80% align=center><TR><TD>�� ������ ������� ������ �� �������.</TD></TR></table>';}

$time=explode(' ',microtime());
$seconds=($time[1]+$time[0]-$start_time);
echo "</table><HR size=+2 width=99% color=navy><BR><p align=center><small>".str_replace("%1", sprintf("%01.3f", $seconds), "����� ������: <b>%1</b> ������.")."</small></p>";
}
} // if isset($event)






if (!isset($_GET['event']) and !isset($_GET['id'])) {

include "$topurl"; addtop($brdskin);

print"<BR><form action='tools.php?event=find' method=POST>

<table class=forumline align=center width=900 cellpadding=4 cellspacing=0 border=1>
<tr><th class=thHead colspan=4 height=25>�����</th></tr><tr>
<td>������: <input type='text' style='width: 250px' class=maininput name=findme size=30></TD>
<TD>���: <select style='FONT-SIZE: 12px; WIDTH: 120px' name=ftype>
<option value='0'>&quot�&quot
<option value='1' selected>&quot���&quot
<option value='2'>��� ����� �������
</select></td>
<td><INPUT type=checkbox name=withregistr><B>� ������ ��������</B></TD>
<TD>��� ������: <select style='FONT-SIZE: 12px; WIDTH: 140px' name=gdefinder>
<option value='1' selected>������ � ������
<option value='4'>������ � ���������
<option value='3'>����� ������
<option value='2'>� ������ � ���������
<option value='0'>�����
</select></td>
</tr><tr>
<td colspan=4 width=\"100%\">
���� ��������:<br><UL>
<LI><B>&quot�&quot</B> - ������ �������������� ��� �����;</LI><br>
<LI><B>&quot���&quot</B> - ���� ������ ���� �� ����;</LI><br>
<LI><B>&quot��� ����� �������&quot</B> - � ������� ��������� ����� ����� �� 100% ��������������� ������ �������;</LI><BR><BR>
<LI><B>&quot� ������ ��������&quot</B> - ����� ������ � ������ ��������� ���� ��������;</LI><BR><BR>
��� ������: <BR>
<LI><B>&quot������ � ������&quot</B> - ����� ������ ������ � ������ ���������;</LI><br>
<LI><B>&quot������ � ���������&quot</B> - ����� ������ � ��������� ����������;</LI><br>
<LI><B>&quot����� ������&quot</B> - ����� �� ����� ��������� ����������;</LI><br>
<LI><B>&quot� ������ � ���������&quot</B> - ����� �� ����� ��������� ����������, � ��������� ����������;</LI><br>
<LI><B>&quot�����&quot</B> - ����� ������ � ����� &quot���&quot, &quot���������&quot, &quot�����&quot ������� ����������;</LI><br>
</UL>������ ���� ��� ������, ������� ���������� � ��������� ���� ������. ��������, ��� ������� &quot������&quot ����� ������� ����� &quot������&quot, &quot�������&quot, &quot��������&quot � ������ ������.
</td>

</tr><tr><td colspan=4 align=center height=28><input type=submit class=longok value='�����'></td></form>
</tr></table><BR><BR>";
}




// ������� �������� �� ������ � �������� ��������� ������ 
if (isset($_GET['event'])) { if ($_GET['event']=="add")  {

sleep(1); // ������ ������ �� �����. �������� ������� �� ����� - � ����� �� ������� ����� - ����� �������� ����� � �� ��������� ������

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE and !isset($_COOKIE['wrbcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}

if (!isset($_POST['name'])) exit("$shapka $back �� �� ����� ���!"); else $name=$_POST['name'];
if (!isset($_POST['email'])) exit("$shapka $back �� �� ����� �����!"); else $email=$_POST['email'];
if (!isset($_POST['tema'])) exit("$shapka $back �� �� ����� ����!"); else $tema=$_POST['tema'];
if (!isset($_POST['msg'])) exit("$shapka $back �� �� ����� ���������!"); else $msg=$_POST['msg'];
if ($name=="" || strlen($name)>$maxname) exit("$shapka $back �� �� ����� ���, ��� ������ ������� ������� ���!</B></center>");
if ($msg=="" || strlen($msg)>$maxmsg) exit("$shapka $back ���� ��������� ��� ������ ��� ��������� $maxmsg ��������.</B></center>");
if(!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $_POST['email']) or $_POST['email']=="") exit("$shapka $back � ������� ���������� E-mail �����!</B></center>");

// ������ �� ������
$name=str_replace("|","&#124;",$name);
$tema=str_replace("|","&#124;",$tema);
$msg=str_replace("|","&#124;",$msg);
$text="$name|$tema|$email|$msg|";
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $tema=$exd[1]; $email=$exd[2]; $msg=$exd[3];

// ��������� ��� �������� �����
$headers=null;
$headers.="From: ".$name." <".$email.">\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// �������� ��� ���������� � ���� ������
$allmsg="<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<style>BODY {FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 13px;} TD {FONT-SIZE: 12px;}</style></head>
<body><center><h4>��������� �� ���������� ����� \"<B><a href='$boardurl'>$boardurl</a></B>\"</h4>
<table border=1 cellpadding=6 cellspacing=0 width=550 bordercolor='#DBDBDB'>
<tr><td colspan=2 align=center bgcolor='#E4E4E4'><B>����������</B></td></tr>
<tr bgcolor='#F2F2F2'><td width=117>���:</td><td width=433><B>$name</B></td></tr>
<tr bgcolor='#F8F8F8'><td>�-����:</td><td><B>$email</B></td></tr>
<tr bgcolor='#F8F8F8'><td>���� ��������:</td><td><small>$time</small> - $date �.</td></tr>
<tr bgcolor='#F8F8F8'><td>����:</td><td><B>$tema</B></td></tr>
<tr bgcolor='#F2F2F2'><td>�����:</td><td>$msg</td></tr>
</table><center><BR>���� ��������� <B><font color=navy>������� ����������</font></B><BR><BR>
<a href='$boardurl'>��������� <B>�����</B></a>";
$printmsg="$allmsg </body></html>";
$allmsg.="<BR><BR><BR>* ��� ��������� ������������� � ���������� ������� � ����� �������� �����. �������� �� ���� �� �����.</body></html>";

// ���������� ������ ������� �� �������� ;-)
mail("$adminemail", "�������� �����. ��������� �� $name", $allmsg, $headers);

// ����� ������������ "�������" � ��������� �������� ����� JavaScript
print "<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 3000);</script>$printmsg"; exit;
}

}



if (isset($_GET['event'])) { if ($_GET['event']=="addrem")  {


print "<HTML><head><META content='text/html; charset=windows-1251' http-equiv=Content-Type></head>
<BODY text=#000000 leftMargin=0 topMargin=0 rightMargin=0 bottomMargin=0 marginheight=0 marginwidth=0><center>
<table border=0 width=510 cellpadding=1 cellspacing=0 bgcolor='#79BBEF'><tr><td>
<table border=0 width=100% cellpadding=1 cellspacing=0 bgcolor='#79BBEF'><tr><td>";

print "
<center><b><font size=+1 color='FFFFFF'>��������� ��������� ��������������</font></b></center>
</td></tr><tr><td colspan=2 width=100% bgcolor=#FFFFFF><center>
<form action=tools.php?event=add method=post name=REPLIER>
<table border=0 cellpadding=0 cellspacing=0 width=500>
<tr><td>&nbsp;</TD></TR>
<tr><td><B>���</B> <input type=text $addstyle value='' maxlength=$maxname name=name size=27> &nbsp;&nbsp;&nbsp; <B>��� E-mail</B> <input type=text $addstyle value='' name=email size=27></td></tr>
<tr><td>���� ���������: &nbsp; <input type=text $addstyle value='' maxlength=$maxzag name=tema size=57></td></tr>
<tr><td><B>���������</B></td></tr>
<tr><td><textarea $addstyle cols=79 rows=10 size=500 name=msg></textarea>";

if ($antispam==TRUE and !isset($wrbname)) {print"<tr class=row1><td>��������: "; nospam();} // �������� !

print"<TR><TD colspan=3><br><center><input type=submit $addstyle value='���������'></form></td></tr></table>"; exit;
}
}





if (isset($_GET['id'])) {

$fid=substr($_GET['id'],0,3);

$shapka="<html><head><Title>����� ����������: $brdname</Title><META http-equiv=Content-Type content='text/html; charset=windows-1251'><STYLE type=text/css>BODY {FONT-WEIGHT: normal; FONT-SIZE: 10pt; COLOR: #333333; FONT-FAMILY: Verdana, Arial Cyr, Times New Roman} TD {COLOR: #000000; FONT-SIZE: 11px;}</style></head><body>";

if (is_file("$datadir/$fid.dat")) {

$lines = file("$datadir/$fid.dat"); $itogo=count($lines); $maxi=$itogo-1;

if ($itogo > 0) {$lt=explode("|",$lines[0]); $tdt=explode("[ktname]",$lt[1]); 

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) {$page=1;} else {$page=$_GET['page']; if ($page<1) $page=1;}

print"$shapka<TABLE bgColor=#797979 cellPadding=4 cellSpacing=1 width=98% align=center><TBODY>
<TR bgColor=#FFFFFF height=28><TD colspan=6 align=center>
����� ���������� &quot;<strong>$brdname</strong>&quot;</TD></TR>
<TR bgColor=#FFFFFF height=28><TD colspan=6 align=center>������: &quot;<strong>$tdt[0]</strong>&quot; -> &quot;<strong>$tdt[1]</strong>&quot;</TD></TR>
<TR bgColor=#DDDDDD height=20 align=center>
<TD width=20%><B>���������</B></TD>
<TD width=80%><B>��������� / ����� ����������</B></TD>
</TR>";

// ���� ���������� - ����� ������ ������ � index.php !!!! - �� ���������� ����!
$p=$itogo; $ivip=0;

do {$p--; $dt=explode("|", $lines[$p]);
if ($dt[9]=="vip") {$ivip++;}

// 5-�� ������� ���� �������� ��� ���������� ������������� � ������� ������ 1.4 � 1.5!!!
// ���� �� �������!
if (!isset($dt[22])) $dt[22]="";
if (!isset($dt[23])) $dt[23]="";
if (!isset($dt[24])) $dt[24]="";
if (!isset($dt[25])) $dt[25]="";
if (!isset($dt[26])) $dt[26]="";
if (!isset($dt[27])) $dt[27]="";

$newlines[$p]="$dt[4]|$dt[9]|$dt[11]|$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[10]|$dt[12]|$dt[13]|$dt[14]|$dt[15]|$dt[16]|$dt[17]|$dt[18]|$dt[19]|$dt[20]|$dt[21]|$dt[22]|$dt[23]|$dt[24]|$dt[25]|$dt[26]|$dt[27]|";
} while($p > 0);

usort($newlines,"prcmp");

$p=$itogo;
do {$p--; $dt=explode("|", $newlines[$p]);
   $lines[$p]="$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[0]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[1]|$dt[11]|$dt[12]|$dt[2]|$dt[13]|$dt[14]|$dt[15]|$dt[16]|$dt[17]|$dt[18]|$dt[19]|$dt[20]|$dt[21]|$dt[22]|$dt[23]|$dt[24]|$dt[25]|$dt[26]|$dt[27]|\r\n";
} while($p > 0);
// ����� ����� ����������

if (!ctype_digit($page)) {$lm=$itogo-1; $fm=0; $page=1;} else {
$fm=$qq*($page-1); if ($fm>$maxi) {$fm=$maxi-$qq;}
$lm=$fm+$qq; if ($lm>$maxi) {$lm=$maxi+1;}}

do {$dt=explode("|", $lines[$fm]);
$fm++; $num=$fm;

if ($fm>=0) {
if (stristr($dt[2],"[email]")) {$tdt=explode("[email]", $dt[2]); $dt[2]="$tdt[0]"; $email="$tdt[1]"; $usdat="$dt[2]";} else {$usdat="<A href='tools.php?event=profile&pname=$dt[2]'>$dt[2]</A>"; $email="";}
if ($dt[4]=="�") {$dt[4]="�����������"; $colorsp="#ff3333";} else {$dt[4]="�����"; $colorsp="#1414CD";}
$deldate=date("d.m.Y",$dt[7]);  // ����������� ���� �������� � ������������ ������
$tekdt=mktime();
$deldays=round(($dt[7]-$tekdt)/86400); // ����� ������� ���� ����� ������� ����������
$dt[5]=str_replace("<br><br>", "<br>", $dt[5]);

// �������� ����� ����/���/���� - � ������� ����
$dney="����"; if ($deldays=="0") {$deldays=1;}
if ($deldays>20) {$ddays=substr($deldays,-1);} else {$ddays=$deldays;}
if ($ddays=="1") {$dney="����";}
if ($ddays=="2" or $ddays=="3" or $ddays=="4") {$dney="���";}

$url="index.php?id=$dt[10]";

if ($dt[9]=="vip") {echo'<TR height=28 bgColor=#EEEEEE>';} else  {echo'<TR height=28 bgColor=#FFFFFF>';}

print"<TD valign=top>
���������� � <B>$num</B><BR>
���: <FONT color=$colorsp><B>$dt[4]</B></FONT><BR>
���������: $usdat <BR>
����: <B>$dt[6]</B><BR>
���� ��������: <BR>$deldate - ����� <B>$deldays</B> $dney <BR>";
if ($dt[9]=="vip") {echo'������: <B>V.I.P.</B>';}
print"</td><TD><B><A href='$url' title='$dt[5]'>$dt[3]</A></B><BR><UL><small>$dt[5]</small></UL></TD></TR>";

if (($dt[9]=="vip") and ($ivip==1))  {echo'<TR height=15 bgColor=#FFFFFF><TD colspan=6>&nbsp;</TD></TR>';}
$ivip--;}

} while($fm < $lm);


echo'</TBODY></TABLE><BR><TABLE cellPadding=0 cellSpacing=0 width=98% align=center><TBODY><TR>';

// ������� ������ �������
$maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) {$page=$maxpage;}
echo'<td width=30%>��������:&nbsp; ';
if ($page>=4 and $maxpage>5) print "<a href=tools.php?id=$id>1</a> ... ";
$f1=$page+2; $f2=$page-2;
if ($page<=2) {$f1=5; $f2=1;} if ($page>=$maxpage-1) {$f1=$maxpage; $f2=$page-3;} if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) {if ($page==$i) {print "<B>$i</B> &nbsp;";} else {print "<a href=tools.php?id=$fid&page=$i>$i</a> &nbsp;";}}
if ($page<=$maxpage-3 and $maxpage>5) print "... <a href=tools.php?id=$fid&page=$maxpage>$maxpage</a>";
if ($page=="all") {echo' <B>���</B>';} else {print "&nbsp; <a style=\"width:10px\" href=\"tools.php?id=$fid&page=all\"><B>���</B></a> &nbsp;";}

print "
<TD align=center width=50%><B><a href='index.php' title='��������� �� ������� �������� ����� ����������'>$boardurl</a></B></TD>
<TD align=right width=20%>����� ����������: <B>$i</B>.</TD>
</TD></TR></TBODY></TABLE>";
}
} else {print"$shapka <BR><BR><BR><BR><BR><BR><BR><BR><BR><center>� ��������� ���� ������� ���������� ���.";}

}



if (is_file("$brdskin/bottom.html")) include "$brdskin/bottom.html";
?>

<center><small>Powered by <a href="http://www.wr-script.ru" title="������ ����� ����������" class="copyright">WR-Board</a> &copy; 1.6 Lux<br></small></font></center>
</body></html>
