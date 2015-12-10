@extends('msc::admin.layouts.admin')

@section('content')
    {{-- 资源报废页面 --}}
    <form action="/msc/admin/resources-manager/rejected-resources" method="post" >
        <input type="hidden" name="access_token" value="5LNYf06v35fB0nu0IgVL26BXHbxU7DzR6cdCTK6c">

        设备ID<input type="text" name="id" value="{{ $resource['id'] }}"><br/>
        报废描述<input type="text" name="reject_detail" value=""><br/>
        <input type="submit" name="submit">
    </form>
@stop{{-- 内容主体区域 --}}