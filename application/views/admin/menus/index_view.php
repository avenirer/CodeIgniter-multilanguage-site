<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <!-- Single button -->
            <?php
            echo anchor('admin/menus/create','Add menu','class="btn btn-primary"');
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
            echo '<th rowspan="2">Menu title</th>';
            echo '<th>Operations</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            if(!empty($menus))
            {
                foreach($menus as $menu)
                {
                    echo '<tr>';
                    echo '<td>'.$menu->id.'</td><td>'.anchor('admin/menus/items/'.$menu->id,$menu->title).'</td>';
                    echo '<td>';
                    echo anchor('admin/menus/edit/'.$menu->id,'<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>');
                    echo ' '.anchor('admin/menus/delete/'.$menu->id,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>','onclick="return confirm(\'Are you sure you want to delete?\')"');
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';

            if(!empty($without_menu))
            {
                echo '<h2>Items without menu</h2>';
                echo '<table class="table table-hover table-bordered table-condensed">';
                echo '<thead>';
                echo '<tr>';
                echo '<th rowspan="2">ID</th>';
                echo '<th rowspan="2">Item title</th>';
                echo '<th>Operations</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach($without_menu as $item_id => $item)
                {
                    echo '<tr>';
                    echo '<td>'.$item_id.'</td>';
                    echo '<td>'.$item['title'].'</td>';
                    echo '<td>';
                    //echo anchor('admin/menus/edit/'.$menu->id,'<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>');
                    echo anchor('admin/menus/delete_item/0/all/'.$item_id,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>','onclick="return confirm(\'Are you sure you want to delete?\')"');
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            ?>
        </div>
    </div>
</div>