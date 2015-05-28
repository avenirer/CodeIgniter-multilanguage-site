<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
    <!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $page_title;?></title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link href="<?php echo site_url('assets/admin/css/bootstrap.min.css');?>" rel="stylesheet">
        <?php echo $before_head;?>
        <link rel="stylesheet" href="<?php echo site_url('assets/admin/css/textext/textext.core.css');?>" type="text/css" />
        <!--<link rel="stylesheet" href="<?php echo site_url('assets/admin/css/textext/textext.plugin.tags.css');?>" type="text/css" />-->
        <link rel="stylesheet" href="<?php echo site_url('assets/admin/css/textext/textext.plugin.autocomplete.css');?>" type="text/css" />
        <!--<link rel="stylesheet" href="<?php echo site_url('assets/admin/css/textext/textext.plugin.focus.css');?>" type="text/css" />-->
        <!--<link rel="stylesheet" href="<?php echo site_url('assets/admin/css/textext/textext.plugin.prompt.css');?>" type="text/css" />-->
        <!--<link rel="stylesheet" href="<?php echo site_url('assets/admin/css/textext/textext.plugin.arrow.css');?>" type="text/css" />-->
        <script src="<?php echo site_url('assets/admin/js/textext.core.js');?>" type="text/javascript" charset="utf-8"></script>
        <!--<script src="<?php echo site_url('assets/admin/js/textext.plugin.tags.js');?>" type="text/javascript" charset="utf-8"></script>-->
        <script src="<?php echo site_url('assets/admin/js/textext.plugin.autocomplete.js');?>" type="text/javascript" charset="utf-8"></script>
        <!--<script src="<?php echo site_url('assets/admin/js/textext.plugin.suggestions.js');?>" type="text/javascript" charset="utf-8"></script>-->
        <script src="<?php echo site_url('assets/admin/js/textext.plugin.filter.js');?>" type="text/javascript" charset="utf-8"></script>
        <!--<script src="<?php echo site_url('assets/admin/js/textext.plugin.focus.js');?>" type="text/javascript" charset="utf-8"></script>-->
        <!--<script src="<?php echo site_url('assets/admin/js/textext.plugin.prompt.js');?>" type="text/javascript" charset="utf-8"></script>-->
        <script src="<?php echo site_url('assets/admin/js/textext.plugin.ajax.js');?>" type="text/javascript" charset="utf-8"></script>
        <!--<script src="<?php echo site_url('assets/admin/js/textext.plugin.arrow.js');?>" type="text/javascript" charset="utf-8"></script>-->
        <script type="text/javascript" src="<?php echo site_url('assets/admin/js/tinymce/tinymce.min.js');?>"></script>
        <script type="text/javascript">
            tinymce.init({
                selector: ".editor",
                theme : 'modern',
                skin : 'light',
                plugins: [
                    "advlist anchor autoresize autolink link image lists charmap print preview hr pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu template paste textcolor"
                ],
                /*content_css: "css/content.css",*/
                menu : { // this is the complete default configuration
                    table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
                    view   : {title : 'View'  , items : 'visualaid'}
                },
                toolbar: "undo redo | paste pastetext | styleselect | bold italic underline strikethrough superscript subscript hr | formats | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | forecolor backcolor | more | code",
                setup: function(editor) {
                    editor.addButton('more', {
                        text: 'more...',
                        icon: false,
                        onclick: function() {
                            editor.insertContent('<!--more-->');
                        }
                    });
                },
                <?php
                if(!empty($uploaded_images))
                {
                echo 'image_list: [';
                $the_files = '';
                foreach($uploaded_images as $image)
                {
                $the_files .= '{title: \''.((strlen($image->title)>0) ? $image->title : $image->file).'\', value: \''.site_url('media/'.$image->file).'\'},';
                }
                echo rtrim($the_files,',');
                echo '],';
                }
                ?>
                image_class_list: [
                    {title: 'None', value: ''},
                    {title: 'Responsive', value: 'img-responsive'},
                    {title: 'Rounded', value: 'img-rounded'},
                    {title: 'Circle', value: 'img-circle'},
                    {title: 'Thumbnail', value: 'img-thumbnail'}
                ],
                image_dimensions: false,
                image_advtab: true,
                relative_urls: false,
                convert_urls: false,
                style_formats: [
                    {title: 'Bold text', inline: 'b'},
                    {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                    {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                    {title: 'Example 1', inline: 'span', classes: 'example1'},
                    {title: 'Example 2', inline: 'span', classes: 'example2'},
                    {title: 'Table styles'},
                    {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                ]
            });
        </script>
    </head>
<body>
<?php
if($this->ion_auth->logged_in()) {
    ?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand"
                   href="<?php echo site_url('admin');?>"><?php echo $website->title?></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><?php echo anchor('admin/menus','Menus');?></li>
                    <li><?php echo anchor('admin/contents/index/page','Pages');?></li>
                    <li><?php echo anchor('admin/contents/index/category','Categories');?></li>
                    <li><?php echo anchor('admin/contents/index/post','Posts');?></li>
                    <li><?php echo anchor('admin/rake','RAKE Tool');?></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Multilanguage <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo site_url('admin/languages');?>">Languages</a></li>
                            <li class="divider"></li>
                            <?php
                            foreach($langs as $slug=>$language)
                            {
                                echo '<li>';
                                echo anchor('admin/dictionary/index/'.$slug.'/1','Dictionary '.$language['name']);
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Take care! <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo site_url('admin/master');?>">Website settings</a></li>
                            </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $this->ion_auth->user()->row()->username;?> <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo site_url('admin/user/profile');?>">Profile page</a></li>
                            <?php echo $current_user_menu;?>
                            <li class="divider"></li>
                            <li><a href="<?php echo site_url('admin/user/logout');?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </nav>
    <div class="container" style="padding-top:60px;">
        <?php
        echo $this->postal->get();
        ?>
    </div>
<?php
}
?>