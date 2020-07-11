<?php

namespace App\Dao;

use App\Dao\Load\LoadDao;
use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\Exhibition;
use App\Models\ExhibitionLanguage;
use App\Models\ExhibitLanguage;
use App\Models\VersionList;
use Illuminate\Support\Facades\DB;

/**
 * 展品列表距离排序
 *
 * @author yyj 20171111
 */
class ResourceDao extends Controller
{

	/**
	 * 图片上传验证
	 *
	 * @author yyj 20180306
	 * @param array $imgs 图片数组
	 * @return array
	 */
	public static function check_upload_imgs($imgs)
	{
		$arr['status'] = true;
		$arr['msg'] = 'success';
		foreach ($imgs as $k => $g) {
			$img_path = request($g['key']);
			if ($g['is_more']) {
				if ($g['required'] && !is_array($img_path) && count($img_path) == 0) {
					$arr['status'] = false;
					$arr['msg'] = '请上传' . $g['name'];
					return $arr;
				}
				$arr['imgs'][$g['key']] = $img_path;
			} else {
				if ($g['required'] && empty($img_path)) {
					$arr['status'] = false;
					$arr['msg'] = '请上传' . $g['name'];
					return $arr;
				}
				$arr['imgs'][$g['key']] = $img_path;
			}
		}
		return $arr;
	}

	/**
	 * 获取当前版本id
	 *
	 * @author yyj 20180306
	 * @return int
	 */
	public static function get_version_id()
	{
		$version_id = VersionList::where('type', 0)->value('id');
		if (empty($version_id)) {
			$version_info = VersionList::create(['type' => 0]);
			$version_id = $version_info->id;
		}
		return $version_id;
	}

	/**
	 * 获取html中的所有图片路径
	 *
	 * @author yyj 20180307
	 * @param string $html_data html字符串
	 * @return array
	 */
	public static function get_html_imgs($html_data)
	{
		$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
		preg_match_all($preg, $html_data, $imgArr);
		$imgs = $imgArr[1];
		return $imgs;
	}

	/**
	 * 获取html中的所有图片路径
	 *
	 * @author yyj 20180307
	 * @param string $type 类型
	 * @param int $id id
	 */
	public static function del_img($type, $id)
	{
		$imgs = [];
		$htmls = [];
		if ($type == 'exhibition') {
			//删除展厅图片
			$exhibition_img = Exhibition::where('id', $id)->value('exhibition_img');
			$imgs = json_decode($exhibition_img, true);
			$htmls = ExhibitionLanguage::where('exhibition_id', $id)->pluck('content')->toArray();
			//删除展厅更新资源
			if (config('exhibit_config.is_version_zip')) {
				$exhibition_path = base_path() . '/public/resource_zip/exhibition/' . $id . '/';
				$version_id = self::get_version_id();
				$version_exhibition_path = base_path() . '/public/resource_zip/version_' . $version_id . '/exhibition/' . $id . '/';
				deldir($exhibition_path, 1);
				deldir($version_exhibition_path, 1);
			}

		}
		if ($type == 'exhibit') {
			//删除展品图片
			$exhibit_img = Exhibit::where('id', $id)->value('exhibit_img');
			$imgs = json_decode($exhibit_img, true);
			foreach (config('exhibit_config.exhibit.content_arr') as $kkk => $ggg) {
				$htmls_arr = ExhibitLanguage::where('exhibit_id', $id)->pluck($ggg['key'])->toArray();
				foreach ($htmls_arr as $k => $g) {
					if (!empty($g)) {
						$htmls[] = $g;
					}
				}
			}
			//删除展品MP3
			$mp3_arr = ExhibitLanguage::where('exhibit_id', $id)->pluck('audio')->toArray();
			foreach ($mp3_arr as $k => $g) {
				if (!empty($g) && file_exists(base_path() . '/public' . $g)) {
					unlink(base_path() . '/public' . $g);
				}
			}
			//删除展厅更新资源
			if (config('exhibit_config.is_version_zip')) {
				$exhibit_path = base_path() . '/public/resource_zip/exhibit/' . $id . '/';
				$version_id = self::get_version_id();
				$version_exhibit_path = base_path() . '/public/resource_zip/version_' . $version_id . '/exhibit/' . $id . '/';
				deldir($exhibit_path, 1);
				deldir($version_exhibit_path, 1);
			}
		}
		self::del_imgs_arr($imgs);
		foreach ($htmls as $k => $g) {
			$imgs = self::get_html_imgs($g);
			self::del_imgs_arr($imgs);
		}
	}

	private static function del_imgs_arr($imgs)
	{
		foreach ($imgs as $k => $g) {
			if (is_array($g)) {
				foreach ($g as $kk => $gg) {
					if (file_exists(base_path() . '/public' . $gg) && !empty($gg)) {
						unlink(base_path() . '/public' . $gg);
					}
				}
			} else {
				if (file_exists(base_path() . '/public' . $g) && !empty($g)) {
					unlink(base_path() . '/public' . $g);
				}
			}
		}
	}

	/**
	 * 图片资源更新
	 *
	 * @author yyj 20180306
	 * @param array $new_info 新资源
	 * @param array $old_info 旧资源
	 * @param string $exhibition_path 整包资源路径
	 * @param string $version_exhibition_path 增量资源路径
	 * @param string $key 图片键名
	 * @param string $config_key 配置文件键名
	 * @return int
	 */
	private static function update_images($new_info, $old_info, $exhibition_path, $version_exhibition_path, $key, $config_key)
	{
		$img_path = $exhibition_path . 'images/';
		$version_img_path = $version_exhibition_path . 'images/';
		if (!file_exists($img_path)) {
			mkdir($img_path, 0777, true);
		}
		if (!file_exists($version_img_path)) {
			mkdir($version_img_path, 0777, true);
		}
		$new_imgs = json_decode($new_info[$key], true);
		$old_imgs = isset($old_info[$key]) ? $old_info[$key] : [];
		//获取资源打包图片数组
		foreach (config('exhibit_config.' . $config_key . '.imgs') as $k => $g) {
			if ($g['is_zip']) {
				$old_img = isset($old_imgs[$g['key']]) ? $old_imgs[$g['key']] : '';
				$new_img = isset($new_imgs[$g['key']]) ? $new_imgs[$g['key']] : '';
				if ($g['is_more']) {
					if (is_array($new_img)) {
						foreach ($new_img as $kk => $gg) {
							$temp = explode(".", $gg);
							$extension = end($temp);
							if (file_exists(base_path() . '/public' . $gg)) {
								copy(base_path() . '/public' . $gg, $img_path . $g['zip_name'] . ($kk + 1) . '.' . $extension);
							}
							if (empty($old_img) || !isset($old_img[$kk]) || $old_img[$kk] !== $gg) {
								//增量更新资源中删除无用图片
								if (isset($old_img[$kk]) && $old_img[$kk] !== $gg) {
									$old_temp = explode(".", $old_img[$kk]);
									$old_extension = end($old_temp);
									if (file_exists($version_img_path . $g['zip_name'] . ($kk + 1) . '.' . $old_extension)) {
										unlink($version_img_path . $g['zip_name'] . ($kk + 1) . '.' . $old_extension);
									}
								}
								if (file_exists(base_path() . '/public' . $gg)) {
									copy(base_path() . '/public' . $gg, $version_img_path . $g['zip_name'] . ($kk + 1) . '.' . $extension);
								}
							}
						}
					}
				} else {
					$temp = explode(".", $new_img);
					$extension = end($temp);
					if (file_exists(base_path() . '/public' . $new_img)) {
						copy(base_path() . '/public' . $new_img, $img_path . $g['zip_name'] . '.' . $extension);
					}
					if (empty($old_img) || $old_img !== $new_img) {
						if ($old_img !== $new_img) {
							//增量更新资源中删除无用图片
							$old_temp = explode(".", $old_img);
							$old_extension = end($old_temp);
							if (file_exists($version_img_path . $g['zip_name'] . '.' . $old_extension)) {
								unlink($version_img_path . $g['zip_name'] . '.' . $old_extension);
							}
						}
						if (file_exists(base_path() . '/public' . $new_img)) {
							copy(base_path() . '/public' . $new_img, $version_img_path . $g['zip_name'] . '.' . $extension);
						}
					}
				}
			}
		}
	}

	/**
	 * 展厅html资源更新
	 *
	 * @author yyj 20180306
	 * @param array $new_info 新资源
	 * @param array $old_info 旧资源
	 * @param string $exhibition_path 整包资源路径
	 * @param string $version_exhibition_path 增量资源路径
	 * @param string $base_html 模板路径
	 * @return int
	 */
	private static function update_exhibition_html($new_info, $old_info, $exhibition_path, $version_exhibition_path, $base_html)
	{
		foreach ($new_info['language'] as $k => $g) {
			$dir = config('language')[$k]['dir'];
			$html_path = $exhibition_path . $dir . '/';
			$version_html_path = $version_exhibition_path . $dir . '/';
			if (!file_exists($html_path)) {
				mkdir($html_path, 0777, true);
			}
			//替换模板文件内容
			$html_data = file_get_contents($base_html);
			$html_data = str_replace('{$name}', $g['exhibition_name'], $html_data);
			$html_data = str_replace('{$address}', $g['exhibition_address'], $html_data);
			$html_data = str_replace('{$content}', $g['content'], $html_data);
			//匹配所有图片地址
			$imgs = self::get_html_imgs($html_data);
			$html_img_path = $html_path . 'images/';
			foreach ($imgs as $kk => $gg) {
				if (!file_exists($html_img_path)) {
					mkdir($html_img_path, 0777, true);
				}
				$temp = explode(".", $gg);
				$extension = end($temp);
				if (file_exists(base_path() . '/public' . $gg)) {
					copy(base_path() . '/public' . $gg, $html_img_path . ($kk + 1) . '.' . $extension);
				}
				$html_data = str_replace($gg, './images/' . ($kk + 1) . '.' . $extension, $html_data);
			}
			file_put_contents($html_path . $dir . '.html', $html_data);

			//增量更新文件修改
			if (empty($old_info['language']) || !isset($old_info['language'][$k]) || md5($g['exhibition_name']) != md5($old_info['language'][$k]['exhibition_name']) || md5($g['exhibition_address']) != md5($old_info['language'][$k]['exhibition_address']) || md5($g['content']) != md5($old_info['language'][$k]['content'])) {
				if (!file_exists($version_html_path)) {
					mkdir($version_html_path, 0777, true);
				}
				deldir($version_html_path, 0);
				$version_html_img_path = $version_html_path . 'images/';
				foreach ($imgs as $kk => $gg) {
					if (!file_exists($version_html_img_path)) {
						mkdir($version_html_img_path, 0777, true);
					}
					$temp = explode(".", $gg);
					$extension = end($temp);
					if (file_exists(base_path() . '/public' . $gg)) {
						copy(base_path() . '/public' . $gg, $version_html_img_path . ($kk + 1) . '.' . $extension);
					}
					$html_data = str_replace($gg, './images/' . ($kk + 1) . '.' . $extension, $html_data);
				}
				file_put_contents($version_html_path . $dir . '.html', $html_data);
			}

		}
	}

	/**
	 * 展厅资源更新
	 *
	 * @author yyj 20180306
	 * @param array $new_info 新资源
	 * @param array $old_info 旧资源
	 * @param int $id 目录id
	 * @return array
	 */
	public static function update_exhibition_resource($new_info, $old_info, $id)
	{
		$exhibition_path = base_path() . '/public/resource_zip/exhibition/' . $id . '/';
		$version_id = self::get_version_id();
		$version_exhibition_path = base_path() . '/public/resource_zip/version_' . $version_id . '/exhibition/' . $id . '/';
		$base_html = base_path() . '/public/resource_zip/exhibition.html';
		if (!file_exists($exhibition_path)) {
			mkdir($exhibition_path, 0777, true);
		} else {
			//清空历史资源
			deldir($exhibition_path, 0);
		}
		if (!file_exists($version_exhibition_path)) {
			mkdir($version_exhibition_path, 0777, true);
		}
		//图片资源更新
		self::update_images($new_info, $old_info, $exhibition_path, $version_exhibition_path, 'exhibition_img', 'exhibition');
		//语种资源更新
		self::update_exhibition_html($new_info, $old_info, $exhibition_path, $version_exhibition_path, $base_html);
	}

	/**
	 * 展品资源更新
	 *
	 * @author yyj 20180306
	 * @param array $new_info 新资源
	 * @param array $old_info 旧资源
	 * @param int $id 目录id
	 * @return array
	 */
	public static function update_exhibit_resource($new_info, $old_info, $id)
	{
		$exhibit_path = base_path() . '/public/resource_zip/exhibit/' . $id . '/';
		$version_id = self::get_version_id();
		$version_exhibit_path = base_path() . '/public/resource_zip/version_' . $version_id . '/exhibit/' . $id . '/';
		if (!file_exists($exhibit_path)) {
			mkdir($exhibit_path, 0777, true);
		} else {
			//清空历史资源
			deldir($exhibit_path, 0);
		}
		if (!file_exists($version_exhibit_path)) {
			mkdir($version_exhibit_path, 0777, true);
		}
		//图片资源更新
		self::update_images($new_info, $old_info, $exhibit_path, $version_exhibit_path, 'exhibit_img', 'exhibit');
		//语种资源更新
		self::update_exhibit_html($new_info, $old_info, $exhibit_path, $version_exhibit_path);
	}

	/**
	 * 展品html资源更新
	 *
	 * @author yyj 20180306
	 * @param array $new_info 新资源
	 * @param array $old_info 旧资源
	 * @param string $exhibit_path 整包资源路径
	 * @param string $version_exhibit_path 增量资源路径
	 * @return int
	 */
	public static function update_exhibit_html($new_info, $old_info, $exhibit_path, $version_exhibit_path)
	{
		foreach ($new_info['language'] as $k => $g) {
			$dir = config('language')[$k]['dir'];
			$html_path = $exhibit_path . $dir . '/';
			$version_html_path = $version_exhibit_path . $dir . '/';
			if (!file_exists($html_path)) {
				mkdir($html_path, 0777, true);
			}
			if (!file_exists($version_html_path)) {
				mkdir($version_html_path, 0777, true);
			}
			//html资源更新
			foreach (config('exhibit_config.exhibit.content_arr') as $kkk => $ggg) {
				$base_html = base_path() . '/public/resource_zip/exhibit_' . $ggg['key'] . '.html';
				//替换模板文件内容
				$html_data = file_get_contents($base_html);
				$html_data = str_replace('{$name}', $g['exhibit_name'], $html_data);
				$html_data = str_replace('{$' . $ggg['key'] . '}', $g[$ggg['key']], $html_data);
				//匹配所有图片地址
				$imgs = self::get_html_imgs($html_data);
				$html_img_path = $html_path . 'images/' . $ggg['key'] . '/';
				foreach ($imgs as $kk => $gg) {
					if (!file_exists($html_img_path)) {
						mkdir($html_img_path, 0777, true);
					}
					$temp = explode(".", $gg);
					$extension = end($temp);
					if (file_exists(base_path() . '/public' . $gg)) {
						copy(base_path() . '/public' . $gg, $html_img_path . ($kk + 1) . '.' . $extension);
					}
					$html_data = str_replace($gg, './images/' . $ggg['key'] . '/' . ($kk + 1) . '.' . $extension, $html_data);
				}
				file_put_contents($html_path . $dir . '_' . $ggg['key'] . '.html', $html_data);

				//增量更新文件修改
				if (empty($old_info['language']) || !isset($old_info['language'][$k]) || md5($g['exhibit_name']) != md5($old_info['language'][$k]['exhibit_name']) || md5($g[$ggg['key']]) != md5($old_info['language'][$k][$ggg['key']])) {
					deldir($version_html_path . 'images/' . $ggg['key'] . '/', 0);
					$version_html_img_path = $version_html_path . 'images/' . $ggg['key'] . '/';
					foreach ($imgs as $kk => $gg) {
						if (!file_exists($version_html_img_path)) {
							mkdir($version_html_img_path, 0777, true);
						}
						$temp = explode(".", $gg);
						$extension = end($temp);
						if (file_exists(base_path() . '/public' . $gg)) {
							copy(base_path() . '/public' . $gg, $version_html_img_path . ($kk + 1) . '.' . $extension);
						}
						$html_data = str_replace($gg, './images/' . $ggg['key'] . '/' . ($kk + 1) . '.' . $extension, $html_data);
					}
					file_put_contents($version_html_path . $dir . '_' . $ggg['key'] . '.html', $html_data);
				}
			}

			//mp3资源更新
			if (!empty($g['audio']) && file_exists(base_path() . '/public' . $g['audio'])) {
				copy(base_path() . '/public' . $g['audio'], $html_path . $dir . '.mp3');
			}
			if (empty($old_info['language']) || !isset($old_info['language'][$k]) || $g['audio'] != $old_info['language'][$k]['audio']) {
				if (!empty($g['audio']) && file_exists(base_path() . '/public' . $g['audio'])) {
					copy(base_path() . '/public' . $g['audio'], $version_html_path . $dir . '.mp3');
				}
			}
		}
	}
}
