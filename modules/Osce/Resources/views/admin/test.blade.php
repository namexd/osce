<html>
    <body>
        <form action="{{route('osce.admin.room.postCreateRoom')}}" method="post">
            <input type="text" name="name"><br />
            <input type="text" name="nfc"><br />
            <input type="text" name="address"><br />
            <input type="text" name="code"><br />
            <input type="text" name="create_user_id"><br />
            <input type="submit" value="提交">
        </form>
    </body>
</html>