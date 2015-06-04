<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <h1>Add <?php echo $content_type;?> in <?php echo strtolower($content_language);?></h1>
            <?php echo form_open();?>
            <div class="form-group">
                <?php
                echo form_label('Parent','parent_id');
                echo form_dropdown('parent_id',$parents,set_value('parent_id',(isset($content->parent_id) ? $content->parent_id : '0')),'class="form-control"');
                ?>
            </div>
            <div class="form-group">
                <?php
                echo form_label('Title','title');
                echo form_error('title');
                echo form_input('title',set_value('title'),'class="form-control"');
                ?>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <?php
                        echo form_label('Short title','short_title');
                        echo form_error('short_title');
                        echo form_input('short_title',set_value('short_title'),'class="form-control"');
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_label('Teaser','teaser');
                        echo form_error('teaser');
                        echo form_textarea('teaser',set_value('teaser'),'class="form-control"');
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_label('Slug','slug');
                        echo form_error('slug');
                        echo form_input('slug',set_value('slug'),'class="form-control"');
                        ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <?php
                        echo form_label('Page title','page_title');
                        echo form_error('page_title');
                        echo form_input('page_title',set_value('page_title'),'class="form-control" placeholder="SEO..."');
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_label('Page keywords','page_keywords');
                        echo form_error('page_keywords');
                        echo form_input('page_keywords',set_value('page_keywords'),'class="form-control" placeholder="SEO..."');
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_label('Page description','page_description');
                        echo form_error('page_description');
                        echo form_textarea('page_description',set_value('page_description'),'class="form-control" placeholder="SEO..."');
                        ?>
                    </div>
                </div>
            </div>
            <?php if(($content_type == 'page') || ($content_type=='category'))
            {
            ?>
                <div class="form-group">
                    <?php
                    echo form_label('Order', 'order');
                    echo form_error('order');
                    echo form_input('order', set_value('order', (isset($content->order) ? $content->order : '0')), 'class="form-control"');
                    ?>
                </div>
            <?php
            }
            if($content_type =='post' || $content_type == 'page')
            {
                ?>
                <div class="form-group">
                    <?php
                    echo form_label('Content', 'content');
                    echo form_error('content');
                    echo form_textarea('content', set_value('content', '', false), 'class="form-control editor"');
                    ?>
                </div>
            <?php
            }
            else
            {
                echo form_error('content');
                echo form_hidden('content', '');
            }
            if($content_type == 'post')
            {
                ?>
                <div class="form-group">
                    <?php
                    echo form_label('Published at', 'published_at');
                    echo form_error('published_at');
                    ?>
                    <div class="input-group date datetimepicker">
                        <?php
                        echo form_input('published_at', set_value('published_at', (isset($content->published_at) ? $content->published_at : date('Y-m-d H:i:s'))), 'class="form-control"');
                        ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            <?php
            }
            else
            {
                echo '<div class="form-group">';
                echo form_error('published_at');
                echo form_hidden('published_at', date('Y-m-d H:i:s'));
                echo '</div>';
            }
            ?>
            <?php echo form_error('content_id');?>
            <?php echo form_hidden('content_id',set_value('content_id',$content_id));?>
            <?php echo form_error('content_type');?>
            <?php echo form_hidden('content_type',set_value('content_type',$content_type));?>
            <?php echo form_error('language_slug');?>
            <?php echo form_hidden('language_slug',set_value('language_slug',$language_slug));?>
            <?php
            $submit_button = 'Add '.$content_type;
            if($content_id!=0) $submit_button = 'Add translation';
            echo form_submit('submit', $submit_button, 'class="btn btn-primary btn-lg btn-block"');?>
            <?php echo anchor('/admin/contents/index/'.$content_type, 'Cancel','class="btn btn-default btn-lg btn-block"');?>
            <?php echo form_close();?>
        </div>
    </div>
</div>