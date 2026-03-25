<?
require_once '../vendor/autoload.php';
//use Google\Cloud\Translate\V3\TranslateClient;
use Google\Cloud\Translate\TranslateClient;
echo "***".$GOOGLE_APPLICATION_CREDENTIALS."$$$";
$_GET['targetLanguage'] = 'fr'; 
$translate = new TranslateClient();
$result = $translate->translate(('Hello'), [
			'target' => $_GET['targetLanguage'],
				]);
			$test='`test`="'.addslashes($result['text']).'",';
			echo $test;
?>

<html>
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
  <meta charset="utf-8">
  <title>receipt</title>


  <?/*   <style type="text/css" media="print">
    @page { size: 76mm 25mm margin: 0mm 0mm 0mm 0mm;} /* output size */
   // body.receipt .sheet { width: 74mm; height: 25mm } /* sheet size */
   // @media print { body.receipt { width: 76mm } } /* fix for Chrome */
  //</style>*/?>
</head>

<body class="receipt">
</body>
</html>