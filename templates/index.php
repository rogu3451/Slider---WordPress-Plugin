<form method="get" action="<?php echo $this->getAdminPanelUrl(); ?>" id="sortby">
    
    <input type="hidden" name="page" value="<?php echo static::$id; ?>">
    <input type="hidden" name="paged" value="<?php echo $pagination->getCurrSite(); ?>">
    
    Sortuj według
    <select name="orderby">
        <?php foreach(Portaledukacyjny_db_model::getOrderOptions() as $key=>$val): ?>
            <option 
                    <?php echo($val == $pagination->getOrderBy()) ? 'selected="selected"' : ''; ?>
                    value="<?php echo $val;?>">
                <?php echo $key;?>
            </option>
        <?php endforeach; ?>
        
    </select> 
    
    <select name="orderdir">
        <?php if($pagination->getOrderDir() == 'asc') : ?>
            <option selected="selected" value="asc">Rosnąco</option>
            <option value="desc">Malejąco</option>
        <?php else: ?>
            <option value="asc">Rosnąco</option>
            <option selected="selected" value="desc">Malejąco</option>
        <?php endif; ?>
    </select>
    
    <input type="submit" class="button-secondary" value="Sortuj" />

</form>

<form action="<?php echo $this->getAdminPanelUrl(array('view'=>'index', 'action'=> 'mass'))?>" method="post" id="bulkActivities" onsubmit="return confirm('Czy napewno chcesz dokonać masowych działań?')">

    
    
    <?php wp_nonce_field($this->action_token.'mass'); ?>
    
    
    <div class="tablenav">
        
        <div class="alignleft actions">
        
            <select name="massaction">
                <option value="0">Masowe działania</option>
                <option value="delete">Usuń</option>
                <option value="public">Publiczny</option>
                <option value="private">Prywatny</option>
            </select>
            
            <input type="submit" class="button-secondary" value="Zastosuj" />
            
        </div>
        
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $pagination->getTotalCount(); ?> slajdy</span>
            
            <?php
            
                $curr_site = $pagination->getCurrSite();
                $last_site = $pagination->getLastSite();
            
                $first_disabled = '';
                $last_disabled = '';
            
                $url_args = array(
                    'orderby' => $pagination->getOrderBy(),
                    'orderdir' => $pagination->getOrderDir()
                );
            
                $url_args['paged'] = 1;
                $first_site_url = $this->getAdminPanelUrl($url_args);
            
            
                $url_args['paged'] = $curr_site-1;
                $prev_site_url = $this->getAdminPanelUrl($url_args); 
            
                $url_args['paged'] = $last_site;
                $last_site_url = $this->getAdminPanelUrl($url_args);
            
            
                $url_args['paged'] = $curr_site+1;
                $next_site_url = $this->getAdminPanelUrl($url_args);
            
                if($curr_site == 1){
                    $first_site_url = '#';
                    $prev_site_url = '#';
                    $first_disabled = 'disabled';
                }else{
                if($curr_site == $last_site){
                    $last_page_url = '#';
                    $next_page_url = '#';
                    $last_disabled = 'disabled';
                }
                }
                
            ?>
            <span class="pagination-links">
                <a href="<?php echo $first_site_url; ?> " title="Idź do pierwszej strony" class="first-page <?php echo $first_disabled; ?>">«</a> &nbsp;&nbsp;
                <a href="<?php echo $prev_site_url; ?>" title="Idź do poprzedniej strony" class="prev-page <?php echo $first_disabled; ?>">‹</a> &nbsp;&nbsp;
                
                <span class="paging-input"><?php echo $curr_site;?> z <span class="total-pages"><?php echo $last_site;?></span></span>
                   &nbsp;&nbsp;<a href="<?php echo $next_site_url; ?>" title="Idź do następnej strony" class="next-page <?php echo $last_disabled; ?>">›</a> 
                   &nbsp;&nbsp;<a href="<?php echo $last_site_url; ?>" title="Idź do ostatniej strony" class="last-page <?php echo $last_disabled; ?>">»</a> 
            </span>
        </div>
        
        <div class="clear"></div>
        
    </div>
    
    <table class="widefat">
        <thead>
            <tr>
                <th class="check-column"><input type="checkbox"/></th>
                <th>ID</th>
                <th>Miniaturka</th>
                <th>Tytuł</th>
                <th>Opis</th>
                <th>Przejdź do kursu</th>
                <th>Pozycja</th>
                <th>Widoczny</th>
            </tr>
        </thead>
        <tbody id="the-list">
            
            <?php if( $pagination->hasItems()): ?>
                <?php foreach($pagination->getItems() as $i=>$item): ?>
                    <tr <?php echo ($i%2) ? 'class="alternate"' : '';?>>
                        <th class="check-column">
                            <input type="checkbox" value="<?php echo $item->id;?>" name="masscheck[]" />
                        </th>
                        <td><?php echo $item->id;?></td>
                        <td>
                            
                            <img src="<?php echo $item->slide_url; ?>" alt="" height="100"/>
                            <div class="row-actions">
                                <span class="edit">
                                    <a class="edit" href="<?php echo $this->getAdminPanelUrl(array('view'=>'form','slideid'=>$item->id)); ?>">Edytuj</a>
                                </span> |  
                                <span class="trash">
                                    <?php
                                        $token = $this->action_token.$item->id;
                                        $url_to_delete = $this->getAdminPanelUrl(array('action' => 'delete', 'slideid' => $item->id));
                                    ?>
                                    <a class="delete" onclick="return confirm('Czy napewno chcesz usunąć ten slajd?')" href="<?php echo wp_nonce_url($url_to_delete, $token) ?>">Usuń</a>
                                </span> 
                            </div>
                        </td>
                        <td><?php echo $item->title; ?></td>
                        <td><?php echo $item->label; ?></td>
                        <td><?php echo $item->go_to_course_url; ?></td>
                        <td><?php echo $item->position; ?></td>
                        <td><?php echo $item->published; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
             <tr>
                <td colspan="8"> Brak slajdów w bazie danych</td>
            </tr>
            <?php endif; ?>
            
        </tbody>
    </table>
    
    <div class="tablenav">
        <div class="tablenav-pages">
            <span class="pagination-links">
                Przejdź do strony
                
                <?php 
                    $url_args = array(
                        'orderby' => $pagination->getOrderBy(),
                        'orderdir' => $pagination->getOrderDir()
                    );
                
                    for($i=1; $i<=$pagination->getLastSite(); $i++){
                        
                        $url_args['paged'] = $i;
                        $url = $this->getAdminPanelUrl($url_args);
                        
                        if($i == $pagination->getCurrSite()){
                            echo "&nbsp;<strong>{$i}</strong>";
                        }
                        else
                        {
                          echo  '&nbsp;<a href="'.$url.'">'.$i.'</a>';
                        }
                        
                        
                    }
                ?>
            
            </span>
        </div>
        
        <div class="clear"></div>
    </div>
    
</form>

<style>
        .pagination-links a {
                color: #555;
                text-decoration: none;
                padding: 0.5rem;
                border-color: #ccc;
                background: #f7f7f7;
                box-shadow: 0 1px 0 #ccc;
                vertical-align: top;
        }
        .pagination-links a:hover {
                background: #e6e6e6;
        }
        .pagination-links {
            margin-right: 2rem;
        }
        
        .disabled {
        pointer-events:none; 
        opacity:0.61;         
        }
</style>