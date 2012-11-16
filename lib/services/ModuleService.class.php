<?php
/**
 * @package modules.icirelais.lib.services
 */
class icirelais_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var icirelais_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return icirelais_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
}