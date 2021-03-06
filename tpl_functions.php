<?php
/**
 * DokuWiki Template Arctic Functions
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Michael Klier <chi@chimeric.de>
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_LF')) define('DOKU_LF',"\n");

// Include language
@include(dirname(__FILE__).'/lang/en/lang.php'); 
if ( $conf['lang'] && $conf['lang'] != 'en' ) 
    @include(dirname(__FILE__).'/lang/'.$conf['lang'].'/lang.php');

// load sidebar contents
$sbl   = explode(',',tpl_getConf('left_sidebar_content'));
$sbr   = explode(',',tpl_getConf('right_sidebar_content'));
$sbpos = tpl_getConf('sidebar');

// set notoc option and toolbar regarding the sitebar setup
switch($sbpos) {
  case 'both':
    $notoc = (in_array('toc',$sbl) || in_array('toc',$sbr)) ? true : false;
    $toolb = (in_array('toolbox',$sbl) || in_array('toolbox',$sbr)) ? true : false;
    break;
  case 'left':
    $notoc = (in_array('toc',$sbl)) ? true : false;
    $toolb = (in_array('toolbox',$sbl)) ? true : false;
    break;
  case 'right':
    $notoc = (in_array('toc',$sbr)) ? true : false;
    $toolb = (in_array('toolbox',$sbr)) ? true : false;
    break;
  case 'none':
    $notoc = false;
    $toolb = false;
    break;
}

/**
 * Prints the sidebars
 * 
 * @author Michael Klier <chi@chimeric.de>
 */
function tpl_sidebar($pos) {

    $sb_order   = ($pos == 'left') ? explode(',', tpl_getConf('left_sidebar_order'))   : explode(',', tpl_getConf('right_sidebar_order'));
    $sb_content = ($pos == 'left') ? explode(',', tpl_getConf('left_sidebar_content')) : explode(',', tpl_getConf('right_sidebar_content'));

    if(tpl_getConf('closedwiki') && !isset($_SERVER['REMOTE_USER'])) {
        if(in_array('toolbox', $sb_content)) {
            print '<div class="toolbox_sidebar sidebar_box">' . DOKU_LF;
            print '  <div class="level1">' . DOKU_LF;
            print '    <ul>' . DOKU_LF;
            print '      <li><div class="li">';
            tpl_actionlink('login');
            print '      </div></li>' . DOKU_LF;
            print '    </ul>' . DOKU_LF;
            print '  </div>' . DOKU_LF;
            print '</div>' . DOKU_LF;
        }
        return;
    }

    tpl_dispatch_ordered_content ($sb_order, $sb_content, "tpl_sidebar_dispatch_" . $pos);
}

/**
 * Gets $content array and call $dispatcher for each item
 * regarding $order array
 */
function tpl_dispatch_ordered_content ($order, $content, $dispatcher) {

    // process contents by given order
    foreach($order as $item) {
        if(in_array($item,$content)) {
            $key = array_search($item,$content);
            unset($content[$key]);
            $dispatcher($item);
        }
    }

    // check for left content not specified by order
    if(is_array($content) && !empty($content) > 0) {
        foreach($content as $item) {
            $dispatcher($item);
        }
    }
}

/**
 * Wrapper for tpl_sidebar_dispatch with param pos=left
 */
function tpl_sidebar_dispatch_left ($sb) {
	tpl_sidebar_dispatch($sb, 'left');
}

/**
 * Wrapper for tpl_sidebar_dispatch with param pos=right
 */
function tpl_sidebar_dispatch_right ($sb) {
	tpl_sidebar_dispatch($sb, 'right');
}

/**
 * Dispatches the given sidebar type to return the right content
 *
 * @author Michael Klier <chi@chimeric.de>
 */
function tpl_sidebar_dispatch($sb,$pos) {
    global $lang;
    global $conf;
    global $ID;
    global $REV;
    global $INFO;

    $svID  = $ID;   // save current ID
    $svREV = $REV;  // save current REV 

    $pname = tpl_getConf('pagename');

    switch($sb) {

        case 'main':
            $main_sb = _getTransSb($pname);
            if($main_sb && auth_quickaclcheck($main_sb) >= AUTH_READ) {
                $always = tpl_getConf('main_sidebar_always');
                if($always or (!$always && !getNS($ID))) {
                    print '<div class="main_sidebar sidebar_box">' . DOKU_LF;
                    print p_sidebar_xhtml($main_sb,$pos) . DOKU_LF;
                    print '</div>' . DOKU_LF;
                }
            }
            break;

        case 'namespace':
            $user_ns  = tpl_getConf('user_sidebar_namespace');
            $group_ns = tpl_getConf('group_sidebar_namespace');
            if(!preg_match("/^".$user_ns.":.*?$|^".$group_ns.":.*?$/", $svID)) { // skip group/user sidebars and current ID
                $ns_sb = _getNsSb($svID);
                if($ns_sb && auth_quickaclcheck($ns_sb) >= AUTH_READ) {
                    print '<div class="namespace_sidebar sidebar_box">' . DOKU_LF;
                    print p_sidebar_xhtml($ns_sb,$pos) . DOKU_LF;
                    print '</div>' . DOKU_LF;
                }
            }
            break;

        case 'user':
            $user_ns = tpl_getConf('user_sidebar_namespace');
            if(isset($INFO['userinfo']['name'])) {
                $user = $_SERVER['REMOTE_USER'];
                $user_sb = _getTransSb($user_ns . ':' . $user . ':' . $pname);
                if(@file_exists(wikiFN($user_sb))) {
                    $subst = array('pattern' => array('/@USER@/'), 'replace' => array($user));
                    print '<div class="user_sidebar sidebar_box">' . DOKU_LF;
                    print p_sidebar_xhtml($user_sb,$pos,$subst) . DOKU_LF;
                    print '</div>';
                }
                // check for namespace sidebars in user namespace too
                if(preg_match('/'.$user_ns.':'.$user.':.*/', $svID)) {
                    $ns_sb = _getNsSb($svID); 
                    if($ns_sb && $ns_sb != $user_sb && auth_quickaclcheck($ns_sb) >= AUTH_READ) {
                        print '<div class="namespace_sidebar sidebar_box">' . DOKU_LF;
                        print p_sidebar_xhtml($ns_sb,$pos) . DOKU_LF;
                        print '</div>' . DOKU_LF;
                    }
                }

            }
            break;

        case 'group':
            $group_ns = tpl_getConf('group_sidebar_namespace');
            if(isset($INFO['userinfo']['name'], $INFO['userinfo']['grps'])) {
                foreach($INFO['userinfo']['grps'] as $grp) {
                    $group_sb = $group_ns.':'.$grp.':'.$pname;
                    $group_sb = _getTransSb($group_sb);
                    if( $group_sb && auth_quickaclcheck(cleanID($group_sb)) >= AUTH_READ) {
                        $subst = array('pattern' => array('/@GROUP@/'), 'replace' => array($grp));
                        print '<div class="group_sidebar sidebar_box">' . DOKU_LF;
                        print p_sidebar_xhtml($group_sb,$pos,$subst) . DOKU_LF;
                        print '</div>' . DOKU_LF;
                    }
                }
            }
            break;

        case 'index':
            print '<div class="index_sidebar sidebar_box">' . DOKU_LF;
            print '  ' . p_index_xhtml($svID,$pos) . DOKU_LF;
            print '</div>' . DOKU_LF;
            break;

        case 'toc':
            if(auth_quickaclcheck($svID) >= AUTH_READ) {
                $toc = tpl_toc(true);
                // replace ids to keep XHTML compliance
                if(!empty($toc)) {
                    $toc = preg_replace('/id="(.*?)"/', 'id="sb__' . $pos . '__\1"', $toc);
                    print '<div class="toc_sidebar sidebar_box">' . DOKU_LF;
                    print ($toc);
                    print '</div>' . DOKU_LF;
                }
            }
            break;
        
        case 'toolbox':
            $act_content = explode(',', tpl_getConf('toolbox_content'));
            $act_order = explode(',', tpl_getConf('toolbox_order'));

            print '<div class="toolbox_sidebar sidebar_box">' . DOKU_LF;
            print '<h1>' . $lang['kunlaborejo_toolbox'] . '</h1>' . DOKU_LF;
            print '  <div class="level1">' . DOKU_LF;
            print '    <ul>' . DOKU_LF;
            tpl_dispatch_ordered_content ($act_order, $act_content, "tpl_dispatch_toolbox_item");
            print '    </ul>' . DOKU_LF;
            print '  </div>' . DOKU_LF;
            print '</div>' . DOKU_LF;
            break;

        case 'trace':
            print '<div class="trace_sidebar sidebar_box">' . DOKU_LF;
            print '  <h1>'.$lang['breadcrumb'].'</h1>' . DOKU_LF;
            print '  <div class="breadcrumbs">' . DOKU_LF;
            ($conf['youarehere'] != 1) ? tpl_breadcrumbs() : tpl_youarehere();
            print '  </div>' . DOKU_LF;
            print '</div>' . DOKU_LF;
            break;

        case 'translation':
            print '<div class="translation_sidebar sidebar_box">' . DOKU_LF;
            print '  <h1>'.$lang['kunlaborejo_translations'].'</h1>' . DOKU_LF;
			$translation = &plugin_load('syntax','translation');
			echo $translation->_showTranslations();
            print '</div>' . DOKU_LF;
            break;

        case 'extra':
            print '<div class="extra_sidebar sidebar_box">' . DOKU_LF;
            @include(dirname(__FILE__).'/' . $pos .'_sidebar.html');
            print '</div>' . DOKU_LF;
            break;

        default:
            // check for user defined sidebars
            if(@file_exists(DOKU_TPLINC.'sidebars/'.$sb.'/sidebar.php')) {
                print '<div class="'.$sb.'_sidebar sidebar_box">' . DOKU_LF;
                @require_once(DOKU_TPLINC.'sidebars/'.$sb.'/sidebar.php');
                print '</div>' . DOKU_LF;
            }
            break;
    }

    // restore ID and REV
    $ID  = $svID;
    $REV = $svREV;
}

/**
 * Dispatches toolbox item to proper HTML 'li' entry
 */
function tpl_dispatch_toolbox_item ($action) {

    if(!actionOK($action)) return;
    // start output buffering
    if($action == 'edit') {
        // check if new page button plugin is available
        if(!plugin_isdisabled('npd') && ($npd =& plugin_load('helper', 'npd'))) {
             $npb = $npd->html_new_page_button(true);
             if($npb) {
                 print '    <li><div class="li">';
                 print $npb;
                 print '</div></li>' . DOKU_LF;
             }
        }
    }
    ob_start();
    print '     <li><div class="li">';
    if(tpl_actionlink($action)) {
       print '</div></li>' . DOKU_LF;
       ob_end_flush();
    } else {
       ob_end_clean();
    }
}


/**
 * Removes the TOC of the sidebar pages and 
 * shows a edit button if the user has enough rights
 *
 * TODO sidebar caching
 * 
 * @author Michael Klier <chi@chimeric.de>
 */
function p_sidebar_xhtml($sb,$pos,$subst=array()) {
    $data = p_wiki_xhtml($sb,'',false);
    if(!empty($subst)) {
        $data = preg_replace($subst['pattern'], $subst['replace'], $data);
    }
    if(auth_quickaclcheck($sb) >= AUTH_EDIT) {
        $data .= '<div class="secedit">'.html_btn('secedit',$sb,'',array('do'=>'edit','rev'=>'','post')).'</div>';
    }
    // strip TOC
    $data = preg_replace('/<div class="toc">.*?(<\/div>\n<\/div>)/s', '', $data);
    // replace headline ids for XHTML compliance
    $data = preg_replace('/(<h.*?><a.*?name=")(.*?)(".*?id=")(.*?)(">.*?<\/a><\/h.*?>)/','\1sb_'.$pos.'_\2\3sb_'.$pos.'_\4\5', $data);
    return ($data);
}

/**
 * Renders the Index
 *
 * copy of html_index located in /inc/html.php
 *
 * TODO update to new AJAX index possible?
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Michael Klier <chi@chimeric.de>
 */
function p_index_xhtml($ns,$pos) {
  require_once(DOKU_INC.'inc/search.php');
  global $conf;
  global $ID;
  $dir = $conf['datadir'];
  $ns  = cleanID($ns);
  #fixme use appropriate function
  if(empty($ns)){
    $ns = dirname(str_replace(':','/',$ID));
    if($ns == '.') $ns ='';
  }
  $ns  = utf8_encodeFN(str_replace(':','/',$ns));

  // extract only the headline
  preg_match('/<h1>.*?<\/h1>/', p_locale_xhtml('index'), $match);
  print preg_replace('#<h1(.*?id=")(.*?)(".*?)h1>#', '<h1\1sidebar_'.$pos.'_\2\3h1>', $match[0]);

  $data = array();
  search($data,$conf['datadir'],'search_index',array('ns' => $ns));

  print '<div id="' . $pos . '__index__tree">' . DOKU_LF;
  print html_buildlist($data,'idx','html_list_index','html_li_index');
  print '</div>' . DOKU_LF;
}

/**
 * searches for namespace sidebars
 *
 * @author Michael Klier <chi@chimeric.de>
 */
function _getNsSb($id) {
    $pname = tpl_getConf('pagename');
    $ns_sb = '';
    $path  = explode(':', $id);
    $trans_ns = _getTransNs();
    $found = false;

    while(count($path) > 0) {
        $cur_ns = implode(':', $path) . ':';

        // don't check if cur_ns = trans_ns
        if ($cur_ns == $trans_ns) break;

        // check sidebar in this ns
        $ns_sb = $cur_ns.$pname;
        if(@file_exists(wikiFN($ns_sb))) return $ns_sb;
        array_pop($path);
    }
    
    // nothing found
    return false;
}

/**
 * Try to get translated version of sidebar
 */
function _getTransSb($sb) {
	$trans_ns = _getTransNs();

	// check for translated sidebar
	if ($trans_ns) {
		$res_sb = $trans_ns . $sb;
		if (@file_exists(wikiFN($res_sb))) return $res_sb;
	}

	// check for untranslated sidebar
	if (@file_exists(wikiFN($sb))) return $sb;

	// neither exists
	return false;
}

/**
 * Returns namespace used for current translation
 *
 * TODO test how param 'translationns' works
 */
function _getTransNs() {
	global $conf;

    $trans = strtolower(str_replace(',',' ',$conf['plugin']['translation']['translations']));
    $trans = array_unique(array_filter(explode(' ',$trans)));
	$cur = $conf['lang'];

	if (in_array($cur, $trans)) {
		$prefix = cleanID($conf['plugin']['translation']['translationns']);
		if ($prefix) $prefix .= ':';
		return $prefix . $cur . ':';
	}
}

/**
 * Checks wether the sidebar should be hidden or not
 *
 * @author Michael Klier <chi@chimeric.de>
 */
function tpl_sidebar_hide() {
    global $ACT;
    $act_hide = array( 'edit', 'diff', 'preview', 'admin', 'conflict', 'draft', 'recover');
    if(in_array($ACT, $act_hide)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Prints login dialog if user is not logged in or userinfo in other case
 */
function tpl_login_dialog() {
	global $ID;
	global $INFO;
	global $lang;
	if ($INFO['userinfo']) {
		print '<div id="userinfo">' . DOKU_LF;

        // TODO: insert avatar

        print '<div id="fn">' . DOKU_LF;
        print $INFO['userinfo']['name'];
        print '</div>';

        print '<div id="actions">' . DOKU_LF;
		tpl_actionlink('profile');
		print ' • ' . DOKU_LF;
		tpl_actionlink('login');

		print '</div></div>' . DOKU_LF;
	} else {
		print '<div id="login__top">' . DOKU_LF;
		tpl_actionlink('login');
		print ' • ' . DOKU_LF;
	    print '<a href="'.wl($ID,'do=register').'" rel="nofollow" class="wikilink1">'.$lang['kunlaborejo_register'].'</a>';
		print '</div>' . DOKU_LF;
	}
}

// vim:ts=4:sw=4:et:enc=utf-8:
?>
