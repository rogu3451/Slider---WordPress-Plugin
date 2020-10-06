<?php

class Portaledukacyjny_pagination {
    
    private $items;
    
    private $order_by;
    private $order_dir;
    
    private $limit;
    private $total_count;
    
    private $curr_site;
    private $last_site;
    
    
    function __construct($items, $order_by, $order_dir, $limit, $total_count, $curr_site, $last_site){
        $this->items = $items;
        $this->order_by = $order_by;
        $this->order_dir = $order_dir;
        $this->limit = $limit;
        $this->total_count = $total_count;
        $this->curr_site = $curr_site;
        $this->last_site = $last_site;
    }
    
    public function hasItems(){
        return !empty($this->items);
    }
    
    public function getItems(){
        return $this->items;
    }
    
    public function getOrderBy(){
        return $this->order_by;
    }
    
    public function getOrderDir(){
        return $this->order_dir;
    }
    
    public function getLimit(){
        return $this->limit;
    }
    
    public function getTotalCount(){
        return $this->total_count;
    }
    
    public function getCurrSite(){
        return $this->curr_site;
    }
    
    public function getLastSite(){
        return $this->last_site;
    }
    
    
    
    
    
}