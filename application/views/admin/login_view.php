<div class="row">
    <div class="col-xs-10 col-xs-offset-1 ol-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-5">
        <h1>Login</h1>
        <?php
        echo $this->postal->get();
        ?>
        <?php echo form_open('',array('class'=>'form-horizontal'));?>
        <div class="form-group">
            <?php echo form_label('Username','identity');?>
            <?php echo form_error('identity');?>
            <?php echo form_input('identity','','class="form-control"');?>
        </div>
        <div class="form-group">
            <?php echo form_label('Password','password');?>
            <?php echo form_error('password');?>
            <?php echo form_password('password','','class="form-control"');?>
        </div>
        <div class="form-group">
            <label>
                <?php echo form_checkbox('remember','1',FALSE);?> Remember me
            </label>
        </div>
        <div class="form-group">
            <?php echo form_submit('submit', 'Log in', 'class="btn btn-primary btn-lg btn-block"');?>
        </div>
        <?php echo form_close();?>
    </div>
</div>