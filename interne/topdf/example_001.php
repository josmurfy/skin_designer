<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
//$pdf->setCreator(PDF_CREATOR);
//$pdf->setAuthor('Nicola Asuni');
//$pdf->setTitle('TCPDF Example 001');
//$pdf->setSubject('TCPDF Tutorial');
//$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->setFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = <<<EOD


<!DOCTYPE html>
<html>
<head>  
  <title>receipt</title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
  <link rel="stylesheet" href="https://www.phoenixliquidation.ca/interne/paper.css">
      <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
   <style type="text/css" media="print">
     @page { size: 3.15in 1.10in landscape;} /* output size */
    body.receipt .sheet { width: 2.9in; height: .96in } /* sheet size */
    @media print { body.receipt { width: 2.9in } } /* fix for Chrome */

	
  </style>
   <style>
 table, th, td {
  border: 0px ;
  padding: 8px;
}

   .center {
  margin-left: auto;
  margin-right: auto;
}
      </style>
  <script src="https://phoenixliquidation.ca/catalog/view/javascript/qr/qrcode.min.js" type="text/javascript"></script>
<script src="https://phoenixliquidation.ca/catalog/view/javascript/qr/qrcode.js" type="text/javascript"></script>
</head>

<body class="receipt">


				<section class="sheet padding-0mm">
				<table class="center">
				<tr>
				
				<td  >
								<div id="qrcode1">									
					<script type="text/javascript">
				  var qrcode = new QRCode("qrcode1", {
					text: "883929676279VG",
					width: 70, 
					height: 70,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});	
					</script>
					
				</div>
				</td>
				<td >
				
				<div  style="text-align: center;font-size: 17px; "><strong>883929676279VG</strong></div>
				
				<div  style="text-align: center;font-size: 17px; "> _________________<br>Listed by : admin </div>
				</td>
				
				</tr>
				</table>
					  </section>
				
</body>
</html>
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
