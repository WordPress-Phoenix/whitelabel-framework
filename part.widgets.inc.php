<?php
//allow widgets to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_WIDGETS_SUBPAGES')) $whitelabelWidgetsSubpagesFlag = WHITELABEL_WIDGETS_SUBPAGES;
if(!defined('WHITELABEL_WIDGETS_SUBPAGES') || $whitelabelWidgetsSubpagesFlag != false)
	get_template_part('part.widgets', 'subpages.inc');	
	
//allow widgets to be completely removed, or overwritten by child them template part
if(defined('WHITELABEL_WIDGETS_FLOATINGSOCIAL')) $whitelabelWidgetsFloatingsocialFlag = WHITELABEL_WIDGETS_FLOATINGSOCIAL;
if(!defined('WHITELABEL_WIDGETS_FLOATINGSOCIAL') || $whitelabelWidgetsFloatingsocialFlag != false)
	get_template_part('part.widgets', 'floatingsocial.inc');	