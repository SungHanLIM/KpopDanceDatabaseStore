<?php
/*
  Plugin Name: YouTube
  Plugin URI: http://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx
  Description: YouTube embed plugin. Embed a responsive YouTube video, playlist gallery, or channel gallery. Add video thumbnails, analytics, SEO, caching...
  Version: 10.4
  Author: EmbedPlus Team
  Author URI: http://www.embedplus.com
 */

/*
  YouTube
  Copyright (C) 2015 EmbedPlus.com

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.

 */

//define('WP_DEBUG', true);

class YouTubePrefs
{

    public static $version = '10.4';
    public static $opt_version = 'version';
    public static $optembedwidth = null;
    public static $optembedheight = null;
    public static $defaultheight = null;
    public static $defaultwidth = null;
    public static $oembeddata = null;
    public static $opt_center = 'centervid';
    public static $opt_glance = 'glance';
    public static $opt_autoplay = 'autoplay';
    public static $opt_cc_load_policy = 'cc_load_policy';
    public static $opt_iv_load_policy = 'iv_load_policy';
    public static $opt_loop = 'loop';
    public static $opt_modestbranding = 'modestbranding';
    public static $opt_rel = 'rel';
    public static $opt_showinfo = 'showinfo';
    public static $opt_playsinline = 'playsinline';
    public static $opt_autohide = 'autohide';
    public static $opt_controls = 'controls';
    public static $opt_theme = 'theme';
    public static $opt_color = 'color';
    public static $opt_listType = 'listType';
    public static $opt_wmode = 'wmode';
    public static $opt_vq = 'vq';
    public static $opt_html5 = 'html5';
    public static $opt_dohl = 'dohl';
    public static $opt_hl = 'hl';
    public static $opt_ssl = 'ssl';
    public static $opt_ogvideo = 'ogvideo';
    public static $opt_nocookie = 'nocookie';
    public static $opt_playlistorder = 'playlistorder';
    public static $opt_acctitle = 'acctitle';
    public static $opt_pro = 'pro';
    public static $opt_oldspacing = 'oldspacing';
    public static $opt_responsive = 'responsive';
    public static $opt_origin = 'origin';
    public static $opt_widgetfit = 'widgetfit';
    public static $opt_defaultdims = 'defaultdims';
    public static $opt_defaultwidth = 'width';
    public static $opt_defaultheight = 'height';
    public static $opt_defaultvol = 'defaultvol';
    public static $opt_vol = 'vol';
    public static $opt_apikey = 'apikey';
    public static $opt_schemaorg = 'schemaorg';
    public static $opt_ftpostimg = 'ftpostimg';
    public static $opt_spdc = 'spdc';
    public static $opt_spdcexp = 'spdcexp';
    public static $opt_migrate = 'migrate';
    public static $opt_migrate_youtube = 'migrate_youtube';
    public static $spdcprefix = 'ytpref';
    public static $spdcall = 'youtubeprefs_spdcall';
    public static $opt_dynload = 'dynload';
    public static $opt_dyntype = 'dyntype';
    public static $opt_gallery_pagesize = 'gallery_pagesize';
    public static $opt_gallery_apikey = 'gallery_apikey';
    public static $opt_gallery_columns = 'gallery_columns';
    public static $opt_gallery_style = 'gallery_style';
    public static $opt_alloptions = 'youtubeprefs_alloptions';
    public static $alloptions = null;
    public static $yt_options = array();
    //public static $epbase = 'http://localhost:2346';
    public static $epbase = '//www.embedplus.com';
    public static $double_plugin = false;
    public static $scriptsprinted = 0;
    public static $badentities = array('&#215;', '×', '&#8211;', '–', '&amp;', '&#038;', '&#38;');
    public static $goodliterals = array('x', 'x', '--', '--', '&', '&', '&');
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    //public static $ytregex = '@^[\r\n]{0,1}[[:blank:]]*https?://(?:www\.)?(?:(?:youtube.com/watch\?)|(?:youtu.be/))([^\s"]+)[[:blank:]]*[\r\n]{0,1}$@im';
    public static $oldytregex = '@^\s*https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed))/{0,1}\?)|(?:youtu.be/))([^\s"]+)\s*$@im';
    public static $ytregex = '@^[\r\t ]*https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed))/{0,1}\?)|(?:youtu.be/))([^\s"]+)[\r\t ]*$@im';
    public static $justurlregex = '@https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed))/{0,1}\?)|(?:youtu.be/))([^\[\s"]+)@i';

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        add_action('admin_init', array("YouTubePrefs", 'check_double_plugin_warning'));

        self::$alloptions = get_option(self::$opt_alloptions);
        if (self::$alloptions == false || version_compare(self::$alloptions[self::$opt_version], self::$version, '<'))
        {
            self::initoptions();
        }

        if (self::$alloptions[self::$opt_oldspacing] == 1)
        {
            self::$ytregex = self::$oldytregex;
        }

        self::$optembedwidth = intval(get_option('embed_size_w'));
        self::$optembedheight = intval(get_option('embed_size_h'));

        self::$yt_options = array(
            self::$opt_autoplay,
            self::$opt_cc_load_policy,
            self::$opt_iv_load_policy,
            self::$opt_loop,
            self::$opt_modestbranding,
            self::$opt_rel,
            self::$opt_showinfo,
            self::$opt_playsinline,
            self::$opt_autohide,
            self::$opt_controls,
            self::$opt_html5,
            self::$opt_hl,
            self::$opt_theme,
            self::$opt_color,
            self::$opt_listType,
            self::$opt_wmode,
            self::$opt_vq,
            'index',
            'list',
            'start',
            'end'
        );

        add_action('media_buttons', 'YouTubePrefs::media_button_wizard', 11);



        //$embedplusmce_wiz = new Add_new_tinymce_btn_Youtubeprefs('|', 'embedplus_youtubeprefs_wiz', plugins_url() . '/youtube-embed-plus/scripts/embedplus_mce_wiz.js');
        //$embedplusmce_prefs = new Add_new_tinymce_btn_Youtubeprefs('|', 'embedplus_youtubeprefs', plugins_url() . '/youtube-embed-plus/scripts/embedplus_mce_prefs.js');
        //$epstatsmce_youtubeprefs = new Add_new_tinymce_btn_Youtubeprefs('|', 'embedplusstats_youtubeprefs', plugins_url() . '/youtube-embed-plus/scripts/embedplusstats_mce.js');

        self::do_ytprefs();
        add_action('admin_menu', 'YouTubePrefs::ytprefs_plugin_menu');
        if (!is_admin())
        {

            add_action('wp_print_scripts', array('YouTubePrefs', 'jsvars'));
            add_action('wp_enqueue_scripts', array('YouTubePrefs', 'jsinitvars'));
            add_action('wp_enqueue_scripts', array('YouTubePrefs', 'fitvids'));
            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_ogvideo] == 1)
            {
                add_action('wp_head', array('YouTubePrefs', 'do_ogvideo'));
            }
        }
    }

    public static function show_glance_list()
    {
        $glancehref = self::show_glance();
        $cnt = self::get_glance_count();

        //display via list
        return
                '<li class="page-count">
            <a href="' . $glancehref . '" class="thickbox ytprefs_glance_button" id="ytprefs_glance_button" title="YouTube Embeds At a Glance">
                ' . number_format_i18n($cnt) . ' With YouTube
            </a>
        </li>';
    }

    public static function show_glance_table()
    {
        $glancehref = self::show_glance();
        $cnt = self::get_glance_count();
        return
                '<tr>
            <td class="first b"><a title="YouTube Embeds At a Glance" href="' . $glancehref . '" class="thickbox ytprefs_glance_button">' . number_format_i18n($cnt) . '</a></td>
            <td class="t"><a title="YouTube Embeds At a Glance" href="' . $glancehref . '" id="ytprefs_glance_button" class="thickbox ytprefs_glance_button">With YouTube</a></td>
        </tr>';
    }

    public static function get_glance_count()
    {
        global $wpdb;
        $query_sql = "
                SELECT count(*) as mytotal
                FROM $wpdb->posts
                WHERE (post_content LIKE '%youtube.com/%' OR post_content LIKE '%youtu.be/%')
                AND post_status = 'publish'";

        $query_result = $wpdb->get_results($query_sql, OBJECT);

        return intval($query_result[0]->mytotal);
    }

    public static function show_glance()
    {
        $glancehref = admin_url('admin.php?page=youtube-ep-glance') . '&random=' . rand(1, 1000) . '&TB_iframe=true&width=780&height=800';
        return $glancehref;
    }

    public static function glance_page()
    {
        ?>
        <div class="wrap">
            <style type="text/css">
                #wphead {display:none;}
                #wpbody{margin-left: 0px;}
                .wrap {font-family: Arial; padding: 0px 10px 0px 10px; line-height: 180%;}
                .bold {font-weight: bold;}
                .orange {color: #f85d00;}
                #adminmenuback {display: none;}
                #adminmenu, adminmenuwrap {display: none;}
                #wpcontent, .auto-fold #wpcontent {margin-left: 0px;}
                #wpadminbar {display:none;}
                html.wp-toolbar {padding: 0px;}
                #footer, #wpfooter, .auto-fold #wpfooter {display: none;}
                .acctitle {background-color: #dddddd; border-radius: 5px; padding: 7px 15px 7px 15px; cursor: pointer; margin: 10px; font-weight: bold; font-size: 12px;}
                .acctitle:hover {background-color: #cccccc;}
                .accbox {display: none; position: relative; margin:  5px 8px 30px 15px; clear: both; line-height: 180%;}
                .accclose {position: absolute; top: -38px; right: 5px; cursor: pointer; width: 24px; height: 24px;}
                .accloader {padding-right: 20px;}
                .accthumb {display: block; width: 300px; float: left; margin-right: 25px;}
                .accinfo {width: 300px; float: left;}
                .accvidtitle {font-weight: bold; font-size: 16px;}
                .accthumb img {width: 300px; height: auto; display: block;}
                .clearboth {clear: both;}
                .pad20 {padding: 20px;}
                .center {text-align: center;}
            </style>
            <script type="text/javascript">
                function accclose(ele)
                {
                    jQuery(ele).parent('.accbox').hide(400);
                }

                (function($j)
                {
                    $j(document).ready(function() {


                        $j('.acctitle').click(function() {
                            var $acctitle = $j(this);
                            var $accbox = $j(this).parent().children('.accbox');
                            var pid = $accbox.attr("data-postid");

                            $acctitle.prepend('<img class="accloader" src="<?php echo plugins_url('images/ajax-loader.gif', __FILE__) ?>" />');
                            jQuery.ajax({
                                type: "post",
                                dataType: "json",
                                timeout: 30000,
                                url: wpajaxurl,
                                data: {action: 'my_embedplus_glance_vids', postid: pid},
                                success: function(response) {
                                    if (response.type == "success") {
                                        $accbox.html(response.data),
                                                $accbox.show(400);
                                    }
                                    else {
                                    }
                                },
                                error: function(xhr, ajaxOptions, thrownError) {

                                },
                                complete: function() {
                                    $acctitle.children('.accloader').remove();
                                }

                            });


                        });
                    });
                })(jQuery);


            </script>
            <?php
            global $wpdb;
            $query_sql = "
                SELECT SQL_CALC_FOUND_ROWS *
                FROM $wpdb->posts
                WHERE (post_content LIKE '%youtube.com/%' OR post_content LIKE '%youtu.be/%')
                AND post_status = 'publish'
                order by post_date DESC LIMIT 0, 10";

            $query_result = $wpdb->get_results($query_sql, OBJECT);

            if ($query_result !== null)
            {
                $total = $wpdb->get_var("SELECT FOUND_ROWS();");
                global $post;
                echo '<h2><img src="' . plugins_url('images/youtubeicon16.png', __FILE__) . '" /> 10 Latest Posts/Pages with YouTube Videos (' . $total . ' Total)</h2>';
                ?>

                We recommend using this page as an easy way to check the results of the global default settings you make (e.g. hide annotations) on your recent embeds. Or, simply use it as an index to jump right to your posts that contain YouTube embeds.

                <?php
                if ($total > 0)
                {
                    echo '<ul class="accord">';
                    foreach ($query_result as $post)
                    {
                        echo '<li>';
                        setup_postdata($post);
                        the_title('<div class="acctitle">', ' &raquo;</div>');
                        echo '<div class="accbox" data-postid="' . $post->ID . '"></div><div class="clearboth"></div></li>';
                    }
                    echo '</ul>';
                }
                else
                {
                    echo '<p class="center bold orange">You currently do not have any YouTube embeds yet.</p>';
                }
            }

            wp_reset_postdata();
            ?>
            To remove this feature from your dashboard, simply uncheck <i>Show "At a Glance" Embed Links</i> in the <a target="_blank" href="<?php echo admin_url('admin.php?page=youtube-my-preferences#jumpdefaults') ?>">plugin settings page &raquo;</a>.

        </div>
        <?php
    }

    public static function my_embedplus_glance_vids()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $postid = intval($_REQUEST['postid']);
            $currpost = get_post($postid);

            $thehtml = '<img class="accclose" onclick="accclose(this)" src="' . plugins_url('images/accclose.png', __FILE__) . '" />';

            $matches = Array();
            $ismatch = preg_match_all(self::$justurlregex, $currpost->post_content, $matches);

            if ($ismatch)
            {
                foreach ($matches[0] as $match)
                {
                    $link = trim(preg_replace('/&amp;/i', '&', $match));
                    $link = preg_replace('/\s/', '', $link);
                    $link = trim(str_replace(self::$badentities, self::$goodliterals, $link));

                    $linkparamstemp = explode('?', $link);

                    $linkparams = array();
                    if (count($linkparamstemp) > 1)
                    {
                        $linkparams = self::keyvalue($linkparamstemp[1], true);
                    }
                    if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
                    {
                        $vtemp = explode('/', $linkparamstemp[0]);
                        $linkparams['v'] = array_pop($vtemp);
                    }

                    $vidid = $linkparams['v'];

                    if ($vidid != null)
                    {
                        try
                        {
                            $odata = self::get_oembed('http://youtube.com/watch?v=' . $vidid, 1920, 1280);
                            $postlink = get_permalink($postid);
                            if ($odata != null && !is_wp_error($odata))
                            {
                                $_name = esc_attr(sanitize_text_field($odata->title));
                                $_description = esc_attr(sanitize_text_field($odata->author_name));
                                $_thumbnailUrl = esc_url("//i.ytimg.com/vi/" . $vidid . "/0.jpg");

                                $thehtml .= '<a target="_blank" href="' . $postlink . '" class="accthumb"><img src="' . $_thumbnailUrl . '" /></a>';
                                $thehtml .= '<div class="accinfo">';
                                $thehtml .= '<a target="_blank" href="' . $postlink . '" class="accvidtitle">' . $_name . '</a>';
                                $thehtml .= '<div class="accdesc">' . (strlen($_description) > 400 ? substr($_description, 0, 400) . "..." : $_description) . '</div>';
                                $thehtml .= '</div>';
                                $thehtml .= '<div class="clearboth pad20"></div>';
                            }
                            else
                            {
                                $thehtml .= '<p class="center bold orange">This <a target="_blank" href="' . $postlink . '">post/page</a> contains a video that has been removed from YouTube.';

                                if (!(self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0))
                                {
                                    $thehtml .='<br><a target="_blank" href="https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx">Activate delete video tracking to catch these cases &raquo;</a>';
                                }
                                $thehtml .= '</strong>';
                            }
                        }
                        catch (Exception $ex)
                        {
                            
                        }
                    }
                    else if (false) // if playlist
                    {
                        
                    }
                }
            }



            if ($currpost != null)
            {
                $result['type'] = 'success';
                $result['data'] = $thehtml;
            }
            else
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function my_embedplus_glance_count()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $thehtml = '';

            try
            {
                if (version_compare(get_bloginfo('version'), '3.8', '>='))
                {
                    $result['container'] = '#dashboard_right_now ul';
                    $thehtml .= self::show_glance_list();
                }
                else
                {
                    $result['container'] = '#dashboard_right_now .table_content table tbody';
                    $thehtml .= self::show_glance_table();
                }
                $result['type'] = 'success';
                $result['data'] = $thehtml;
            }
            catch (Exception $e)
            {
                $result['type'] = 'error';
            }

            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function media_button_wizard()
    {
        add_thickbox();

        $wizhref = self::$epbase . '/wpembedcode-simple-search.aspx?pluginversion=' . YouTubePrefs::$version .
                '&wpversion=' . get_bloginfo('version') .
                '&settingsurl=' . urlencode(admin_url('admin.php?page=youtube-my-preferences#jumpdefaults')) .
                '&dashurl=' . urlencode(admin_url('admin.php?page=youtube-ep-analytics-dashboard')) .
                '&blogwidth=' . YouTubePrefs::get_blogwidth() .
                '&domain=' . urlencode(site_url()) .
                '&prokey=' . urlencode(YouTubePrefs::$alloptions[YouTubePrefs::$opt_pro]) .
                '&myytdefaults=' . urlencode(http_build_query(YouTubePrefs::$alloptions)) .
                '&random=' . rand(1, 1000) .
                '&TB_iframe=true&width=950&height=800';
        ?>
        <script type="text/javascript">
            function widen_ytprefs_wiz() {
                setTimeout(function() {
                    jQuery("#TB_window").animate({marginLeft: '-' + parseInt((950 / 2), 10) + 'px', width: '950px'}, 300);
                    jQuery("#TB_window iframe").animate({width: '950px'}, 300);
                }, 15);
            }
            jQuery(document).ready(function() {
                jQuery("#ytprefs_wiz_button").click(widen_ytprefs_wiz);
                jQuery(window).resize(widen_ytprefs_wiz);
            });
        </script>
        <a href="<?php echo $wizhref; ?>" class="thickbox button ytprefs_media_link" id="ytprefs_wiz_button" title="Visual YouTube Search Tool and Wizard - An easier embedding option"><span></span> YouTube</a>
        <?php
    }

    public static function check_double_plugin_warning()
    {
        if (is_plugin_active('embedplus-for-wordpress/embedplus.php'))
        {
            add_action('admin_notices', array("YouTubePrefs", "double_plugin_warning"));
            //self::$double_plugin = true;
        }
    }

    public static function double_plugin_warning()
    {
        global $pagenow;
        $user_id = get_current_user_id();
        if ($pagenow != 'plugins.php' || get_user_meta($user_id, 'embedplus_double_plugin_warning', true) != 1)
        {
            //echo '<div class="error">' . $_SERVER['QUERY_STRING'] .'</div>';
            if ($pagenow == 'plugins.php' || strpos($_SERVER['QUERY_STRING'], 'youtube-my-preferences') !== false ||
                    strpos($_SERVER['QUERY_STRING'], 'embedplus-video-analytics-dashboard') !== false ||
                    strpos($_SERVER['QUERY_STRING'], 'youtube-ep-analytics-dashboard') !== false ||
                    strpos($_SERVER['QUERY_STRING'], 'embedplus-official-options') !== false)
            {
                ?>
                <style type="text/css">
                    .embedpluswarning img
                    {
                        vertical-align: text-bottom;
                    }
                    div.bgyellow {background-color: #FCFC94; position: relative;}
                    a.epxout, a.epxout:hover {font-weight: bold; color: #ffffff; background-color: #ff8888; text-decoration: none;
                                              border-radius: 20px; font-size: 15px; position: absolute; top: 3px; right: 3px;
                                              line-height: 20px; text-align: center; width: 20px; height: 20px; display: block; cursor: pointer;}
                    </style>
                    <div class="error bgyellow embedpluswarningbox">
                    <p class="embedpluswarning">
                        <?php
                        if ($pagenow == 'plugins.php')
                        {
                            echo '<a class="epxout">&times;</a>';
                        }
                        ?>
                        Seems like you have two different YouTube plugins by the EmbedPlus Team installed: <b><img src="<?php echo plugins_url('images/youtubeicon16.png', __FILE__) ?>" /> YouTube</b> and <b><img src="<?php echo plugins_url('images/btn_embedpluswiz.png', __FILE__) ?>" /> Advanced YouTube Embed.</b> We strongly suggest keeping only the one you prefer, so that they don't conflict with each other while trying to create your embeds.</p>
                </div>
                <iframe allowTransparency="true" src="<?php echo self::$epbase . '/both-plugins-conflict.aspx' ?>" style="width:2px; height: 2px;" ></iframe>
                <script type="text/javascript">
                    (function($) {
                        $(document).ready(function() {
                            $('.epxout').click(function() {
                                $.ajax({
                                    type: "post",
                                    dataType: "json",
                                    timeout: 30000,
                                    url: wpajaxurl,
                                    data: {action: 'my_embedplus_dismiss_double_plugin_warning'},
                                    success: function(response) {
                                        if (response.type == "success") {
                                            $(".embedpluswarningbox").hide();
                                        }
                                    },
                                    error: function(xhr, ajaxOptions, thrownError) {
                                    },
                                    complete: function() {
                                    }
                                });
                            });

                        });
                    })(jQuery);
                </script>
                <?php
            }
        }
    }

    public static function my_embedplus_dismiss_double_plugin_warning()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'embedplus_double_plugin_warning', 1);
            $result['type'] = 'success';
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function jsvars()
    {
        //https://support.cloudflare.com/hc/en-us/articles/200169436-How-can-I-have-Rocket-Loader-ignore-my-script-s-in-Automatic-Mode-
        $responsiveselector = '[]';
        if (self::$alloptions[self::$opt_widgetfit] == 1)
        {
            $responsiveselector = '["iframe.__youtube_prefs_widget__"]';
        }
        if (self::$alloptions[self::$opt_responsive] == 1)
        {
            $responsiveselector = '["iframe[src*=\'youtube.com\']","iframe[src*=\'youtube-nocookie.com\']","iframe[data-ep-src*=\'youtube.com\']","iframe[data-ep-src*=\'youtube-nocookie.com\']","iframe[data-ep-gallerysrc*=\'youtube.com\']"]';
        }
        ?>
        <script data-cfasync="false">
            var eppathtoscripts = "<?php echo plugins_url('scripts/', __FILE__); ?>";
            var epresponsiveselector = <?php echo $responsiveselector; ?>;
            var epdovol = true; //<?php echo (self::$alloptions[self::$opt_defaultvol] == 1 ? 'true' : 'false') ?>;
        </script>
        <?php
    }

    public static function jsinitvars()
    {

        //https://support.cloudflare.com/hc/en-us/articles/200169436-How-can-I-have-Rocket-Loader-ignore-my-script-s-in-Automatic-Mode-
        $responsiveselector = '[]';
        if (self::$alloptions[self::$opt_widgetfit] == 1)
        {
            $responsiveselector = '["iframe.__youtube_prefs_widget__"]';
        }
        if (self::$alloptions[self::$opt_responsive] == 1)
        {
            $responsiveselector = '["iframe[src*=\'youtube.com\']","iframe[src*=\'youtube-nocookie.com\']","iframe[data-ep-src*=\'youtube.com\']","iframe[data-ep-src*=\'youtube-nocookie.com\']"]';
        }
        ?>
        <script data-cfasync="false">
            var eppathtoscripts = "<?php echo plugins_url('scripts/', __FILE__); ?>";
            var epresponsiveselector = <?php echo $responsiveselector; ?>;
            var epdovol = true; //<?php echo (self::$alloptions[self::$opt_defaultvol] == 1 ? 'true' : 'false') ?>;
        </script>
        <?php
    }

    public static function fitvids()
    {
        wp_enqueue_script('__ytprefsfitvids__', plugins_url('scripts/fitvids.min.js', __FILE__), false, false, true);
    }

    public static function initoptions()
    {
        //vanilla defaults
        $_center = 0;
        $_glance = 1;
        $_autoplay = get_option('youtubeprefs_autoplay', 0);
        $_cc_load_policy = get_option('youtubeprefs_cc_load_policy', 0);
        $_iv_load_policy = get_option('youtubeprefs_iv_load_policy', 1);
        $_loop = get_option('youtubeprefs_loop', 0);
        $_modestbranding = get_option('youtubeprefs_modestbranding', 0);
        $_rel = get_option('youtubeprefs_rel', 1);
        $_showinfo = get_option('youtubeprefs_showinfo', 1);
        $_html5 = get_option('youtubeprefs_html5', 0);
        $_theme = get_option('youtubeprefs_theme', 'dark');
        $_color = get_option('youtubeprefs_color', 'red');
        $_vq = get_option('youtubeprefs_vq', '');
        $_autohide = 2;
        $_pro = '';
        $_ssl = 0;
        $_nocookie = 0;
        $_playlistorder = 0;
        $_acctitle = 0;
        $_ogvideo = 0;
        $_migrate = 0;
        $_migrate_youtube = 0;
        $_controls = 2;
        $_oldspacing = 1;
        $_responsive = 0;
        $_widgetfit = 1;
        $_schemaorg = 0;
        $_ftpostimg = 0;
        $_spdc = 0;
        $_spdcexp = 24;
        $_dynload = 0;
        $_dyntype = '';
        $_wmode = 'opaque';
        $_defaultdims = 0;
        $_defaultwidth = '';
        $_defaultheight = '';
        $_playsinline = 0;
        $_origin = 0;
        $_defaultvol = 0;
        $_vol = '';
        $_apikey = '';
        $_hl = '';
        $_dohl = 0;
        $_gallery_columns = 3;
        $_gallery_pagesize = 15;
        $_gallery_style = 'grid';

        $arroptions = get_option(self::$opt_alloptions);

        //update vanilla to previous settings if exists
        if ($arroptions !== false)
        {
            $_center = self::tryget($arroptions, self::$opt_center, 0);
            $_glance = self::tryget($arroptions, self::$opt_glance, 1);
            $_autoplay = self::tryget($arroptions, self::$opt_autoplay, 0);
            $_cc_load_policy = self::tryget($arroptions, self::$opt_cc_load_policy, 0);
            $_iv_load_policy = self::tryget($arroptions, self::$opt_iv_load_policy, 1);
            $_loop = self::tryget($arroptions, self::$opt_loop, 0);
            $_modestbranding = self::tryget($arroptions, self::$opt_modestbranding, 0);
            $_rel = self::tryget($arroptions, self::$opt_rel, 1);
            $_showinfo = self::tryget($arroptions, self::$opt_showinfo, 1);
            $_playsinline = self::tryget($arroptions, self::$opt_playsinline, 0);
            $_origin = self::tryget($arroptions, self::$opt_origin, 0);
            $_html5 = self::tryget($arroptions, self::$opt_html5, 0);
            $_hl = self::tryget($arroptions, self::$opt_hl, '');
            $_dohl = self::tryget($arroptions, self::$opt_dohl, 0);
            $_theme = self::tryget($arroptions, self::$opt_theme, 'dark');
            $_color = self::tryget($arroptions, self::$opt_color, 'red');
            $_wmode = self::tryget($arroptions, self::$opt_wmode, 'opaque');
            $_vq = self::tryget($arroptions, self::$opt_vq, '');
            $_pro = self::tryget($arroptions, self::$opt_pro, '');
            $_ssl = self::tryget($arroptions, self::$opt_ssl, 0);
            $_nocookie = self::tryget($arroptions, self::$opt_nocookie, 0);
            $_playlistorder = self::tryget($arroptions, self::$opt_playlistorder, 0);
            $_acctitle = self::tryget($arroptions, self::$opt_acctitle, 0);
            $_ogvideo = self::tryget($arroptions, self::$opt_ogvideo, 0);
            $_migrate = self::tryget($arroptions, self::$opt_migrate, 0);
            $_migrate_youtube = self::tryget($arroptions, self::$opt_migrate_youtube, 0);
            $_controls = self::tryget($arroptions, self::$opt_controls, 2);
            $_autohide = self::tryget($arroptions, self::$opt_autohide, 2);
            $_oldspacing = self::tryget($arroptions, self::$opt_oldspacing, 1);
            $_responsive = self::tryget($arroptions, self::$opt_responsive, 0);
            $_widgetfit = self::tryget($arroptions, self::$opt_widgetfit, 1);
            $_schemaorg = self::tryget($arroptions, self::$opt_schemaorg, 0);
            $_ftpostimg = self::tryget($arroptions, self::$opt_ftpostimg, 0);
            $_spdc = self::tryget($arroptions, self::$opt_spdc, 0);
            $_spdcexp = self::tryget($arroptions, self::$opt_spdcexp, 24);
            $_dynload = self::tryget($arroptions, self::$opt_dynload, 0);
            $_dyntype = self::tryget($arroptions, self::$opt_dyntype, '');
            $_defaultdims = self::tryget($arroptions, self::$opt_defaultdims, 0);
            $_defaultwidth = self::tryget($arroptions, self::$opt_defaultwidth, '');
            $_defaultheight = self::tryget($arroptions, self::$opt_defaultheight, '');
            $_defaultvol = self::tryget($arroptions, self::$opt_defaultvol, 0);
            $_vol = self::tryget($arroptions, self::$opt_vol, '');
            $_apikey = self::tryget($arroptions, self::$opt_apikey, '');
            $_gallery_pagesize = self::tryget($arroptions, self::$opt_gallery_pagesize, 15);
            $_gallery_columns = self::tryget($arroptions, self::$opt_gallery_columns, 3);
            $_gallery_style = self::tryget($arroptions, self::$opt_gallery_style, 'grid');
        }
        else
        {
            $_oldspacing = 0;
        }

        $all = array(
            self::$opt_version => self::$version,
            self::$opt_center => $_center,
            self::$opt_glance => $_glance,
            self::$opt_autoplay => $_autoplay,
            self::$opt_cc_load_policy => $_cc_load_policy,
            self::$opt_iv_load_policy => $_iv_load_policy,
            self::$opt_loop => $_loop,
            self::$opt_modestbranding => $_modestbranding,
            self::$opt_rel => $_rel,
            self::$opt_showinfo => $_showinfo,
            self::$opt_playsinline => $_playsinline,
            self::$opt_origin => $_origin,
            self::$opt_autohide => $_autohide,
            self::$opt_html5 => $_html5,
            self::$opt_hl => $_hl,
            self::$opt_dohl => $_dohl,
            self::$opt_theme => $_theme,
            self::$opt_color => $_color,
            self::$opt_wmode => $_wmode,
            self::$opt_vq => $_vq,
            self::$opt_pro => $_pro,
            self::$opt_ssl => $_ssl,
            self::$opt_nocookie => $_nocookie,
            self::$opt_playlistorder => $_playlistorder,
            self::$opt_acctitle => $_acctitle,
            self::$opt_ogvideo => $_ogvideo,
            self::$opt_migrate => $_migrate,
            self::$opt_migrate_youtube => $_migrate_youtube,
            self::$opt_controls => $_controls,
            self::$opt_oldspacing => $_oldspacing,
            self::$opt_responsive => $_responsive,
            self::$opt_widgetfit => $_widgetfit,
            self::$opt_schemaorg => $_schemaorg,
            self::$opt_ftpostimg => $_ftpostimg,
            self::$opt_spdc => $_spdc,
            self::$opt_spdcexp => $_spdcexp,
            self::$opt_dynload => $_dynload,
            self::$opt_dyntype => $_dyntype,
            self::$opt_defaultdims => $_defaultdims,
            self::$opt_defaultwidth => $_defaultwidth,
            self::$opt_defaultheight => $_defaultheight,
            self::$opt_defaultvol => $_defaultvol,
            self::$opt_vol => $_vol,
            self::$opt_apikey => $_apikey,
            self::$opt_gallery_columns => $_gallery_columns,
            self::$opt_gallery_style => $_gallery_style,
            self::$opt_gallery_pagesize => $_gallery_pagesize
        );

        update_option(self::$opt_alloptions, $all);
        update_option('embed_autourls', 1);
        self::$alloptions = get_option(self::$opt_alloptions);
    }

    public static function tryget($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function wp_above_version($ver)
    {
        global $wp_version;
        if (version_compare($wp_version, $ver, '>='))
        {
            return true;
        }
        return false;
    }

    public static function do_ytprefs()
    {
        add_filter('autoptimize_filter_js_exclude', 'YouTubePrefs::ao_override_jsexclude', 10, 1);
        if (!is_admin())
        {
            add_filter('the_content', 'YouTubePrefs::apply_prefs_content', 1);
            add_filter('widget_text', 'YouTubePrefs::apply_prefs_widget', 1);
            add_shortcode('embedyt', array('YouTubePrefs', 'apply_prefs_shortcode'));
            if (self::$alloptions[self::$opt_migrate] == 1)
            {
                if (self::$alloptions[self::$opt_migrate_youtube] == 1)
                {
                    add_shortcode('youtube', array('YouTubePrefs', 'apply_prefs_shortcode_youtube'));
                    add_shortcode('youtube_video', array('YouTubePrefs', 'apply_prefs_shortcode_youtube'));
                }
            }
        }
        else
        {
            if (self::$alloptions[self::$opt_ftpostimg] == 1 && self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
            {
                add_action('save_post', array('YouTubePrefs', 'doftpostimg'), 110, 3);
            }
        }
    }

    public static function ao_override_jsexclude($exclude)
    {
        if (strpos($exclude, 'ytprefs.min.js') === false)
        {
            return $exclude . ",ytprefs.min.js";
        }
        return $exclude;
    }

    public static function apply_prefs_shortcode($atts, $content = null)
    {
        $content = trim($content);
        $currfilter = current_filter();
        if (preg_match(self::$justurlregex, $content))
        {
            return self::get_html(array($content), $currfilter == 'widget_text' ? false : true);
        }
        return '';
    }

    public static function apply_prefs_shortcode_youtube($atts, $content = null)
    {
        $content = 'http://www.youtube.com/watch?v=' . trim($content);
        $currfilter = current_filter();
        if (preg_match(self::$justurlregex, $content))
        {
            return self::get_html(array($content), $currfilter == 'widget_text' ? false : true);
        }
        return '';
    }

    public static function apply_prefs_content($content)
    {
        $content = preg_replace_callback(self::$ytregex, "YouTubePrefs::get_html_content", $content);
        return $content;
    }

    public static function apply_prefs_widget($content)
    {
        $content = preg_replace_callback(self::$ytregex, "YouTubePrefs::get_html_widget", $content);
        return $content;
    }

    public static function get_html_content($m)
    {
        return self::get_html($m, true);
    }

    public static function get_html_widget($m)
    {
        return self::get_html($m, false);
    }

    public static function get_gallery_page($playlistId, $pageToken, $pageSize, $columns, $style, $apiKey)
    {
        if (empty($apiKey))
        {
            return '<div>Please enter your YouTube API key to embed galleries.</div>';
        }
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && $style == 'listview')
        {
            //$columns = 1;
        }

        $apiEndpoint = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,status&playlistId=' . $playlistId
                . '&maxResults=' . $pageSize
                . '&key=' . $apiKey;
        if ($pageToken != null)
        {
            $apiEndpoint .= '&pageToken=' . $pageToken;
        }
        $spdckey = '';
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1)
        {
            try
            {
                $spdckey = self::$spdcprefix . '_' . md5($apiEndpoint . $columns . $style);
                $spdcval = get_transient($spdckey);
                if (!empty($spdcval))
                {
                    //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $spdcval . "\r\n");
                    return $spdcval;
                }
            }
            catch (Exception $ex)
            {
                
            }
        }

        $code = '';

        $apiResult = wp_remote_get($apiEndpoint);
        $jsonResult = json_decode($apiResult['body']);

        $resultsPerPage = $jsonResult->pageInfo->resultsPerPage;
        $totalResults = $jsonResult->pageInfo->totalResults;

        $nextPageToken = '';
        $prevPageToken = '';
        if (isset($jsonResult->nextPageToken))
        {
            $nextPageToken = $jsonResult->nextPageToken;
        }

        if (isset($jsonResult->prevPageToken))
        {
            $prevPageToken = $jsonResult->prevPageToken;
        }

        $cnt = 0;
        foreach ($jsonResult->items as $item)
        {

            $thumb = new stdClass();

            $thumb->id = isset($item->snippet->resourceId->videoId) ? $item->snippet->resourceId->videoId : null;
            $thumb->id = $thumb->id ? $thumb->id : $item->id->videoId;
            $thumb->title = $item->snippet->title;
            $thumb->privacyStatus = isset($item->status->privacyStatus) ? $item->status->privacyStatus : null;

            if ($thumb->privacyStatus == 'private')
            {
                $thumb->img = plugins_url('/images/private.png', __FILE__);
                $thumb->quality = 'medium';
            }
            else
            {
                if (isset($item->snippet->thumbnails->high->url))
                {
                    $thumb->img = $item->snippet->thumbnails->high->url;
                    $thumb->quality = 'high';
                }
                elseif (isset($item->snippet->thumbnails->default->url))
                {
                    $thumb->img = $item->snippet->thumbnails->default->url;
                    $thumb->quality = 'default';
                }
                else
                {
                    $thumb->img = $item->snippet->thumbnails->medium->url;
                    $thumb->quality = 'medium';
                }
            }

            $code .= self::get_thumbnail_html($thumb, $style);
            $cnt++;

            if ($cnt % $columns === 0)
            {
                $code .= '<div class="epyt-gallery-rowbreak"></div>';
            }
        }

        $totalPages = ceil($totalResults / $resultsPerPage);
        $pagination = '<div class="epyt-pagination">';
        if ($totalPages > 1)
        {
            if (!empty($prevPageToken))
            {
                $pagination .= '<div class="epyt-pagebutton epyt-prev" data-playlistid="' . esc_attr($playlistId)
                        . '" data-pagesize="' . intval($pageSize)
                        . '" data-pagetoken="' . esc_attr($prevPageToken)
                        . '" data-style="' . esc_attr($style)
                        . '" data-columns="' . intval($columns)
                        . '"><div>&laquo;</div> ' . _('Prev') . '</div>';
            }

            $pagination .= '<div class="epyt-pagenumbers">';
            $pagination .= '<div class="epyt-current">1</div><div class="epyt-pageseparator">' . _("of") . '</div><div class="epyt-totalpages">' . $totalPages . '</div>';
            $pagination .= '</div>';

            if (!empty($nextPageToken))
            {
                $pagination .= '<div class="epyt-pagebutton epyt-next" data-playlistid="' . esc_attr($playlistId)
                        . '" data-pagesize="' . intval($pageSize)
                        . '" data-pagetoken="' . esc_attr($nextPageToken)
                        . '" data-style="' . esc_attr($style)
                        . '" data-columns="' . intval($columns)
                        . '">' . _('Next') . ' <div>&raquo;</div></div>';
            }
            $pagination .= '<div class="epyt-loader"><img src="' . plugins_url('images/gallery-page-loader.gif', __FILE__) . '"></div>';
        }
        $pagination .= '</div>';

        $code = $pagination . $code . $pagination;

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1)
        {
            $daysecs = self::$alloptions[self::$opt_spdcexp] * 60 * 60;
            set_transient($spdckey, $code, $daysecs);
            $allk = get_option(self::$spdcall, array());
            $allk[] = $spdckey;
            update_option(self::$spdcall, $allk);

            //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $code . "\r\n");
        }
        return $code;
    }

    public static function get_thumbnail_html($thumb, $style)
    {
        $escId = esc_attr($thumb->id);
        $code = '';
        $styleclass = '';

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && $style == 'listview')
        {
            $styleclass = 'epyt-listview';
        }

        $code .= '<div data-videoid="' . $escId . '" class="epyt-gallery-thumb ' . $styleclass . '">';
        $code .= '<div class="epyt-gallery-img-box"><div class="epyt-gallery-img" style="background-image: url(' . esc_attr($thumb->img) . ')">' .
                '<div class="epyt-gallery-playhover"><img class="epyt-play-img" src="' . plugins_url('images/playhover.png', __FILE__) . '" /><div class="epyt-gallery-playcrutch"></div></div>' .
                '</div></div>';
        $code .= '<div class="epyt-gallery-title">' . esc_html($thumb->title) . '</div>';
        $code .= '</div>';


        return $code;
    }

    public static function my_embedplus_gallery_page()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            check_ajax_referer('embedplus-nonce', 'security');
            echo self::get_gallery_page($_POST['playlistId'], $_POST['pageToken'], $_POST['pageSize'], $_POST['columns'], $_POST['style'], self::$alloptions[self::$opt_apikey]);
            die();
        }
    }

    public static function get_html($m, $iscontent)
    {
        //$time_start = microtime(true);

        $link = trim(str_replace(self::$badentities, self::$goodliterals, $m[0]));

        $link = preg_replace('/\s/', '', $link);
        $linkparamstemp = explode('?', $link);

        $linkparams = array();
        if (count($linkparamstemp) > 1)
        {
            $linkparams = self::keyvalue($linkparamstemp[1], true);
        }
        if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
        {
            $vtemp = explode('/', $linkparamstemp[0]);
            $linkparams['v'] = array_pop($vtemp);
        }

        //$linkscheme = 'http';
        $youtubebaseurl = 'youtube';
        $schemaorgoutput = '';
        $voloutput = '';
        $dynsrc = '';
        $dyntype = '';
        $acctitle = '';

        $finalparams = $linkparams + self::$alloptions;

        $spdckey = '';
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1)
        {
            try
            {
                $kparams = $finalparams;
                ksort($kparams);
                $jparams = json_encode($kparams);
                $spdckey = self::$spdcprefix . '_' . md5($jparams);
                $spdcval = get_transient($spdckey);
                if (!empty($spdcval))
                {
                    //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $spdcval . "\r\n");
                    return $spdcval;
                }
            }
            catch (Exception $ex)
            {
                
            }
        }

        self::init_dimensions($link, $linkparams, $finalparams);

        if (self::$alloptions[self::$opt_nocookie] == 1)
        {
            $youtubebaseurl = 'youtube-nocookie';
        }


//        if (self::$alloptions[self::$opt_ssl] == 1)
//        {
//            $linkscheme = 'https';
//        }

        if (self::$alloptions[self::$opt_defaultvol] == 1)
        {
            $voloutput = ' data-vol="' . self::$alloptions[self::$opt_vol] . '" ';
        }


//        if (!(self::$alloptions[self::$opt_dohl] == 1 && isset($finalparams[self::$opt_hl]) && strlen($finalparams[self::$opt_hl]) == 2))
//        {
//            unset($finalparams[self::$opt_hl]);
//        }
        if (self::$alloptions[self::$opt_dohl] == 1)
        {
            $locale = get_locale();
            $finalparams[self::$opt_hl] = $locale;
        }
        else
        {
            unset($finalparams[self::$opt_hl]);
        }

        if (isset($finalparams[self::$opt_html5]) && $finalparams[self::$opt_html5] == 0)
        {
            unset($finalparams[self::$opt_html5]);
        }

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {

            if (self::$alloptions[self::$opt_schemaorg] == 1 && isset($finalparams['v']))
            {
                $schemaorgoutput = self::getschemaorgoutput($finalparams['v']);
            }



            if (self::$alloptions[self::$opt_dynload] == 1
            //&& $finalparams[self::$opt_autoplay] != 1
            )
            {
                $dynsrc = 'data-ep-';
                $dyntype = ' data-ep-a="' . self::$alloptions[self::$opt_dyntype] . '" ';
            }

            if (isset($linkparams[self::$opt_vol]) && is_numeric(trim($linkparams[self::$opt_vol])))
            {
                $voloutput = ' data-vol="' . $linkparams[self::$opt_vol] . '" ';
            }
        }
        else
        {
            if (isset($finalparams[self::$opt_vol]))
            {
                unset($finalparams[self::$opt_vol]);
            }
        }

        $centercode = '';
        if ($finalparams[self::$opt_center] == 1)
        {
            $centercode = ' style="display: block; margin: 0px auto;" ';
        }

        if (self::$alloptions[self::$opt_acctitle] == 1)
        {
            try
            {
                //attr escape
                if (self::$oembeddata)
                {
                    $acctitle = self::$oembeddata->title;
                }
                else
                {
                    $odata = self::get_oembed('http://youtube.com/watch?v=' . $linkparams['v'], 1920, 1280);
                    $acctitle = $odata->title;
                }

                if ($acctitle)
                {
                    $acctitle = ' title="' . esc_attr($acctitle) . '" ';
                }
            }
            catch (Exception $e)
            {
                
            }
        }

        // playlist cleanup
        $videoidoutput = isset($linkparams['v']) ? $linkparams['v'] : '';

        if ((self::$alloptions[self::$opt_playlistorder] == 1 || isset($finalparams['plindex'])) && isset($finalparams['list']))
        {
            try
            {
                $videoidoutput = '';
                $finalparams['index'] = intval($finalparams['plindex']);
            }
            catch (Exception $ex)
            {
                
            }
        }

        $galleryWrapper1 = '';
        $galleryWrapper2 = '';
        $galleryCode = '';
        if (
                isset($finalparams['layout']) && strtolower($finalparams['layout']) == 'gallery' && isset($finalparams['list'])
        )
        {
            $dynsrc = 'data-ep-gallery';
            $galleryWrapper1 = '<div class="epyt-gallery" data-currpage="1">';
            $galleryWrapper2 = '</div>';
            $galleryCode = '<div class="epyt-gallery-list">' . self::get_gallery_page($finalparams['list'], null, $finalparams[self::$opt_gallery_pagesize], intval($finalparams[self::$opt_gallery_columns]), $finalparams[self::$opt_gallery_style], self::$alloptions[self::$opt_apikey]) . '</div>';
            $videoidoutput = 'GALLERYVIDEOID';
        }

        $code1 = '<iframe ' . $dyntype . $centercode . ' id="_ytid_' . rand(10000, 99999) . '" width="' . self::$defaultwidth . '" height="' . self::$defaultheight .
                '" ' . $dynsrc . 'src="//www.' . $youtubebaseurl . '.com/embed/' . $videoidoutput . '?';
        $code2 = '" frameborder="0" type="text/html" class="__youtube_prefs__' . ($iscontent ? '' : ' __youtube_prefs_widget__') .
                '"' . $voloutput . $acctitle . ' allowfullscreen webkitallowfullscreen mozallowfullscreen ></iframe>' . $schemaorgoutput;

        $origin = '';

        try
        {
            if (self::$alloptions[self::$opt_origin] == 1)
            {
                $url_parts = parse_url(site_url());
                $origin = 'origin=' . $url_parts['scheme'] . '://' . $url_parts['host'] . '&';
            }
        }
        catch (Exception $e)
        {
            $origin = '';
        }
        $finalsrc = 'enablejsapi=1&' . $origin;

        if (count($finalparams) > 1)
        {
            foreach ($finalparams as $key => $value)
            {
                if (in_array($key, self::$yt_options))
                {
                    if (!empty($galleryCode) && ($key == 'listType' || $key == 'list'))
                    {
                        
                    }
                    else
                    {
                        $finalsrc .= htmlspecialchars($key) . '=' . htmlspecialchars($value) . '&';
                        if ($key == 'loop' && $value == 1 && !isset($finalparams['list']))
                        {
                            $finalsrc .= 'playlist=' . $finalparams['v'] . '&';
                        }
                    }
                }
            }
        }

        $code = $galleryWrapper1 . $code1 . $finalsrc . $code2 . $galleryCode . $galleryWrapper2; //. '<!--' . $m[0] . '-->';
        // reset static vals for next embed
        self::$defaultheight = null;
        self::$defaultwidth = null;
        self::$oembeddata = null;





        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1)
        {
            $daysecs = self::$alloptions[self::$opt_spdcexp] * 60 * 60;
            set_transient($spdckey, $code, $daysecs);
            $allk = get_option(self::$spdcall, array());
            $allk[] = $spdckey;
            update_option(self::$spdcall, $allk);

            //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $code . "\r\n");
        }
        return $code;
    }

    public static function debuglog($str)
    {
        $handle = fopen(__DIR__ . "\\debug.txt", "a+");
        fwrite($handle, $str);
        fclose($handle);
    }

    public static function spdcpurge()
    {
        $allk = get_option(self::$spdcall, array()); {
            foreach ($allk as $t)
            {
                $success = delete_transient($t);
            }
            update_option(self::$spdcall, array());
        }
    }

    public static function keyvalue($qry, $includev)
    {
        $ytvars = explode('&', $qry);
        $ytkvp = array();
        foreach ($ytvars as $k => $v)
        {
            $kvp = explode('=', $v);
            if (count($kvp) == 2 && ($includev || strtolower($kvp[0]) != 'v'))
            {
                $ytkvp[$kvp[0]] = $kvp[1];
            }
        }

        return $ytkvp;
    }

    public static function getschemaorgoutput($vidid)
    {
        $schemaorgcode = '';
        try
        {
            $ytapilink = 'https://www.googleapis.com/youtube/v3/videos?id=' . $vidid . '&part=contentDetails,snippet&key=' . self::$alloptions[self::$opt_apikey];


            $apidata = wp_remote_get($ytapilink);
            if (!is_wp_error($apidata))
            {
                $raw = wp_remote_retrieve_body($apidata);
                if (!empty($raw))
                {
                    $json = json_decode($raw, true);
                    if (is_array($json))
                    {
                        $_name = esc_attr(sanitize_text_field(str_replace("@", "&#64;", $json['items'][0]['snippet']['title'])));
                        $_description = esc_attr(sanitize_text_field(str_replace("@", "&#64;", $json['items'][0]['snippet']['description'])));
                        $_thumbnailUrl = esc_url("http://i.ytimg.com/vi/" . $vidid . "/0.jpg");
                        $_duration = $json['items'][0]['contentDetails']['duration']; // "T0H9M35S" "PT9M35S"
                        $_uploadDate = sanitize_text_field($json['items'][0]['snippet']['publishedAt']); // "2014-10-03T15:30:12.000Z"

                        $schemaorgcode = '<span itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
                        $schemaorgcode .= '<meta itemprop="embedURL" content="http://www.youtube.com/embed/' . $vidid . '">';
                        $schemaorgcode .= '<meta itemprop="name" content="' . $_name . '">';
                        $schemaorgcode .= '<meta itemprop="description" content="' . $_description . '">';
                        $schemaorgcode .= '<meta itemprop="thumbnailUrl" content="' . $_thumbnailUrl . '">';
                        $schemaorgcode .= '<meta itemprop="duration" content="' . $_duration . '">';
                        $schemaorgcode .= '<meta itemprop="uploadDate" content="' . $_uploadDate . '">';
                        $schemaorgcode .= '</span>';
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            
        }
        return $schemaorgcode;
    }

    public static function secondsToDuration($seconds)
    {
        $remaining = $seconds;
        $parts = array();
        $multipliers = array(
            'hours' => 3600,
            'minutes' => 60,
            'seconds' => 1
        );

        foreach ($multipliers as $type => $m)
        {
            $parts[$type] = (int) ($remaining / $m);
            $remaining -= ($parts[$type] * $m);
        }

        return $parts;
    }

    public static function formatDuration($parts)
    {
        $default = array(
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0
        );

        extract(array_merge($default, $parts));

        return "T{$hours}H{$minutes}M{$seconds}S";
    }

    public static function init_dimensions($url, $urlkvp, $finalparams)
    {
        // get default dimensions; try embed size in settings, then try theme's content width, then just 480px
        if (self::$defaultwidth == null)
        {
            global $content_width;
            if (empty($content_width))
            {
                $content_width = $GLOBALS['content_width'];
            }

            if (isset($urlkvp['width']) && is_numeric($urlkvp['width']))
            {
                self::$defaultwidth = $urlkvp['width'];
            }
            else if (self::$alloptions[self::$opt_defaultdims] == 1 && (isset(self::$alloptions[self::$opt_defaultwidth]) && is_numeric(self::$alloptions[self::$opt_defaultwidth])))
            {
                self::$defaultwidth = self::$alloptions[self::$opt_defaultwidth];
            }
            else if (self::$optembedwidth)
            {
                self::$defaultwidth = self::$optembedwidth;
            }
            else if ($content_width)
            {
                self::$defaultwidth = $content_width;
            }
            else
            {
                self::$defaultwidth = 480;
            }



            if (isset($urlkvp['height']) && is_numeric($urlkvp['height']))
            {
                self::$defaultheight = $urlkvp['height'];
            }
            else if (self::$alloptions[self::$opt_defaultdims] == 1 && (isset(self::$alloptions[self::$opt_defaultheight]) && is_numeric(self::$alloptions[self::$opt_defaultheight])))
            {
                self::$defaultheight = self::$alloptions[self::$opt_defaultheight];
            }
            else
            {
                self::$defaultheight = self::get_aspect_height($url, $urlkvp, $finalparams);
            }
        }
    }

    public static function get_oembed($url, $height, $width)
    {
        require_once( ABSPATH . WPINC . '/class-oembed.php' );
        $oembed = _wp_oembed_get_object();
        $args = array();
        $args['width'] = $width;
        $args['height'] = $height;
        $args['discover'] = false;
        self::$oembeddata = $oembed->fetch('https://www.youtube.com/oembed', $url, $args);
        return self::$oembeddata;
    }

    public static function get_aspect_height($url, $urlkvp, $finalparams)
    {

        // attempt to get aspect ratio correct height from oEmbed
        $aspectheight = round((self::$defaultwidth * 9) / 16, 0);


        if ($url)
        {
            $odata = self::get_oembed($url, self::$defaultwidth, self::$defaultwidth);

            if ($odata)
            {
                $aspectheight = $odata->height;
            }
        }

        if ($finalparams[self::$opt_controls] != 0 && $finalparams[self::$opt_autohide] != 1)
        {
            //add 28 for YouTube's own bar
            $aspectheight += 28;
        }
        return $aspectheight;
    }

    public static function doftpostimg($postid, $post, $update)
    {
        if (current_user_can('edit_posts') && current_user_can('edit_pages'))
        {
            if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !in_array($post->post_status, array('publish', 'pending', 'draft', 'future', 'private')))
            {
                return;
            }
            try
            {
                self::doftpostimgfor($post);
            }
            catch (Exception $ex)
            {
                // display error message
            }
        }
    }

    public static function doftpostimgfor($post)
    {
        $search_content = isset($post->post_content) ? $post->post_content : '';
        $search_content = substr($search_content, 0, 1500);

        $search_content = apply_filters('youtube_embedplus_video_content', $search_content);

        $vid_match = null;
        if ($search_content && $post->ID && !has_post_thumbnail($post->ID) && preg_match(self::$justurlregex, $search_content, $vid_match)
        )
        {

            $first_vid_link = trim(str_replace(self::$badentities, self::$goodliterals, $vid_match[0]));

            $first_vid_link = preg_replace('/\s/', '', $first_vid_link);
            $linkparamstemp = explode('?', $first_vid_link);

            $linkparams = array();
            if (count($linkparamstemp) > 1)
            {
                $linkparams = self::keyvalue($linkparamstemp[1], true);
            }
            if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
            {
                $vtemp = explode('/', $linkparamstemp[0]);
                $linkparams['v'] = array_pop($vtemp);
            }



            $just_id = $linkparams['v'];
            $ftimgurl = null;
            if ($just_id)
            {
                require_once( ABSPATH . WPINC . '/class-oembed.php' );
                $oembed = _wp_oembed_get_object();
                $args = array();
                $args['width'] = 1920;
                $args['height'] = 1080;
                $args['discover'] = false;
                $odata = $oembed->fetch('https://www.youtube.com/oembed', 'http://youtube.com/watch?v=' . $just_id, $args);

                if ($odata)
                {
                    $ftimgurl = $odata->thumbnail_url;
                }
            }

            $ftimgid = $ftimgurl && !is_wp_error($ftimgurl) ? self::media_sideload($ftimgurl, $post->ID, sanitize_title(preg_replace("/[^a-zA-Z0-9\s]/", "-", $post->title))) : 0;

            if (!$ftimgid)
            {
                return;
            }

            set_post_thumbnail($post->ID, $ftimgid);
        }
    }

    public static function media_sideload($url, $post_id, $filename = null)
    {
        if (!$url || !$post_id)
        {
            return new WP_Error('missing', __('Please provide a valid URL and post ID', ''));
        }

        $post_title = get_the_title($post_id);

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $tmp = download_url($url);

        if (is_wp_error($tmp))
        {
            @unlink($file_array['tmp_name']);
            $file_array['tmp_name'] = '';
            return $tmp;
        }

        preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);
        $url_filename = basename($matches[0]);
        $url_type = wp_check_filetype($url_filename);

        if (!empty($filename))
        {
            $filename = sanitize_file_name($filename);
            $tmppath = pathinfo($tmp);
            $new = $tmppath['dirname'] . '/' . $filename . '.' . $tmppath['extension'];
            rename($tmp, $new);
            $tmp = $new;
        }

        $file_array['tmp_name'] = $tmp;
        if (!empty($filename))
        {
            $file_array['name'] = $filename . '.' . $url_type['ext'];
        }
        else
        {
            $file_array['name'] = $url_filename;
        }

        $post_data = array(
            'post_title' => $post_title,
            'post_parent' => $post_id,
        );

        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $att_id = media_handle_sideload($file_array, $post_id, null, $post_data);

        if (is_wp_error($att_id))
        {
            @unlink($file_array['tmp_name']);
            return $att_id;
        }

        return $att_id;
    }

    public static function do_ogvideo()
    {
        global $wp_query;
        $the_content = $wp_query->post->post_content;
        $matches = Array();
        $ismatch = preg_match_all(self::$justurlregex, $the_content, $matches);

        if ($ismatch)
        {
            $match = $matches[0][0];

            $link = trim(preg_replace('/&amp;/i', '&', $match));
            $link = preg_replace('/\s/', '', $link);
            $link = trim(str_replace(self::$badentities, self::$goodliterals, $link));

            $linkparamstemp = explode('?', $link);

            $linkparams = array();
            if (count($linkparamstemp) > 1)
            {
                $linkparams = self::keyvalue($linkparamstemp[1], true);
            }
            if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
            {
                $vtemp = explode('/', $linkparamstemp[0]);
                $linkparams['v'] = array_pop($vtemp);
            }
            ?>
            <meta property="og:type" content="video">
            <meta property="og:video" content="https://www.youtube.com/v/<?php echo $linkparams['v']; ?>?autohide=1&amp;version=3">
            <meta property="og:video:type" content="application/x-shockwave-flash">
            <meta property="og:video:width" content="480">
            <meta property="og:video:height" content="360">
            <meta property="og:image" content="https://img.youtube.com/vi/<?php echo $linkparams['v']; ?>/0.jpg">
            <?php
        }
    }

    public static function ytprefs_plugin_menu()
    {
        //add_menu_page('YouTube Settings', 'YouTube', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.00392854349');

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            add_menu_page('YouTube Settings', 'YouTube PRO', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.000392854349');
            //add_menu_page('YouTube Analytics Dashboard', 'PRO Analytics', 'manage_options', 'youtube-ep-analytics-dashboard', 'YouTubePrefs::epstats_show_options', plugins_url('images/epstats16.png', __FILE__), '10.000492884349');
            add_submenu_page('youtube-my-preferences', '', '', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options');
            add_submenu_page('youtube-my-preferences', 'YouTube Analytics Dashboard', '<img style="width: 16px; height: 16px; vertical-align: text-top;" src="' . plugins_url('images/epstats16.png', __FILE__) . '" />&nbsp;&nbsp;PRO Analytics', 'manage_options', 'youtube-ep-analytics-dashboard', 'YouTubePrefs::epstats_show_options');
        }
        else
        {
            add_menu_page('YouTube Settings', 'YouTube Free', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options', plugins_url('images/youtubeicon16.png', __FILE__), '10.000392854349');
            add_submenu_page('youtube-my-preferences', '', '', 'manage_options', 'youtube-my-preferences', 'YouTubePrefs::ytprefs_show_options');
            add_submenu_page('youtube-my-preferences', 'YouTube PRO', '<img style="width: 16px; height: 16px; vertical-align: text-top;" src="' . plugins_url('images/iconwizard.png', __FILE__) . '" />&nbsp;&nbsp;YouTube PRO', 'manage_options', 'youtube-ep-analytics-dashboard', 'YouTubePrefs::epstats_show_options');
        }
        add_submenu_page(null, 'YouTube Posts', 'YouTube Posts', 'manage_options', 'youtube-ep-glance', 'YouTubePrefs::glance_page');
    }

    public static function epstats_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (self::$double_plugin)
        {
            //add_action('admin_notices', array("YouTubePrefs", "double_plugin_warning"));
            self::double_plugin_warning();
        }


        // Now display the settings editing screen
        ?>
        <div class="wrap">
            <style type="text/css">
                .wrap {font-family: Arial;}
                .epicon { width: 20px; height: 20px; vertical-align: middle; padding-right: 5px;}
                .epindent {padding-left: 25px;}
                iframe.shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
                .bold {font-weight: bold;}
                .orange {color: #f85d00;}
            </style>
            <br>
            <?php
            $thishost = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "");
            $thiskey = self::$alloptions[self::$opt_pro];

            $dashurl = self::$epbase . "/dashboard/pro-easy-video-analytics.aspx?ref=protab&domain=" . $thishost . "&prokey=" . $thiskey;

            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
            {
                //// header
                echo "<h2>" . '<img src="' . plugins_url('images/epstats16.png', __FILE__) . '" /> ' . __('YouTube Analytics Dashboard') . "</h2>";
                echo '<p><i>Logging you in below... (You can also <a class="button-primary" target="_blank" href="' . $dashurl . '">click here</a> to launch your dashboard in a new tab)</i></p>';
            }
            else
            {
                //// header
                echo "<h2>" . '<img style="vertical-align: text-bottom;" src="' . plugins_url('images/iconwizard.png', __FILE__) . '" /> ' . __('YouTube Plugin PRO') . "</h2><p class='bold orange'>This tab is here to provide direct access to analytics. Graphs and other data about your site will show below after you activate PRO.</p><br>";
            }
            ?>
            <iframe class="shadow" src="<?php echo $dashurl ?>" width="1060" height="2700" scrolling="auto"/>
        </div>
        <?php
    }

    public static function my_embedplus_pro_record()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $tmppro = preg_replace('/[^A-Za-z0-9-]/i', '', $_REQUEST[self::$opt_pro]);
            $new_options = array();
            $new_options[self::$opt_pro] = $tmppro;
            $all = get_option(self::$opt_alloptions);
            $all = $new_options + $all;
            update_option(self::$opt_alloptions, $all);

            if (strlen($tmppro) > 0)
            {
                $result['type'] = 'success';
            }
            else
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function my_embedplus_clearspdc()
    {
        $result = array();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            try
            {
                self::spdcpurge();
                $result['type'] = 'success';
            }
            catch (Exception $ex)
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function custom_admin_pointers_check()
    {
        //return false; // ooopointer shut all off;
        $admin_pointers = self::custom_admin_pointers();
        foreach ($admin_pointers as $pointer => $array)
        {
            if ($array['active'])
                return true;
        }
    }

    public static function glance_script()
    {
        add_thickbox();
        ?>
        <script type="text/javascript">
            function widen_ytprefs_glance() {
                setTimeout(function() {
                    jQuery("#TB_window").animate({marginLeft: '-' + parseInt((780 / 2), 10) + 'px', width: '780px'}, 300);
                    jQuery("#TB_window iframe").animate({width: '780px'}, 300);
                }, 15);
            }

            (function($j)
            {
                $j(document).ready(function() {

                    $j.ajax({
                        type: "post",
                        dataType: "json",
                        timeout: 30000,
                        url: wpajaxurl,
                        data: {action: 'my_embedplus_glance_count'},
                        success: function(response) {
                            if (response.type == "success") {
                                $j(response.container).append(response.data);
                                $j(".ytprefs_glance_button").click(widen_ytprefs_glance);
                                $j(window).resize(widen_ytprefs_glance);
                                if (typeof ep_do_pointers == 'function')
                                {
                                    //ep_do_pointers($j);
                                }
                            }
                            else {
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {

                        },
                        complete: function() {
                        }
                    });

                });

            })(jQuery);
        </script>
        <?php
    }

    public static function custom_admin_pointers_footer()
    {
        $admin_pointers = self::custom_admin_pointers();
        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            function ep_do_pointers($)
            {
        <?php
        foreach ($admin_pointers as $pointer => $array)
        {
            if ($array['active'])
            {
                ?>
                        $('<?php echo $array['anchor_id']; ?>').pointer({
                            content: '<?php echo $array['content']; ?>',
                            position: {
                                edge: '<?php echo $array['edge']; ?>',
                                align: '<?php echo $array['align']; ?>'
                            },
                            close: function() {
                                $.post(wpajaxurl, {
                                    pointer: '<?php echo $pointer; ?>',
                                    action: 'dismiss-wp-pointer'
                                });
                            }
                        }).pointer('open');
                <?php
            }
        }
        ?>
            }

            ep_do_pointers(jQuery); // switch off all pointers via js ooopointer
            /* ]]> */
        </script>
        <?php
    }

    public static function custom_admin_pointers()
    {
        $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        $version = str_replace('.', '_', self::$version); // replace all periods in version with an underscore
        $prefix = 'custom_admin_pointers' . $version . '_';

        $new_pointer_content = '<h3>' . __('New Update') . '</h3>'; // ooopointer

        $new_pointer_content .= '<p>'; // ooopointer
        if (!(self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0))
        {
            $new_pointer_content .= __("This update improves playlist and channel features in FREE and <a target=_blank href=" . self::$epbase . '/dashboard/pro-easy-video-analytics.aspx?ref=frompointer' . ">PRO galleries &raquo;</a>");
        }
        else
        {
            $new_pointer_content .= __("This update improves playlist and channel features in FREE and PRO galleries.");
        }
        $new_pointer_content .= '</p>';

        return array(
            $prefix . 'new_items' => array(
                'content' => $new_pointer_content,
                'anchor_id' => 'a.toplevel_page_youtube-my-preferences', //'#ytprefs_glance_button', 
                'edge' => 'top',
                'align' => 'left',
                'active' => (!in_array($prefix . 'new_items', $dismissed) )
            ),
        );
    }

    public static function postchecked($idx)
    {
        return isset($_POST[$idx]) && $_POST[$idx] == (true || 'on');
    }

    public static function ytprefs_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (self::$double_plugin)
        {
            //add_action('admin_notices', array("YouTubePrefs", "double_plugin_warning"));
            self::double_plugin_warning();
        }


        // variables for the field and option names 
        $ytprefs_submitted = 'ytprefs_submitted';

        // Read in existing option values from database

        $all = get_option(self::$opt_alloptions);

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$ytprefs_submitted]) && $_POST[$ytprefs_submitted] == 'Y')
        {
            // Read their posted values

            $new_options = array();
            $new_options[self::$opt_center] = self::postchecked(self::$opt_center) ? 1 : 0;
            $new_options[self::$opt_glance] = self::postchecked(self::$opt_glance) ? 1 : 0;
            $new_options[self::$opt_autoplay] = self::postchecked(self::$opt_autoplay) ? 1 : 0;
            $new_options[self::$opt_cc_load_policy] = self::postchecked(self::$opt_cc_load_policy) ? 1 : 0;
            $new_options[self::$opt_iv_load_policy] = self::postchecked(self::$opt_iv_load_policy) ? 1 : 3;
            $new_options[self::$opt_loop] = self::postchecked(self::$opt_loop) ? 1 : 0;
            $new_options[self::$opt_modestbranding] = self::postchecked(self::$opt_modestbranding) ? 1 : 0;
            $new_options[self::$opt_rel] = self::postchecked(self::$opt_rel) ? 1 : 0;
            $new_options[self::$opt_showinfo] = self::postchecked(self::$opt_showinfo) ? 1 : 0;
            $new_options[self::$opt_playsinline] = self::postchecked(self::$opt_playsinline) ? 1 : 0;
            $new_options[self::$opt_origin] = self::postchecked(self::$opt_origin) ? 1 : 0;
            $new_options[self::$opt_controls] = self::postchecked(self::$opt_controls) ? 2 : 0;
            $new_options[self::$opt_autohide] = self::postchecked(self::$opt_autohide) ? 1 : 2;
            $new_options[self::$opt_html5] = self::postchecked(self::$opt_html5) ? 1 : 0;
            $new_options[self::$opt_theme] = self::postchecked(self::$opt_theme) ? 'dark' : 'light';
            $new_options[self::$opt_color] = self::postchecked(self::$opt_color) ? 'red' : 'white';
            $new_options[self::$opt_wmode] = self::postchecked(self::$opt_wmode) ? 'opaque' : 'transparent';
            $new_options[self::$opt_vq] = self::postchecked(self::$opt_vq) ? 'hd720' : '';
            $new_options[self::$opt_nocookie] = self::postchecked(self::$opt_nocookie) ? 1 : 0;
            $new_options[self::$opt_playlistorder] = self::postchecked(self::$opt_playlistorder) ? 1 : 0;
            $new_options[self::$opt_acctitle] = self::postchecked(self::$opt_acctitle) ? 1 : 0;
            $new_options[self::$opt_ogvideo] = self::postchecked(self::$opt_ogvideo) ? 1 : 0;
            $new_options[self::$opt_migrate] = self::postchecked(self::$opt_migrate) ? 1 : 0;
            $new_options[self::$opt_migrate_youtube] = self::postchecked(self::$opt_migrate_youtube) ? 1 : 0;
            //$new_options[self::$opt_ssl] = self::postchecked(self::$opt_ssl) ? 1 : 0;
            $new_options[self::$opt_oldspacing] = self::postchecked(self::$opt_oldspacing) ? 1 : 0;
            $new_options[self::$opt_responsive] = self::postchecked(self::$opt_responsive) ? 1 : 0;
            $new_options[self::$opt_widgetfit] = self::postchecked(self::$opt_widgetfit) ? 1 : 0;
            $new_options[self::$opt_schemaorg] = self::postchecked(self::$opt_schemaorg) ? 1 : 0;
            $new_options[self::$opt_ftpostimg] = self::postchecked(self::$opt_ftpostimg) ? 1 : 0;
            $new_options[self::$opt_spdc] = self::postchecked(self::$opt_spdc) ? 1 : 0;
            $new_options[self::$opt_dynload] = self::postchecked(self::$opt_dynload) ? 1 : 0;
            $new_options[self::$opt_defaultdims] = self::postchecked(self::$opt_defaultdims) ? 1 : 0;
            $new_options[self::$opt_defaultvol] = self::postchecked(self::$opt_defaultvol) ? 1 : 0;
            $new_options[self::$opt_dohl] = self::postchecked(self::$opt_dohl) ? 1 : 0;

            $_defaultwidth = '';
            try
            {
                $_defaultwidth = is_numeric(trim($_POST[self::$opt_defaultwidth])) ? intval(trim($_POST[self::$opt_defaultwidth])) : $_defaultwidth;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_defaultwidth] = $_defaultwidth;

            $_defaultheight = '';
            try
            {
                $_defaultheight = is_numeric(trim($_POST[self::$opt_defaultheight])) ? intval(trim($_POST[self::$opt_defaultheight])) : $_defaultheight;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_defaultheight] = $_defaultheight;

            $_vol = '';
            try
            {
                $_vol = is_numeric(trim($_POST[self::$opt_vol])) ? intval(trim($_POST[self::$opt_vol])) : $_vol;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_vol] = $_vol;

            $_gallery_pagesize = 12;
            try
            {
                $_gallery_pagesize = is_numeric(trim($_POST[self::$opt_gallery_pagesize])) ? intval(trim($_POST[self::$opt_gallery_pagesize])) : $_gallery_pagesize;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_gallery_pagesize] = $_gallery_pagesize;


            $_gallery_columns = 3;
            try
            {
                $_gallery_columns = is_numeric(trim($_POST[self::$opt_gallery_columns])) ? intval(trim($_POST[self::$opt_gallery_columns])) : $_gallery_columns;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_gallery_columns] = $_gallery_columns;

            $_gallery_style = 'grid';
            try
            {
                $_gallery_style = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_gallery_style])));
            }
            catch (Exception $ex)
            {
                $_gallery_style = 'grid';
            }
            $new_options[self::$opt_gallery_style] = $_gallery_style;


            $_apikey = '';
            try
            {
                $_apikey = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_apikey])));
                if (empty($_apikey))
                {
                    $_apikey = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_gallery_apikey])));
                }
            }
            catch (Exception $ex)
            {
                $_apikey = '';
            }
            $new_options[self::$opt_apikey] = $_apikey;

            $_hl = '';
            try
            {
                $temphl = strtolower(trim($_POST[self::$opt_hl]));
                $_hl = preg_match('/^[a-z][a-z]$/i', $temphl) ? $temphl : '';
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_hl] = $_hl;

            $_dyntype = '';
            try
            {
                $tempdyntype = trim($_POST[self::$opt_dyntype]);
                $_dyntype = preg_match('/^[a-zA-Z-]+$/i', $tempdyntype) ? $tempdyntype : '';
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_dyntype] = $_dyntype;

            $_spdcexp = 24;
            try
            {
                $_spdcexp = is_numeric(trim($_POST[self::$opt_spdcexp])) ? intval(trim($_POST[self::$opt_spdcexp])) : $_spdcexp;
            }
            catch (Exception $ex)
            {
                
            }
            $new_options[self::$opt_spdcexp] = $_spdcexp;



            $all = $new_options + $all;

            update_option(self::$opt_alloptions, $all);

            try
            {
                self::spdcpurge();
                if ($all[self::$opt_spdc] == 1)
                {
                    wp_remote_get(site_url());
                }
            }
            catch (Exception $ex)
            {
                
            }
            ?>
            <div class="updated"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
            <?php
        }


        // Now display the settings editing screen

        echo '<div class="wrap" style="max-width: 1000px;">';

        // header

        echo "<h2>" . '<img src="' . plugins_url('images/youtubeicon16.png', __FILE__) . '" /> ' . __('YouTube Settings') . "</h2>";

        // settings form
        ?>

        <style type="text/css">
            .wrap {font-family: Arial; color: #000000;}
            #ytform p { line-height: 20px; margin-bottom: 11px; }
            #ytform ul li {margin-left: 30px; list-style: disc outside none;}
            .ytindent {padding: 0px 0px 0px 20px; font-size: 12px;}
            .ytindent ul, .ytindent p {font-size: 12px;}
            .shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
            .gopro {margin: 0px;}
            .gopro img {vertical-align: middle;
                        width: 19px;
                        height: 19px;
                        padding-bottom: 4px;}
            .gopro li {margin-bottom: 0px;}
            .orange {color: #f85d00;}
            .bold {font-weight: bold;}
            .grey{color: #888888;}
            #goprobox {border-radius: 15px; padding: 10px 15px 15px 15px; margin-top: 15px; border: 3px solid #CCE5EC; position: relative;}
            #salenote {position: absolute; right: 10px; top: 10px; width: 75px; height: 30px;}
            #nonprosupport {border-radius: 15px; padding: 10px 15px 20px 15px;  border: 3px solid #ff6655;}
            .pronon {font-weight: bold; color: #f85d00;}
            ul.reglist li {margin: 0px 0px 0px 30px; list-style: disc outside none;}
            .procol {width: 475px; float: left;}
            .ytindent .procol ul {font-size: 11px;}
            .smallnote {font-style: italic; font-size: 10px;}
            .italic {font-style: italic;}
            .ytindent h3 {font-size: 15px; line-height: 22px; margin: 5px 0px 10px 0px;}
            #wizleftlink {float: left; display: block; width: 240px; font-style: italic; text-align: center; text-decoration: none;}
            .button-primary {font-weight: bold; white-space: nowrap;}
            .wp-core-ui p.submit .button-primary {font-size: 20px; height: 50px; padding: 0 20px 1px;
                                                  background: #2ea2cc; /* Old browsers */
                                                  background: -moz-linear-gradient(top,  #2ea2cc 0%, #007396 98%); /* FF3.6+ */
                                                  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#2ea2cc), color-stop(98%,#007396)); /* Chrome,Safari4+ */
                                                  background: -webkit-linear-gradient(top,  #2ea2cc 0%,#007396 98%); /* Chrome10+,Safari5.1+ */
                                                  background: -o-linear-gradient(top,  #2ea2cc 0%,#007396 98%); /* Opera 11.10+ */
                                                  background: -ms-linear-gradient(top,  #2ea2cc 0%,#007396 98%); /* IE10+ */
                                                  background: linear-gradient(to bottom,  #2ea2cc 0%,#007396 98%); /* W3C */
                                                  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2ea2cc', endColorstr='#007396',GradientType=0 ); /* IE6-9 */
            }
            p.submit em {display: inline-block; padding-left: 20px; vertical-align: middle; width: 240px; margin-top: -6px;}
            #opt_pro {box-shadow: 0px 0px 5px 0px #1870D5; width: 320px;vertical-align: top;}
            #goprobox h3 {font-size: 13px;}
            .chx p {margin: 0px 0px 5px 0px;}
            .cuz {background-image: linear-gradient(to bottom,#4983FF,#0C5597) !important; color: #ffffff;}
            .brightpro {background-image: linear-gradient(to bottom,#ff5500,#cc2200) !important; color: #ffffff;}
            #boxdefaultdims {font-weight: bold; padding: 0px 10px; <?php echo $all[self::$opt_defaultdims] ? '' : 'display: none;' ?>}
            .textinput {border-width: 2px !important;}
            h3.sect {border-radius: 10px; background-color: #D9E9F7; padding: 5px 5px 5px 10px; position: relative; font-weight: bold;}
            h3.sect a {text-decoration: none; color: #E20000;}
            h3.sect a.button-primary {color: #ffffff;} 
            h4.sect {border-radius: 10px; background-color: #D9E9F7; padding: 5px 5px 5px 10px; position: relative; font-weight: bold;}

            .ytnav {margin-bottom: 15px;}
            .ytnav a {font-weight: bold; display: inline-block; padding: 5px 10px; margin: 0px 20px 0px 0px; border: 1px solid #cccccc; border-radius: 6px;
                      text-decoration: none; background-color: #ffffff;}
            .jumper {height: 25px;}
            .ssschema {float: right; width: 350px; height: auto; margin-right: 10px;}
            .ssfb {float: right; height: auto; margin-right: 10px; margin-left: 15px; margin-bottom: 10px;}
            .totop {position: absolute; right: 20px; top: 5px; color: #444444; font-size: 10px;}
            input[type=checkbox] {border: 1px solid #000000;}
            .chktitle {display: inline-block; padding: 1px 5px 1px 5px; border-radius: 3px; background-color: #ffffff; border: 1px solid #dddddd;}
            b, strong {font-weight: bold;}
            input.checkbox[disabled] {border: 1px dashed #444444;}
            .pad10 {padding: 10px;}
            #boxdohl {font-weight: bold; padding: 0px 10px;  <?php echo $all[self::$opt_dohl] ? '' : 'display: none;' ?>}
            #boxdyn {font-weight: bold; padding: 0px 10px;  <?php echo $all[self::$opt_dynload] ? '' : 'display: none;' ?>}
            #boxspdc {padding: 0px 10px;  <?php echo $all[self::$opt_spdc] ? '' : 'display: none;' ?>}
            #boxdefaultvol {font-weight: bold; padding: 0px 10px;  <?php echo $all[self::$opt_defaultvol] ? '' : 'display: none;' ?>}
            .vol-output {display: none; width: 30px; color: #008800;}
            .vol-range {background-color: #dddddd; border-radius: 3px; cursor: pointer;}
            input#vol {vertical-align: middle;}
            .vol-seeslider {display: none;}
            input#spdcexp {width: 70px;}
            .indent-option {margin-left: 25px;}
            #boxschemaorg {font-weight: bold; padding: 7px 0;  <?php echo $all[self::$opt_schemaorg] ? 'display: block;' : 'display: none;' ?>}
            #boxmigratelist { <?php echo $all[self::$opt_migrate] ? '' : 'display: none;' ?>}
            .apikey-msg {display: inline-block; width: 45%; vertical-align: top;}
            .apikey-video{margin-left: 3%; display: inline-block; width: 50%; position: relative; padding-top: 29%}
            .apikey-video iframe{display: block; width: 100%; height: 100%; position: absolute; top: 0; left: 0;}
            #boxnocookie {display: inline-block; border-radius: 3px; padding: 2px 4px 2px 4px; color: red; background-color: yellow; font-weight: bold; <?php echo $all[self::$opt_nocookie] ? '' : 'display: none;' ?>}
            .strike {text-decoration: line-through;}
            .upgchecks { padding: 20px; border-radius: 15px; border: 1px dotted #777777; background-color: #fcfcfc; }
            .clearboth {clear: both;}
            div.hr {clear: both; border-bottom: 1px dotted #A8BDD8; margin: 20px 0 20px 0;}
            .wp-pointer-buttons a.close {margin-top: 0 !important;}
            .pad20{padding: 20px 0 20px 0;}
            .ssgallery {float: right; width: 130px; height: auto; margin-left: 15px; border: 3px solid #ffffff;}
            .ssaltgallery {float: right; height: auto; margin-right: 10px; margin-left: 15px; margin-bottom: 10px; width: 210px;}
        </style>
        <div class="ytindent">
            <br>
            <div id="jumphowto"></div>
            <div class="ytnav">
                <a href="#jumphowto">How To Embed</a>
                <a href="#jumpwiz">Visual Wizard</a>
                <a href="#jumpdefaults">Defaults</a>
                <a href="#jumpgallery">Gallery Settings</a>
                <a href="#jumpoverride">How To Override Defaults</a>
                <a target="_blank" href="<?php echo self::$epbase . "/dashboard/pro-easy-video-analytics.aspx?ref=protab" ?>" style="border-color: #888888;">Upgrade?</a>
                <a href="#jumpsupport">Support</a>
            </div>

            <form name="form1" method="post" action="" id="ytform">
                <input type="hidden" name="<?php echo $ytprefs_submitted; ?>" value="Y">

                <h3 class="sect">
                    <?php _e("How to Insert a YouTube Video or Playlist") ?> <!--<span class="pronon">(For Free and <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank">PRO Users &raquo;</a>)</span>-->
                </h3>
                <p>
                    <b>For videos:</b> <i>Method 1 - </i> Do you already have a URL to the video you want to embed in a post, page, or even a widget? All you have to do is paste it on its own line, as shown below (including the http:// part). Easy, eh?<br>
                    <i>Method 2 - </i> If you want to do some formatting (e.g. add HTML to center a video) or have two or more videos next to each other on the same line, wrap each link with the <code>[embedyt]...[/embedyt]</code> shortcode. <b>Tip for embedding videos on the same line:</b> As shown in the example image below, decrease the size of each video so that they fit together on the same line (See the "How To Override Defaults" section for height and width instructions).
                </p>
                <p>
                    <b>For playlists:</b> Go to the page for the playlist that lists all of its videos (<a target="_blank" href="http://www.youtube.com/playlist?list=PL70DEC2B0568B5469">Example &raquo;</a>). Click on the video that you want the playlist to start with. Copy and paste that browser URL into your blog on its own line. If you want the first video to always be the latest video in your playlist, check the option "Playlist Ordering" in the settings down below (you will also see this option available if you use the Pro Wizard). If you want to have two or more playlists next to each other on the same line, wrap each link with the <code>[embedyt]...[/embedyt]</code> shortcode.
                </p>                
                <p>
                    <b>For channel playlists:</b> At your editor, click on the <img style="vertical-align: text-bottom;" src="<?php echo plugins_url('images/wizbuttonbig.png', __FILE__) ?>"> wizard button and choose the option <i>Search for a video or channel to insert in my editor.</i> Then, click on the <i>channel playlist</i> option there (instead of <i>single video</i>). Search for the channel username and follow the rest of the directions there.
                </p>
                <p>
                    <b>Examples:</b><br><br>
                    <img style="width: 900px; height: auto;" class="shadow" src="<?php echo plugins_url('images/sshowto.png', __FILE__) ?>" />
                </p>
                <p>
                    Always follow these rules for any URL:
                </p>
                <ul class="reglist">
                    <li>Make sure the URL is really on its own line by itself. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode (Example:  <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]</code>)</li>
                    <li>Make sure the URL is <strong>not</strong> an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the URL and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/></li>
                    <li>Make sure you did <strong>not</strong> format or align the URL in any way. If your URL still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                    <li>If you really want to align the video, try wrapping the link with the shortcode first. For example: <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK[/embedyt]</code> Using the shortcode also allows you to have two or more videos next to each other on the same line.  Just put the shortcoded links together on the same line. For example:<br>
                        <code>[embedyt]http://www.youtube.com/watch?v=ABCDEF[/embedyt] [embedyt]http://www.youtube.com/watch?v=GHIJK[/embedyt]</code>
                </ul>       

                <div class="jumper" id="jumpwiz"></div>
                <h3 class="sect">Visual YouTube Wizard <a href="#top" class="totop">&#9650; top</a></h3>

                <p>
                    Let's say you don't know the exact URL of the video you wish to embed.  Well, we've made the ability to directly search YouTube and insert videos right from your editor tab as a free feature to all users.  
                    Simply click the <img style="vertical-align: text-bottom;" src="<?php echo plugins_url('images/wizbuttonbig.png', __FILE__) ?>"> wizard button found above 
                    your editor to start the wizard (see image above to locate this button).  There, you'll be given the option to enter your search terms.  
                    Click the "Search" button to view the results.  Each result will have an <span class="button-primary cuz">&#9660; Insert Into Editor</span> button that 
                    you can click to directly embed the desired video link to your post without having to copy and paste.             
                </p>
                <p>
                    The ability to read the latest Internet discussions about the videos you want to embed is now free to all users.
                </p>
                <p>
                    <b class="orange">Even more options are available to PRO users!</b> Simply click the <a href="<?php echo self::$epbase . '/dashboard/pro-easy-video-analytics.aspx?ref=protab' ?>" target="_blank" class="button-primary cuz">&#9658; Customize</a> button on the wizard to further personalize your embeds without having to enter special codes yourself.
                    <br>
                    <br>

                    <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank" style="text-decoration: none;"><img style="width: 500px; margin: 0 auto; display: block;" src="<?php echo plugins_url('images/ssprowizard.png', __FILE__) ?>" ></a>

                </p>
                <div class="jumper" id="jumpdefaults"></div>
                <h3 class="sect">
                    <?php _e("Default YouTube Options") ?> <a href="#top" class="totop">&#9650; top</a>
                </h3>
                <p>
                    <?php _e("One of the benefits of using this plugin is that you can set site-wide default options for all your videos (click \"Save Changes\" when finished). However, you can also override them (and more) on a per-video basis. Directions on how to do that are in the next section.") ?>
                </p>
                <p class="submit">
                    <input type="submit" onclick="return savevalidate();" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    <em>If you're using a separate caching plugin and you do not see your changes after saving, you might want to reset your cache.</em>
                </p>

                <div class="ytindent chx">
                    <p>
                        <input name="<?php echo self::$opt_glance; ?>" id="<?php echo self::$opt_glance; ?>" <?php checked($all[self::$opt_glance], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_glance; ?>"><?php _e('<b class="chktitle">At a glance:</b> Show "At a Glance" Embed Links') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_center; ?>" id="<?php echo self::$opt_center; ?>" <?php checked($all[self::$opt_center], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_center; ?>"><?php _e('<b class="chktitle">Centering:</b> Automatically center all your videos (not necessary if all your videos span the whole width of your blog).') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_autoplay; ?>" id="<?php echo self::$opt_autoplay; ?>" <?php checked($all[self::$opt_autoplay], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_autoplay; ?>"><?php _e('<b class="chktitle">Autoplay:</b>  Automatically start playing your videos.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_iv_load_policy; ?>" id="<?php echo self::$opt_iv_load_policy; ?>" <?php checked($all[self::$opt_iv_load_policy], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_iv_load_policy; ?>"><?php _e('<b class="chktitle">Annotations:</b> Show annotations by default.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_loop; ?>" id="<?php echo self::$opt_loop; ?>" <?php checked($all[self::$opt_loop], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_loop; ?>"><?php _e('<b class="chktitle">Looping:</b> Loop all your videos.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_modestbranding; ?>" id="<?php echo self::$opt_modestbranding; ?>" <?php checked($all[self::$opt_modestbranding], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_modestbranding; ?>"><?php _e('<b class="chktitle">Modest Branding:</b> No YouTube logo will be shown on the control bar.  Instead, the logo will only show as a watermark when the video is paused/stopped.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>" <?php checked($all[self::$opt_rel], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_rel; ?>"><?php _e('<b class="chktitle">Related Videos:</b> Show related videos at the end.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_showinfo; ?>" id="<?php echo self::$opt_showinfo; ?>" <?php checked($all[self::$opt_showinfo], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_showinfo; ?>"><?php _e('<b class="chktitle">Show Title:</b> Show the video title and other info.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_acctitle; ?>" id="<?php echo self::$opt_acctitle; ?>" <?php checked($all[self::$opt_acctitle], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_acctitle; ?>"><b class="chktitle">Accessible Title Attributes: <sup class="orange">NEW</sup></b> Improve accessibility by using title attributes for screen reader support. It should help your site pass functional accessibility evaluations (FAE). </label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_theme; ?>" id="<?php echo self::$opt_theme; ?>" <?php checked($all[self::$opt_theme], 'dark'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_theme; ?>"><?php _e('<b class="chktitle">Dark Theme:</b> Use the dark theme (uncheck to use light theme).') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_color; ?>" id="<?php echo self::$opt_color; ?>" <?php checked($all[self::$opt_color], 'red'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_color; ?>"><?php _e('<b class="chktitle">Red Progress Bar:</b> Use the red progress bar (uncheck to use a white progress bar). Note: Using white will disable the modestbranding option.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_vq; ?>" id="<?php echo self::$opt_vq; ?>" <?php checked($all[self::$opt_vq], 'hd720'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_vq; ?>"><?php _e('<b class="chktitle strike">HD Quality:</b> Force HD quality when available. <b>NOTE: YouTube has deprecated this unofficially supported option.</b>') ?> </label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_wmode; ?>" id="<?php echo self::$opt_wmode; ?>" <?php checked($all[self::$opt_wmode], 'opaque'); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_wmode; ?>"><?php _e('<b class="chktitle">Wmode:</b> Use "opaque" wmode (uncheck to use "transparent"). Opaque may have higher performance.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_defaultdims; ?>" id="<?php echo self::$opt_defaultdims; ?>" <?php checked($all[self::$opt_defaultdims], 1); ?> type="checkbox" class="checkbox">                        
                        <span id="boxdefaultdims">
                            Width: <input type="text" name="<?php echo self::$opt_defaultwidth; ?>" id="<?php echo self::$opt_defaultwidth; ?>" value="<?php echo trim($all[self::$opt_defaultwidth]); ?>" class="textinput" style="width: 50px;"> &nbsp;
                            Height: <input type="text" name="<?php echo self::$opt_defaultheight; ?>" id="<?php echo self::$opt_defaultheight; ?>" value="<?php echo trim($all[self::$opt_defaultheight]); ?>" class="textinput" style="width: 50px;">
                        </span>

                        <label for="<?php echo self::$opt_defaultdims; ?>"><?php _e('<b class="chktitle">Default Dimensions:</b> Make your videos have a default size. (NOTE: Checking the responsive option will override this size setting) ') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_responsive; ?>" id="<?php echo self::$opt_responsive; ?>" <?php checked($all[self::$opt_responsive], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_responsive; ?>"><?php _e('<b class="chktitle">Responsive Video Sizing:</b> Make your videos responsive so that they dynamically fit in all screen sizes (smart phone, PC and tablet). NOTE: While this is checked, any custom hardcoded widths and heights you may have set will dynamically change too. <b>Do not check this if your theme already handles responsive video sizing.</b>') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_widgetfit; ?>" id="<?php echo self::$opt_widgetfit; ?>" <?php checked($all[self::$opt_widgetfit], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_widgetfit; ?>"><?php _e('<b class="chktitle">Autofit Widget Videos:</b> Make each video that you embed in a widget area automatically fit the width of its container.</b>') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_playsinline; ?>" id="<?php echo self::$opt_playsinline; ?>" <?php checked($all[self::$opt_playsinline], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_playsinline; ?>"><?php _e('<b class="chktitle">iOS Playback:</b> Check this to allow your embeds to play inline within your page when viewed on iOS (iPhone and iPad) browsers. Uncheck it to have iOS launch your embeds in fullscreen instead.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_origin; ?>" id="<?php echo self::$opt_origin; ?>" <?php checked($all[self::$opt_origin], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_origin; ?>"><b class="chktitle">Extra Player Security: </b>
                            Add site origin information with each embed code as an extra security measure. In YouTube's/Google's own words, checking this option "protects against malicious third-party JavaScript being injected into your page and hijacking control of your YouTube player." We especially recommend checking it as it adds higher security than the built-in YouTube embedding method that comes with the current version of WordPress (i.e. oembed).
                        </label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_nocookie; ?>" id="<?php echo self::$opt_nocookie; ?>" <?php checked($all[self::$opt_nocookie], 1); ?> type="checkbox" class="checkbox">
                        <span id="boxnocookie">
                            Reminder: If you see errors while testing your playlist embeds or watching your videos on mobile, please uncheck this option.
                        </span>
                        <label for="<?php echo self::$opt_nocookie; ?>">
                            <b class="chktitle">No Cookies:</b> Prevent YouTube from leaving tracking cookies on your visitors browsers unless they actual play the videos. This is coded to apply this behavior on links in your past post as well. <b>NOTE: Research shows that YouTube's support of Do Not Track can be error-prone. </b>
                        </label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_controls; ?>" id="<?php echo self::$opt_controls; ?>" <?php checked($all[self::$opt_controls], 2); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_controls; ?>"><b class="chktitle">Show Controls:</b> Show the player's control bar. Unchecking this option creates a cleaner look but limits what your viewers can control (play position, volume, etc.).</label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_autohide; ?>" id="<?php echo self::$opt_autohide; ?>" <?php checked($all[self::$opt_autohide], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_autohide; ?>"><b class="chktitle">Autohide Controls:</b> Slide away the control bar after the video starts playing. It will automatically slide back in again if you mouse over the video. If you unchecked "Show Controls" above, then what you select for Autohide does not matter since there are no controls to even hide.</label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_oldspacing; ?>" id="<?php echo self::$opt_oldspacing; ?>" <?php checked($all[self::$opt_oldspacing], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_oldspacing; ?>">
                            <b class="chktitle">Legacy Spacing:</b> Continue the spacing style from version 4.0 and older. Those versions required you to manually add spacing above and below your video. Unchecking this will automatically add the spacing.
                        </label>
                    </p>
        <!--                    <p>
                        <input name="<?php echo self::$opt_ssl; ?>" id="<?php echo self::$opt_ssl; ?>" <?php checked($all[self::$opt_ssl], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_ssl; ?>">
                            <b class="chktitle">HTTPS/SSL Player:</b> Do you have a website that uses HTTPS? Check this to use the secure YouTube player for all of your embeds.
                            This will go back and also secure your past embeds as they are loaded on their pages. Most web browsers will warn users when they access web pages via HTTPS that contain embedded content loaded via HTTP. If your main site is currently accessed via HTTPS, using HTTPS URLs for your YouTube embeds will prevent your users from running into that warning. If you're not currently supporting HTTPS/SSL, <a href="http://embedplus.com/convert-old-youtube-embeds-to-https-ssl.aspx" target="_blank">here's some motivation from Google &raquo;</a>
                        </label>
                    </p>-->
                    <p>
                        <input name="<?php echo self::$opt_defaultvol; ?>" id="<?php echo self::$opt_defaultvol; ?>" <?php checked($all[self::$opt_defaultvol], 1); ?> type="checkbox" class="checkbox">                        
                        <span id="boxdefaultvol">
                            Volume: <span class="vol-output"></span> <input min="0" max="100" step="1" type="text" name="<?php echo self::$opt_vol; ?>" id="<?php echo self::$opt_vol; ?>" value="<?php echo trim($all[self::$opt_vol]); ?>" >
                        </span>
                        <label for="<?php echo self::$opt_defaultvol; ?>">
                            <b class="chktitle">Volume Initialization: </b>
                            Set an initial volume level for all of your embedded videos.  Check this and you'll see a <span class="vol-seeslider">slider</span> <span class="vol-seetextbox">textbox</span> for setting the start volume to a value between 0 (mute) and 100 (max) percent.  Leaving it unchecked means you want the visitor's default behavior.  This feature is experimental and is less predictable on a page with more than one embed. Read more about why you might want to <a href="<?php echo self::$epbase ?>/mute-volume-youtube-wordpress.aspx" target="_blank">initialize YouTube embed volume here &raquo;</a>
                        </label>
                    </p>

                    <p>
                        <input name="<?php echo self::$opt_cc_load_policy; ?>" id="<?php echo self::$opt_cc_load_policy; ?>" <?php checked($all[self::$opt_cc_load_policy], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_cc_load_policy; ?>"><?php _e('<b class="chktitle">Closed Captions:</b> Turn on closed captions by default.') ?></label>
                    </p>
                    <p>
                        <input name="<?php echo self::$opt_dohl; ?>" id="<?php echo self::$opt_dohl; ?>" <?php checked($all[self::$opt_dohl], 1); ?> type="checkbox" class="checkbox">                        
        <!--                        <span id="boxdohl">
                            Language: <input type="text" name="<?php echo self::$opt_hl; ?>" id="<?php echo self::$opt_hl; ?>" value="<?php echo trim($all[self::$opt_hl]); ?>" class="textinput" style="width: 50px;" maxlength="2">
                        </span>-->
                        <label for="<?php echo self::$opt_dohl; ?>"><b class="chktitle">Player Localization / Internationalization: </b>
                            Automatically detect your site's default language (using get_locale) and set your YouTube embeds interface language so that it matches. Specifically, this will set the player's tooltips and caption track if your language is natively supported by YouTube. We suggest checking this if English is not your site's default language.  <a href="<?php echo self::$epbase ?>/youtube-iso-639-1-language-codes.aspx" target="_blank">See here for more details &raquo;</a></label>
                    </p>                    
                    <p>
                        <input name="<?php echo self::$opt_html5; ?>" id="<?php echo self::$opt_html5; ?>" <?php checked($all[self::$opt_html5], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_html5; ?>">
                            <b class="chktitle strike">HTML5 First:</b> 
                            As of January 2015, YouTube began serving the HTML5 player by default; therefore, this plugin no longer needs a special HTML5 setting.  This option is simply kept here as a notice.
                        </label>
                    </p>

                    <p>
                        <input name="<?php echo self::$opt_playlistorder; ?>" id="<?php echo self::$opt_playlistorder; ?>" <?php checked($all[self::$opt_playlistorder], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_playlistorder; ?>">
                            <b class="chktitle">Playlist Ordering: <sup class="orange bold">NEW</sup></b> Check this option if you want your playlists to begin with the latest added video by default. (Unchecking this will force playlists to always start with your selected specific video, even if you add videos to the playlist later).
                        </label>
                    </p>

                    <p>
                        <input name="<?php echo self::$opt_migrate; ?>" id="<?php echo self::$opt_migrate; ?>" <?php checked($all[self::$opt_migrate], 1); ?> type="checkbox" class="checkbox">
                        <label for="<?php echo self::$opt_migrate; ?>">
                            <b class="chktitle">Migrate Shortcodes: <sup class="orange bold">NEW</sup></b> Inherit other shortcodes.
                        </label>
                    <div id="boxmigratelist">
                        <ul>
                            <li><input name="<?php echo self::$opt_migrate_youtube; ?>" id="<?php echo self::$opt_migrate_youtube; ?>" <?php checked($all[self::$opt_migrate_youtube], 1); ?> type="checkbox" class="checkbox"><label for="<?php echo self::$opt_migrate_youtube; ?>">"Youtube Embed" : <code>youtube</code> and <code>youtube_video</code> shortcodes</label></li>
                            <li class="smallnote orange" style="list-style: none;">This feature is beta. More shortcodes coming.</li>
                        </ul>

                    </div>
                    </p>
                </div>
                <div class="jumper" id="jumpgallery"></div>
                <h3 class="sect">Gallery Settings and Directions<sup class="orange bold">NEW</sup></h3>
                <img class="ssgallery" src="<?php echo plugins_url('images/ssgallery.png', __FILE__) ?>">
                <p>
                    You can now make playlist embeds (and channel-playlist embeds) have a gallery layout. <strong>First, you must obtain your YouTube API key</strong>. 
                    Don't worry, it's an easy process. Just <a href="https://www.youtube.com/watch?v=2vmBACVETf4" target="_blank">click this link &raquo;</a> and follow the video on that page to get your API key. Then paste it in the box below, and click the "Save Changes" button:
                </p>
                <p>
                    Simply add the following parameter to the end of your playlist url:
                </p>
                <p><code>&layout=gallery</code></p>
                <p>For example, below is a playlist that has been converted into a gallery. Notice the new layout parameter, with no spaces:</p>
                <p>
                    <code style="font-size: .9em;">http://www.youtube.com/watch?listType=playlist&width=474&height=266&list=UUAuUUnT6oDeKwE6v1NGQxug&plindex=0<b>&layout=gallery</b></code>
                </p>
                <p class="smallnote">
                    PRO users can just use the playlist or channel wizard steps to fully to build codes like the above.
                </p>
                <p>
                    Below are the settings for galleries:
                </p>
                <div class="ytindent chx">
                    <p>
                        <b class="chktitle">YouTube API Key:</b> 
                        <input type="text" name="<?php echo self::$opt_gallery_apikey; ?>" id="<?php echo self::$opt_gallery_apikey; ?>" value="<?php echo trim($all[self::$opt_apikey]); ?>" class="textinput" style="width: 200px;">
                        Required so your site can get the thumbnails, title text, and other content from YouTube’s servers that used to render galleries. <a href="https://www.youtube.com/watch?v=2vmBACVETf4" target="_blank">Click this link &raquo;</a> and follow the video to the right to get your API key.
                    </p>
                    <p>
                        <label for="<?php echo self::$opt_gallery_pagesize; ?>"><b class="chktitle">Gallery Page Size:</b></label>
                        <input name="<?php echo self::$opt_gallery_pagesize; ?>" id="<?php echo self::$opt_gallery_pagesize; ?>" type="number" class="textinput" style="width: 60px;" value="<?php echo trim($all[self::$opt_gallery_pagesize]); ?>">
                        Enter how many thumbnails per page should be shown at once.
                    </p>
                    <p>
                        <label for="<?php echo self::$opt_gallery_columns; ?>"><b class="chktitle">Number of Columns:</b></label>
                        <input name="<?php echo self::$opt_gallery_columns; ?>" id="<?php echo self::$opt_gallery_columns; ?>" type="number" class="textinput" style="width: 60px;" value="<?php echo trim($all[self::$opt_gallery_columns]); ?>">                        
                        Enter how many thumbnails can fit per row.
                    </p>
                </div>

                <p class="submit">
                    <br>
                    <input type="submit" onclick="return savevalidate();" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    <em>If you're using a separate caching plugin and you do not see your changes after saving, you might want to reset your cache.</em>
                </p>


                <div class="upgchecks">
                    <h3 class="sect">PRO Features</h3>
                    <?php
                    if ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0)
                    {
                        ?>
                        <p class="smallnote orange">Below are PRO features for enhanced SEO and performance (works for even past embed links). Gallery options for PRO users will also be listed here.</p>

                        <p>
                            <input name="<?php echo self::$opt_spdc; ?>" id="<?php echo self::$opt_spdc; ?>" <?php checked($all[self::$opt_spdc], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_spdc; ?>">
                                <b>(PRO)</b> <b class="chktitle">Faster Page Loads (Caching): <sup class="orange">NEW</sup></b> 
                                Use embed caching to speed up your page loads. By default, WordPress needs to request information from YouTube.com's servers for every video you embed, every time a page is loaded. These data requests can add time to your total page load time. Turn on this feature to cache that data (instead of having to request for the same information every time you load a page). This should then make your pages that have videos load faster.  It's been noted that even small speed ups in page load can help increase visitor engagement, retention, and conversions. Caching also makes galleries run faster.
                            </label>
                        <div class="indent-option">
                            <div id="boxspdc">
                                <label>
                                    <b class="chktitle">Cache Liftime (hours): <sup class="orange">NEW</sup></b>
                                    <input name="<?php echo self::$opt_spdcexp; ?>" id="<?php echo self::$opt_spdcexp; ?>" value="<?php echo trim($all[self::$opt_spdcexp]); ?>" type="number" min="1"/>
                                </label>
                                Tip: If your pages rarely change, you may wish to set this to a much higher value than 24 hours.

                                <div class="pad20">
                                    <input type="button" class="button button-primary" value="Click to clear YouTube cache"/>
                                    <span style="display: none;" id="clearspdcloading" class="orange bold">Clearing...</span>
                                    <span  class="orange bold" style="display: none;" id="clearspdcsuccess">Finished clearing YouTube cache.</span>
                                    <span class="orange bold" style="display: none;" id="clearspdcfailed">Sorry, there seemed to be a problem clearing the cache.</span>
                                </div>
                            </div>
                        </div>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <img class="ssaltgallery" src="<?php echo plugins_url('images/ssaltgallery.jpg', __FILE__) ?>" />
                            <?php $cleanstyle = trim($all[self::$opt_gallery_style]); ?>
                            <select name="<?php echo self::$opt_gallery_style; ?>" id="<?php echo self::$opt_gallery_style; ?>" >
                                <option value="">Gallery Style</option>
                                <option value="grid" <?php echo 'grid' === $cleanstyle ? 'selected' : '' ?> >Default (grid)</option>
                                <option value="listview" <?php echo 'listview' === $cleanstyle ? 'selected' : '' ?> >List view</option>
                            </select>
                            <label for="<?php echo self::$opt_gallery_style; ?>">
                                <b>(PRO)</b>  <b class="chktitle">Alternate Gallery Styling: <sup class="orange">NEW</sup></b></b>
                                Switch from the grid style of the FREE version to another gallery style.  Right now, we provide a vertical (single column) list style as an alternative, with more designs coming. This current alternative was inspired by the standard YouTube playlist player’s “table of contents,” except our gallery’s video list is always visible and shown under the playing video. <a target="_blank" href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx">Read more here &raquo;</a>
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <input name="<?php echo self::$opt_schemaorg; ?>" id="<?php echo self::$opt_schemaorg; ?>" <?php checked($all[self::$opt_schemaorg], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_schemaorg; ?>">
                                <b>(PRO)</b> <b class="chktitle">Video SEO Tags:</b> Update your YouTube embeds with Google, Bing, and Yahoo friendly video SEO markup.
                            </label>
                            <span id="boxschemaorg">
                                <span class="apikey-msg">
                                    The video SEO tags include data like the title, description, and thumbnail information of each video you embed.  This plugin automatically extracts this data directly from YouTube using the version 3 API,
                                    which will soon replace the version 2 API. This particular API version requires that you obtain an API key so that YouTube can authenticate the requests.  Don't worry, it's an easy process.  
                                    Just <a href="https://developers.google.com/youtube/registering_an_application" target="_blank">click this link &raquo;</a> and follow the video to the right to get your API key. Then paste it in the box below, and click the "Save Changes" button:
                                    <br>
                                    <span style="vertical-align: middle; display: inline-block;">
                                        YouTube API Key: <input type="text" name="<?php echo self::$opt_apikey; ?>" id="<?php echo self::$opt_apikey; ?>" value="<?php echo trim($all[self::$opt_apikey]); ?>" class="textinput" style="width: 200px;">
                                    </span>
                                </span>
                                <span class="apikey-video">
                                    <iframe width="384" height="216" src="https://www.youtube.com/embed/2vmBACVETf4?rel=0" frameborder="0" allowfullscreen></iframe>
                                </span>
                            </span>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <input name="<?php echo self::$opt_dynload; ?>" id="<?php echo self::$opt_dynload; ?>" <?php checked($all[self::$opt_dynload], 1); ?> type="checkbox" class="checkbox">                        
                            <span id="boxdyn">
                                Animation:
                                <?php $cleandyn = trim($all[self::$opt_dyntype]); ?>
                                <select name="<?php echo self::$opt_dyntype; ?>" id="<?php echo self::$opt_dyntype; ?>" >
                                    <option value="">Select type</option>
                                    <option value="rotateIn" <?php echo 'rotateIn' === $cleandyn ? 'selected' : '' ?> >rotate in</option>
                                    <option value="slideInRight" <?php echo 'slideInRight' === $cleandyn ? 'selected' : '' ?> >slide from right</option>
                                    <option value="slideInLeft" <?php echo 'slideInLeft' === $cleandyn ? 'selected' : '' ?> >slide from left</option>
                                    <option value="bounceIn" <?php echo 'bounceIn' === $cleandyn ? 'selected' : '' ?> >bounce in</option>
                                    <option value="flipInX" <?php echo 'flipInX' === $cleandyn ? 'selected' : '' ?> >flip up/down</option>
                                    <option value="flipInY" <?php echo 'flipInY' === $cleandyn ? 'selected' : '' ?> >flip left/right</option>
                                    <option value="pulse" <?php echo 'pulse' === $cleandyn ? 'selected' : '' ?> >pulse</option>
                                    <option value="tada" <?php echo 'tada' === $cleandyn ? 'selected' : '' ?> >jiggle</option>
                                    <option value="fadeInDown" <?php echo 'fadeInDown' === $cleandyn ? 'selected' : '' ?> >fade in downward</option>
                                    <option value="fadeInUp" <?php echo 'fadeInUp' === $cleandyn ? 'selected' : '' ?> >fade in upward</option>
                                    <option value="zoomInDown" <?php echo 'zoomInDown' === $cleandyn ? 'selected' : '' ?> >zoom in downward</option>
                                    <option value="zoomInUp" <?php echo 'zoomInUp' === $cleandyn ? 'selected' : '' ?> >zoom in upward</option>
                                </select>
                            </span>
                            <label for="<?php echo self::$opt_dynload; ?>">
                                <b>(PRO)</b>  <b class="chktitle">Special Lazy-Loading Effects:</b>
                                Add eye-catching special effects that will make your YouTube embeds bounce, flip, pulse, or slide as they lazy load on the screen.  Check this box to select your desired effect. <a target="_blank" href="<?php echo self::$epbase ?>/add-special-effects-to-youtube-embeds-in-wordpress.aspx">Read more here &raquo;</a>
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <img class="ssfb" src="<?php echo plugins_url('images/ssfb.jpg', __FILE__) ?>" />
                            <input name="<?php echo self::$opt_ogvideo; ?>" id="<?php echo self::$opt_ogvideo; ?>" <?php checked($all[self::$opt_ogvideo], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_ogvideo; ?>">
                                <b>(PRO)</b> <b class="chktitle">Facebook Open Graph Markup:</b>  Update YouTube embeds on your pages with Open Graph markup to enhance Facebook sharing and discovery of the pages. Your shared pages, for example, will also display embedded video thumbnails on Facebook Timelines.
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <img class="ssfb" src="<?php echo plugins_url('images/youtube_thumbnail_sample.jpg', __FILE__) ?>" />
                            <input name="<?php echo self::$opt_ftpostimg; ?>" id="<?php echo self::$opt_ftpostimg; ?>" <?php checked($all[self::$opt_ftpostimg], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_ftpostimg; ?>">
                                <b>(PRO)</b> <b class="chktitle">Automatic Video Thumbnails: <sup class="orange">NEW</sup></b> 
                                Automatically grab the thumbnail image of the first video embedded in each post or page, and use it as the featured image.  If your theme can display featured images of posts on your blog home, you’ll see the thumbnails there as shown in the picture on the right.  All you have to do is click Update on a post or page and the plugin does the rest!
                                (Example shown on the right) <a target="_blank" href="<?php echo self::$epbase ?>/add-youtube-video-thumbnails-featured-image-wordpress.aspx">Watch example here &raquo;</a>
                            </label>
                        </p>

                        <?php
                    }
                    else
                    {
                        ?>
                        <p class="smallnote orange">Below are PRO features for enhanced SEO and performance (works for even past embed links). </p>
                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <b class="chktitle">Faster Page Loads (Caching): <sup class="orange">NEW</sup></b>  <span class="pronon">(PRO Users)</span> 
                                Use embed caching to speed up your page loads. By default, WordPress needs to request information from YouTube.com's servers for every video you embed, every time a page is loaded. These data requests can add time to your total page load time. Turn on this feature to cache that data (instead of having to request for the same information every time you load a page). This should then make your pages that have videos load faster.  It's been noted that even small speed ups in page load can help increase visitor engagement, retention, and conversions. Caching also makes galleries run faster.
                            </label>
                        <div class="indent-option">
                            <label>
                                <b class="chktitle">Cache Liftime (hours): <sup class="orange">NEW</sup></b> 
                                <input disabled value="24" type="number">
                                Tip: If your pages rarely change, you may wish to set this to a much higher value than 24 hours.
                            </label>
                        </div>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <img class="ssaltgallery" src="<?php echo plugins_url('images/ssaltgallery.jpg', __FILE__) ?>" />
                            <select disabled>
                                <option value="">Gallery Style</option>
                            </select>
                            <label>
                                <b>(PRO)</b>  <b class="chktitle">Alternate Gallery Styling: <sup class="orange">NEW</sup></b> <span class="pronon">(PRO Users)</span> 
                                Switch from the grid style of the FREE version to another gallery style.  Right now, we provide a vertical (single column) list style as an alternative, with more designs coming. This current alternative was inspired by the standard YouTube playlist player’s “table of contents,” except our gallery’s video list is always visible and shown under the playing video. <a target="_blank" href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx">Read more here &raquo;</a>
                            </label>
                        </p>

                        <div class="hr"></div>

                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <b class="chktitle">Video SEO Tags:</b>  <span class="pronon">(PRO Users)</span> Update your YouTube embeds with Google, Bing, and Yahoo friendly video SEO markup.
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <b class="chktitle">Special Loading Effects:</b>  <span class="pronon">(PRO Users)</span> 
                                Add eye-catching special effects that will make your YouTube embeds bounce, flip, pulse, or slide as they load on the screen.  Check this box to select your desired effect. <a target="_blank" href="<?php echo self::$epbase ?>/add-special-effects-to-youtube-embeds-in-wordpress.aspx">Read more here &raquo;</a>
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <img class="ssfb" src="<?php echo plugins_url('images/ssfb.jpg', __FILE__) ?>" />
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <b class="chktitle">Facebook Open Graph Markup:</b> <span class="pronon">(PRO Users)</span>  Update YouTube embeds on your pages with Open Graph markup to enhance Facebook sharing and discovery of the pages. Your shared pages, for example, will also display embedded video thumbnails on Facebook Timelines.
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <img class="ssfb" src="<?php echo plugins_url('images/youtube_thumbnail_sample.jpg', __FILE__) ?>" />
                            <input disabled type="checkbox" class="checkbox">
                            <label>
                                <b class="chktitle">Automatic Video Thumbnails:</b>  <span class="pronon">(PRO Users)</span> 
                                Automatically grab the thumbnail image of the first video embedded in each post or page, and use it as the featured image. 
                                All you have to do is click Update on a post or page and the plugin does the rest! 
                                (Example shown on the right) <a target="_blank" href="<?php echo self::$epbase ?>/add-youtube-video-thumbnails-featured-image-wordpress.aspx">Read more here &raquo;</a>
                            </label>
                        </p>
                        <div class="hr"></div>
                        <p>
                            <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank">Activate the above and several other features &raquo;</a>
                        </p>
                        <?php
                    }
                    ?>
                    <div class="clearboth"></div>
                </div>
                <p class="submit">
                    <br>
                    <input type="submit" onclick="return savevalidate();" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    <em>If you're using a separate caching plugin and you do not see your changes after saving, you might want to reset your cache.</em>
                </p>

                <hr>


                <div class="jumper" id="jumpoverride"></div>

                <h3 class="sect">
                    <?php _e("How To Override Defaults / Other Options") ?> <a href="#top" class="totop">&#9650; top</a>
                </h3>
                <p>Suppose you have a few videos that need to be different from the above defaults. You can add options to the end of a link as displayed below to override the above defaults. Each option should begin with '&'.
                    <br><span class="smallnote orange">PRO users: You can use the <a href="<?php echo self::$epbase . '/dashboard/pro-easy-video-analytics.aspx?ref=protab' ?>" target="_blank" class="button-primary cuz">&#9658; Customize</a> button that you will see inside the wizard, instead of memorizing the following.</span>
                    <?php
                    _e('<ul>');
                    _e("<li><strong>width</strong> - Sets the width of your player. If omitted, the default width will be the width of your theme's content.<em> Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&width=500</strong>&height=350</em></li>");
                    _e("<li><strong>height</strong> - Sets the height of your player. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500<strong>&height=350</strong></em> </li>");
                    _e("<li><strong>autoplay</strong> - Set this to 1 to autoplay the video (or 0 to play the video once). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&autoplay=1</strong></em> </li>");
                    _e("<li><strong>cc_load_policy</strong> - Set this to 1 to turn on closed captioning (or 0 to leave them off). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&cc_load_policy=1</strong></em> </li>");
                    _e("<li><strong>iv_load_policy</strong> - Set this to 3 to turn off annotations (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&iv_load_policy=3</strong></em> </li>");
                    _e("<li><strong>loop</strong> - Set this to 1 to loop the video (or 0 to not loop). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&loop=1</strong></em> </li>");
                    _e("<li><strong>modestbranding</strong> - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&modestbranding=1</strong></em> </li>");
                    _e("<li><strong>rel</strong> - Set this to 0 to not show related videos at the end of playing (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&rel=0</strong></em> </li>");
                    _e("<li><strong>showinfo</strong> - Set this to 0 to hide the video title and other info (or 1 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&showinfo=0</strong></em> </li>");
                    _e("<li><strong>theme</strong> - Set this to 'light' to make the player have the light-colored theme (or 'dark' for the dark theme). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&theme=light</strong></em> </li>");
                    _e("<li><strong>color</strong> - Set this to 'white' to make the player have a white progress bar (or 'red' for a red progress bar). Note: Using white will disable the modestbranding option. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&color=white</strong></em> </li>");
                    _e("<li><strong>vq</strong> - Set this to 'hd720' or 'hd1080' to force the video to have HD quality. Leave blank to let YouTube decide. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&vq=hd720</strong></em> </li>");
                    _e("<li><strong>controls</strong> - Set this to 0 to completely hide the video controls (or 2 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&controls=0</strong></em> </li>");
                    _e("<li><strong>autohide</strong> - Set this to 1 to slide away the control bar after the video starts playing. It will automatically slide back in again if you mouse over the video. (Set to  2 to always show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&autohide=1</strong></em> </li>");
                    _e("<li><strong>playsinline</strong> - Set this to 1 to allow videos play inline with the page on iOS browsers. (Set to 0 to have iOS launch videos in fullscreen instead). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&playsinline=1</strong></em> </li>");
                    _e("<li><strong>origin</strong> - Set this to 1 to add the 'origin' parameter for extra JavaScript security. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&origin=1</strong></em> </li>");
                    _e('</ul>');

                    _e("<p>You can also start and end each individual video at particular times. Like the above, each option should begin with '&'</p>");
                    _e('<ul>');
                    _e("<li><strong>start</strong> - Sets the time (in seconds) to start the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&start=20</strong></em> </li>");
                    _e("<li><strong>end</strong> - Sets the time (in seconds) to stop the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&end=100</strong></em> </li>");
                    _e('</ul>');
                    ?>

            </form>
            <div class="jumper" id="jumppro"></div>
            <div id="goprobox">
                <?php
                if ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0)
                {
                    echo "<h3>" . __('Thank you for going PRO.');
                    echo ' &nbsp;<input type="submit" name="showkey" class="button-primary" style="vertical-align: 15%;" id="showprokey" value="View my PRO key" />';
                    echo "</h3>";
                    ?>
                    <?php
                }
                else
                {
                    ?>

                    <h3 class="sect">
                        <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" class="button-primary" target="_blank">Want to go PRO? (Low Prices) &raquo;</a> &nbsp; 
                        PRO users help keep new features coming and our coffee cups filled. Go PRO and get these perks in return:
                    </h3>
                    <div class="procol">
                        <ul class="gopro">
                            <li>
                                <img src="<?php echo plugins_url('images/iconcache.png', __FILE__) ?>">
                                Faster Page Loads (Caching)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/iconwizard.png', __FILE__) ?>">
                                Full Visual Embedding Wizard (Easily customize embeds without memorizing codes)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/icongallery.png', __FILE__) ?>">
                                Alternate Gallery Styling
                            </li>       
                            <li>
                                <img src="<?php echo plugins_url('images/iconfx.png', __FILE__) ?>">
                                Add eye-catching special effects as your videos load
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/deletechecker.png', __FILE__) ?>">
                                Deleted Video Checker (alerts you if YouTube deletes videos you embedded)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/globe.png', __FILE__) ?>">
                                Alerts when visitors from different countries are blocked from viewing your embeds
                            </li>                 
                            <li>
                                <img src="<?php echo plugins_url('images/mobilecompat.png', __FILE__) ?>">
                                Check if your embeds have restrictions that can block mobile viewing
                            </li>       

                        </ul>
                    </div>
                    <div class="procol" style="max-width: 465px;">
                        <ul class="gopro">
                            <li>
                                <img src="<?php echo plugins_url('images/videothumbs.png', __FILE__) ?>">
                                Automatic video thumbnail images (just click 'Update')  <sup class="orange bold">NEW</sup>
                            </li>       
                            <li>
                                <img src="<?php echo plugins_url('images/prioritysupport.png', __FILE__) ?>">
                                Priority support (Puts your request in front)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/bulletgraph45.png', __FILE__) ?>">
                                User-friendly video analytics dashboard
                            </li>

                            <li id="fbstuff">
                                <img src="<?php echo plugins_url('images/iconfb.png', __FILE__) ?>">
                                Automatic Open Graph tagging for Facebook
                            </li>
                            <!--                            <li>
                                                            <img src="<?php echo plugins_url('images/iconythealth.png', __FILE__) ?>">
                                                            Instant YouTube embed diagnostic reports
                                                        </li>                          -->
                            <li>
                                <img src="<?php echo plugins_url('images/vseo.png', __FILE__) ?>">
                                Automatic tagging for video SEO (will even work for your old embeds)
                            </li>
                            <li>
                                <img src="<?php echo plugins_url('images/iconvolume.png', __FILE__) ?>">
                                Fine-Grained Volume Initialization – Individual video volume settings in the wizard
                            </li>       

                            <li>
                                <img src="<?php echo plugins_url('images/infinity.png', __FILE__) ?>">
                                Unlimited PRO upgrades and downloads
                            </li>
                            <!--                            <li>
                                                            <img src="<?php echo plugins_url('images/questionsale.png', __FILE__) ?>">
                                                            What else? You tell us!                                
                                                        </li>                           -->
                        </ul>
                    </div>
                    <div style="clear: both;"></div>
                    <br>
                    <h3 class="bold">Enter and save your PRO key (emailed to you):</h3>
                <?php } ?>
                <form name="form2" method="post" action="" id="epform2" class="submitpro" <?php
                if ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0)
                {
                    echo 'style="display: none;"';
                }
                ?>>

                    <input name="<?php echo self::$opt_pro; ?>" id="opt_pro" value="<?php echo $all[self::$opt_pro]; ?>" type="text">
                    <input type="submit" name="Submit" class="button-primary" id="prokeysubmit" value="<?php _e('Save Key') ?>" />
                    <?php
                    if (!($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0))
                    {
                        ?>                    
                        &nbsp; &nbsp; &nbsp; <span style="font-size: 25px; color: #cccccc;">|</span> &nbsp; &nbsp; &nbsp; <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" class="button-primary brightpro" target="_blank">Click here to go PRO &raquo;</a>
                        <?php
                    }
                    ?>
                    <br>
                    <span style="display: none;" id="prokeyloading" class="orange bold">Verifying...</span>
                    <span  class="orange bold" style="display: none;" id="prokeysuccess">Success! Please refresh this page.</span>
                    <span class="orange bold" style="display: none;" id="prokeyfailed">Sorry, that seems to be an invalid key, or it has been used already.</span>

                </form>

            </div>
            <div class="smallnote">
                <!--&nbsp; *Upcoming: We've started developing a feature that will recommend YouTube embeds that you might want to include in a post while you're actually<br>
                writing/editing. It will apply some experimental artificial intelligence techniques on your post content for these recommendations, all at the click of a button.
                -->
            </div>
            <div class="jumper" id="jumpsupport"></div>
            <div id="nonprosupport">
                <h3 class="bold">Support tips for all users (Free and PRO)</h3>
                We've found that a common support request has been from users that are pasting video links on single lines, as required, but are not seeing the video embed show up. One of these suggestions is usually the fix:
                <ul class="reglist">
                    <li>Make sure the URL is really on its own line by itself. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode (Example:  <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]</code>)</li>
                    <li>Make sure the URL is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the URL and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/>.</li>
                    <li>Make sure you did <strong>not</strong> format or align the URL in any way. If your URL still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                    <li>Try wrapping the URL with the <code>[embedyt]...[/embedyt]</code> shortcode. For example: <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK[/embedyt]</code> Using the shortcode also allows you to have two or more videos next to each other on the same line.  Just put the shortcoded links together on the same line. For example:<br>
                        <code>[embedyt]http://www.youtube.com/watch?v=ABCDEF&width=400&height=250[/embedyt] [embedyt]http://www.youtube.com/watch?v=GHIJK&width=400&height=250[/embedyt]</code>
                        <br> TIP: As shown above, decrease the size of each video so that they fit together on the same line (See the "How To Override Defaults" section for height and width instructions)
                    </li>
                    <li>Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Fourteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.</li>
                    <li>If your videos always appear full size, try turning off "Responsive video sizing."</li>
                    <li>If none of the above work, you can contact us here if you still have issues: ext@embedplus.com. We'll try to respond within a week. PRO users should use the priority form below for faster replies.</li>                        
                </ul>
                <p>
                    Deactivating the No Cookies option has also been proven to solve player errors.
                </p>
                <p>
                    We also have a YouTube channel. We use it to provide users with some helper videos and a way to keep updated on new features as they are introduced. <a href="https://www.youtube.com/subscription_center?add_user=EmbedPlus" target="_blank">Subscribe for tips and updates here &raquo;</a>
                </p>
            </div>
            <br>
            <h3 class="sect">
                Priority Support <span class="pronon">(<a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank">PRO Users &raquo;</a>)</span><a href="#top" class="totop">&#9650; top</a>
            </h3>
            <p>
                <strong>PRO users:</strong> Below, We've enabled the ability to have priority support with our team.  Use this to get one-on-one help with any issues you might have or to send us suggestions for future features.  We typically respond within minutes during normal work hours. We're always happy to accept any testimonials you might have as well. 
            </p>


            <iframe src="<?php echo self::$epbase ?>/dashboard/prosupport.aspx?simple=1&prokey=<?php echo $all[self::$opt_pro]; ?>&domain=<?php echo site_url(); ?>" width="500" height="<?php echo ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0) ? "500" : "140"; ?>"></iframe>

            <?php
            if (!($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0))
            {
                ?>
                <br>
                <br>
                <iframe src="<?php echo self::$epbase ?>/dashboard/likecoupon.aspx" width="600" height="500"></iframe>
            <?php }
            ?>
            <div class="ytnav">
                <a href="#jumphowto">How To Embed</a>
                <a href="#jumpwiz">Visual Wizard</a>
                <a href="#jumpdefaults">Defaults</a>
                <a href="#jumpgallery">Gallery Settings</a>
                <a href="#jumpoverride">How To Override Defaults</a>
                <a target="_blank" href="<?php echo self::$epbase . "/dashboard/pro-easy-video-analytics.aspx?ref=protab" ?>" style="border-color: #888888;">Upgrade?</a>
                <a href="#jumpsupport">Support</a>
            </div>


            <script type="text/javascript">

                function savevalidate()
                {
                    var valid = true;
                    var alertmessage = '';
                    if (jQuery("#<?php echo self::$opt_defaultdims; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_defaultwidth; ?>").val())) &&
                                jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_defaultheight; ?>").val()))))
                        {
                            alertmessage += "Please enter valid numbers for default height and width, or uncheck the option.";
                            jQuery("#boxdefaultdims input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                        }
                    }

                    if (jQuery("#<?php echo self::$opt_defaultvol; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_vol; ?>").val()))))
                        {
                            alertmessage += "Please enter a number between 0 and 100 for the default volume, or uncheck the option.";
                            jQuery("#boxdefaultvol input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                        }
                    }

                    if (jQuery("#<?php echo self::$opt_spdc; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_spdcexp; ?>").val()))))
                        {
                            alertmessage += "Please enter a valid number of hours (greater than 0) for the cache lifetime, or uncheck the option.";
                            jQuery("#boxspdc input[type=number], #boxspdc input[type=text]").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                        }
                    }



                    if (jQuery("#<?php echo self::$opt_schemaorg; ?>").is(":checked"))
                    {
                        if (!(jQuery.trim(jQuery("#<?php echo self::$opt_apikey; ?>").val()).length > 0))
                        {
                            alertmessage += "Please enter a valid YouTube API key, or uncheck the 'Video SEO Tags' option.";
                            jQuery("#boxschemaorg input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                        }
                    }



                    if (jQuery("#<?php echo self::$opt_dynload; ?>").is(":checked"))
                    {
                        if (!(/^[A-Za-z-]+$/.test(jQuery.trim(jQuery("#<?php echo self::$opt_dyntype; ?>").val()))))
                        {
                            alertmessage += "Please select an animation, or uncheck the option.";
                            jQuery("#boxdyn select").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                        }
                    }




                    //                    if (jQuery("#<?php echo self::$opt_dohl; ?>").is(":checked"))
                    //                    {
                    //                        if (!(/^[A-Za-z][A-Za-z]$/.test(jQuery.trim(jQuery("#<?php echo self::$opt_hl; ?>").val()))))
                    //                        {
                    //                            alertmessage += "Please enter a valid 2-letter language code.";
                    //                            jQuery("#boxdohl input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                    //                            valid = false;
                    //                        }
                    //                    }

                    if (!valid)
                    {
                        alert(alertmessage);
                    }
                    return valid;
                }

                var prokeyval;
                var mydomain = escape("http://" + window.location.host.toString());

                jQuery(document).ready(function($) {
                    jQuery('#<?php echo self::$opt_defaultdims; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdefaultdims").show(500);
                        }
                        else
                        {
                            jQuery("#boxdefaultdims").hide(500);
                        }

                    });

                    jQuery('#<?php echo self::$opt_dynload; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdyn").show(500);
                        }
                        else
                        {
                            jQuery("#boxdyn").hide(500);
                        }

                    });

                    jQuery('#<?php echo self::$opt_spdc; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxspdc").show(500);
                        }
                        else
                        {
                            jQuery("#boxspdc").hide(500);
                        }
                    });


                    jQuery('#<?php echo self::$opt_migrate; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxmigratelist").show(500);
                        }
                        else
                        {
                            jQuery("#boxmigratelist").hide(500);
                        }
                    });



                    jQuery('#<?php echo self::$opt_nocookie; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxnocookie").show(500);
                        }
                        else
                        {
                            jQuery("#boxnocookie").hide(500);
                        }

                    });

                    jQuery('#<?php echo self::$opt_schemaorg; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxschemaorg").show(500);
                        }
                        else
                        {
                            jQuery("#boxschemaorg").hide(500);
                        }
                    });


                    //                    jQuery('#<?php echo self::$opt_dohl; ?>').change(function()
                    //                    {
                    //                        if (jQuery(this).is(":checked"))
                    //                        {
                    //                            jQuery("#boxdohl").show(500);
                    //                        }
                    //                        else
                    //                        {
                    //                            jQuery("#boxdohl").hide(500);
                    //                        }
                    //
                    //                    });



                    jQuery('#<?php echo self::$opt_defaultvol; ?>').change(function()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdefaultvol").show(500);
                        }
                        else
                        {
                            jQuery("#boxdefaultvol").hide(500);
                        }

                    });

                    var rangedetect = document.createElement("input");
                    rangedetect.setAttribute("type", "range");
                    var canrange = rangedetect.type !== "text";
                    //canrange = false;
                    if (canrange)
                    {
                        $("input#vol").prop("type", "range").addClass("vol-range").on("input change", function() {
                            $('.vol-output').text($(this).val() > 0 ? $(this).val() + '%' : 'Mute');
                        });
                        $('.vol-output').css("display", "inline-block").text($("input#vol").val() > 0 ? $("input#vol").val() + '%' : 'Mute');
                        $('.vol-seeslider').show();
                        $('.vol-seetextbox').hide();
                    }
                    else
                    {
                        $("input#vol").width(40);
                    }


                    jQuery('#boxspdc input.button').click(function() {
                        jQuery('#clearspdcloading').show();
                        jQuery('#clearspdcfailed').hide();
                        jQuery('#clearspdcsuccess').hide();

                        $clearbutton = jQuery(this);
                        $clearbutton.attr('disabled', 'disabled');

                        jQuery.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: wpajaxurl,
                            data: {action: 'my_embedplus_clearspdc'},
                            success: function(response) {
                                if (response.type == "success") {
                                    jQuery("#clearspdcsuccess").show();
                                }
                                else {
                                    jQuery("#clearspdcfailed").show();
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                jQuery("#clearspdcfailed").show();
                            },
                            complete: function() {
                                jQuery('#clearspdcloading').hide();
                                $clearbutton.removeAttr('disabled');
                            }

                        });

                    });




                    jQuery("#showcase-validate").click(function() {
                        window.open("<?php echo self::$epbase . "/showcase-validate.aspx?prokey=" . self::$alloptions[self::$opt_pro] ?>" + "&domain=" + mydomain);
                    });

                    jQuery('#showprokey').click(function() {
                        jQuery('.submitpro').show(500);
                        return false;
                    });

                    jQuery('#prokeysubmit').click(function() {
                        jQuery(this).attr('disabled', 'disabled');
                        jQuery('#prokeyfailed').hide();
                        jQuery('#prokeysuccess').hide();
                        jQuery('#prokeyloading').show();
                        prokeyval = jQuery('#opt_pro').val();

                        var tempscript = document.createElement("script");
                        tempscript.src = "//www.embedplus.com/dashboard/wordpress-pro-validatejp.aspx?simple=1&prokey=" + prokeyval + "&domain=" + mydomain;
                        var n = document.getElementsByTagName("head")[0].appendChild(tempscript);
                        setTimeout(function() {
                            n.parentNode.removeChild(n);
                        }, 500);
                        return false;
                    });

                    window.embedplus_record_prokey = function(good) {

                        jQuery.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: wpajaxurl,
                            data: {action: 'my_embedplus_pro_record', <?php echo self::$opt_pro; ?>: (good ? prokeyval : "")},
                            success: function(response) {
                                if (response.type == "success") {
                                    jQuery("#prokeysuccess").show();
                                }
                                else {
                                    jQuery("#prokeyfailed").show();
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                jQuery('#prokeyfailed').show();
                            },
                            complete: function() {
                                jQuery('#prokeyloading').hide();
                                jQuery('#prokeysubmit').removeAttr('disabled');
                            }

                        });

                    };

                });
            </script>
            <?php
            if (function_exists('add_thickbox'))
            {
                add_thickbox();
            }
            ?>

            <?php
        }

        public static function ytprefsscript()
        {
            wp_enqueue_style(
                    '__EPYT__style', plugins_url('styles/ytprefs.min.css', __FILE__)
            );
            $cols = floatval(self::$alloptions[self::$opt_gallery_columns]);
            $cols = $cols == 0 ? 3.0 : $cols;
            $colwidth = 100.0 / $cols;
            $custom_css = "
                .epyt-gallery-thumb {
                        width: {$colwidth}%;
                }";
            wp_add_inline_style('__EPYT__style', $custom_css);


            wp_enqueue_script('__ytprefs__', plugins_url('scripts/ytprefs.min.js', __FILE__), array('jquery'));
            wp_localize_script('__ytprefs__', '_EPYT_', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('embedplus-nonce')
            ));


            if (!is_admin() && (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0) && self::$alloptions[self::$opt_dynload] == 1)
            {
                wp_enqueue_style('__dyntype__', plugins_url('scripts/embdyn.min.css', __FILE__));
                wp_enqueue_script('__dynload__', plugins_url('scripts/embdyn.min.js', __FILE__), array('jquery'));
            }
        }

        public static function get_blogwidth()
        {
            $blogwidth = null;
            try
            {
                $embed_size_w = intval(get_option('embed_size_w'));

                global $content_width;
                if (empty($content_width))
                {
                    $content_width = $GLOBALS['content_width'];
                }

                $blogwidth = $embed_size_w ? $embed_size_w : ($content_width ? $content_width : 450);
            }
            catch (Exception $ex)
            {
                
            }

            $blogwidth = preg_replace('/\D/', '', $blogwidth); //may have px

            return $blogwidth;
        }

    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//class start
    class Add_new_tinymce_btn_Youtubeprefs
    {

        public $btn_arr;
        public $js_file;

        /*
         * call the constructor and set class variables
         * From the constructor call the functions via wordpress action/filter
         */

        function __construct($seperator, $btn_name, $javascrip_location)
        {
            $this->btn_arr = array("Seperator" => $seperator, "Name" => $btn_name);
            $this->js_file = $javascrip_location;
            add_action('init', array($this, 'add_tinymce_button'));
            add_filter('tiny_mce_version', array($this, 'refresh_mce_version'));
        }

        /*
         * create the buttons only if the user has editing privs.
         * If so we create the button and add it to the tinymce button array
         */

        function add_tinymce_button()
        {
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
                return;
            if (get_user_option('rich_editing') == 'true')
            {
                //the function that adds the javascript
                add_filter('mce_external_plugins', array($this, 'add_new_tinymce_plugin'));
                //adds the button to the tinymce button array
                add_filter('mce_buttons', array($this, 'register_new_button'));
            }
        }

        /*
         * add the new button to the tinymce array
         */

        function register_new_button($buttons)
        {
            array_push($buttons, $this->btn_arr["Seperator"], $this->btn_arr["Name"]);
            return $buttons;
        }

        /*
         * Call the javascript file that loads the
         * instructions for the new button
         */

        function add_new_tinymce_plugin($plugin_array)
        {
            $plugin_array[$this->btn_arr['Name']] = $this->js_file;
            return $plugin_array;
        }

        /*
         * This function tricks tinymce in thinking
         * it needs to refresh the buttons
         */

        function refresh_mce_version($ver)
        {
            $ver += 3;
            return $ver;
        }

    }

//class end


    register_activation_hook(__FILE__, array('YouTubePrefs', 'initoptions'));
    $youtubeplgplus = new YouTubePrefs();


    add_action('wp_enqueue_scripts', array('YouTubePrefs', 'ytprefsscript'), 100);
    add_action("wp_ajax_my_embedplus_pro_record", array('YouTubePrefs', 'my_embedplus_pro_record'));
    add_action("wp_ajax_my_embedplus_clearspdc", array('YouTubePrefs', 'my_embedplus_clearspdc'));
    add_action("wp_ajax_my_embedplus_glance_vids", array('YouTubePrefs', 'my_embedplus_glance_vids'));
    add_action("wp_ajax_my_embedplus_glance_count", array('YouTubePrefs', 'my_embedplus_glance_count'));
    add_action("wp_ajax_my_embedplus_dismiss_double_plugin_warning", array('YouTubePrefs', 'my_embedplus_dismiss_double_plugin_warning'));
    add_action("wp_ajax_my_embedplus_gallery_page", array('YouTubePrefs', 'my_embedplus_gallery_page'));
    add_action("wp_ajax_nopriv_my_embedplus_gallery_page", array('YouTubePrefs', 'my_embedplus_gallery_page'));

    add_action('admin_enqueue_scripts', 'youtubeprefs_admin_enqueue_scripts');

    function youtubeprefs_admin_enqueue_scripts()
    {
        wp_enqueue_style('embedplusyoutube', plugins_url() . '/youtube-embed-plus/scripts/embedplus_mce.css');
        add_action('wp_print_scripts', 'youtubeprefs_output_scriptvars');

        if (
        //(!(isset(YouTubePrefs::$alloptions[YouTubePrefs::$opt_pro]) && strlen(trim(YouTubePrefs::$alloptions[YouTubePrefs::$opt_pro])) > 0)) && // display only if not pro ooopointer
                (get_bloginfo('version') >= '3.3') && YouTubePrefs::custom_admin_pointers_check()
        )
        {
            add_action('admin_print_footer_scripts', 'YouTubePrefs::custom_admin_pointers_footer');

            wp_enqueue_script('wp-pointer');
            wp_enqueue_style('wp-pointer');
        }

        if (YouTubePrefs::$alloptions['glance'] == 1)
        {
            add_action('admin_print_footer_scripts', 'YouTubePrefs::glance_script');
        }
    }

    function youtubeprefs_output_scriptvars()
    {
        YouTubePrefs::$scriptsprinted++;
        if (YouTubePrefs::$scriptsprinted == 1)
        {
            $blogwidth = YouTubePrefs::get_blogwidth();
            $epprokey = YouTubePrefs::$alloptions[YouTubePrefs::$opt_pro];
            $myytdefaults = http_build_query(YouTubePrefs::$alloptions);
            ?>
            <script type="text/javascript">
                var wpajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
                if (window.location.toString().indexOf('https://') == 0)
                {
                    wpajaxurl = wpajaxurl.replace("http://", "https://");
                }

                var epblogwidth = <?php echo $blogwidth; ?>;
                var epprokey = '<?php echo $epprokey; ?>';
                var epbasesite = '<?php echo YouTubePrefs::$epbase; ?>';
                var epversion = '<?php echo YouTubePrefs::$version; ?>';
                var myytdefaults = '<?php echo $myytdefaults; ?>';
                var eppluginadminurl = '<?php echo admin_url('admin.php?page=youtube-my-preferences'); ?>';

                // Create IE + others compatible event handler
                var epeventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
                var epeventer = window[epeventMethod];
                var epmessageEvent = epeventMethod == "attachEvent" ? "onmessage" : "message";

                // Listen to message from child window
                epeventer(epmessageEvent, function(e)
                {
                    var embedcode = "";
                    try
                    {
                        if (e.data.indexOf("youtubeembedplus") == 0)
                        {

                            embedcode = e.data.split("|")[1];
                            if (embedcode.indexOf("[") !== 0)
                            {
                                embedcode = "<p>" + embedcode + "</p>";
                            }

                            if (window.tinyMCE !== null && window.tinyMCE.activeEditor !== null && !window.tinyMCE.activeEditor.isHidden())
                            {
                                if (typeof window.tinyMCE.execInstanceCommand !== 'undefined')
                                {
                                    window.tinyMCE.execInstanceCommand(
                                            window.tinyMCE.activeEditor.id,
                                            'mceInsertContent',
                                            false,
                                            embedcode);
                                }
                                else
                                {
                                    send_to_editor(embedcode);
                                }
                            }
                            else
                            {
                                embedcode = embedcode.replace('<p>', '\n').replace('</p>', '\n');
                                if (typeof QTags.insertContent === 'function')
                                {
                                    QTags.insertContent(embedcode);
                                }
                                else
                                {
                                    send_to_editor(embedcode);
                                }
                            }
                            tb_remove();

                        }
                    }
                    catch (err)
                    {
                        if (typeof console !== 'undefined')
                            console.log(err.message);
                    }


                }, false);






            </script>
            <?php
        }
    }
    