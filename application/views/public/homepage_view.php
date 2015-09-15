<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top: 60px;">
<?php
echo $this->lang->line('homepage_welcome');

echo '<br />'.$current_lang['slug'];
echo '<br />hello';
echo '<pre>';
print_r($langs);
echo '</pre>';
?>
</div>
