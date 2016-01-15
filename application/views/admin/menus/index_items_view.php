<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <!-- Single button -->
            <?php
            if(sizeof($langs)>1) {
                ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                            aria-expanded="false">Add item to <?php echo $menu->title;?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <?php
                        foreach ($langs as $slug => $language) {
                            echo '<li>' . anchor('admin/menus/create_item/' . $menu->id . '/' . $slug, $language['name']) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            <?php
            }
            else
            {
                echo anchor('admin/menus/create_item/' . $menu->id . '/' .$current_lang['slug'],'Add item to '.$menu->title,'class="btn btn-primary"');
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="margin-top: 10px;">
            <?php
            echo '<table class="table table-hover table-bordered table-condensed">';
            echo '<thead>';
            echo '<tr>';
            echo '<th rowspan="2">ID</th>';
            echo '<th rowspan="2">Item title</th>';
            echo '<th rowspan="2">Parent</th>';
            foreach($langs as $slug => $language)
            {
                echo '<th>'.$slug.'</th>';
            }
            echo '<th rowspan="2"> </th>';
            echo '</tr>';
            echo '<tr>';
            foreach($langs as $slug => $language)
            {
                echo '<th>Operations</th>';
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if(!empty($items))
            {

                foreach($items as $item_id => $item)
                {
                    echo '<tr>';
                    echo '<td>'.$item_id.'</td><td>';
                    if(!empty($item['translations']))
                    {
                        $titles = array();
                        foreach($item['translations'] as $language=>$translation)
                        {
                            $titles[] = '<strong>'.$language.'</strong>: '.$translation['title'];
                        }
                        echo implode('<br />',$titles);
                    }
                    echo '</td>';
                    echo '<td>'.$item['parent_id'].'</td>';
                    foreach($langs as $slug=>$language)
                    {
                        echo '<td>';
                        if(array_key_exists($slug,$item['translations']))
                        {
                            echo anchor('admin/menus/edit_item/'.$item['menu_id'].'/'.$slug.'/'.$item_id,'<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>');
                            echo ' '.anchor('admin/menus/delete_item/'.$item['menu_id'].'/'.$slug.'/'.$item_id,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>','onclick="return confirm(\'Are you sure you want to delete?\')"');
                            /*echo '<br />'.$category['translations'][$slug]['created_at'];
                            echo '<br />'.$category['translations'][$slug]['last_update'];*/
                        }
                        else
                        {
                            echo anchor('admin/menus/create_item/'.$item['menu_id'].'/'.$slug.'/'.$item_id,'<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>');
                        }
                        echo '</td>';
                    }
                    echo '<td>'.anchor('admin/menus/delete_item/'.$item['menu_id'].'/all/'.$item_id,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>','onclick="return confirm(\'Are you sure you want to delete?\')"').'</td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '<nav><ul class="pagination">';
            echo $next_previous_pages;
            echo '</ul></nav>';
            ?>
        </div>
    </div>
</div>