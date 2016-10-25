<?php
session_start();
$session_id = $_REQUEST['session_id'];
$session = $_SESSION['session'];

if (!isset($session_id) || $session_id != "$session" || $session_id == '') {
	echo "Authorization Failed.<br><a href='index.php'>Restart, Please</a>";
} else {
	$action = $_POST['action'];
	switch($action) {
		case 1: {
			$player_id = $_POST['player_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/".$player_id.".jpg";
			$image_url2 = "../images/".$player_id.".png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				print('Please Upload Only JPG or PNG-Filetype');
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/".$player_id . "." . $check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 2: {
			$player_id = $_POST['player_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/".$player_id."_1.jpg";
			$image_url2 = "../images/".$player_id."_1.png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/".$player_id."_1.".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 3: {
			$player_id = $_POST['player_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/".$player_id."_2.jpg";
			$image_url2 = "../images/".$player_id."_2.png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/".$player_id."_2.".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 4: {
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/header.jpg";
			$image_url2 = "../images/header.png";

			if($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if(file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/header.".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 5: {
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/team_logo.jpg";
			$image_url2 = "../images/team_logo.png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/team_logo.".$check_type[1]);
				list($width, $height, $type, $attr) = getimagesize("../images/team_logo.".$check_type[1]."");

				if ($width > 80) {
					unlink("../images/team_logo.$check_type[1]");
					echo "Image is too big. Maximum width is 80 pixels.";
				} else {
					echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
				}
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 6: {
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$opponent_id = $_POST['opponent_id'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/opponent_logo_".$opponent_id.".jpg";
			$image_url2 = "../images/opponent_logo_".$opponent_id.".png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if(file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/opponent_logo_".$opponent_id.".".$check_type[1]);
				list($width, $height, $type, $attr) = getimagesize("../images/opponent_logo_".$opponent_id.".".$check_type[1]."");

				if($width > 80) {
					unlink("../images/opponent_logo_".$opponent_id.".".$check_type[1]."");
					echo "Image is too big. Maximum width is 80 pixels.";
				} else {
					echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
				}
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 7: {
			$manager_id = $_POST['manager_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/manager".$manager_id.".jpg";
			$image_url2 = "../images/manager".$manager_id.".png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if(file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/manager".$manager_id.".".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 8: {
			$manager_id = $_POST['manager_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/manager".$manager_id."_1.jpg";
			$image_url2 = "../images/manager".$manager_id."_1.png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}
			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}
			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/manager".$manager_id."_1.".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 9: {
			$manager_id = $_POST['manager_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/manager".$manager_id."_2.jpg";
			$image_url2 = "../images/manager".$manager_id."_2.png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}

			if (file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}

			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/manager".$manager_id."_2.".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;

		case 10: {
			$news_id = $_POST['news_id'];
			$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			$check_type = explode(".",$_FILES['image_file']['name']);
			$image_url = "../images/news_picture".$news_id.".jpg";
			$image_url2 = "../images/news_picture".$news_id.".png";

			if ($check_type[1] != 'jpg' && $check_type[1] != 'png') {
				echo "Please Upload Only JPG or PNG-Filetype";
				exit;
			}

			if(file_exists($image_url)) {
				unlink("$image_url");
			} else if (file_exists($image_url2)) {
				unlink("$image_url2");
			}

			if (is_uploaded_file($_FILES['image_file']['tmp_name'])) {
				copy($_FILES['image_file']['tmp_name'], "../images/news_picture".$news_id.".".$check_type[1]);
				echo "Image Uploaded Succesfully!<br><a href='$HTTP_REFERER'>Back</a>";
			} else {
				echo "Upload Attack. Filename: ".$_FILES['image_file']['name'];
			}
		}
		break;
	}
}
?>
