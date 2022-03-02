<?php
	include_once FFRCRUD_PATH.'classes/Template.php';
	include_once FFRCRUD_PATH.'classes/Helper.php';
	$template = new Template;
	$helper = new Helper;

	$query = $helper->prepareReport();
	if(!$query->have_posts()) return;
	//$countReports = count($query);

	// Display Loader
	$html = '<div class="field-reports-loading d-none my-4"><i class="fas fa-circle-notch"></i></div>';
	
  // Get Pagination
  // if($countReports > 6) {
  //   $html .= '<div id="field-reports-pagination"><ul>';
  //   $numb = 1;            
  //   for($i = 0; $i < $countReports; $i++){
  //       if(!($i % 4 != 0)){
  //           $page = 3+$i;         
  //           $active = ($numb === 1) ? 'active-pagination' : '';
  //           $html .= '<li class="'.$active.'" data-page="'.$page.'">'.$numb.'</li>';
  //           $numb++;
  //       }                
  //   }
  //   $html .= '</ul></div>';
  // }
?>

<div id="fr-content" class="px-4" data-user-profile="<?php echo $helper->profileUser->data->ID; ?>">
	<ul class="nav nav-tabs justify-content-end">
	  <li class="nav-item">
	    <button class="nav-link" data-view="list"><i class="fas fa-list"></i></button>
	  </li>
	  <li class="nav-item">
	    <button class="nav-link active" data-view="grid"><i class="fas fa-th"></i></button>
	  </li>
	</ul>
	<div class="fr-content">
		<div class="d-none row p-4" id="fr-list">
				<?php echo $template->reportListTemplate($query); ?>
		</div>
		<div class="row p-4" id="fr-grid">
				<?php echo $template->reportGridTemplate($query); ?>
		</div>
	</div>	
	<div id="reportPagination" class="my-4 text-center">
		<?php		
		echo $helper->report_pagination($query);
		wp_reset_postdata(); ?>
	</div>
	<?php echo $html; ?>
</div>