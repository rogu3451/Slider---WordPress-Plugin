<?php
ob_start();
session_start();
    /*
        Plugin Name: Portal Edukacyjny - Slider
        Plugin URI:
        Description: Plugin do zarządzania elementami slidera dla portalu edukacyjnego.
        Version: 1.0
        Author: Karol Rogoziński
        Author URI: https://www.facebook.com/karol.rogozinski.3
        Text Domain: Portal Edukacyjny
    */
    
    require_once 'lib/Portaledukacyjny_db_model.php';
    require_once 'lib/Portaledukacyjny_addSlide.php';
    require_once 'lib/Portaledukacyjny_show_slides.php';
    require_once 'lib/Portaledukacyjny_pagination.php';
    require_once 'lib/Request.php';

    class PortaledukacynySlider {
        
        private static $id = 'portaledukacyny_slider';
        private $version = '1.0.0';
        private $user_capabitlity = 'manage_options';
        private $db_model;
        private $token = 'portaledukacyny_action';
        private $paginate_limit = 3;
        
        function __construct() {
            $this->db_model = new Portaledukacyjny_db_model();
            
            //wywołaj w czasie aktywacji
            register_activation_hook(__FILE__, array($this, 'onActivate')); 
            
            
            //wywołaj w czasie odinstalowywania pluginu
            register_uninstall_hook(__FILE__, array('PortaledukacynySlider', 'uninstallPlugin'));
            
            //rejestracja w menu przycisku
            add_action('admin_menu',array($this,'createAdminPanel'));
            
            //rejestracja skryptow w panelu administratora
            add_action('admin_enqueue_scripts',array($this,'addAdminScripts'));
            
            //rejestracja funkcji AJAX
            add_action('wp_ajax_checkAvailablePosition',array($this,'checkFreePosition'));
            add_action('wp_ajax_getLastAvailablePosition',array($this,'getLastAvailablePosition'));
            
            
        }
        
        function addAdminScripts(){
            
            wp_register_script(
                'portaledukacyjny-script', 
                plugins_url('/js/scripts.js', __FILE__), 
                array('jquery','media-upload','thickbox')
            );
            
            if(get_current_screen()->id == 'toplevel_page_'.static::$id)
            {
                
                wp_enqueue_script('jquery');
                
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
            
                wp_enqueue_script('media-upload');
                
                wp_enqueue_script('portaledukacyjny-script');
                
            }
            
        }
        
        
        function checkFreePosition(){
                $position = isset($_POST['position']) ? (int)($_POST['position']): 0;
                $message = '';
            
                if($position < 1){
                    $message = 'Podana wartość jest nieprawidłowa. Podaj liczbę większą od 0.';
                }
                else
                {
                    if(!$this->db_model->checkAvailablePosition($position)){
                        $message = 'Podana pozycja jest zajęta';
                    }
                    else
                    {
                         $message = 'Pozycja jest wolna';
                    } 
                }
                echo $message;
                die;
        }
        
        
        function getLastAvailablePosition(){
            echo $this->db_model->getLastAvailablePosition();
            die;
        }
        
        
        static function uninstallPlugin(){
            $db_model = new Portaledukacyjny_db_model();
            $db_model->dropTable();
            
            $plugin_version = static::$id.'-version';
            delete_option($plugin_version);
        }
        
        
        
        
        function onActivate(){
            $ver_opt = static::$id.'-version';
            $installed_version = get_option($ver_opt, NULL);
            
            if($installed_version == NULL){
                $this->db_model->createDbTable();
                update_option($ver_opt, $this->version);
            }
            else
            {
                switch(version_compare($installed_version, $this->version)) {
                    case 0:
                        // wersja zainstalowana jest taka sama
                        break;
                    case 1:
                        // wersja zainstalowana jest nowsza od tej
                        break;
                    case -1:
                        // wersja zainstalowana jest starsza od tej
                        break;
                    
                }
            }
        }
        
        
        function createAdminPanel(){
            add_menu_page('Portal Edukacyjny - Slider','Slider Portal Edu',$this->user_capabitlity, static::$id, array($this, 'printAdminPanel'));
        }
        
        function printAdminPanel(){
            
            $request = Request::instance();
            
            $view = $request->getQuerySingleParam('view', 'index');
            $action = $request->getQuerySingleParam('action');
            $slideid = (int)$request->getQuerySingleParam('slideid');
            
            switch($view){
                case 'index':
                    
                    if($action == 'delete'){
                        
                        $token = $this->action_token.$slideid;
                        $wp_nonce = $request->getQuerySingleParam('_wpnonce',NULL);
                        
                        if(wp_verify_nonce($wp_nonce, $token)){
                            
                            if($this->db_model->deleteSlide($slideid) !== FALSE){
                                $this->setMessage('Slajd został usunięty');
                            }else{
                                $this->setMessage('Błąd usuwania slajdu','error');
                            }
                        }
                        else
                        {
                            $this->setMessage('Token nie został poprawnie zweryfikowany','error');
                        }
                        
                        $this->redirect($this->getAdminPanelUrl());
                    }
                    else
                    {
                        if($action == 'mass'){
                            
                            if($request->isMethod('POST') && check_admin_referer($this->action_token.'mass')) {
                                
                                $mass_action = (isset($_POST['massaction'])) ? $_POST['massaction'] : NULL;
                                $mass_check = (isset($_POST['masscheck'])) ? $_POST['masscheck'] : array();
                                
                                if(count($mass_check) < 1){
                                    $this->setMessage('Nie znaleziono slajdów do modyfikacji','error');
                                }else{
                                    if($mass_action == 'delete'){
                                        if($this->db_model->massDelete($mass_check) !== FALSE) {
                                            $this->setMessage('Zaznaczone slajdy zostały pomyślnie usunięte'); 
                                        }
                                            else
                                            {
                                            $this->setMessage('Zaznaczone slajdy nie zostały pomyślnie usunięte','error'); 
                                            }
                                        }
                                    
                                    if($mass_action == 'public' || $mass_action == 'private'){
                                        if($this->db_model->massPublicChange($mass_check, $mass_action) !== FALSE) {
                                            $this->setMessage('Widoczność zaznaczonych slajdów została zmieniona'); 
                                        }
                                            else
                                            {
                                            $this->setMessage('Widoczność zaznaczonych slajdów nie została zmieniona','error'); 
                                            }
                                        }
                                    
                                   
                                }
                            }
                            $this->redirect($this->getAdminPanelUrl());
                        }
                        
                        
                    }
                    
                    
                    $curr_page = (int)$request->getQuerySingleParam('paged',1);
                    $order_by = $request->getQuerySingleParam('orderby', 'id');
                    $order_dir = $request->getQuerySingleParam('orderdir', 'asc');
                    
                    
                    $pagination = $this->db_model->getPagination($curr_page, $this->paginate_limit, $order_by, $order_dir);
                    
                    $this->renderView('index', array(
                        
                        'pagination' => $pagination
                    
                    ));
                    break;
                
                case 'form':
                    
                    if($slideid > 0)
                    {
                         $addSlide = new Portaledukacyjny_addSlide($slideid);
                         
                        if(!$addSlide->ifExistInstance()){
                            $this->setMessage('Taki slajd nie istnieje', 'error');
                            $this->redirect($this->getAdminPanelUrl());
                        }
                    }
                    else
                    {
                         $addSlide = new Portaledukacyjny_addSlide();
                    }
                    
                   
                    
                    if($action == 'save_to_db' && $request->isMethod('POST') && isset($_POST['entry'])) {
                       
                        
                        if(check_admin_referer($this->token)){
                            
                            
                            
                            $addSlide -> setFields($_POST['entry']);
                            
                            
                            
                            if($addSlide -> form_validate()){

                                
                                $slide_id = $this->db_model->saveSlide($addSlide);
                                if($slide_id !== FALSE){
                                    
                                    if($addSlide->ifIdExist())
                                    {
                                        $this->setMessage('Slajd został zmodifikowany');
                                    }
                                    else
                                    {
                                       $this->setMessage('Slajd został dodany'); 
                                    }
                                    $this->redirect($this->getAdminPanelUrl(array('view'=>'form', 'slideid' => $slide_id)));
                                    
                                }else{
                                    $this->setMessage('Nie wprowadzono żadnych zmian', 'error');
                                }
                            }
                            else
                            {

                                 $this->setMessage('Uzupełnij poprawnie formularz', 'error');
                            }
                            
                        }
                        else
                        {
                             $this->setMessage('Token formularza jest niepoprawny', 'error');
                        }
                        
                    }
                    
                    $this->renderView('form', array(
                        'AddSlide' => $addSlide
                    ));
                    break;
                    
                default:
                    $this->renderView('404');
                    break;
                    
            }
            
        }
        
        private function renderView($view, array $args = array()){
            
            extract($args);
            
            $template_path = plugin_dir_path(__FILE__).'templates/';
            $view = $template_path.$view.'.php';
            require_once $template_path.'main_template.php';
        }
        
        public function getAdminPanelUrl(array $args = array()){
            $url = admin_url('admin.php?page='.static::$id);
            $url = add_query_arg($args, $url);
            
            return $url;
        }
        
         public function setMessage($msg, $state = 'updated'){
             $_SESSION[__CLASS__]['message'] = $msg;
             $_SESSION[__CLASS__]['state'] =  $state;
         }
        
         public function getMessage(){
             if(isset($_SESSION[__CLASS__]['message'])){
                 $msg = $_SESSION[__CLASS__]['message'];
                 unset($_SESSION[__CLASS__]);
                 return $msg;
             }
             
             return NULL;
         }
        
        public function getMessageState(){
            if(isset($_SESSION[__CLASS__]['state'])){
                 return $_SESSION[__CLASS__]['state'];
             }
             return NULL;
        }
        
        public function hasMessage(){
            return isset($_SESSION[__CLASS__]['message']);
        }
        
        
        public function redirect($location){
            wp_safe_redirect($location);
            exit;
        }
        
        
    }

    $PortaledukacynySlider = new PortaledukacynySlider();
ob_flush(); 
?>