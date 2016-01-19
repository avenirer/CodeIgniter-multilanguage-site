<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top: 40px;">
    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <h1>Edit group</h1>
            <?php echo form_open();?>
            <div class="form-group">
                <?php echo form_label('Group name','group_name');?>
                <?php echo form_error('group_name');?>
                <?php echo form_input('group_name',set_value('group_name',$group->name),'class="form-control"');?>
            </div>
            <div class="form-group">
                <?php echo form_label('Group description','group_description');?>
                <?php echo form_error('group_description');?>
                <?php echo form_input('group_description',set_value('group_description',$group->description),'class="form-control"');?>
            </div>
            <?php echo form_hidden('group_id',set_value('group_id',$group->id));?>
            <?php echo form_submit('submit', 'Edit group', 'class="btn btn-primary btn-lg btn-block"');?>
            <?php echo anchor('/admin/groups', 'Cancel','class="btn btn-default btn-lg btn-block"');?>
            <?php echo form_close();?>
        </div>
    </div>
</div>
