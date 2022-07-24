<?php
namespace GDO\Sitemap;

use GDO\Core\GDO_Module;
use GDO\UI\GDT_Link;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Page;

/**
 * Build a sitemap reachable from the bottom bar.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class Module_Sitemap extends GDO_Module
{
	##############
	### Module ###
	##############
	public function onLoadLanguage() : void { $this->loadLanguage('lang/sitemap'); }
	
	public function getConfig() : array
	{
	    return [
	        GDT_Checkbox::make('hook_sidebar')->initial('1'),
	    ];
	}
	public function cfgBottomBar() { return $this->getConfigValue('hook_sidebar'); }
	
	#############
	### Hooks ###
	#############
	public function onInitSidebar() : void
	{
	    if ($this->cfgBottomBar())
	    {
	        GDT_Page::instance()->bottomBar()->addField(GDT_Link::make('link_sitemap')->href($this->href('Show')));
	    }
	}

}
