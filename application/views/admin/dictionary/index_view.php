<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <?php
            echo anchor('admin/dictionary/add-word/'.$language_slug,'Add word','class="btn btn-primary" target="_blank"');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" style="margin-top: 10px;">
            <?php
            echo '<table class="table table-hover table-bordered table-condensed">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Word</th>';
            echo '<th>Verified</th>';
            echo '<th>Operations</th>';
            echo '<tr>';
            echo '</thead>';
            echo '<tbody>';
            if(!empty($words))
            {

                foreach($words as $word)
                {
                    echo '<tr';
                    if($word->parent_id=='0') echo ' class="success"';
                    echo '>';
                    echo '<td>'.$word->id.'</td>';
                    echo '<td>'.$word->word.'</td>';
                    echo '<td>';
                    $style = ($word->verified=='1') ? '' : ' style="color: red;"';
                    $icon = ($word->verified=='1') ? 'up' : 'down';
                    echo '<span class="glyphicon glyphicon-thumbs-'.$icon.'" aria-hidden="true"'.$style.'></span>';
                    echo '</td>';
                    echo '<td>';
                    echo anchor('admin/dictionary/edit/'.$language_slug.'/'.$word->id,'<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>','target="_blank"');
                    echo ' '.anchor('admin/dictionary/delete/'.$language_slug.'/'.$word->id,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>','onclick="return confirm(\'Are you sure you want to delete?\')"');
                    echo '</td>';
                    /*
                    echo ' '.anchor('admin/images/index/post/'.$post_id,'<span class="glyphicon glyphicon-picture"></span>');
                    $publish = ($post['published']=='1') ? 0 : 1;
                    $style = ($post['published']=='1') ? '' : ' style="color: red;"';
                    $icon = ($post['published'] == '1') ? 'up' : 'down';
                    echo ' '.anchor('admin/posts/publish/'.$post_id.'/'.$publish,'<span class="glyphicon glyphicon-thumbs-'.$icon.'" aria-hidden="true"'.$style.'></span>');
                    echo '</td>';*/
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