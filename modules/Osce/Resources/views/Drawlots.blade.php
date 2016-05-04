<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>draw</title>
</head>
<body>
<form action="{{route('osce.pad.postDrawlots')}}" method="post">
    uid：<input type="text" name="uid" id="uid"><br /><br />
    room_id：<input type="text" name="room_id" id="room_id"><br /><br />
    exam_id：<input type="text" name="exam_id" id="exam_id"><br /><br />
    <input type="submit" value="submit">
</form>
</body>
</html>