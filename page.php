<?php
include('top.php');

if (isset($_REQUEST['id'])){ $id = $_REQUEST['id']; }

if ($id == '' || !is_numeric($id)) {
	$id = 1;
}
$get_pages = mysqli_query($db_connect, "SELECT
	PageTitle AS page_title,
	PageContent AS page_content,
	PagePublish AS publish
	FROM team_pages
	WHERE PageID = '$id'
	AND PagePublish = '1'
	LIMIT 1
") or die(mysqli_error());
$data = mysqli_fetch_array($get_pages);
mysqli_free_result($get_pages);

echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td bgcolor='#".(CELLBGCOLORTOP)."' align='left' valign='middle'><b><i>".$data['page_title']."</i></b></td>\n";
echo "</tr><tr>\n";
echo "<td align='left' valign='middle' bgcolor='#".(CELLBGCOLORBOTTOM)."'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
echo "<td valign='top'>".$data['page_content']."</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

include('bottom.php');
?>