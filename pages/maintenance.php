<?php require('../inc/sec.php');?>

<?php
require_once('../inc/config.php');
require_once('../inc/opendb.php');
if($_GET['do'] == 'backupDB'){
	
	$file = 'backup-'.date("d-m-Y").'.sql';
	$mime = "text/sql";
	
	header( 'Content-Type: '.$mime );
	header( 'Content-Disposition: attachment; filename="' .$file. '"' );
	
	$cmd = "mysqldump -u $dbuser --password=$dbpass $dbname";   
	passthru($cmd);
	
}elseif(isset($_POST['restore'])){
	if($_FILES["file"]["size"] > 0){

		$file_tmp = escapeshellcmd($_FILES["file"]["tmp_name"]);   
		$cmd = "mysql -u$dbuser -p$dbpass $dbname < $file_tmp"; 
		passthru($cmd,$e);
				
		if(!$e){
			header("Location: /pages/maintenance.php?do=restoreDB&err=0");
		}else{
			header("Location: /pages/maintenance.php?do=restoreDB&err=1");
		}
	}else{

		header("Location: /pages/maintenance.php?do=restoreDB&err=2");
	}

}elseif(isset($_POST['import_ifra'])){
	$filename=$_FILES["file"]["tmp_name"];  
    $file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));

    $ext = explode(",","xls,xlsx");

    if(in_array($file_ext,$ext)=== false){
		 echo '<div class="alert alert-danger alert-dismissible"><strong>File upload error: </strong>Extension not allowed, please choose a '.$ext.' file.</div>';
	}else{
		
	if($_FILES["file"]["size"] > 0){
	require_once('../func/SimpleXLSX.php');
   	mysqli_query($conn, "TRUNCATE IFRALibrary");

	$xlsx = SimpleXLSX::parse($filename);

	try {
       $link = new PDO( "mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
       $link->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }

	$fields = 'ifra_key,image,amendment,prev_pub,last_pub,deadline_existing ,deadline_new,name,cas,cas_comment,synonyms,formula,flavor_use,prohibited_notes,restricted_photo_notes,restricted_notes,specified_notes,type,risk,contrib_others,contrib_others_notes,cat1,cat2,cat3,cat4,cat5A ,cat5B,cat5C,cat5D,cat6,cat7A,cat7B,cat8,cat9,cat10A,cat10B,cat11A,cat11B,cat12';
	$values = substr(str_repeat('?,', count(explode(',' , $fields))), 0 , strlen($x) - 1);
	$stmt = $link->prepare( "INSERT INTO IFRALibrary ($fields) VALUES ($values)");
	$cols = $xlsx->dimension()[0];//$dim[0];
		foreach ( $xlsx->rows() as $k => $r ) {
			for ( $i = 0; $i < $cols; $i ++ ) {
				$l = $i+1;
				$stmt->bindValue( $l, $r[ $i]);
			}
			try {
		    	$stmt->execute();
				$err = '0';
			} catch (Exception $e) {
				$err = '1';
			}
   		}
		header("Location: /pages/maintenance.php?do=IFRA&err=$err");
	}
}
	
		  
}elseif($_GET['do'] == 'restoreDB'){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import CSV file</title>
<link href="../css/sb-admin-2.css" rel="stylesheet">
  
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
</head>

<body>
    <div id="wrap">
        <div class="container">
      
<form action="" method="post" enctype="multipart/form-data" name="restoredb" target="_self">
             <table width="100%" border="0">
                              <tr>
                                <td colspan="2" class="badge-primary">Restore DB</td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php 
								if($_GET['err'] == '0'){
									echo '<div class="alert alert-success alert-dismissible"><strong>Success: </strong>Database restored!</div>';
								}elseif($_GET['err'] == '1'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>import failed, check your sql file!</div>';
								}elseif($_GET['err'] == '2'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>invalid file!</div>';
								}
		
								?></td>
                              </tr>
                              <tr>
                                <td width="21%">Choose file:</td>
                                <td width="79%"><span class="col-md-4">
                                  <input type="file" name="file" id="file" class="input-large" />
                                </span></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><input type="submit" name="restore" id="restore" value="Restore" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="2"><p class="alert-link">Notes:</p>
                                <p class="bg-gray-100">File must be a valid sql backup</p></td>
                              </tr>
                            </table>
</form>
<?php }elseif($_GET['do'] == 'IFRA'){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Maintenance</title>
<link href="../css/sb-admin-2.css" rel="stylesheet">
  
<link href="../css/bootstrap-select.min.css" rel="stylesheet">
</head>

<body>
    <div id="wrap">
        <div class="container">
<form action="" method="post" enctype="multipart/form-data" name="importIFRA" target="_self">
             <table width="100%" border="0">
                              <tr>
                                <td colspan="2" class="badge-primary">Import IFRA Library</td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php 
								if($_GET['err'] == '0'){
									echo '<div class="alert alert-success alert-dismissible"><strong>Success: </strong>Library imported!</div>';
								}elseif($_GET['err'] == '1'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>import failed!</div>';
								}elseif($_GET['err'] == '2'){
									echo '<div class="alert alert-danger alert-dismissible"><strong>Error: </strong>invalid file!</div>';
								}
		
								?></td>
                              </tr>
                              <tr>
                                <td width="21%">Choose file:</td>
                                <td width="79%"><span class="col-md-4">
                                  <input type="file" name="file" id="file" class="input-large" />
                                </span></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><input type="submit" name="import_ifra" id="import" value="Import" /></td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td colspan="2"><p class="alert-link">Notes:</p>
                                  <p class="alert-link">IMPORTANT:</p>
                                  <p class="alert-link"> This operation will wipe out any data already in your IFRA Library, so please make sure the file you uploading is in right format.</p>
                                  <p class="alert-link">The IFRA xls can be downloaded from its official <a href="https://ifrafragrance.org/safe-use/standards-guidance" target="_blank">web site</a></p>
                                  <p class="alert-link">BEFORE IMPORT:                                 </p>
                                  <ul>
                                    <li>Take a backup.</li>
                                    <li>Open the xlsx file and remove the first 3 rows which contain column names and file info.</li>
                                  </ul>
                                </td>
                 </tr>
                            </table>
</form>
 </div>
</div>
</body>
</html>
<?php }?>