<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Excel导入</title>
</head>
<body>
    <h1>教室导入</h1>
    <form action="{{url('msc/admin/upload/upload-user')}}" method="post" enctype="multipart/form-data">
        <input type="file" value="选择文件" name="user"><br />
        <input type="submit" value="提交">
    </form>
</body>
</html>