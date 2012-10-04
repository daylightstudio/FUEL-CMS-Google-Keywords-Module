<div id="fuel_main_content_inner">
	<p class="instructions">
		<?=lang('google_keywords_instructions')?>
	</p>

	<div class="float_left"><label for="domain"><?=lang('google_keywords_label_domain')?></label> <?=$this->form->text('domain', $domain)?></div>
	<div class="float_left">
		<label for="keywords" style="padding-left: 20px;"><?=lang('google_keywords_label_keywords')?></label> 
		<?php if (is_array($keywords)){?>
			<?=$this->form->select('keywords', $keywords)?>
		<?php } else { ?>
			<?=$this->form->text('keywords', $keywords, 'size="30"')?>
		<?php } ?>
	</div>
	<div class="float_left" style="margin: -1px 0 0 10px;">
	
		<?=$this->form->submit(lang('btn_submit_keywords'), 'submit_keywords')?>
	</div>				
	<div class="clear"></div>

	<br />
	<div id="keyword_loader" class="loader hidden float_left"></div>
	<div id="results">

	</div>

</div>
