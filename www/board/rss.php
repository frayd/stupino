<? // WR-board v 1.6.1 LUX // 06.08.10 �. // Miha-ingener@yandex.ru
   // ������ ��� ����������� ��������� ��� � ��������� � ������� RSS-��������

include ("config.php");

// ��������� ������ ��������
$rss="http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
$url=str_replace("rss.php","index.php",$rss);

if (!isset($_GET['whatisthis'])) {

$brdname=strip_tags($brdname);
$brdmaintext=strip_tags($brdmaintext);

// ��������� RSS

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

//18|��������[ktname]������������|�������|as-dom.moy.su|�|������ �������|13.10.2009|1258022009|19|no|199283|1255430009|����������|+7 (918)-829-99-96||||||������������||

// ������ �������� � �� ����� �� �����
$lines=file("$datadir/newmsg.dat");
$itogo=sizeof($lines); $x=$itogo-1;
do { $dt=explode("|",$lines[$x]);

// ������������ ���� � ������ ����� RSS    
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
   <description>� &lt;b&gt;�������:&lt;/b&gt; &lt;a href=\"$url?id=$fid\"&gt; $razdel&lt;/a&gt; �������: $rubrika &lt;b&gt;$name&lt;/b&gt; �����: &lt;br&gt;&lt;br&gt; $msg &lt;br&gt;&lt;br&gt;</description>
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

} else { // �������� �� ������������ RSS, ������� ������� ���������� �� RSS 

echo '<html>
<head>
<meta http-equiv="Content-Type"
content="text/html; charset=windows-1251">
<title>��� ����� RSS</title>
</head>
<body bgcolor="#FFFFFF">
<p><font size="5">��� ����� RSS?</font></p>
<p><strong>RSS</strong> - ��� ���������� �� <b>R</b>eally
<b>S</b>imple <b>S</b>yndication, ��� � ��������
�� ������� ������, ��� ������������� �������� ����������?
����, ������, ������������� ������� ��������������, - ��� �����
���������, �� �� ����� �������. </p>
<p>������� �������: </p>
<blockquote>
    <p>Syndicate - 1) ��������� ������,  ������������� ����������, ������
    � �. �. � ��������� �� ���������  ������� ��� �������������
    ����������, (���.) 2) �����������  ���������� � ��. (��.)</p>
</blockquote>
<p>�����, <strong>RSS</strong> - ��� �������
������������ ����������.</p>
<p><strong>RSS </strong>- ��� �������������
XML, ������, ���������� ����������� ��� ����, ����� ����� � ������
�������� ���������. ���������� ����������� Netscape ��� �� �������
Netcenter, �� ������ �������� ������������ � ���� ������������ ������ ��������������.</p>
<p>� ��������� ����� <strong>RSS</strong>
�������� ��������� ��� �������� ���� �������� ����� �� ������� ����
��������� ������������, ��� ����������� �������� ������� ���
������ ������������ (�� ���� ������ ��� ��������� WEB-��������), ��� �
�������.</p> 
<p>��� ������ � <b>RSS</b> �� ���� ����� ���������� ���
���������� ����������� ��������� ��� ������ <b>RSS</b>-������. �� �����������
��� ���������� ������� � �������������, ���������� �
���������� ��������� <a  href="http://www.google.ru/search?hl=ru&amp;q=Abilon+RSS&amp;lr=">Abilon</a>
�������� ������� �� ������ � ������ on-line �������� ��� ���������� �
����� ����� � ���������� �� ����� ����� ���������� � ������ ������ � �����
�������.</p>
<p>����� <b>RSS</b>-������� ����� ���������� - <b>'.$rss.'</b></p>
</body>
</html>
'; }

?>
