<?php 
define('_EURO_', chr(128));
ob_start();
?>

<style>
div { font-size:16px; }
</style>

<page backimg="https://www.assoedc.com//wp-content/plugins/edc_adherent/pdf/COM_Population3_2017.png" orientation="P">

	<div style="margin-top:37.6mm; margin-left:50mm"><?php echo $num_adh;?></div>
    
    
	<div style="margin-top:82.8mm; margin-left:121.5mm; width:20mm; text-align:right;"><?php echo $cotisation;?> <?php echo _EURO_;?>&nbsp;</div>
	<div style="margin-top:0mm; margin-left:121.5mm; width:20mm; text-align:right;"><?php echo $parainage;?> <?php echo _EURO_;?>*</div>
	<div style="margin-top:0mm; margin-left:121.5mm; color:#CF0A2C; width:20mm; text-align:right;"><?php echo $total;?> <?php echo _EURO_;?>&nbsp;</div>
    
	<div style="margin-top:121.4mm; margin-left:67.2mm; color:#CF0A2C; width:20mm; text-align:right;"><a href="https://www.assoedc.com/recommandez-edc/" style="font-size:13.5px;">ici</a></div>
    
    
</page>


<?php
$content = ob_get_clean();


//echo $content; die();

// convert in PDF
require_once(dirname(__FILE__).'/../lib/html2pdf/html2pdf.class.php');
try
{
	$html2pdf = new HTML2PDF('P','A4','fr', false, 'ISO-8859-15', array(0, 0, 0, 0));
	$html2pdf->writeHTML(str_replace('&oelig;', 'oe', $content));
	$html2pdf->Output('AppelCotisation2017.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>