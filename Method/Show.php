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

/**
 * Show all available module methods.
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.4
 */
final class Show extends MethodPage
{
	public function showInSitemap() : bool { return false; }
	
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
// 			$method = $module->getMethod(Strings::rsubstrTo($entry, '.php'));
// 			foreach ($this->getSitemapMethods($module, $method, $user) as $method)
// 			{
// 				$methods[] = $method;
// 			}
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
		
		if (true !== $method->checkPermission($user))
		{
			return false;
		}
		
		return true;
	}
	
	private function initDefaultMethod(GDO_Module $module, Method $method, GDO_User $user)
	{
		$parameters = $method->gdoParameterCache();
		foreach ($parameters as $gdt)
		{
			if (isset($gdt->notNull) && $gdt->notNull)
			{
				if (!$gdt->getVar())
				{
					return false;
				}
			}
		}
		return true;
	}
	
}
