<?php
/**
 * 数组处理函数库 /common/array_utils.php
 * ==================================================
 * 处理数组的函数，多用于处理数据库中返回的数据
 * copy_array_from_specified_content($arr, ...$c)
 *   从数组$arr中复制指定key的value
 * copy_array_without_specified_content($arr, ...$c)
 *   从数组$arr中不复制指定key的value
 * ==================================================
 */

	function copy_array_from_specified_content($arr, ...$c) {
		$t = [];
		foreach ($arr as $k => $v) {
			foreach ($c as $k2 => $v2) {
				if ($v2 == $k) $t[$k] = $v;
			}
		}
		return $t;
	}

	function copy_array_without_specified_content($arr, ...$c) {
		$t = [];
		foreach ($arr as $k => $v) {
			foreach ($c as $k2 => $v2) {
				if ($v2 != $k) $t[$k] = $v;
			}
		}
		return $t;
	}
?>
