<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
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
        <?php echo form_hidden('redirect_to',$redirect_to);?>
        <?php echo form_submit('submit', 'Log in', 'class="btn btn-primary btn-lg btn-block"');?>
        <?php echo form_close();?>
    </div>
</div>