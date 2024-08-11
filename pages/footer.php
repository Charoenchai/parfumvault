<?php 
if (!defined('pvault_panel')){ die('Not Found');}
if(!$commit = file_get_contents(__ROOT__.'/COMMIT')){
	$commit = "REL";
}
	
?>

  <footer class="sticky-footer">
    <div class="container my-auto">
      <div class="copyright text-center my-auto">
      
        <strong><i class="fa-solid fa-heart mx-2"></i><a href="https://www.paypal.com/paypalme/jbparfum" target="_blank" class="pv_point_gen">Donate if you found this useful.
</a></strong>
	<hr>
        <strong><a href="https://www.perfumersvault.com" target="_blank"><?php echo $product; ?></a></strong>
        <div id="footer_release" class="pv_point_gen"> Version: <strong><?php echo $ver ." - ". $commit; ?> | <a href="https://discord.gg/WxNE8kR8ug" target="_blank">Discord Server</a></strong></div>
        <div class="mt-2">Copyright &copy; 2017-<?php echo date('Y'); ?></div>
      </div>
    </div>
  </footer>

<script>
$('#footer_release').click(function() {
	$('#release_notes').modal('show');
});
</script>
