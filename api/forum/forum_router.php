<?php
	check_method("GET", "POST", "PUT", "DELETE");
	$ac = get_remaining_slash_arg_count();
	if ($_method == "GET") {
		allow_remaining_slash_arg_count(0, 1, 2);
		if ($ac == 0) # 不带参数：获取板块列表
			require("getCategoryList.php");
		else if ($ac == 1) # 1个参数：获取板块中的话题列表
			require("getTopicList.php");
		else # 2个参数：获取指定话题的详情
			require("getTopicContent.php");
	} else {
		require_login();
		if ($_method == "POST") {
			allow_remaining_slash_arg_count(0, 1, 2);
			if ($ac == 0) # 不带参数：创建板块
				require("createCategory.php");
			else if ($ac == 1) # 1个参数：发帖
				require("postTopic.php");
			else # 2个参数：回帖/盖楼
				require("replyTopic.php");
		} else if ($_method == "PUT") {
			allow_remaining_slash_arg_count(1, 2, 3);
			if ($ac == 1) # 1个参数：更新板块信息
				require("modifyCategory.php");
			else if ($ac == 2) # 2个参数：修改帖子信息
				require("modifyTopic.php");
			else # 3个参数：修改楼层
				require("modifyTopicReply.php");
		} else if ($_method == "DELETE") {
			allow_remaining_slash_arg_count(1, 2, 3);
			if ($ac == 1) # 1个参数：删除板块
				require("deleteCategory.php");
			else if ($ac == 2) # 2个参数：删除帖子
				require("deleteTopic.php");
			else # 3个参数：删除楼层
				require("deleteTopicReply.php");
		}
	}
?>
