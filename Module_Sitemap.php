<?php
namespace GDO\Sitemap;

use GDO\Core\GDO_Module;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_PageBar;
use GDO\UI\GDT_Bar;

/**
 * Build a sitemap reachable from the bottom bar.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class Module_Sitemap extends GDO_Module
{

	# #############
	# ## Module ###
	# #############
	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/sitemap');
	}

	public function getConfig(): array
	{
		return [
			GDT_PageBar::make('hook_sidebar')->initial('bottom'),
		];
	}

	public function cfgSideBar(): ?GDT_Bar
	{
		return $this->getConfigValue('hook_sidebar');
	}

	# ############
	# ## Hooks ###
	# ############
	public function onInitSidebar(): void
	{
		if ($bar = $this->cfgSideBar())
		{
			$bar->addFields(GDT_Link::make('link_sitemap')->href($this->href('Show')));
		}
	}

}
