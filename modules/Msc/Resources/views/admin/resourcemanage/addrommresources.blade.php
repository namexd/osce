<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<form  method="post" action="http://test.com/msc/admin/resources-manager/add-resources" >

			<input type="hidden" name="resources_type" value="CLASSROOM" />

			名称<input type="text" name="name" value="" /><br/>
			编码<input type="text" name="code" value="" /><br/>
			开始时间<input type="text" name="begintime" value="" /><br/>
			结束时间<input type="text" name="endtime" value="" /><br/>
			管理员姓名<input type="text" name="manager_name" value="" /><br/>
			管理员手机<input type="text" name="manager_mobile" value="" /><br/>
			地址<input type="text" name="location" value="" /><br/>
			总人数<input type="text" name="person_total" value="" /><br/>
			描述<input type="text" name="detail" value="" /><br/>
			图片<input type="text" name="images_path[]" />
			<input type="submit" value="提交"/>
		</form>
	</body>

</html>