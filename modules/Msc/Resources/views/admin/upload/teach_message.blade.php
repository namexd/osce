<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>上传excel</title>
</head>
<body>
<form action="{{url('msc/admin/upload/teach-message-excel')}}" method="post" enctype="multipart/form-data">
    <input type="file" name="teach" ><br>
    <input type="submit" value="提交">
</form>
</body>
</html>