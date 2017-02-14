<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top: 60px;">
<?php
echo '<h1>'.$title.'</h1>';
echo $content;
if($posts)
{
    echo '<pre>';
    print_r($posts);
    echo '</pre>';
   foreach($posts as $post)
   {
       echo '<h2>'.$post->title.'</h2>';
       echo $post->teaser;
       echo anchor($post->url, $post->title);
   }
}
?>
</div>
