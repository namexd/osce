<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>删除一场考试</title>
</head>
<body>
<form action="{{route('osce.over-exam.destroy')}}" method="post">
  <label>考试id:<input type="text" name="exam_id"></label> <br />
  <input type="submit" value="submit">
</form>
</body>
</html>