<?php
namespace GDO\Sitemap\Method;

use GDO\UI\MethodPage;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO_Module;
use GDO\Install\Installer;
use GDO\Util\Strings;
use GDO\User\GDO_User;
use GDO\Core\Method;
use GDO\Cronjob\MethodCronjob;
use GDO\Language\Module_Language;
use GDO\Language\Method\SwitchLanguage;

/**
 * Show all available module methods.
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.4
 */
final class Show extends MethodPage
{
	public function showInSitemap() { return false; }
	
	public function getTitleLangKey() { return 'link_sitemap'; }
	
	protected function getTemplateVars()
	{
		return [
			'moduleMethods' => $this->getModuleMethods(),
		];
	}
	
	private function getModuleMethods()
	{
		$moduleMethods = array();
		foreach (ModuleLoader::instance()->getEnabledModules() as $module)
		{
			$moduleMethods[$module->getName()] = $this->getModuleMethodsB($module);
		}
		return $moduleMethods;
	}
	
	private function getModuleMethodsB(GDO_Module $module)
	{
		$methods = array();
		$user = GDO_User::current();
		Installer::loopMethods($module, function($entry, $fullpath, $args=null) use($module, &$methods, $user) {
			$method = $module->getMethod(Strings::rsubstrTo($entry, '.php'));
			foreach ($this->getSitemapMethods($module, $method, $user) as $method)
			{
				$methods[] = $method;
			}
			$method = $module->getMethod(Strings::rsubstrTo($entry, '.php'));
			if ($this->_showInSitemap($module, $method, $user))
			{
				$methods[] = $method;
			}
		});
		return $methods;
	}
	
	private function _showInSitemap(GDO_Module $module, Method $method, GDO_User $user)
	{
		if (!$method->showInSitemap())
		{
			return false;
		}
		
		if ($method instanceof MethodCronjob)
		{
			return false;
		}
		
		if ($method->isAjax())
		{
			return false;
		}
		
		if (!$this->initDefaultMethod($module, $method, $user))
		{
			return false;
		}
		
		if (!$method->hasUserPermission($user))
		{
			return false;
		}
		
		return true;
	}
	
	private function initDefaultMethod(GDO_Module $module, Method $method, GDO_User $user)
	{
		try
		{
			$method->init();
			if ($parameters = $method->gdoParameterCache())
			{
				foreach ($parameters as $gdt)
				{
					if ($gdt->notNull)
					{
						return false;
					}
				}
			}
			return true;
		}
		catch (\Throwable $ex)
		{
		}
		return false;
	}
	
	private function getSitemapMethods(GDO_Module $module, Method $method, GDO_User $user)
	{
		$methods = [];
		if ($module === Module_Language::instance())
		{
			if ($method instanceof SwitchLanguage)
			{
				foreach (Module_Language::instance()->cfgSupported() as $lang)
				{
					$m = SwitchLanguage::make();
					$m->setGDOParamter('_lang', $lang->getISO());
					$methods[] = $m;
				}
			}
		}
		return $methods;
	}
	
}
