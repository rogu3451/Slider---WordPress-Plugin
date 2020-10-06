(function($){
 
    $(document).ready(function(){
                      
         $('#portaledukacyjny-position').keyup(function(){
             var $this = $(this);
             
             $('#db-info').text('Trwa sprawdzanie pozycji...');
             
             var post_data = {
                 position: $this.val(),
                 action: 'checkAvailablePosition'
             };
             
             $.post(ajaxurl, post_data, function(result){
                 
                $('#db-info').text(result); 
             });
             
         });    
        
        
        $('#get-last-pos').click(function(){
             
             
             $('#db-info').text('Trwa pobieranie pozycji...');
             
             var get_data = {
                 action: 'getLastAvailablePosition'
             };
             
             $.get(ajaxurl, get_data, function(result){
                 
                 $('#portaledukacyjny-position').val(result); 
                 $('#db-info').text('Pobrano ostatnią wolną pozycję');
             });
             
         });    
        
        
         window.send_to_editor = function(html){
            
            var img_url = $(html).attr('src');
             
            $('#portaledukacyjny-slide-url').val(img_url);
            tb_remove();
            
            
            var $prevImg = $('<img>').attr('src', img_url);
            $('#slide-preview').empty().append($prevImg);
            
            
        }
        
        
        $('#portaledukacyjny-slide-btn').click(function(){
           
            var url = 'media-upload.php?TB_iframe=true&type=image';
            
            tb_show('Wybierz slajd', url, false);
            
            return false;
        });
    });   
 
 })(jQuery);