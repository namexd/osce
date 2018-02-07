<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
    <table>
        <tr>
            <th width="50">题目</th>
            <th width="20">答对人数</th>
            <th width="20">答错人数</th>
        </tr>
        @if(!empty($data))
            @foreach($data as $value)
                <tr>
                    <td>{{ $value['question'] }}</td>
                    <td>{{ $value['true'] }}</td>
                    <td>{{ $value['false'] }}</td>
                    @if(!empty($value['option']))
                        @foreach($value['option'] as $k=>$v)
                            <td>{{ $k."  ".$v['count'] }}</td>
                            <td width="20" >{{ $v['student'] }}</td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        @endif
    </table>
</body>

</html>