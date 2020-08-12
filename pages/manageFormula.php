<?php 
require('../inc/sec.php');

require_once('../inc/config.php');
require_once('../inc/opendb.php');
require_once('../inc/settings.php');

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('../logs/pvault.log', 'a');
fwrite($fp, $req_dump);
fclose($fp);

//DIVIDE - MULTIPLY
if($_GET['formula'] && $_GET['do']){
	$formula = mysqli_real_escape_string($conn, $_GET['formula']);
	
	$q = mysqli_query($conn, "SELECT quantity,ingredient FROM formulas WHERE name = '$formula'");
	while($cur =  mysqli_fetch_array($q)){
		if($_GET['do'] == 'multiply'){
			$nq = $cur['quantity']*2;
		}elseif($_GET['do'] == 'divide'){
			$nq = $cur['quantity']/2;
		}
		
		if(empty($nq)){
			print 'error';
			return;
		}
		
		mysqli_query($conn,"UPDATE formulas SET quantity = '$nq' WHERE name = '$formula' AND quantity = '".$cur['quantity']."' AND ingredient = '".$cur['ingredient']."'");
	}

//DELET INGREDIENT
}elseif($_GET['action'] == 'deleteIng' && $_GET['ingID'] && $_GET['ing']){
	$id = mysqli_real_escape_string($conn, $_GET['ingID']);
	$ing = mysqli_real_escape_string($conn, $_GET['ing']);
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	if(mysqli_query($conn, "DELETE FROM formulas WHERE id = '$id' AND name = '$fname'")){
				
		echo  '<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
				'.$ing.' removed from the formula!
				</div>';
	}else{
		echo  '<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
				'.$ing.' cannot be removed from the formula!
				</div>';
	}
	
//ADD INGREDIENT
}elseif($_GET['action'] == 'addIng' && $_GET['fname']){// && $_GET['quantity'] && $_GET['ingredient']){
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	$ingredient = mysqli_real_escape_string($conn, $_GET['ingredient']);
	$quantity = mysqli_real_escape_string($conn, $_GET['quantity']);
	$concentration = mysqli_real_escape_string($conn, $_GET['concentration']);
	$dilutant = mysqli_real_escape_string($conn, $_GET['dilutant']);

	if (empty($quantity) || empty($concentration)){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error: </strong>Missing fields</div>';
	}else
		
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND name = '$fname'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$ingredient.' already exists in formula!
		'.mysqli_error($conn).'</div>';
	}else{

		if(mysqli_query($conn,"INSERT INTO formulas(fid,name,ingredient,ingredient_id,concentration,quantity,dilutant) VALUES('".base64_encode($fname)."','$fname','$ingredient','$ingredient_id','$concentration','$quantity','$dilutant')")){
			echo '<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					'.$ingredient.' added to formula!
					</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					Error adding '.$ingredient.'!
					</div>';
		}
	}
	
//REPLACE INGREDIENT
}elseif($_GET['action'] == 'repIng' && $_GET['fname']){
	$fname = mysqli_real_escape_string($conn, $_GET['fname']);
	$ingredient = mysqli_real_escape_string($conn, $_REQUEST['value']);
	$oldIngredient = mysqli_real_escape_string($conn, $_REQUEST['pk']);

			
	if(mysqli_num_rows(mysqli_query($conn, "SELECT ingredient FROM formulas WHERE ingredient = '$ingredient' AND name = '$fname'"))){
		echo '<div class="alert alert-danger alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  		<strong>Error: </strong>'.$ingredient.' already exists in formula!
		'.mysqli_error($conn).'
		</div>';
	}else{
		if(mysqli_query($conn, "UPDATE formulas SET ingredient = '$ingredient' WHERE ingredient = '$oldIngredient' AND name = '$fname'")){
			echo '<div class="alert alert-success alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					'.$oldIngredient.' replaced with '.ingredient.'!
					</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
					Error replacing '.$oldIngredient.mysqli_error($conn).'!
					</div>';
		}
	}

//CLONE FORMULA
}elseif($_GET['action'] == 'clone' && $_GET['formula']){
	$fname = mysqli_real_escape_string($conn, $_GET['formula']);
	$fid = base64_encode($fname);
	$newName = $fname.' - (Copy)';
	$newFid = base64_encode($newName);
		if(mysqli_num_rows(mysqli_query($conn, "SELECT fid FROM formulasMetaData WHERE fid = '$newFid'"))){
			echo '<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
  				<strong>Error: </strong>'.$newName.' already exists, please remove or rename it first!</div>';
		}else{
			$sql.=mysqli_query($conn, "INSERT INTO formulasMetaData (fid, name, notes, profile, image, sex) SELECT '$newFid', '$newName', notes, profile, image, sex FROM formulasMetaData WHERE fid = '$fid'");
			$sql.=mysqli_query($conn, "INSERT INTO formulas (fid, name, ingredient, ingredient_id, concentration, dilutant, quantity) SELECT '$newFid', '$newName', ingredient, ingredient_id, concentration, dilutant, quantity FROM formulas WHERE fid = '$fid'");
		}
	if($sql){
		echo '<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>
			'.$fname.' cloned as '.$newName.'!
			</div>';
	}
	
	
//DELETE FORMULA
}elseif($_GET['action'] == 'delete' && $_GET['fid']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	if(mysqli_query($conn, "DELETE FROM formulas WHERE fid = '$fid'")){
		mysqli_query($conn, "DELETE FROM formulasMetaData WHERE fid = '$fid'");
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Formula deleted!</div>';
	}else{
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error</strong> deleting '.$fid.' formula!</div>';
	}

//MAKE FORMULA
}elseif($_GET['action'] == 'makeFormula' && $_GET['fid'] && $_GET['q'] && $_GET['qr'] && $_GET['ingId']){
	$fid = mysqli_real_escape_string($conn, $_GET['fid']);
	$ingId = mysqli_real_escape_string($conn, $_GET['ingId']);
	$qr = trim($_GET['qr']);
	$q = trim($_GET['q']);

	if($qr == $q){
		if(mysqli_query($conn, "UPDATE makeFormula SET toAdd = '0' WHERE fid = '$fid' AND id = '$ingId'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Added!</div>';
		}else{
			echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>Error</strong> '.mysqli_error($conn).'</div>';
		}
	}else{
		$sub_tot = $qr - $q;
		if(mysqli_query($conn, "UPDATE makeFormula SET quantity='$sub_tot' WHERE fid = '$fid' AND id = '$ingId'")){
			echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Updated!</div>';
		}
		
	}
	return;
	
//CART MANAGE
}elseif($_GET['action'] == 'addToCart' && $_GET['material']){
	$material = mysqli_real_escape_string($conn, $_GET['material']);
	$qS = mysqli_fetch_array(mysqli_query($conn, "SELECT supplier, supplier_link FROM ingredients WHERE name = '$material'"));
	
	if(empty($qS['supplier_link'])){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$material.'</strong> cannot be added to cart as missing supplier info. Please update material supply details first.</div>';
		return;
	}		
	if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM cart WHERE name = '$material'"))){
		echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$material.'</strong> already in cart</div>';
		return;
	}
									
	if(mysqli_query($conn, "INSERT INTO cart (name,supplier) VALUES ('$material','".$qS['supplier_link']."')")){
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a><strong>'.$material.'</strong> added to cart!</div>';
		return;
	}
	
}elseif($_GET['action'] == 'removeFromCart' && $_GET['material']){




//PRINTING
}elseif($_GET['action'] == 'printLabel' && $_GET['name']){
	$name = $_GET['name'];
	
	

	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		
		if($_GET['batchID']){
			$bNo = $_GET['batchID'];
		}else{
			$bNO = 'N/A';
		}
				
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$name'"));
		$info = "Production: ".date("d/m/Y")."\nProfile: ".$q['profile']."\nSex: ".$q['sex']."\nB. NO: ".$bNo."\nDescription:\n\n".wordwrap($q['notes'],30);
		$w = '720';
		$h = '860';
	}else{
		$w = '720';
		$h = '260';
	}
		
	$lbl = imagecreatetruecolor($w, $h);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, $w, $h, $white);
	
	$text = trim($name.$extras);
	$font = '../fonts/Arial.ttf';

	imagettftext($lbl, $settings['label_printer_font_size'], 0, 0, 150, $black, $font, $text);
	$lblF = imagerotate($lbl, 90 ,0);
	
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		imagettftext($lblF, 25, 0, 200, 300, $black, $font, $info);
	}
	$extras = '';
	if($_GET['dilution'] && $_GET['dilutant']){
		$extras = ' @'.$_GET['dilution'].'% in '.$_GET['dilutant'];
		imagettftext($lblF, 40, 90, 200, 600, $black, $font, $extras);
	}
	$save = "../tmp/labels/".base64_encode($text.'png');

	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
		//echo '<img src="'.$save.'"/>';
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Print sent!</div>';
	}

//PRINT BOX LABEL
}elseif($_GET['action'] == 'printBoxLabel' && $_GET['name']){
	if(empty($_GET['copies']) || !is_numeric($_GET['copies'])){
		$copies = '1';
	}else{
		$copies = intval($_GET['copies']);
	}
	
	if($settings['label_printer_size'] == '62' || $settings['label_printer_size'] == '62 --red'){
		$name = mysqli_real_escape_string($conn, $_GET['name']);
		$q = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM formulasMetaData WHERE name = '$name'"));
		$qIng = mysqli_query($conn, "SELECT ingredient FROM formulas WHERE name = '$name'");
		
		while($ing = mysqli_fetch_array($qIng)){
				$chName = mysqli_fetch_array(mysqli_query($conn, "SELECT chemical_name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
				if($chName['chemical_name']){
					$getAllergen['name'] = $chName['chemical_name'];
				}else{
					$getAllergen = mysqli_fetch_array(mysqli_query($conn, "SELECT name FROM ingredients WHERE name = '".$ing['ingredient']."' AND allergen = '1'"));
				}

			$allergen[] = $getAllergen['name'];
		}
		$allergen[] = 'Denatureted Ethyl Alcohol '.$_GET['carrier'].'% Vol, Fragrance, DPG, Distilled Water';

		if($_GET['batchID']){
			$bNo = $_GET['batchID'];
		}else{
			$bNO = 'N/A';
		}
		$allergenFinal = implode(", ",array_filter($allergen));
		$info = "FOR EXTERNAL USE ONLY. \nKEEP AWAY FROM HEAT AND FLAME. \nKEEP OUT OF REACH OF CHILDREN. \nAVOID SPRAYING IN EYES. \n \nProduction: ".date("d/m/Y")." \nB. NO: ".$bNo." \nwww.johnbelekios.com";
		$w = '720';
		$h = '860';
	}
		
	$lbl = imagecreatetruecolor($h, $w);

	$white = imagecolorallocate($lbl, 255, 255, 255);
	$black = imagecolorallocate($lbl, 0, 0, 0);	
	
	imagefilledrectangle($lbl, 0, 0, $h, $w, $white);
	
	$text = strtoupper($q['product_name']);
	$font = '../fonts/Arial.ttf';
				//font size 15 rotate 0 center 360 top 50
	imagettftext($lbl, 30, 0, 250, 50, $black, $font, $text);
	imagettftext($lbl, 25, 0, 300, 100, $black, $font, 'INGREDIENTS');
	$lblF = imagerotate($lbl, 0 ,0);
	
	imagettftext($lblF, 22, 0, 50, 150, $black, $font, wordwrap ($allergenFinal, 60));
	imagettftext($lblF, 22, 0, 150, 490, $black, $font, wordwrap ($info, 50));

	$save = "../tmp/labels/".base64_encode($text.'png');

	if(imagepng($lblF, $save)){
		imagedestroy($lblF);
		for ($k = 0; $k < $copies; $k++){
			//echo '<img src="'.$save.'"/>';
			shell_exec('/usr/bin/brother_ql -m '.$settings['label_printer_model'].' -p tcp://'.$settings['label_printer_addr'].' print -l '.$settings['label_printer_size'].' '. $save);
		}
		echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">x</a>Print sent!</div>';
	}
}

?>