<?php
namespace GDO\Sitemap;

use GDO\Core\GDO_Module;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_PageBar;

/**
 * Build a sitemap reachable from the bottom bar.
 *
 * @version 7.0.1
 * @since 6.10.0
 * @author gizmore
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

	public function onInitSidebar(): void
	{
		if ($bar = $this->cfgSideBar())
		{
			$bar->addFields(GDT_Link::make('link_sitemap')->href($this->href('Show')));
		}
	}

	# ############
	# ## Hooks ###
	# ############

	public function cfgSideBar(): ?GDT_Bar
	{
		return $this->getConfigValue('hook_sidebar');
	}

}
