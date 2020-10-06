<?php 

class Portaledukacyjny_addSlide {
    
    private $id = NULL;
    private $slide_url = NULL;
    private $title = NULL;
    private $label = NULL;
    private $go_to_course_url = NULL;
    private $position = NULL;
    private $published = 'Nie';
    
    private $validation_errors = array();
    
    private $exist_instance = FALSE;
    
    
    function __construct($id = NULL) {
        $this->id = $id;
        $this->getFromDB();
    }
    
    
    private function getFromDB() {
        if(isset($this->id)){
            $db_model= new Portaledukacyjny_db_model();
            $single_row = $db_model->getRow($this->id);
            
            if(isset($single_row)){
                $this->setFields($single_row);
                $this->exist_instance = TRUE;
            }
        }
    }
    
    public function ifExistInstance(){
        return $this->exist_instance;
    }
    
    function getField($field){
        if(isset($this->{$field})){
            return $this->{$field};
        }
        return NULL;
    }
    
    function ifIdExist() {
        return isset($this->id);
    }
    
    function isPublished(){
        return ($this->published == 'Tak');
    }
    
    function setFields($fields){
        
        foreach($fields as $key => $val){
            $this->published = 'Nie'; // jeśli w inpucie nie zaznaczymy checkbox to przypisz domyślną wartość NIE dla klucza published
            $this->{$key} = $val;
        }
        
    }
    
    function setValidationError($field, $error_msg){
        $this->validation_errors[$field] = $error_msg;
    }
    
    function getValidationError($field){
        if(isset($this->validation_errors[$field])){
            return $this->validation_errors[$field];
        }
        return NULL;
    }
    
    
    function hasValidationError($field){
        return isset($this->validation_errors[$field]);
    }   
    
    function hasValidationErrors(){
        return (count($this->validation_errors) > 0);
    }
    
    function form_validate(){
        
        /* pole slide_url */
        if(empty($this->slide_url)){
            $this->setValidationError('slide_url', 'Uzupełnij to pole');
        }else
        if(!filter_var($this->slide_url, FILTER_VALIDATE_URL)){
            $this->setValidationError('slide_url', 'Podaj poprawny adres URL');
        }else
        if(strlen($this->slide_url) > 255){
            $this->setValidationError('slide_url', 'To pole może mieć maksymalnie 255 znaków');
        }
        
        /* pole title */
        if(empty($this->title)){
            $this->setValidationError('title', 'Uzupełnij to pole');
        }else
        if(strlen($this->slide_url) > 255){
            $this->setValidationError('title', 'To pole może mieć maksymalnie 255 znaków');
        }
        
        /* pole label */
        if(!empty($this->label)){
            $available_tags = array(
                'strong' => array(),
                'b' => array()
            );
            
            $this->label = wp_kses($this->label, $available_tags);
            
            if(strlen($this->label) > 255){
            $this->setValidationError('label', 'To pole może mieć maksymalnie 255 znaków');
            }
        }
        
        /* pole go_to_course_url */
        if(!empty($this->go_to_course_url)){
            
            $this->go_to_course_url = esc_url($this->go_to_course_url);
            
            if(strlen($this->go_to_course_url) > 255){
            $this->setValidationError('go_to_course_url', 'To pole może mieć maksymalnie 255 znaków');
            }
        }
        
        
        /* pole position */
        if(empty($this->position)){
            $this->setValidationError('position', 'Uzupełnij to pole');
        }else{
            $this->position = (int)$this->position;
            if($this->position < 1){
                $this->setValidationError('position', 'To pole musi być liczbą większą od 0');
        }
        }
        
        
        /* pole published */
        if(isset($this->published) && $this->published == 'Tak'){
            $this->published = 'Tak';
        }else{
            $this->published = 'Nie';
        }
            
        return (!$this->hasValidationErrors());
    }
    
    
}