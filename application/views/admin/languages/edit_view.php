<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <h1>Edit language</h1>
            <?php echo form_open();?>
            <div class="form-group">
                <?php
                echo form_label('Language name','language_name');
                echo form_error('language_name');
                echo form_input('language_name',set_value('language_name',$language->language_name),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('Language slug','slug');
                echo form_error('slug');
                echo form_input('slug',set_value('slug',$language->slug),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('Language directory','language_directory');
                echo form_error('language_directory');
                echo form_input('language_directory',set_value('language_directory',$language->language_directory),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('Language code','language_code');
                echo form_error('language_code');
                echo form_input('language_code',set_value('language_code',$language->language_code),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('Default language','default');
                echo form_dropdown('default',array('0' => 'Not default', '1'=>'Default'),set_value('default',$language->default),'class="form-control"');
                ?>
            </div>
            <?php echo form_error('language_id');?>
            <?php echo form_hidden('language_id',$language->id);?>
            <?php echo form_submit('submit', 'Edit language', 'class="btn btn-primary btn-lg btn-block"');?>
            <?php echo anchor('/admin/languages', 'Cancel','class="btn btn-default btn-lg btn-block"');?>
            <?php echo form_close();?>
        </div>
    </div>
</div>