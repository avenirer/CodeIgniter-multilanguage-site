<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="container" style="margin-top:60px;">
    <div class="row">
        <div class="col-lg-12">
            <h1>Rapid keyword extraction tool</h1>
            <?php echo $text;?>

            <?php
            if(!empty($not_in_dictionary))
            {
                echo '<h2>Entities not currently in dictionary:</h2>';
                echo '<p>This part is important when you want to make sure you\'ve written the text correctly as the poor written words will appear here. If you want, you can at any time edit the content by '.anchor('admin/contents/edit/'.$language_slug.'/'.$content_id,'clicking here','target="_blank"').'.</p>';
                echo '<p>You can also add the words to the dictionary by simply clicking on them. Note that after you click them, they will be added to the dictionary, but you must access '.anchor('admin/dictionary/index/'.$language_slug,'the dictionary','target="_blank"').' to edit the words you\'ve added. I advise you do do the editing in the same time as inserting (in a new tab).</p>';

                foreach ($not_in_dictionary as $word)
                {
                    echo anchor('admin/dictionary/add-word-from-content/'.$content_id.'/'.$language_slug.'/'.urlencode($word),$word).'<br />';
                }
            }
            else
            {
                echo '<p>If you don\'t see some words that should be inside the <strong>Key words</strong> list, the reason is most likely the fact that those words weren\'t verified in the <strong>Dictionary</strong> interface. You can visit '.anchor('admin/dictionary/index/'.$language_slug,'the dictionary','target="_blank"').' to edit the words you\'ve added.</p>';
            }

            echo anchor('admin/contents/edit/'.$language_slug.'/'.$content_id,'Get back to the content','class="btn btn-primary btn-lg btn-block" target="_blank"');
            echo anchor('/admin/contents/index/'.$content_type, 'Cancel','class="btn btn-default btn-lg btn-block"');
            echo '</div>';

            echo '<div class="col-lg-6">';

            if(!empty($the_words))
            {
                echo '<h2>The key words</h2>';
                echo '<table class="table table-hover table-bordered table-condensed">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Keyword</th>';
                echo '<th>Appearances</th>';
                echo '<th>Density</th>';
                echo '<th>Operations</th>';
                echo '</tr>';
                echo '<tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($the_words as $the_word) {
                    if(array_key_exists($the_word['id'],$selected_keywords))
                    {
                        echo '<tr class="success">';
                    }
                    else
                    {
                        echo '<tr>';
                    }
                    if ($the_word['appearances'] > 1) {
                        echo '<th>'.anchor('admin/dictionary/edit/'.$language_slug.'/'.$the_word['id'],$the_word['id'],'target="_blank"').'</th>';
                        echo '<th>'.$the_word['string'].'</th>';
                        echo '<td';
                        if(array_key_exists($the_word['id'],$selected_keywords) && $the_word['appearances']!=$selected_keywords[$the_word['id']]['appearances'])
                        {
                            echo ' class="danger"';
                        }
                        echo '>';
                        if(array_key_exists($the_word['id'],$selected_keywords) && $the_word['appearances']!=$selected_keywords[$the_word['id']]['appearances'])
                        {
                            echo $selected_keywords[$the_word['id']]['appearances'];
                        }
                        else
                        {
                            echo $the_word['appearances'];
                        }
                        echo '</td>';
                        echo '<td>'.$the_word['density'].'%</td>';
                        echo '<td>';
                        if(array_key_exists($the_word['id'],$selected_keywords))
                        {
                            echo anchor('admin/rake/add_remove_keyword/'.$language_slug.'/'.$content_id.'/'.$the_word['id'],'<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>');
                        }
                        else
                        {
                            echo anchor('admin/rake/add_remove_keyword/'.$language_slug.'/'.$content_id.'/'.$the_word['id'].'/'.$the_word['appearances'],'<span class="glyphicon glyphicon-thumbs-down" aria-hidden="true" style="color:red;"></span>');
                        }
                        echo ' '.anchor('admin/rake/refresh/'.$language_slug.'/'.$content_id.'/'.$the_word['id'].'/'.$the_word['appearances'],'<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>');
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }

            echo '</div>';
            echo '<div class="col-lg-6">';

            if(!empty($the_phrases))
            {
                echo '<h2>The key phrases</h2>';
                echo '<table class="table table-hover table-bordered table-condensed">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Phrase</th>';
                echo '<th>Score</th>';
                echo '</tr>';
                echo '<tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach($the_phrases as $phrase)
                {
                    echo '<tr>';
                    echo '<th>'.implode(' ',$phrase['string']).'</th>';
                    echo '<td>'.$phrase['score'].'</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }

            echo '</div>';?>
        </div>
    </div>
</div>