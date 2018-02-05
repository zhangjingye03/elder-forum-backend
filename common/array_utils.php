<?php

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
