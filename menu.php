
<li><?php tpl_link(wl('pri_ni:komenco'), $lang['kunlaborejo_about']) ?> </li>
<li><?php tpl_link(wl('projektoj:komenco'), $lang['kunlaborejo_projects']) ?></li>
<li><?php tpl_link(wl('kluboj:komenco'), $lang['kunlaborejo_clubs']) ?></li>
<li><?php tpl_link(wl('renkontigxoj:komenco'), $lang['kunlaborejo_meets']) ?></li>
<li><?php tpl_link(wl('helpo:komenco'), $lang['kunlaborejo_help']) ?></li>

<?php if (actionOK('admin')) { ?>
	<li class="right admin"><?php tpl_actionlink ('admin') ?></li>
<?php } ?>

<li class="right"><?php tpl_link(wl('membrejo:komenco'),  $lang['kunlaborejo_member']) ?></li>
