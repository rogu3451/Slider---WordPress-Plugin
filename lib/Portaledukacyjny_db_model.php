<?php

class Portaledukacyjny_db_model {
    private $table_name = 'portaledukacyny_slider_db';
    private $wpdb;
    
    function __construct() {
            global $wpdb;
            $this->wpdb = $wpdb;
    }
    
    function getTableName() { // Zwraca nazwe tabeli wraz z prefiksem Wordpressa
        return $this->wpdb->prefix.$this->table_name;
    }
    
    function createDbTable() {
        
        $table_name = $this->getTableName();
        
        $sql_query = '
            CREATE TABLE IF NOT EXISTS '.$table_name.'(
                id INT NOT NULL AUTO_INCREMENT,
                slide_url VARCHAR(255) NOT NULL,
                title VARCHAR(255) NOT NULL,
                label VARCHAR(255) DEFAULT NULL,
                go_to_course_url VARCHAR(255) DEFAULT NULL,
                position INT NOT NULL,
                published enum("Tak", "Nie") NOT NULL DEFAULT "Nie",
                PRIMARY KEY(id)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8';
        
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        
        dbDelta($sql_query);
        
        
    }
    
    
    function checkAvailablePosition($position){
        $position = (int)$position;
        $table_name = $this->getTableName();
        
        $sql_query = "SELECT COUNT(*) FROM {$table_name} WHERE position = %d";
        $prepare_statement = $this->wpdb->prepare($sql_query, $position);
        
        $quantity = (int)$this->wpdb->get_var($prepare_statement);
        
        return ($quantity<1);
    }
    
    function getLastAvailablePosition(){
        $table_name = $this->getTableName();
        $sql_query = "SELECT MAX(position) FROM {$table_name}";
        $free_position = (int)$this->wpdb->get_var($sql_query);
        
        return ($free_position+1);
    }
    
    function saveSlide(Portaledukacyjny_addSlide $slide){
        
        $save_to_db = array(
            'slide_url' => $slide->getField('slide_url'),
            'title' => $slide->getField('title'),
            'label' => $slide->getField('label'),
            'go_to_course_url' => $slide->getField('go_to_course_url'),
            'position' => $slide->getField('position'),
            'published' => $slide->getField('published'),
        );
        $maping = array('%s','%s','%s','%s','%d','%s');
        $table_name = $this->getTableName();
        
       
        if($slide->ifIdExist()){
            if($this->wpdb->update($table_name, $save_to_db, array('id' => $slide->getField('id')), $maping, '%d'))
            {
                return $slide->getField('id');
            }
            else
            {
                return FALSE;
            }
            
        }
        else
        {
            if($this->wpdb->insert($table_name, $save_to_db, $maping)){
            return $this->wpdb->insert_id;
            }
            else
            {
            return FALSE;
            }
        }
        
        
        
    }
    
    function getRow($id){
        $table_name = $this->getTableName();
        $sql_query = "SELECT * FROM {$table_name} WHERE id = %d";
        $prepare_statement = $this->wpdb->prepare($sql_query, $id);
        return $this->wpdb->get_row($prepare_statement);
    }
    
    function getPagination($curr_site, $limit='10', $order_by='id', $order_dir='asc')
    {
        $curr_site = (int)$curr_site;
            if($curr_site<1){
                $curr_site = 1;
            }
        $limit = (int)$limit;
        
        $order_options = static::getOrderOptions();
        $order_by = (!in_array($order_by, $order_options)) ? 'id' : $order_by;
        
        $order_dir = in_array($order_dir, array('asc','desc')) ? $order_dir : 'asc';
        
        $offset = ($curr_site-1)*$limit;
        
        $table_name = $this->getTableName();
        
        $count_query = "SELECT COUNT(*) FROM {$table_name}";
        $count = $this->wpdb->get_var($count_query);
        
        $last_site = ceil($count/$limit);
        
        
        
        $sql_query = "SELECT * FROM {$table_name} ORDER BY {$order_by} {$order_dir} LIMIT {$offset}, {$limit}";
        
        $slide_list = $this->wpdb->get_results($sql_query);
        
        $pagination = new Portaledukacyjny_pagination($slide_list, $order_by, $order_dir, $limit, $count, $curr_site, $last_site);
        
        return $pagination;

    }
    
    
    function deleteSlide($slideid){
        $slideid = (int)$slideid;
        $table_name = $this->getTableName();
        $sql_query = "DELETE FROM {$table_name} WHERE id= %d";
        $prepare_statement = $this->wpdb->prepare($sql_query, $slideid);
        
        return $this->wpdb->query($prepare_statement);
        
    }
    
    
    function massDelete(array $mass_delete_list)
    {
        $mass_delete_list = array_map('intval', $mass_delete_list);
        $table_name = $this->getTableName();
        $list_to_string = implode(',', $mass_delete_list);
        $sql_query = "DELETE FROM {$table_name} WHERE id IN({$list_to_string})";
        
 
        return $this->wpdb->query($sql_query);
        
    }
    
    
    function massPublicChange(array $mass_change_list, $state_to_change){
        $mass_change_list = array_map('intval', $mass_change_list);
        $state='';
        

        switch($state_to_change){
            default:
            case 'public': $state = 'Tak'; break;
            case 'private': $state = 'Nie'; break;
        }
           
        $table_name = $this->getTableName();
        $list_to_string = implode(',', $mass_change_list);
        $sql_query = "UPDATE {$table_name} SET published='{$state}' WHERE id IN({$list_to_string})";
        
        return $this->wpdb->query($sql_query);
    }
    
    
    function getPublicSlides() {
        $table_name = $this->getTableName();
        $sql_query = "SELECT * FROM {$table_name} WHERE published='Tak' ORDER BY position";
        
        return $this->wpdb->get_results($sql_query);
        
    }
    
    
    function dropTable(){
        $table_name = $this->getTableName();
        $sql_query = "DROP TABLE {$table_name}";
        return $this->wpdb->query($sql_query);
    }
    
    static function getOrderOptions(){
        return array(
        'ID' => 'id',
        'Pozycja' => 'position',
        'Widoczność' => 'published'
        );
    }

}