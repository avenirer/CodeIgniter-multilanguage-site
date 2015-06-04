<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<footer>
    <div class="container">
        <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
    </div>
</footer>
<script src="<?php echo site_url('assets/admin/js/bootstrap.min.js');?>"></script>
<script src="<?php echo site_url('assets/admin/js/bootstrap-datetimepicker.min.js');?>"></script>
<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            locale: 'en',
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true,
            showTodayButton: true
        });
    });
</script>

<?php echo $before_body;?>
</body>
</html>