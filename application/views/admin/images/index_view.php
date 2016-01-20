<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <h1>Upload images</h1>
            <?php echo form_open_multipart();?>
            <div class="form-group">
                <?php
                echo form_label('Upload file(s)','images[]');
                echo form_error('images[]');
                echo $upload_errors;
                echo form_upload('images[]','','class="form-control" multiple');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('Title(s)','titles');
                echo form_error('titles');
                echo form_input('titles',set_value('titles'),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('File name(s)','file_names');
                echo form_error('file_names');
                echo form_input('file_names',set_value('file_names'),'class="form-control"');
                ?>
            </div>
            <?php /*
            echo form_error('content_type');?>
            echo form_hidden('content_type',set_value('content_type',$content_type));
            */?>
            <?php echo form_error('content_id');?>
            <?php echo form_hidden('content_id',$content_id)?>
            <?php
            $submit_button = 'Upload image(s)';
            echo '<div class="form-group">';
            echo form_submit('submit', $submit_button, 'class="btn btn-primary btn-lg btn-block"');
            echo '</div>';
            ?>
            <?php /* echo anchor('/admin/images/index/'.$content_type.'/'.$content_id, 'Cancel','class="btn btn-default btn-lg btn-block"');*/?>
            <?php echo form_close();?>
        </div>
        <?php
        if(!empty($show_images)) {
            ?>
            <div class="col-lg-12">
                <?php
                echo '<table class="table table-hover table-bordered table-condensed">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Image</th>';
                echo '<th>Title</th>';
                echo '<th>File</th>';
                echo '<th>Operations</th>';
                echo '<tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach($show_images as $image)
                {
                    echo '<tr>';
                    echo '<td>'.$image->id.'</td>';
                    echo '<td>'.anchor('media/'.$image->file,'<img src="'.site_url('media/'.$image->file).'" class="img-thumbnail" style="max-width: 100px; max-height: 100px;" />','target="_blank"').'</td>';
                    echo '<td>'.$image->title.' '.anchor('admin/images/edit_title/'.$image->id,'<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>').'</td>';
                    echo '<td>'.$image->file.'</td>';
                    echo '<td>';
                    echo anchor('admin/images/delete/'.$image->id,'<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>','onclick="return confirm(\'Are you sure you want to delete?\')"');
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>