<div class="slider_admin">
    <h1> 
        <a  style="text-decoration: none;" href="<?php echo $this->getAdminPanelUrl(); ?>">Portal Edukacyjny - Slider</a>
        <a class="button-primary" href="<?php echo $this->getAdminPanelUrl(array('view' => 'form')); ?>">Dodaj nowy slajd</a>
    </h1>
    
    <?php if($this->hasMessage()): ?>
        <div id="message" class="<?php echo $this->getMessageState(); ?>">
            <p><?php echo $this->getMessage(); ?></p>
        </div>
    <?php endif; ?>

    <?php require_once $view; ?>
    
    <br style="clear: both;">
</div>