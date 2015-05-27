<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rake extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
        if(!$this->ion_auth->in_group('admin'))
        {
            $this->session->set_flashdata('message','You are not allowed to visit the RAKE page');
            redirect('admin','refresh');
        }
        $this->load->model('content_model');
        $this->load->model('content_translation_model');
        $this->load->model('dictionary_model');
        $this->load->model('language_model');
        $this->load->model('phrase_model');
        $this->load->model('keyword_model');
        $this->load->model('keyphrase_model');
        $this->load->library('form_validation');
        $this->load->helper('text');
	}

    public function index()
    {
        $this->render('admin/rake/index_view');
    }

	public function check()
	{
        if($phrases = $this->phrase_model->limit(50)->order_by('last_check','asc')->get_all())
        {
            $message = '';
            $modified = 0;
            $deleted = 0;
            foreach($phrases as $phrase)
            {
                if($this->keyphrase_model->where('phrase_id',$phrase->id)->get())
                {
                    $new_words = array();
                    $words = explode('|',$phrase->phrase);
                    foreach($words as $word)
                    {
                        $the_word = $this->dictionary_model->get($word);
                        $new_words[] = ($the_word->parent_id == '0') ? $the_word->id : $the_word->parent_id;
                    }
                    if($words != $new_words)
                    {
                        $words = implode('|',$new_words);
                        $now = date('Y-m-d H:i:s');
                        if($another_phrase = $this->phrase_model->where(array('phrase'=>$words))->get())
                        {
                            $this->keyphrase_model->where('phrase_id',$phrase->id)->update(array('phrase_id'=>$another_phrase->id));
                            $this->phrase_model->delete($phrase->id);
                            $deleted++;
                        }
                        else
                        {
                            $this->phrase_model->update(array('phrase' => $words, 'last_check' => $now), $phrase->id);
                        }
                        $modified++;
                    }

                }
                else
                {
                    $this->phrase_model->delete($phrase->id);
                    $deleted++;
                }
            }
            $message .= $modified.' key phrases were modified. ';
            $message .= $deleted.' phrases were deleted. ';
            $this->session->set_flashdata('message', $message);;

        }
        redirect('admin/rake','refresh');
	}

    public function analyze($language_slug,$content_id)
    {
        $selected_keywords = $this->keyword_model->where(array('content_id'=>$content_id,'language_slug'=>$language_slug))->get_all();
        $keywords = array();
        if($selected_keywords)
        {
            foreach($selected_keywords as $word)
            {
                $keywords[$word->word_id] = array('appearances'=>$word->appearances,'keyword_id'=>$word->id);
            }
        }
        $this->data['selected_keywords'] = $keywords;
        $this->data['language_slug'] = $language_slug;
        $this->data['content_id'] = $content_id;
        $content = $this->content_model->get($content_id);
        $this->data['content_type'] = $content->content_type;
        $translation = $this->content_translation_model->where(array('language_slug'=>$language_slug,'content_id' => $content_id))->get();
        $deleted_keyphrases = $this->keyphrase_model->where(array('content_id'=>$content_id,'language_slug'=>$language_slug))->delete();
        //$deleted_keywords = $this->keyword_model->where(array('content_id'=>$content_id,'language_slug'=>$language_slug))->delete();
        //$this->data['message'] = 'The key phrases were refreshed. ';


        $text = strip_tags($translation->title).'. '.strip_tags($translation->content);
        $text = trim($text,".");
        $text = html_entity_decode($text, ENT_QUOTES,'UTF-8');
        $text = str_replace(PHP_EOL, ' ', $text);
        $text = convert_accented_characters($text);
        $text = $this->_add_stop_signs($text);


        $blocks = $this->_break_into_blocks($text);
        $this->data['text'] = implode('<span class="glyphicon glyphicon-stop text-primary"></span>',$blocks);

        $words = array();
        $blocks_as_arrays = array();
        $depth = 0;
        $total_entities = 0;
        foreach($blocks as $block)
        {
            $block = explode(' ',$block);
            if(sizeof($block)>$depth) $depth = sizeof($block);
            if(sizeof($block)>1)
            {
                $blocks_as_arrays[] = $block;
            }
            foreach($block as $entity)
            {
                $total_entities++;
                if(!is_numeric(trim($entity)) && !in_array(trim($entity),$words))
                {
                    $words[] = strtolower($entity);
                }
            }
        }



        //echo '<pre>';
        //print_r($blocks_as_arrays);
        //echo '</pre>';

        //echo '<pre>';
        //print_r($words);
        //echo '</pre>';

        $not_in_dictionary = array();
        foreach($words as $word)
        {
            if(!($this->dictionary_model->where(array('word'=>$word,'language_slug'=>$language_slug))->get()))
            {
                $not_in_dictionary[] = strtolower($word);

            }
        }

        $this->data['not_in_dictionary'] = $not_in_dictionary;

        $noise_words = array();
        $noises = $this->dictionary_model->where(array('noise'=>'1','parent_id'=>'0','language_slug'=>$language_slug))->get_all();
        if(!empty($noises))
        {
            foreach($noises as $noise)
            {
                $noise_words[] = $noise->id;
            }
        }
//        echo '<h1>Noise words</h1>';
//        print_r($noise_words);

        $dictionary = $this->dictionary_model->where('language_slug',$language_slug)->where('verified','1')->where('word', $words)->get_all();

        //echo '<h1>Dictionar</h1>';
        $word_ids = array();

        if(!empty($dictionary)) {
            foreach ($dictionary as $word_dictionary) {
                if ($word_dictionary->parent_id == '0') {
                    $word_ids[$word_dictionary->word] = $word_dictionary->id;
                } else {
                    $word_ids[$word_dictionary->word] = $word_dictionary->parent_id;
                }
            }
        }
        ksort($word_ids);
//        echo '<pre>';
//        print_r($word_ids);
//        echo '</pre>';


        $the_base_words = array();
        if(!empty($word_ids)) {
            $base_words = $this->dictionary_model->where('language_slug', $language_slug)->where('id', $word_ids, NULL, FALSE)->get_all();
            foreach($base_words as $the_word)
            {
                $the_base_words[$the_word->id] = $the_word->word;
            }
        }

//        echo '<h1>Cuvintele de baza din dictionar</h1>';
//        echo '<pre>';
//        print_r($the_base_words);
//        echo '</pre>';



        $degs = array(); //aici pastram toate cuvintele impreuna cu numarul aparitiilor
        $word_appearances = array();

        foreach($blocks_as_arrays as $block_key => $block)
        {
            $block_as_ids = array();
            foreach($block as $entity_key=>$entity)
            {
                $entity = strtolower($entity);
                if(array_key_exists($entity,$word_ids))
                {
                    $entity = $word_ids[$entity];
                }
                else
                {
                    break;
                }
                $block_as_ids[] = $entity;
                if(!in_array($entity, $noise_words))
                {
                    if (!array_key_exists($entity, $word_appearances)) {
                        $word_appearances[$entity] = 1;
                    } else {
                        $word_appearances[$entity] = $word_appearances[$entity] + 1;
                    }
                    $degs[$entity][$block_key][] = $entity_key;
                }
            }
            $blocks_as_ids[] = $block_as_ids;
        }

        //echo '<h1>Inlocuim cuvintele cu id-urile cuvintelor</h1>';

        //echo '<pre>';
        //print_r($blocks_as_ids);
        //echo '</pre>';
        //exit;

        //        echo '<h1>Avem entitatile cu numarul de aparitii</h1>';
        //        arsort($word_appearances);
        //        echo '<pre>';
        //        print_r($word_appearances);
        //        echo '</pre>';

        //echo '<h1>Si scoatem entitatile intr-un array organizat dupa bloc de text si pozitie</h1>';
        //echo '<pre>';
        //arsort($degs);
        //print_r($degs);
        //echo '</pre>';

        //echo '<h1>Nu ne intereseaza entitatile care au o singura aparitie</h1>';
        $candidate_words = array();
        foreach($word_appearances as $key=>$word)
        {
            if($word>1)
            {
                $candidate_words[$key] = $word;
            }
        }
        //echo '<pre>';
        //print_r($candidate_words);
        //echo '</pre>';
        // exit;


        $initial_phrases = array();
        foreach($candidate_words as $word_key => $word)
        {
            $the_word = $degs[$word_key];
            foreach($the_word as $block_key => $location)
            {
                foreach($location as $loc)
                {
                    $new_key = '';
                    for ($k = 0; $k <= $depth; $k++)
                    {
                        if(array_key_exists(($loc + $k),$blocks_as_ids[$block_key]))
                        {
                            //echo $blocks_as_ids[$block_key][($loc + $k)].'<br />';
                            $new_key .= $blocks_as_ids[$block_key][($loc + $k)];
                            $new_key .= '|';
                            $trim_new_key = rtrim($new_key,'|');
                            //echo $trim_new_key.'<br />';
                            if (!array_key_exists($trim_new_key, $initial_phrases)) {
                                $initial_phrases[$trim_new_key] = 1;
                            } else {
                                $initial_phrases[$trim_new_key] = $initial_phrases[$trim_new_key] + 1;
                            }
                        }
                        else
                        {
                            break;
                        }
                    }
                }
            }
        }

        //echo '<h1>Numarul de aparitii ale frazelor de text</h1>';
        //echo '<pre>';
        //print_r($initial_phrases);
        //echo '</pre>';


        //eliminam cuvintele zgomot de la sfarsitul frazelor

        foreach($initial_phrases as $phrase=>$freq)
        {
            $phrase_words = explode('|',$phrase);
            $number_words = sizeof($phrase_words);
            //echo '<br />phrase '.$phrase.'<br />';
            for($i=(sizeof($phrase_words)-1);$i>0;$i--)
            {
                if(in_array($phrase_words[$i],$noise_words))
                {
                    array_pop($phrase_words);
                }
                else
                {
                    break;
                }
            }
            $new_phrase = implode('|',$phrase_words);
            if($phrase != $new_phrase)
            {
                $frequency = $initial_phrases[$phrase];
                if(array_key_exists($new_phrase,$initial_phrases))
                {
                    $initial_phrases[$new_phrase] = $initial_phrases[$new_phrase] + $frequency;
                }
                else
                {
                    $initial_phrases[$new_phrase] = $frequency;
                }
                unset($initial_phrases[$phrase]);
            }


        }

        //echo '<h1>Nu luam in considerare "frazele" care au un singur cuvant</h1>';
        $candidate_phrases = array();
        $previous_key = '';
        foreach($initial_phrases as $phrase_key => $deg)
        {
            $word_keys = explode('|',$phrase_key);
            if(sizeof($word_keys)>1 && $deg>1) {
                $candidate_phrases[$phrase_key] = $deg;
            }
            $previous_key = $phrase_key;
        }
        //echo '<pre>';
        //print_r($candidate_phrases);
        //echo '</pre>';


        //echo '<h1>Frecventa entitatilor in combinatii de fraze</h1>';

        $previous_key = '';
        $word_frequencies = array();
        foreach($candidate_phrases as $phrase_key => $deg)
        {
            $word_keys = explode('|',$phrase_key);
            foreach ($word_keys as $key) {
                if(strpos($previous_key,$phrase_key)===FALSE) {
                    if (!in_array($key, $noise_words)) {
                        if (!array_key_exists($key, $word_frequencies)) {
                            $word_frequencies[$key] = 1;
                        } else {
                            $word_frequencies[$key] = $word_frequencies[$key] + 1;
                        }
                    }
                }
            }
            $previous_key = $phrase_key;
        }


        //echo '<pre>';
        arsort($word_frequencies);
        //print_r($word_frequencies);
        //echo '</pre>';

        $extracted_phrases = array();
        foreach($candidate_phrases as $phrase_keys => $appearances)
        {
            $score = 0;
            $word_keys = explode('|',$phrase_keys);
            foreach($word_keys as $key)
            {
                $word_deg = isset($word_appearances[$key]) ? $word_appearances[$key] : 0;
                $word_freq = isset($word_frequencies[$key]) ? $word_frequencies[$key] : 0;
                if(!in_array($key,$noise_words)) {
                    if ($word_deg != 0 & $word_freq != 0) {
                        $add_score = ($word_deg / $word_freq);
                        if (in_array($key, $blocks_as_ids[0])) {
                            $add_score = ($add_score * 2);
                        }
                        $score = $score + $add_score;

                    }
                }
            }
            $extracted_phrases[$phrase_keys] = number_format($score,2) * $appearances;
        }

        //echo '<h1>Calculam scorurile pentru fraze</h1>';
        //echo '<pre>';
        arsort($extracted_phrases);
        //print_r($extracted_phrases);
        //echo '</pre>';

        $the_words = array();
        foreach($word_appearances as $key => $number)
        {
            $string = '';
            if(array_key_exists($key, $the_base_words))
            {
                $string = $the_base_words[$key];
            }
            else
            {
                $string = $key;
                $key = '';
            }
            $density = ($number/$total_entities)*100;
            $density = number_format($density,2);
            $the_words[$key] = array('id'=>$key,'string'=>$string,'appearances'=>$number,'density' => $density);
        }



        $the_phrases = array();

        /*
        echo '<pre>';
        print_r($extracted_phrases);
        echo '</pre>';
        exit;
        */

        if(!empty($extracted_phrases))
        {
            foreach ($extracted_phrases as $key => $score)
            {
                if ($this->phrase_model->where(array('phrase'=>$key,'language_slug'=>$language_slug))->get() === FALSE)
                {
                    $phrase_id = $this->phrase_model->insert(array('phrase'=>$key,'language_slug'=>$language_slug));
                }
                else
                {
                    $phrase = $this->phrase_model->where(array('phrase'=>$key,'language_slug'=>$language_slug))->get();
                    $phrase_id = $phrase->id;
                }
                $this->load->model('keyphrase_model');
                {
                    if($this->keyphrase_model->where(array('content_id'=>$content_id,'phrase_id'=>$phrase_id,'language_slug'=>$language_slug))->get() === FALSE)
                    {
                        $this->keyphrase_model->insert(array('content_id'=>$content_id,'phrase_id'=>$phrase_id,'language_slug'=>$language_slug));
                    }
                    else
                    {
                        $phrase_score = $this->keyphrase_model->where(array('content_id'=>$content_id,'phrase_id'=>$phrase_id,'language_slug'=>$language_slug))->get();
                        $this->keyphrase_model->update(array('content_id'=>$content_id,'phrase_id'=>$phrase_id,'language_slug'=>$language_slug,'score'=>$score),$phrase_score->id);
                    }
                }
            }
            $this->content_translation_model->where(array('content_id'=>$content_id, 'language_slug'=>$language_slug))->update(array('rake'=>'1'));
        }

        foreach($extracted_phrases as $key => $score)
        {
            $word_keys = explode('|',$key);
            $strings = array();
            foreach($word_keys as $word_id)
            {
                if(array_key_exists($word_id, $the_words)) {
                    $strings[] = $the_words[$word_id]['string'];
                }
                elseif(array_key_exists($word_id, $the_base_words))
                {
                    $strings[] = $the_base_words[$word_id];
                }
                else
                {
                    $strings[] = $word_id;
                }
            }
            $the_phrases[$key] = array('string'=>$strings,'score'=>$score);
        }

        $score = array();
        foreach ($the_words as $key => $row)
        {
            $score[$key] = $row['appearances'];
        }

        array_multisort($score, SORT_DESC, $the_words);

        /*
        echo '<pre>';
        print_r($the_phrases);
        echo '</pre>';
        exit;
        */
        $this->data['the_words'] = $the_words;
        $this->data['the_phrases'] = $the_phrases;

        $this->render('admin/rake/edit_view');

    }

    protected function _add_stop_signs($string)
    {
        $string = str_replace(array('. ',', ','; ',': ','"','„','”'.'','(',')','[',']','?','!',' si ','-',' - '),'|',$string);
        return $string;
    }

    protected function _break_into_blocks($text)
    {
        $text_arr = explode('|',$text);

        $blocks = array();
        foreach($text_arr as $block)
        {
            if(strlen(trim($block))>0)
            {
                $block = str_replace(array(' - ','&nbsp;'),' ',$block);
                $block = preg_replace('/ +/',' ',$block);
                $blocks[] = trim($block);
            }

        }
        return $blocks;
    }

    public function add_remove_keyword($language_slug, $content_id, $word_id, $appearances=0)
    {
        if($keyword = $this->keyword_model->where(array('word_id'=>$word_id,'content_id'=>$content_id,'language_slug'=>$language_slug))->get())
        {
            $this->session->set_flashdata('message', 'The keyword was deleted.');
            $this->keyword_model->delete($keyword->id);
        }
        else
        {
            $insert_data = array('word_id'=>$word_id,'content_id'=>$content_id,'language_slug'=>$language_slug,'appearances'=>$appearances);
            if($this->keyword_model->insert($insert_data))
            {
                $this->session->set_flashdata('message', 'The keyword was inserted.');
            }

        }
        redirect('admin/rake/analyze/'.$language_slug.'/'.$content_id,'refresh');

    }

    public function refresh($language_slug, $content_id, $word_id, $appearances=0)
    {
        if($this->keyword_model->where(array('word_id'=>$word_id,'content_id'=>$content_id,'language_slug'=>$language_slug))->update(array('appearances'=>$appearances)))
        {
            $this->session->set_flashdata('message', 'The keyword was updated.');
        }
        redirect('admin/rake/analyze/'.$language_slug.'/'.$content_id,'refresh');
    }


}