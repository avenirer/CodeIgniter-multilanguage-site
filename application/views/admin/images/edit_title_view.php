<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <h1>Edit title</h1>
            <?php echo form_open_multipart();?>
            <div class="form-group">
                <?php
                echo form_label('Title','title');
                echo form_error('title');
                echo form_input('title',set_value('title',$image->title),'class="form-control"');
                ?>
            </div>
            <?php echo form_error('image_id');?>
            <?php echo form_hidden('image_id',$image->id);?>
            <?php
            $submit_button = 'Edit image title';
            echo '<div class="form-group">';
            echo form_submit('submit', $submit_button, 'class="btn btn-primary btn-lg btn-block"');
            echo '</div>';
            ?>
            <?php /* echo anchor('/admin/images/index/'.$content_type.'/'.$content_id, 'Cancel','class="btn btn-default btn-lg btn-block"');*/?>
            <?php echo form_close();?>
        </div>
    </div>
</div>