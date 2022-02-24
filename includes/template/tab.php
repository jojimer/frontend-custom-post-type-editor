<?php
	$user = uwp_get_displayed_user();
	if($user && $user->roles[0] === 'vip-member') {

	$args = [
	    'author' => $user->data->ID,
	    'post_type' => 'field-report',
	    'order' => 'DESC',
	    'posts_per_page' => -1,
	];

	$query = get_posts($args);
	if(count($query) === 0) return;
	$countReports = count($query);
	$initial = ($countReports > 3 ) ? array_slice($query, 0, 4) : $query;
	wp_reset_postdata();

	// Display Loader
	$html = '<div class="field-reports-loading d-none"><i class="fas fa-circle-notch"></i></div>';
	
  // Get Pagination
  if($countReports > 4) {
    $html .= '<div id="field-reports-pagination"><ul>';
    $numb = 1;            
    for($i = 0; $i < $countReports; $i++){
        if(!($i % 4 != 0)){
            $page = 3+$i;         
            $active = ($numb === 1) ? 'active-pagination' : '';
            $html .= '<li class="'.$active.'" data-page="'.$page.'">'.$numb.'</li>';
            $numb++;
        }                
    }
    $html .= '</ul></div>';
  }
?>

<div id="fr-content" class="px-3">
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
			<?php	foreach ($initial as $key => $value) { ?>
				<div class="col-12 mb-4">
			    <div class="card">
			      <div class="card-body d-flex">
			      	<div class="fr-thumbnail col-3">
			      		<img class="img-thumbnail" src="https://mmagazine.local/app/uploads/2021/10/zion-canyon1-scaled.jpg" alt="">
			      	</div>
			        <div class="fr-content col-9">
			        	<h5 class="card-title">Card title</h5>
				        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
			        </div>
			      </div>
			    </div>
			  </div>
			 <?php } ?>
		</div>
		<div class="row p-4" id="fr-grid">
			<?php	foreach ($initial as $key => $value) { ?>
				<div class="col-4 mb-4">
			    <div class="card">
			      <div class="card-body">
			      		<img class="img-thumbnail" src="https://mmagazine.local/app/uploads/2021/10/zion-canyon1-scaled.jpg" alt="">
			        	<h5 class="card-title">Card title</h5>
				        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
			      </div>
			    </div>
			  </div>
			 <?php } ?>
		</div>
	</div>
	<div class="my-4">
		<?php echo $html; ?>
	</div>
</div>

<?php }