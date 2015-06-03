<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <h1>Upload featured image</h1>
            <?php echo form_open_multipart();?>
            <div class="form-group">
                <?php
                echo form_label('Upload file','image');
                echo form_error('featured_image');
                echo $upload_errors;
                echo form_upload('featured_image',set_value('featured_image'),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('File name','file_name');
                echo form_error('file_name');
                echo form_input('file_name',set_value('file_name'),'class="form-control"');
                ?>
            </div>

            <?php echo form_error('content_id');?>
            <?php echo form_hidden('content_id',set_value('content_id',$content->id));?>
            <?php
            $submit_button = 'Upload featured image';
            echo form_submit('submit', $submit_button, 'class="btn btn-primary btn-lg btn-block"');?>
            <?php echo anchor('/admin/contents/index/'.$content->content_type, 'Cancel','class="btn btn-default btn-lg btn-block"');?>
            <?php echo form_close();?>
        </div>
    </div>
</div>