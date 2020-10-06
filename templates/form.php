<?php
    $form_args = array('view' => 'form', 'action'=>'save_to_db');

    if($AddSlide->ifIdExist()) {
        $form_args['slideid'] = $AddSlide->getField('id');
    }
?>               

<form action="<?php echo $this->getAdminPanelUrl($form_args); ?>" method="post" id="portaledukacyjny-slide-form">

    <?php wp_nonce_field($this->token); ?>
    
    <table class="form-table">
    
        <tbody>
        
            <tr class="form-field">
                <th>
                    <label for="portaledukacyjny-slide-url">Slajd:</label>
                </th>
                <td>
                    <a class="button-secondary" id="portaledukacyjny-slide-btn">Wybierz slajd z biblioteki mediów</a>
                    <input type="hidden" name="entry[slide_url]" id="portaledukacyjny-slide-url" value="<?php echo $AddSlide->getField('slide_url'); ?>"/>
                    
                    <?php if($AddSlide->hasValidationError('slide_url')): ?>
                    <p class="description error"><?php echo $AddSlide->getValidationError('slide_url'); ?></p>
                    <?php else: ?>
                    <p class="description">To pole jest wymagane</p>
                    <?php endif; ?>
                    
                    <p id="slide-preview">
                        <?php if($AddSlide->getField('slide_url')!=NULL): ?>
                            <img src="<?php echo $AddSlide->getField('slide_url'); ?>" alt="" />
                        <?php endif;?>
                    </p>
                </td>
            </tr>
            
            
            <tr class="form-field">
                <th>
                    <label for="portaledukacyjny-title">Tytuł:</label>
                </th>
                <td>
                    <input type="text" name="entry[title]" id="portaledukacyjny-title" value="<?php echo $AddSlide->getField('title'); ?>"/>
                     <?php if($AddSlide->hasValidationError('title')): ?>
                    <p class="description error"><?php echo $AddSlide->getValidationError('title'); ?></p>
                    <?php else: ?>
                    <p class="description">To pole jest wymagane</p>
                    <?php endif; ?>
                </td>
            </tr>
            
            
             <tr class="form-field">
                <th>
                    <label for="portaledukacyjny-label">Etykieta:</label>
                </th>
                <td>
                    <input type="text" name="entry[label]" id="portaledukacyjny-label" value="<?php echo $AddSlide->getField('label'); ?>"/>
                     <?php if($AddSlide->hasValidationError('label')): ?>
                    <p class="description error"><?php echo $AddSlide->getValidationError('label'); ?></p>
                    <?php else: ?>
                    <p class="description">To pole jest opcjonalne</p>
                    <?php endif; ?>
                   
                </td>
            </tr>
            
            <tr class="form-field">
                <th>
                    <label for="portaledukacyjny-go-to-course-url">Link "Przejdź do kursu":</label>
                </th>
                <td>
                    <input type="text" name="entry[go_to_course_url]" id="portaledukacyjny-go-to-course-url" value="<?php echo $AddSlide->getField('go_to_course_url'); ?>"/>
                     <?php if($AddSlide->hasValidationError('go_to_course_url')): ?>
                    <p class="description error"><?php echo $AddSlide->getValidationError('go_to_course_url'); ?></p>
                    <?php else: ?>
                    <p class="description">To pole jest opcjonalne</p>
                    <?php endif; ?>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label for="portaledukacyjny-position">Pozycja:</label>
                </th>
                <td>
                    <input type="text" name="entry[position]" id="portaledukacyjny-position" value="<?php echo $AddSlide->getField('position'); ?>"/>
                    <a class="button-secondary" id="get-last-pos">Pobierz ostatnią wolną pozycję</a>
                    
                    <?php if($AddSlide->hasValidationError('position')): ?>
                    <p id="db-info" class="description error"><?php echo $AddSlide->getValidationError('position'); ?></p>
                    <?php else: ?>
                    <p id="db-info" class="description">To pole jest wymagane</p>
                    <?php endif; ?>
                    
                </td>
            </tr>
            
            <tr class="form-field">
                <th>
                    <label for="portaledukacyjny-published">Opublikowany:</label>
                </th>
                <td>
                    <input type="checkbox" name="entry[published]" id="portaledukacyjny-published" value="Tak" <?php echo ($AddSlide->isPublished()) ? 'checked="checked"' : '';?>/>
                    
                </td>
            </tr>
        
        </tbody>
    
    </table>
    
    <p class="submit">
        <a href="<?php echo $this->getAdminPanelUrl(); ?>" class="button-secondary">Wstecz</a>
        &nbsp;
        <input type="submit" class="button-primary" value="Zapisz zmiany" />
    </p>

</form>