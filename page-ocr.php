<?php
/*
Template Name: OCR Template
*/
get_header();

include get_stylesheet_directory(). '/class-ocr_decode.php';


if ($_FILES && $_FILES["filename"]["error"]== UPLOAD_ERR_OK)
{
	$methodType = key($_FILES);
	$site_url = $_SERVER["DOCUMENT_ROOT"];
	if (!file_exists('wp-content/uploads/docs/')) {
	    mkdir('wp-content/uploads/docs/', 0777, true);
	}
    $name = "wp-content/uploads/docs/" . $_FILES[$methodType]["name"];

    $filePath = $site_url.'/'.$name;

    $getExtension = explode( '.', $name );
    $extension = end( $getExtension );

    if (($extension=='jpg')|($extension=='jpeg')|($extension=='png')|($extension=='pdf')) {
    	move_uploaded_file($_FILES[$methodType]["tmp_name"], $name);

		$ocr = new ocr_decode($filePath, $methodType);
		$dataFromOCR = $ocr->getDataFromOCR();

    } else {
    	echo "File format not supported";
    }
}

?>

<h2>ID</h2>
<form method="post" enctype="multipart/form-data">
Choice file: <input type="file" name="teudatZehut" size="10"/><br /><br />
<input type="submit" value="Upload" />
</form>
<hr>

<h2>Prescription</h2>
<form method="post" enctype="multipart/form-data">
Choice file: <input type="file" name="prescription" size="10" /><br /><br />
<input type="submit" value="Upload" />
</form>
<hr>

<h2>License</h2>
<form method="post" enctype="multipart/form-data">
Choice file: <input type="file" name="license" size="10" /><br /><br />
<input type="submit" value="Upload" />
</form>
<hr>

<?php
get_footer();

