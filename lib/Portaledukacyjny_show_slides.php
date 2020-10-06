<?php

    function portaledukacyjny_show_slides(){
        $db_model = new Portaledukacyjny_db_model();
        
        $slides_to_show =  $db_model->getPublicSlides();
        
        
        if(!empty($slides_to_show)){
            foreach($slides_to_show as $slide) {
?>
    
    
		<div class="single-slide">
				<div class="title-slide">
					<div class="div1"></div>
					<div class="div2"> <h3><?php echo $slide->title; ?></h3></div>
					<div class="div3"><img src="http://localhost/inzynierka/wp-content/uploads/2019/12/poland.png" /></div>
				</div>
				<div class="content-slide">
					<div class="left">
						<div class="label-slide">
							<?php echo $slide->label; ?>
						</div>
						<div class="btn-slide animated pulse infinite">
							<?php if(!empty($slide->go_to_course_url)): ?>
							  <a href="<?php echo $slide->go_to_course_url; ?>">Przejdź do kursu</a>
							<?php endif; ?>
						</div>
					</div>
					<div class="right">
							<img src="<?php echo $slide->slide_url; ?>" alt="">
					</div>
				</div>
				<div class="video-slide">
					<p><strong> Kurs Wideo </strong> </p>
					<a href="<?php echo $slide->go_to_course_url; ?>">
						<img src="http://localhost/inzynierka/wp-content/uploads/2019/12/video.png" />
					</a>
				</div>
				
		 </div>
        
<?php
            }
        }
    }


?>