<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__))))); 

require_once(__ROOT__.'/inc/sec.php');
require_once(__ROOT__.'/inc/opendb.php');

$id = mysqli_real_escape_string($conn, $_POST["id"]);

?>

<h3>Attachments</h3>
<hr>
<div class="card-body">
    <div class="text-right">
      <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars mx-2"></i>Actions</button>
            <div class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addAttachment"><i class="fa-solid fa-plus mx-2"></i>Add new</a></li>
            </div>
      </div>                    
    </div>
</div>
<table id="tdAttachments" class="table table-striped" style="width:100%">
  <thead>
      <tr>
          <th>Name</th>
          <th>File</th>
          <th>Notes</th>
          <th>Size</th>
          <th>Created</th>
          <th></th>
      </tr>
   </thead>
</table>

<script type="text/javascript" language="javascript" >
$(document).ready(function() {

	$('[data-bs-toggle="tooltip"]').tooltip();
	var tdAttachments = $('#tdAttachments').DataTable( {
		columnDefs: [
			{ className: 'text-center', targets: '_all' },
			{ orderable: false, targets: [5] }
		],
		dom: 'lfrtip',
		processing: true,
		language: {
			loadingRecords: '&nbsp;',
			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			emptyTable: 'No attachments found.',
			search: 'Search:'
		},
		ajax: {	
			url: '/core/list_formula_attachments_data.php',
			type: 'POST',
			data: {
					id: '<?=$id?>',
				},
		},
		columns: [
		  { data : 'name', title: 'Name', render: name },
		  { data : 'docData', title: 'File', render: docData},
		  { data : 'notes', title: 'Notes', render: notes},
		  { data : 'docSize', title: 'Size', render: docSize},
		  { data : 'created', title: 'Created', render: created},
		  { data : null, title: '', render: actions},		   
		],
		
		drawCallback: function ( settings ) {
				extrasShow();
		},
		order: [[ 0, 'asc' ]],
		lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
		pageLength: 20,
		displayLength: 20
	});


	function name(data, type, row){
		return '<a href="#" class="name pv_point_gen" data-name="name" data-type="text" data-pk="'+row.id+'">'+row.name+'</a>';    
	};
	
	function docData(data, type, row){
		return '<a href="/pages/viewDoc.php?id='+row.id+'" target="_blank" class="fa fa-file-alt"></a>';    
	};
	
	function docSize(data, type, row){
		return '<a href="#" class="pv_point_gen">'+row.docSize+'</a>';    
	};
	
	
	function notes(data, type, row){
		return '<a href="#" class="notes pv_point_gen" data-name="notes" data-type="textarea" data-pk="'+row.id+'">'+row.notes+'</a>';    
	};
	
	function created(data, type, row){
		return '<a href="#" class="pv_point_gen">'+row.created+'</a>';    
	};
	
	function actions(data, type, row){
		return '<a href="#" id="dDel" class="fas fa-trash link-danger" data-id="'+row.id+'" data-name="'+row.name+'"></a>';    
	};
	
	$('#tdAttachments').editable({
		  container: 'body',
		  selector: 'a.name',
		  type: 'POST',
		  url: "/pages/update_data.php?ingDoc=update&ingID=<?=$id;?>",
		  title: 'Attachment name',
	 });
	  
	 $('#tdAttachments').editable({
		  container: 'body',
		  selector: 'a.notes',
		  type: 'POST',
		  url: "/pages/update_data.php?ingDoc=update&ingID=<?=$id;?>",
		  title: 'Notes',
	 });
	
		
	$('#tdAttachments').on('click', '[id*=dDel]', function () {
		var d = {};
		d.ID = $(this).attr('data-id');
		d.Name = $(this).attr('data-name');
	
		bootbox.dialog({
		   title: "Confirm file deletion",
		   message : 'Permanently delete <strong>'+ d.Name +'</strong> document?',
		   buttons :{
			   main: {
				   label : "Remove",
				   className : "btn-danger",
				   callback: function (){
						
					$.ajax({ 
						url: '/pages/update_data.php', 
						type: 'GET',
						data: {
							doc: 'delete',
							id: d.ID,
							ownerID: '<?=$id?>'
						},
						dataType: 'html',
						success: function (data) {
							reload_doc_data();
						}
					  });
					
					 return true;
				   }
			   },
			   cancel: {
				   label : "Cancel",
				   className : "btn-secondary",
				   callback : function() {
					   return true;
				   }
			   }   
		   },onEscape: function () {return true;}
	   });
	});
	
	$('#addAttachment').on('click', '[id*=doc_upload]', function () {
			
		$("#doc_inf").html('<div class="alert alert-info alert-dismissible">Please wait, file upload in progress....</div>');
		$("#doc_upload").prop("disabled", true);
		$("#doc_upload").prop('value', 'Please wait...');
			
		var fd = new FormData();
		var files = $('#doc_file')[0].files;
		var doc_name = $('#doc_name').val();
		var doc_notes = $('#doc_notes').val();
		var isBatch = 0;
		if($("#isBatch").prop("checked")){
			isBatch = 1;
		}
	
		if(files.length > 0 ){
			fd.append('doc_file',files[0]);
	
				$.ajax({
				  url: '/pages/upload.php?type=5&isBatch=' + isBatch + '&doc_name=' + btoa(doc_name) + '&doc_notes=' + btoa(doc_notes) + '&id=<?=$id;?>',
				  type: 'POST',
				  data: fd,
				  dataType: 'json',
				  contentType: false,
				  processData: false,
						cache: false,
				  success: function(response){
					if ( response.success ) {
						$("#doc_upload").prop("disabled", false);
						$("#doc_upload").prop('value', 'Upload');
						reload_doc_data();
						var msg = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a>' + response.success + '</div>';
								
					} else {
						$("#doc_upload").prop("disabled", false);
						$("#doc_upload").prop('value', 'Upload');
						var msg = '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>' + response.error + '</strong></div>';
					}
					$('#doc_inf').html(msg);
				  },
			   });
			}else{
				$("#doc_inf").html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-bs-dismiss="alert" aria-label="close">x</a><strong>Error:</strong> Please select a file to upload!</div>');
				$("#doc_upload").prop("disabled", false);
				$("#doc_upload").prop('value', 'Upload');
			}
			
	});
	
	function reload_doc_data() {
		$('#tdAttachments').DataTable().ajax.reload(null, true);
	};

});
</script>


<!-- ADD DOCUMENT-->
<div class="modal fade" id="addAttachment" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addAttachment" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add attachment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="doc_inf"></div>
        <div class="row">
          <div class="mb-3">
            <label for="doc_name" class="form-label">Attachment name</label>
            <input class="form-control" name="doc_name" type="text" id="doc_name" />
          </div>
          <div class="mb-3">
            <label for="doc_notes" class="form-label">Notes</label>
            <input class="form-control" name="doc_notes" type="text" id="doc_notes" />
          </div>
          <div class="mb-3">
            <label for="doc_file" class="form-label">File</label>
            <input type="file" name="doc_file" id="doc_file" class="form-control" />
          </div>
          <div class="mb-3">
            <input type="checkbox" class="form-check-input" id="isBatch">
            <label class="form-check-label" for="isBatch">This is a batch file</label>
          </div>
        </div>
        <div class="dropdown-divider"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <input type="submit" name="button" class="btn btn-primary" id="doc_upload" value="Upload">
      </div>
    </div>
  </div>
</div>

