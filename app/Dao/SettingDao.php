<?php

namespace App\Dao;

use App\Models\Setting;

/**
 * 网站配置业务模型
 *
 * @author lxp 20160627
 */
class SettingDao extends Setting
{

	private static $settings;

	/**
	 * 取得配置项的值
	 *
	 * @author lxp 20160702
	 * @param string $skey
	 * @return mixed
	 */
	public static function getSetting($skey)
	{
		if (static::$settings === null) {
			$settings = Setting::select('skey', 'svalue')->get();
			if ($settings->isNotEmpty()) {
				static::$settings = $settings->mapWithKeys(function ($item) {
					return [$item->skey => $item->svalue];
				});
			} else {
				static::$settings = [];
			}
		}

		if (isset(static::$settings[$skey])) {
			return unserialize(static::$settings[$skey]);
		} else {
			return '';
		}
	}

	/**
	 * 设置配置项
	 *
	 * @author lxp 20160702
	 * @param string $skey
	 * @param mixed $svalue 为null时则删除该键
	 */
	public static function setSetting($skey, $svalue)
	{
		$setting = Setting::where('skey', $skey)->first();
		if (!$setting) {
			$setting = new Setting();
			$setting->skey = $skey;
		}
		$setting->svalue = serialize($svalue);
		$setting->save();

		static::$settings = null;
	}
}
