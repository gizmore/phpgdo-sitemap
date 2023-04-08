<?php
namespace GDO\Sitemap\Method;

use GDO\Core\GDO_Module;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Cronjob\MethodCronjob;
use GDO\Install\Installer;
use GDO\UI\MethodPage;
use GDO\User\GDO_User;
use GDO\Util\Strings;

/**
 * Show all available module methods.
 *
 * @version 7.0.3
 * @since 6.10.4
 * @author gizmore
 */
final class Show extends MethodPage
{

	public function getTitleLangKey(): string { return 'link_sitemap'; }

	public function isShownInSitemap(): bool { return false; }


	protected function getTemplateVars(): array
	{
		return [
			'moduleMethods' => $this->getModuleMethods(),
		];
	}

	private function getModuleMethods(): array
	{
		$moduleMethods = [];

		$modules = ModuleLoader::instance()->getEnabledModules();

		usort($modules, function (GDO_Module $a, GDO_Module $b)
		{
			return Strings::compare($a->renderName(), $b->renderName());
		});

		foreach ($modules as $module)
		{
			$moduleMethods[$module->getName()] = $this->getModuleMethodsB($module);
		}
		return $moduleMethods;
	}

	private function getModuleMethodsB(GDO_Module $module): array
	{
		$methods = [];
		$user = GDO_User::current();
		Installer::loopMethods($module, function ($entry, $fullpath, $args = null) use ($module, &$methods, $user)
		{
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

	private function _showInSitemap(GDO_Module $module, Method $method, GDO_User $user): bool
	{
		if (!$method->isShownInSitemap())
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

	private function initDefaultMethod(GDO_Module $module, Method $method, GDO_User $user): bool
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
