<!-- indexer::stop -->
<?php $sb_id = "shoutbox_".$this->shoutbox_id; ?>

<?php if ($this->headline): ?>
	<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif;?>

<div class="shoutbox" id="<?php echo $sb_id; ?>">

<div class="menubar">
	<button type="button" onclick="Shoutbox.refresh('<?php echo $sb_id; ?>')"><img src="/system/themes/default/images/reload.gif"></button>
</div>

<div class="entries"><ul id="<?php echo $sb_id; ?>_list">

<?php foreach ($this->comments as $comment): ?>
	<?php echo $comment; ?>
<?php endforeach; ?>

</ul></div>

<?php if (!$this->loggedIn): ?>
	<p class="info">Bitte einloggen um etwas zu schreiben!</p>
<?php else: ?>

<form action="?shoutbox_action=shout" method="post">
	<input class="request_token" type="hidden" name="REQUEST_TOKEN" value="{{request_token}}">  
	<input type="hidden" name="FORM_SUBMIT" value="com_Shoutbox_<?php echo $this->shoutbox_id; ?>" />
	<input type="hidden" name="shoutbox" value="shout" />
	<div class="txtarea">
	<textarea id="<?php echo $sb_id; ?>_textarea" name="comment" rows="<?php echo $this->shoutbox_rows; ?>" cols="<?php echo $this->shoutbox_cols; ?>"></textarea>
	</div>
	<button type="submit" class="submit">Senden</button>
	<div class="smiley_legend">
		<ul class="smiley_list">
			<li><span title=":)" class="emoticon emoticon-1"></span></li>
			<li><span title=":(" class="emoticon emoticon-2"></span></li>
			<li><span title=";)" class="emoticon emoticon-3"></span></li>
			<li><span title="8)" class="emoticon emoticon-4"></span></li>			
			<li><span title="*JOKE*" class="emoticon emoticon-5"></span></li>
			<li><span title=":'(" class="emoticon emoticon-6"></span></li>			
		</ul>
		<ul class="smiley_list">			
			<li><span title=":|" class="emoticon emoticon-7"></span></li>
			<li><span title=":-*" class="emoticon emoticon-8"></span></li>			
			<li><span title="*angel*" class="emoticon emoticon-9"></span></li>
			<li><span title="]:-|" class="emoticon emoticon-10"></span></li>			
			<li><span title=":-(|)" class="emoticon emoticon-11"></span></li>
			<li><span title=":o" class="emoticon emoticon-12"></span></li>			
		</ul>
	</div>
</form>

<?php endif; // !loggedIn ?>

<script type="text/javascript">
<!--//--><![CDATA[//><!--
	window.addEvent('domready', function() {
		<?php if ($this->loggedIn): ?>
    	Shoutbox.init('shoutbox_<?php echo $this->shoutbox_id; ?>');
    	<?php endif;?>
    	Shoutbox.updateShoutboxLinkTags('shoutbox_<?php echo $this->shoutbox_id; ?>');
	});
	// Refresh automatisieren
//--><!]]>
</script>


</div>
<!-- indexer::continue -->