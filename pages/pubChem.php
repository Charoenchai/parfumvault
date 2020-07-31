<?php

if(!$_GET['cas']){
	echo 'Error: Missing CAS number';
	return;
}

$cas = trim($_GET['cas']);
$type = 'PNG';

$api = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug';
$cids = trim(file_get_contents($api.'/compound/name/'.$cas.'/cids/TXT'));

$image = 'data:image/png;base64,'.base64_encode(file_get_contents($api.'/compound/cid/'.$cids.'/'.$type.'?record_type=2d&image_size=large'));
$data = json_decode(trim(file_get_contents($api.'/compound/name/'.$cas.'/JSON')),true);
		

?>

<table width="100%" border="0">
                  <tr>
                    <td width="20%" rowspan="5" valign="top"><img src="<?php echo $image;?>"/></td>
                    <td width="34%">Molecular Formula:</td>
                    <td width="46%"><strong><?php echo $data['PC_Compounds']['0']['props']['16']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Molecular Weight:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['17']['value']['fval'];?></strong></td>
                  </tr>
                  <tr>
                    <td>Canonical Smiles:</td>
                    <td><strong><?php echo $data['PC_Compounds']['0']['props']['18']['value']['sval'];?></strong></td>
                  </tr>
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                   <td colspan="2">&nbsp;</td>
            </tr>
</table>