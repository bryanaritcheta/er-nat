<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/

add_theme_support('avia_template_builder_custom_css');

add_filter( 'avf_google_heading_font',  'avia_add_heading_font');
function avia_add_heading_font($fonts)
{
$fonts['Helvetica'] = 'Helvetica:400,600,800';
return $fonts;
}

add_filter( 'avf_google_content_font',  'avia_add_content_font');
function avia_add_content_font($fonts)
{
$fonts['Source Sans Pro'] = 'Source Sans Pro:400,600,800';
return $fonts;
}



add_filter('avia_breadcrumbs_args', 'change_breadcrumb_before', 10, 1);
function change_breadcrumb_before($args) {
	$args['before'] = '<span class="breadcrumb-title">' . __( '', 'avia_framework' ) . '</span>';
	return $args; 	
}


add_filter('avf_title_args', 'fix_single_post_title', 10, 2);
function fix_single_post_title($args,$id)
{
    if ( $args['title'] == 'Blog - Latest News' )
    {
        $args['title'] = "Blog - Latest News";
        $args['link'] = get_permalink($id);
        $args['heading'] = 'h1';
    }

    return $args;
}



function thisBlogPosts($attributes,$content = null){
    
	extract( shortcode_atts( array(
	'f1' => 0,
	'f2' => 0,
	'f3' => 0
	), $attributes ) );

		$string = '';

		$featured_posts = array($f1,$f2,$f3);

		$my_wp_query = new WP_Query();
        $all_wp_pages = $my_wp_query->query(array('post_type' => 'post', 'post__in' => $featured_posts, 'post_status' => 'publish'));  

        $count=0;
          foreach ($all_wp_pages as $post){
          	$count++;

          
          		$dt = new DateTime($post->post_date);

          		$string .= "<div class='post-custom'>";
	          		$string .= "<div class='post-date'>";
		          		$string .= '<span class="post-day">'.$count.'</span>';
	          			//$string .= '<span class="post-day">'.$dt->format('d').'</span>';
		          		//$string .= '<span class="post-month">'.$dt->format('M').'</span>';
	          		$string .= "</div>";

	          		$string .= "<div class='post-content'>";
		          		$string .= '<a href="'.get_permalink($post->ID).'">'.truncateStringWords($post->post_title,50).'</a><br>';
		          		$string .= truncateStringWords(wp_strip_all_tags($post->post_content),95).'..<br>';
		          	$string .= "</div>";	
		        $string .= "</div>";	  	
          }
          


	return $string;


}

add_shortcode('showThisBlogPosts','thisBlogPosts');


function truncateStringWords($str, $maxlen){

    if (strlen($str) <= $maxlen) return $str;

    $newstr = substr($str, 0, $maxlen);
    if (substr($newstr, -1, 1) != ' ') $newstr = substr($newstr, 0, strrpos($newstr, " "));

    return $newstr;
}





function thisList($attributes,$content = null){

	extract( shortcode_atts( array(
	'item' => 0
	), $attributes ) );


	$string = '<div class="list-spinach"><img src="/wp-content/uploads/spinach-icon.png"> <h4>'.$item.'</h4></div>';


	return $string;
}

add_shortcode('showThisList','thisList');

function change_avia_date_format($date, $function) {
  if(!empty($function) && $function != 'avia_get_comment_list') $date = get_option('date_format');
  return $date;
}
add_filter('avia_widget_time', 'change_avia_date_format', 10, 2);




function modify_share_title(){
	return "Share this article on: ";
}
add_filter('avia_social_share_title', 'modify_share_title');






function getJob($attributes,$content = null){
    
        extract( shortcode_atts( array(
        'records' => 0,
        'post_type' => 'career'
        ), $attributes ) );


        $pg =  getUriSegment(3);

        $page = $pg ;

        if ($page <= 0)
            $page = 1;

        $per_page = 10; // Set how many records do you want to display per page.

        $startpoint = ($page * $per_page) - $per_page;

        $string = '';


        $cnt=0;
        $my_wp_query = new WP_Query();

        $all_wp_pages = $my_wp_query->query(array('post_type' => $post_type,'post_status'=>'publish','orderby'=>'post_date','order'=>'DESC'));     

        foreach ($all_wp_pages as $post){

        $cnt++;

            $string .= '<div class="job-list-item" style="border-bottom:1px solid #ccc;padding:50px 0;">';

            $string .= '<a href="'.get_permalink($post->ID).'" style="color:#F57320;"><h3 style="color:#F57320;">'.$post->post_title.'</h3></a>';

            $type = get_post_meta ($post->ID,'career_type');

            $count_type =  count($type[0]);

            $type_string ='';   

            for($x=0;$x<$count_type;$x++)
                    $type_string .= $type[0][$x]." / ";
           

            $string .= '<div style="font-size: 12px;">'.$type_string.''.get_post_meta ($post->ID,'career_state', true).'</div>';


            $string .= '<div style="margin-top:20px;">'.get_post_meta ($post->ID,'career_intro', true).'</div>';


            $string .= '<a href="'.get_permalink($post->ID).'" class="read-more-work">READ MORE</a>';


            $string .= '</div>';

        }


        if($cnt==0){
            $string .=  "<h3>No jobs available.</h3>";
        }   

        $string .=  pagination($per_page, $page, $category, $post_type);

        return $string;
}

add_shortcode('displayJobs','getJob');       


function getUriSegments() {
    return explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}
 
function getUriSegment($n) {
    $segs = getUriSegments();
    return count($segs)>0&&count($segs)>=($n-1)?$segs[$n]:'';
}



function pagination($per_page, $page, $category, $post_type) {


    $url = get_permalink().'?';

    $my_wp_query = new WP_Query();

    $all_wp_count = $my_wp_query->query(array('post_type' => $post_type,'category_name' => $category ,'post_status'=>'publish','orderby'=>'post_title','order'=>'ASC'));     
    $count=0;
    
    foreach ($all_wp_count as $post){
            $count++;
    }


    $total = $count;
    $adjacents = "2";

    $prevlabel = "&lsaquo;";
    $nextlabel = "&rsaquo;";

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;

    $prev = $page - 1;
    $next = $page + 1;

    $lastpage = ceil($total / $per_page);

    $lpm1 = $lastpage - 1; // //last page minus 1

    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<ul class='pagination'>";
        //$pagination .= "<li class='page_info'>Page {$page} of {$lastpage}</li>";

        if ($page > 1)
            $pagination.= "<li><a href='{$url}page={$prev}'>{$prevlabel}</a></li>";

        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination.= "<li><a class='current'>{$counter}</a></li>";
                else
                    $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {

            if ($page < 1 + ($adjacents * 2)) {

                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
                }
                $pagination.= "<li class='dot'>...</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
                }
                $pagination.= "<li class='dot'>..</li>";
                $pagination.= "<li><a href='{$url}page={$lpm1}'>{$lpm1}</a></li>";
                $pagination.= "<li><a href='{$url}page={$lastpage}'>{$lastpage}</a></li>";
            } else {

                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<li><a class='current'>{$counter}</a></li>";
                    else
                        $pagination.= "<li><a href='{$url}page={$counter}'>{$counter}</a></li>";
                }
            }
        }

        if ($page < $counter - 1)
            $pagination.= "<li><a href='{$url}page={$next}'>{$nextlabel}</a></li>";

        $pagination.= "</ul>";
    }

    return $pagination;
}


function showsPrevNext($attributes,$content = null){
    
        extract( shortcode_atts( array(
        'parent' => ''
        ), $attributes ) );


        $categories = get_the_category();
         if ( ! empty( $categories ) ) {
            $post_cat =  esc_html( $categories[0]->name );   
        }



        //$parent_id = wp_get_post_parent_id( $post_ID);

        if($post_cat == 'Blog'){

            $my_wp_query = new WP_Query();
            $all_wp_pages = $my_wp_query->query(array('post_type' => 'post','category_name' => 'Blog' ,'orderby'=>'post_date','order'=>'DESC', 'posts_per_page'=>-1));          
            
            $idx=0;
            foreach ($all_wp_pages as $post){

                    $index[$idx] = $post->ID;

                    if($post->ID == get_the_ID())
                        $myindex = $idx; 
                    
                    $idx++; 
            }


        //$string .= '<div class="flex_column av_one_fifth first  avia-builder-el-5  el_after_av_one_full  el_before_av_one_fifth  column-top-margin"></div>';    
        //$string .= '<div class="flex_column av_one_fifth   avia-builder-el-6  el_after_av_one_fifth  el_before_av_three_fifth  column-top-margin"></div>';

        //$string .= '<div class="flex_column av_three_fifth   avia-builder-el-7  el_after_av_one_fifth  avia-builder-el-last "><section class="avia_codeblock_section avia_code_block_0" itemscope="itemscope" itemtype="https://schema.org/CreativeWork"><div class="avia_codeblock " itemprop="text" > ';


        $string .= '<div class="arrow-sculptures">';


        if($myindex > 0)
            $string .= '<a href="'.get_permalink($index[$myindex - 1]).'"><span class="avia-attach-element-select avia_icon_preview avia-font-entypo-fontello avia-active-element" title="Charcode: \ue870" data-element-nr="ue870" data-element-font="entypo-fontello"></span> Previous</a>';

        if($myindex > 0 && $myindex < $idx-1)
            $string .= ' | ';       

        if($myindex < $idx-1)
            $string .= '<a href="'.get_permalink($index[$myindex + 1]).'">Next <span class="avia-attach-element-select avia_icon_preview avia-font-entypo-fontello " title="Charcode: \ue871" data-element-nr="ue871" data-element-font="entypo-fontello"></span></a>';
        
        
        $string .= '</div>'; 


        //$string .= '</div></section></div>';


        return $string;
        }

}


add_shortcode('getPrevNext','showsPrevNext');


/*
add_filter('avf_load_google_map_api', 'disable_google_map_api', 10, 1);

function disable_google_map_api($load_google_map_api) {
	$load_google_map_api = false;
	return $load_google_map_api;
} */

add_filter('acf/settings/google_api_key', function () {
    return 'AIzaSyD6KV5jXfqOOasvz2xsFDXhud_JHsQiaws';
});