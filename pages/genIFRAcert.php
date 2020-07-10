<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');
require_once('../func/getIFRAMeta.php');
$bottle = $_GET['bottle'];
$type = $_GET['conc'];

if(mysqli_num_rows(mysqli_query($conn, "SELECT id FROM IFRALibrary"))== 0){
		echo 'You need to <a href="maintenance.php?do=IFRA">import</a> the IFRA xls first.';
		exit;
}

$fid = mysqli_real_escape_string($conn, $_GET['fid']);
$meta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE fid = '$fid'"));


$mg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS total_mg FROM formulas WHERE fid = '$fid'"));

$new_conc = $bottle/100*$type;


if (empty($settings['brandLogo'])){ 
	$logo = "../img/logo.png";
}else{
	$logo = $settings['brandLogo'];
}


?>

<link href="../css/ifraCert.css" rel="stylesheet">

<div>
	<p style="margin-bottom: 0.63in"><img src="<?php echo $logo; ?>" width="120px" height="120px"/></p>
</div>
<h1 class="western"><font face="Arial, sans-serif"><span style="font-style: normal">CERTIFICATE OF CONFORMITY OF FRAGRANCE MIXTURES WITH IFRA STANDARDS</span></font><br>
</h1>
<p align=center style="widows: 0; orphans: 0"><font face="Helvetica 65 Medium, Arial Narrow, sans-serif"><font size=4><B><font face="Arial, sans-serif"><font size=2 style="font-size: 11pt"><u>This
  Certificate assesses the conformity of a fragrance mixture with IFRA
  Standards and provides restrictions for use as necessary. It is based
  only on those materials subject to IFRA Standards for the toxicity
  endpoint(s) described in each Standard. </u></font></font></b></font></font>
</p>
<p align=center style="widows: 0; orphans: 0"><br>
</p>
<hr size="1">
</p>
<p class="western"><font face="Arial, sans-serif"><u><b>CERTIFYING PARTY:</b></u></font></p>
<p class="western"><font face="Arial, sans-serif"><span><b><span style="background: #ffff00">Name
of the fragrance supplier delivering the certificate</span></b></span></font></p>
<p class="western"><font face="Arial, sans-serif"><B><span style="background: #ffff00">Address
of the fragrance supplier</span></b></font></p>
</p>
<p class="western"><font face="Arial, sans-serif"><u><b>CERTIFICATE DELIVERED TO: </b></u></font>
</p>
<p class="western"><font face="Arial, sans-serif"><span ><b>Customer:
</b></span></font><font face="Arial, sans-serif"><span ><B><span style="background: #ffff00">Name of the fragrance supplier or finished product manufacturer</span></b></span></font></p>
<p  class="western"><br>
</p>
<p class="western"><font face="Arial, sans-serif"><u><b>SCOPE OF THE CERTIFICATE:</b></u></font></p>
<p class="western"><font face="Arial, sans-serif"><span >Product: <B><?php echo $meta['product_name'];?></b></span></font></p>
<p class="western">Size:<strong> <?php echo $bottle; ?>ml</strong></p>
<p class="western">Concentration: <strong><?php echo $type; ?>%</strong></p>
<hr size="1"><br>
<font face="Arial, sans-serif"><span ><U><B>COMPULSORY INFORMATION:</b></u></span></font>
<p class="western" style="margin-right: -0.12in">
  <font face="Arial, sans-serif"><span >We certify that the above mixture is in compliance with the Standards of the INTERNATIONAL FRAGRANCE ASSOCIATION (IFRA), up to and including the <strong><?php echo getIFRAMeta('MAX(amendment)',$conn);?></strong> Amendment to the IFRA Standards (published </span><b><?php echo getIFRAMeta('MAX(last_pub)',$conn);?></span></b>),
  provided it is used in the following</span></font>  <font face="Arial, sans-serif"><span >category(ies)
at a maximum concentration level of:</span></font></p>
<table width="61%" border="1">
  <tr>
    <th bgcolor="#d9d9d9"><strong>IFRA<br>
Category(ies) [see Table 12 in Guidance for the use of IFRA<br>
Standards for details]</strong></th>
    <th bgcolor="#d9d9d9"><strong>Level of use (%)*</strong></th>
  </tr>
  <tr>
    <td align="center"><strong>Category 4</strong></td>
    <td align="center"><?php echo $type; ?>%</td>
  </tr>
</table>
<p  class="western" style="margin-right: -0.12in"><font face="Arial, sans-serif"><I>*Actual use level or maximum use level</I></font> </p>
<p  class="western" style="margin-right: -0.12in">
  <font face="Arial, sans-serif"><span >For other kinds of, application or use at higher concentration levels, a new evaluation may be needed; please contact </span></font><font face="Arial, sans-serif"><span ><span style="background: #ffff00">(name of the fragrance supplier)</span></span></font><font face="Arial, sans-serif"><span >.
</span></font></p>
<p class="western" style="margin-right: -0.12in">
  <font face="Arial, sans-serif"><span ><u><b>(OPTIONAL INFORMATION):</b></u></span></font></p>
<p class="western" style="margin-right: -0.12in">
  <font face="Arial, sans-serif"><span >Information about presence and concentration of fragrance ingredients subject to IFRA Standards in the fragrance mixture </span></font><font face="Arial, sans-serif"><B><?php echo $meta['product_name'];?></b></font><font face="Arial, sans-serif"><span> is as follows:</span></font></p>
<p class="western" style="margin-right: -0.12in">&nbsp;</p>
<table width="61%" border="1">
  <tr>
    <th width="22%" bgcolor="#d9d9d9"><strong>Materials under the scope of IFRA Standards:</strong></th>
    <th width="12%" bgcolor="#d9d9d9"><strong>CAS number(s):</strong></th>
    <th width="28%" bgcolor="#d9d9d9"><strong>Recommendation from IFRA Standard:</strong></th>
    <th width="38%" bgcolor="#d9d9d9"><strong>Concentration (%) in fragrance mixture or finished product:</strong></th>
  </tr>
    <?php 
	$fq = mysqli_query($conn, "SELECT ingredient,quantity,concentration FROM formulas WHERE fid = '$fid'");

	while($ing = mysqli_fetch_array($fq)){
  		$cas = mysqli_fetch_array(mysqli_query($conn,"SELECT cas FROM ingredients WHERE name = '".$ing['ingredient']."'"));
		//if ($cas['cas']){
		$ifra = mysqli_fetch_array(mysqli_query($conn,"SELECT name,cat4,risk,type,cas FROM IFRALibrary WHERE cas LIKE '%".$cas['cas']."%' ")); 
		
		$new_quantity = $ing['quantity']/$mg['total_mg']*$new_conc;
		$conc = $new_quantity/$bottle * 100;						
		$conc_p = number_format($ing['concentration'] / 100 * $conc, 3);
		
		echo '<tr>
    	<td align="center">'.$ifra['name'].'</td>
    	<td align="center">'.$ifra['cas'].'</td>
    	<td align="center">'.$ifra['risk']; 
		if($ifra['cat4']){
			echo ' - MAX usage: '.$ifra['cat4'].'%</td>';
		}
		echo '
    	<td align="center">'.$conc_p.'%</td> 
 	</tr>';
		//}
  } 
  ?>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
  <p><font face="Arial, sans-serif"><span >Signature </span></font><font face="Arial, sans-serif"><span><I>(If generated electronically, no signature)</i></span></font></p>
  <p><font face="Arial, sans-serif"><span >Date: </span></font><strong><?php echo date('d/M/Y');?></strong></p>
  </p>
<div>
	<p style="margin-right: 0in; margin-top: 0.08in">
	<font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span><u>Disclaimer</u>:
	</span></font></font></p>
	<p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span>This Certificate provides restrictions for use of the specified product based only on those materials restricted by IFRA Standards for the toxicity endpoint(s) described in each Standard.</span></font></font></p>
  <p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span>This Certificate does not provide certification of a comprehensive safety assessment of all product constituents.</span></font></font></p>
	<p style="margin-right: 0in; margin-top: 0.08in"><font face="Segoe UI, sans-serif"><font size=1 style="font-size: 8pt"><span> This certificate is the responsibility of the fragrance supplier issuing it. It has not been prepared or endorsed by IFRA in anyway. </span></font></font>
  </p>
</div>