<?php
include('top.php');

if (isset($_REQUEST['id'])){ $id = $_REQUEST['id']; }

if ($id == '' || !is_numeric($id)) {
	$id = 1;
}
$query = mysqli_query($db_connect, "SELECT
	page_title,
	page_content,
	publish
	FROM team_pages
	WHERE page_id = '".$id."'
	AND publish = '1'
	LIMIT 1
") or die(mysqli_error());
$data = mysqli_fetch_array($query);
mysqli_free_result($query);

echo "<h1>".$data['page_title']."</h1>\n";
echo "<table align='center' width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#".(BORDERCOLOR)."'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'>\n";
echo "<tr>\n";
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