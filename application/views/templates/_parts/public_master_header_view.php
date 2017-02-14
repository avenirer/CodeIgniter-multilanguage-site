<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
    <!DOCTYPE html>
<html lang="<?php echo $_SESSION['set_language'];?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $page_title;?></title>
        <?php
        foreach($langs as $lang_slug=>$lang)
        {
            if($lang_slug!=$_SESSION['set_language'])
            {
                echo '<link rel="alternate" href="'.site_url($lang['alternate_link']).'" hreflang="'.str_replace('_','-',$lang['language_code']).'" />'."\r\n";
            }
        }
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link href="<?php echo site_url('assets/admin/css/bootstrap.min.css');?>" rel="stylesheet">
        <?php echo $before_head;?>
    </head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
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
                   href="<?php echo site_url($lang_slug);?>"><?php echo $website->title;?></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <?php echo $top_menu;?>
                </ul>
                <?php
                if(sizeof($langs)>1) {
                    ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false"><?php echo $current_lang['name'];?> <span
                                    class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <?php
                                foreach($langs as $slug => $language)
                                {
                                    if($slug!=$current_lang['slug']) {
                                        echo '<li>' . anchor($language['alternate_link'], $language['name']) . '</li>';
                                    }
                                ?>
                                <?php
                                }
                                ?>
                            </ul>
                        </li>
                    </ul>
                <?php
                }
                    ?>
            </div>
            <!--/.nav-collapse -->
        </div>
    </nav>
    <?php if($this->session->flashdata('message')) {?>
    <div class="container" style="padding-top:40px;">
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('message');?>
        </div>
    </div>
    <?php }?>