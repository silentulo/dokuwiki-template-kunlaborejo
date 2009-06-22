<?php
/**
 * DokuWiki Arctic Template
 *
 * This is the template you need to change for the overall look
 * of DokuWiki.
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Michael Klier <chi@chimeric.de>
 * @link   http://wiki.splitbrain.org/template:arctic
 * @link   http://chimeric.de/projects/dokuwiki/template/arctic
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

// include custom arctic template functions
require_once(dirname(__FILE__).'/tpl_functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php tpl_pagetitle()?>
    [<?php echo strip_tags($conf['title'])?>]
  </title>

  <?php tpl_metaheaders()?>

  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />

  <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>

</head>
<body>
<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<div id="dokuwiki">

    <?php html_msgarea()?>

  <div id="header">
    <div class="site">
      <div id="logo">
        <?php tpl_link(wl(),
                       '<img src="' . DOKU_TPL . 'images/logo-2.png">',
                       'name="dokuwiki__top" accesskey="h" title="'.$conf['title'].'"')?>
      </div>
      <div id="userinfo">
      	Place for user info
      </div>
      <?php if(tpl_getConf('search') == 'top') { ?>
        <div id="searchtool">
          <?php if(tpl_getConf('search') == 'top') tpl_searchform() ?>
        </div>
      <?php }?>
    </div>
  </div>
    
  <div id="mainmenu">
    <div class="site">
    	Place for menu
	</div>
  </div>
  <div id="submenu">
    <div class="site">
    	Place for submenu
    </div>
  </div>
    
  <?php if(tpl_getConf('trace')) {?> 
    <div id="breadcrumbs">
      <div class="site">  
        <?php ($conf['youarehere'] != 1) ? tpl_breadcrumbs() : tpl_youarehere();?>
      </div>
    </div>
  <?php } ?>

    <?php /*old includehook*/ @include(dirname(__FILE__).'/header.html')?>
    <?php /*old includehook*/ @include(dirname(__FILE__).'/pageheader.html')?>

    <?php flush()?>
    
    <div class="site">
      <div id="left_sidebar">
        <?php if(tpl_getConf('search') == 'left') tpl_searchform() ?>
        <?php tpl_sidebar('left') ?>
      </div>
      <div id="page">
        <?php ($notoc) ? tpl_content(false) : tpl_content() ?>
      </div>
      <div id="right_sidebar">
        <?php if(tpl_getConf('search') == 'right') tpl_searchform() ?>
        <?php tpl_sidebar('right') ?>
      </div>
    </div>

  <div class="push"> </div>
</div>

<?php flush()?>

<div id="footer">
  <div class="site">
    <div id="page_meta" class="meta">
        <?php tpl_pageinfo()?>
    </div>
  </div>
  <div id="footer_info">
    <div class="site">
      <?php tpl_license('button', true)?>
      <a target="_blank" href="http://www.chimeric.de" title="www.chimeric.de"><img src="<?php echo DOKU_TPL?>images/button-chimeric-de.png" width="80" height="15" alt="www.chimeric.de" border="0" /></a>
      <a target="_blank" href="http://jigsaw.w3.org/css-validator/check/referer" title="Valid CSS"><img src="<?php echo DOKU_TPL?>images/button-css.png" width="80" height="15" alt="Valid CSS" border="0" /></a>
      <a target="_blank" href="http://wiki.splitbrain.org/wiki:dokuwiki" title="Driven by DokuWiki"><img src="<?php echo DOKU_TPL?>images/button-dw.png" width="80" height="15" alt="Driven by DokuWiki" border="0" /></a>
      <a target="_blank" href="http://www.firefox-browser.de" title="do yourself a favour and use a real browser - get firefox"><img src="<?php echo DOKU_TPL?>images/button-firefox.png" width="80" height="15" alt="do yourself a favour and use a real browser - get firefox!!" border="0" /></a>
      <a target="_blank" href="<?php echo DOKU_BASE?>feed.php" title="Recent changes RSS feed"><img src="<?php echo DOKU_TPL?>images/button-rss.png" width="80" height="15" alt="Recent changes RSS feed" border="0" /></a>
      <a target="_blank" href="http://validator.w3.org/check/referer" title="Valid XHTML 1.0"><img src="<?php echo DOKU_TPL?>images/button-xhtml.png" width="80" height="15" alt="Valid XHTML 1.0" border="0" /></a>
    </div>
  </div>
</div>


<div class="no"><?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?></div>
</body>
</html>
