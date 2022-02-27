<?php
	include_once FFRCRUD_PATH.'classes/Helper.php';
	$user = uwp_get_displayed_user();
	$help = new Helper;
	//if($user && ($user->roles[0] === 'vip-member' || $user->roles[0] === 'administrator')) {

	$args = [
	    'author' => $user->data->ID,
	    'post_type' => 'field-report',
	    'order' => 'DESC',
	    'posts_per_page' => 6,
	    'offset' => 0
	];

	$query = get_posts($args);
	if(count($query) === 0) return;
	$countReports = count($query);
	// $initial = ($countReports > 6 ) ? array_slice($query, 0, 6) : $query;

	// Display Loader
	$html = '<div class="field-reports-loading d-none"><i class="fas fa-circle-notch"></i></div>';
	
  // Get Pagination
  if($countReports > 6) {
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

<div id="fr-content" class="px-4">
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
<?php	foreach ($query as $post) :
				$images = get_field('images',$post->ID); 
				$image = wp_get_attachment_image_src($images[0]['image'],'medium')[0]; ?>
					<div class="col-12 mb-4 report<?php echo $post->ID; ?> report-item">
				    <div class="card">
				      <div class="card-body d-flex px-2">
				      	<?php if($help->checkUser()) { ?>
				      	<div class="fr-action-wrap">
				      		<span class="fr-edit" title="Edit"><i class="fas fa-pencil-alt"></i></span>
				      		<span class="fr-delete"
				      		  data-post-id="<?php echo $post->ID; ?>"
				      		  data-post-title="<?php echo $post->post_title; ?>"
				      		  title="Delete" 
				      		  data-toggle="modal" 
				      		  data-target="#deleteReport">
				      		  	<i class="fas fa-times"></i>
				      		</span>
				      	</div>
				      	<?php } ?>
				      	<div class="fr-thumbnail col-3">
				      		<div class="fr-d-grid">
				      		<?php
				      		$images = get_field('images',$post->ID);
				      		$imageCount = count($images);
				      		$images = ($imageCount > 8 ) ? array_slice($images, 0, 8) : $images;
				      		foreach($images as $image) : 
									$img = wp_get_attachment_image_src($image['image'],'medium'); ?>
									<div class="img-thumb-wrap">
									<img class="img-thumbnail" src="<?php echo $img[0]; ?>" alt="<?php echo $post->post_title; ?>"></div>
								<?php endforeach; 
								  	if($imageCount > 7){
								  		echo '<div class="img-thumb-wrap fr-link"><a href="'.$post->guid.'">'.$imageCount.'+</a></div>';
								  	}
								 ?>							  
				      	</div>
				      	</div>
				        <div class="fr-content col-9">
				        	<h5 class="card-title fr-link"><?php echo '<a href="'.$post->guid.'">'.$post->post_title.'</a>'; ?></h5>
					        <p class="card-text fr-link"><?php echo wp_trim_words( $post->post_excerpt, $num_words = 120, '... <a class="mt-3 float-right" href="'.$post->guid.'"> Read More ></a>' ) ?></p>
				        </div>
				      </div>
				    </div>
				  </div>
<?php endforeach; // Posts ?>
		</div>
		<div class="row p-4" id="fr-grid">
<?php foreach ($query as $post) :	?>
				<div class="col-4 mb-4 report<?php echo $post->ID; ?> report-item">
			    <div class="card">
			      <div class="card-body p-4 position-relative">
			      	<?php if($help->checkUser()) { ?>
			      	<div class="fr-action-wrap">
			      		<span class="fr-edit" title="Edit"><i class="fas fa-pencil-alt"></i></span>
			      		<span class="fr-delete"
			      		  data-post-id="<?php echo $post->ID; ?>"
			      		  data-post-title="<?php echo $post->post_title; ?>"
			      		  title="Delete" 
			      		  data-toggle="modal" 
			      		  data-target="#deleteReport">
			      		  	<i class="fas fa-times"></i>
			      		</span>
			      	</div>
			      	<?php } ?>
			      	<div class="fr-d-grid">
			      		<?php
			      		$images = get_field('images',$post->ID);
			      		$imageCount = count($images);
			      		$images = ($imageCount > 8 ) ? array_slice($images, 0, 8) : $images;
			      		foreach($images as $image) : 
								$img = wp_get_attachment_image_src($image['image'],'medium'); ?>
								<div class="img-thumb-wrap">
								<img class="img-thumbnail" src="<?php echo $img[0]; ?>" alt="<?php echo $post->post_title; ?>"></div>
							<?php endforeach; 
							  	if($imageCount > 7){
							  		echo '<div class="img-thumb-wrap fr-link"><a href="'.$post->guid.'">'.$imageCount.'+</a></div>';
							  	}
							 ?>							  
			      	</div>			      		
			        <h5 class="card-title fr-link"><?php echo '<a href="'.$post->guid.'">'.$post->post_title.'</a>'; ?></h5>
				      <p class="card-text fr-link"><?php echo wp_trim_words( $post->post_excerpt, $num_words = 25, '... <br><a class="mt-3 float-right" href="'.$post->guid.'"> Read More ></a>' ) ?></p>
			      </div>
			    </div>
			  </div>
<?php endforeach; ?>
		</div>
	</div>
	<div class="my-4">
		<?php echo $html; wp_reset_postdata(); ?>
	</div>
</div>