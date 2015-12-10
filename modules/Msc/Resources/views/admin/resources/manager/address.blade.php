@extends('msc::admin.layouts.admin')

@section('content')
    {{-- 资源地址新增页面 --}}
    <form action="/msc/admin/resources-manager/add-address" method="post" >
        <input type="hidden" name="access_token" value="5LNYf06v35fB0nu0IgVL26BXHbxU7DzR6cdCTK6c">

        地址<input type="text" name="name" value=""><br/>
        编号<input type="text" name="code" value=""><br/>
        上级ID<input type="text" name="pid" value=""><br/>
        <input type="submit" name="submit">
    </form>
@stop{{-- 内容主体区域 --}}