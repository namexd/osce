@extends('msc::admin.layouts.admin')

@section('content')
    {{-- 资源编辑页面 --}}
    <form action="/msc/admin/resources-manager/edit-resources" method="post"  enctype="multipart/form-data">
        <input type="hidden" name="access_token" value="5LNYf06v35fB0nu0IgVL26BXHbxU7DzR6cdCTK6c">

        <input type="hidden" name="id" value="3"><br/>
        <input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051447430.png"><br/>
        <input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051498214.png"><br/>
        <input type="hidden" name="images_path[]" value="/images/201511/13/201511131440332814.png"><br/>
        <input type="hidden" name="images_path[]" value="/images/201511/13/2015111314403328745.png"><br/>
        <input type="hidden" name="images_path[]" value="/images/201511/13/2015111311051447431.png"><br/>

        设备名称<input type="text" name="name" value="{{ $resource['code'] }}"><br/>
        设备分类<input type="text" name="cateid" value=""><br/>
        设备编码<input type="text" name="code" value=""><br/>
        设备负责人<input type="text" name="manager_name" value=""><br/>
        设备负责人电话<input type="text" name="manager_mobile" value=""><br/>
        地址<input type="text" name="location_id" value=""><br/>
        设备功能<input type="text" name="detail" value=""><br/>

        <input type="submit" name="submit">
    </form>
@stop{{-- 内容主体区域 --}}