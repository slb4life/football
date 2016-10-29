<?php
include('top.php');
$script_name = "news_archive.php?".$_SERVER['QUERY_STRING'];

echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$locale_news_archive."</i></b></td>\n";
echo "</tr><tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
$get_news = mysqli_query($db_connect, "SELECT
	N.news_id AS news_id,
	N.news_subject AS news_subject,
	DATE_FORMAT(N.news_date, '$how_to_print') AS news_date
	FROM team_news N
	ORDER BY N.news_date DESC, N.news_id DESC
	LIMIT 5
") or die(mysqli_error());

if (mysqli_num_rows($get_news) == 0) {
	echo "".$locale_no_news_archive."";
} else {
	while($data = mysqli_fetch_array($get_news)) {
		echo "<tr>\n";
		echo "<td align='right' valign='top'><i>".$data['news_date']."</i></td>\n";
		echo "<td align='left' valign='top' width='85%'><b><a href='index.php?id=".$data['news_id']."'>".$data['news_subject']."</a></b></td>\n";
		echo "</tr>\n";
	}
}
mysqli_free_result($get_news);

echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

include('bottom.php');
?>