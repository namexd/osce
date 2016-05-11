<?php
/**
 * Created by PhpStorm.
 * User: 梧桐雨间的枫叶
 * Date: 2016/1/2
 * Time: 21:01
 */

namespace Modules\Osce\Http\Controllers\Admin;
use App\Repositories\Common;
use DB;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Osce\Entities\CaseModel;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\StandardItem;
use Modules\Osce\Entities\Subject;
use Modules\Osce\Entities\SubjectCases;
use Modules\Osce\Entities\SubjectItem;
use Modules\Osce\Entities\SubjectSupply;
use Modules\Osce\Entities\Supply;
use Modules\Osce\Entities\TeacherSubject;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common as OsceCommon;

class ShowLogController extends CommonController
{
   public function ShowLog(Request $request){

       header('Content-type:text/html;charset=utf-8');
       #设置执行时间不限时
       set_time_limit(0);
       if(env('ShowlogFlag',false) == false)
       {
           dd('浏览器显示日志开关未打开');
       }
       $name =$request->input('name','laravel-');
       $date = $request->input('date',date('Y-m-d'));
       $path = dirname(__FILE__).'/../../../../../storage/logs/';
       $filename = $path.$name.$date.'.log';

       if(!file_exists($filename)){
           echo $name.$date.'.log'.'no exist';
       }
       else{
           $content = file_get_contents($filename);
           $content = str_replace(array("\r\n","\n"),'<br>',$content);
           $content .= '<br> log over';
           echo $content;
       }
       exit();
   }
    public function ShowLogTest(Request $request){

        header('Content-type:text/html;charset=utf-8');
        #设置执行时间不限时
        set_time_limit(0);
        #清除并关闭缓冲，输出到浏览器之前使用这个函数。
        ob_end_clean();
        #控制隐式缓冲泻出，默认off，打开时，对每个 print/echo 或者输出命令的结果都发送到浏览器。
        ob_implicit_flush(1);

        if(env('ShowlogFlag',false) == false)
        {
            dd('浏览器显示日志开关未打开');
        }
        $name =$request->input('name','laravel-');
        $date = $request->input('date',date('Y-m-d'));
        $path = dirname(__FILE__).'/../../../../../storage/logs/';
        $filename = $path.$name.$date.'.log';
        $tempfile = $path.$name.$date.'_tmp.log';
        $difffile = $path.'diff_'.$name.$date.'_tmp.log';

        if(!file_exists($filename)){
            echo $filename.'no exist';
        }
        else{
            echo str_repeat(" ",1024);
            $content = file_get_contents($filename);
            $content = str_replace(array("\r\n","\n"),'<br>',$content);
            echo $content;
            echo '<br> log over';

            /* $file = fopen($filename,"r");

             while(! feof($file))
             {
                 $content = fgets($file);
                 $content = str_replace("\r\n",'<br>',$content);
                 $content = str_replace("\n",'<br>',$content);
                 echo $content;
             }

             fclose($file);
          */

            while(true){
                system('diff -N '.$filename.' '.$tempfile. ' > '.$difffile);
                $content = file_get_contents($difffile);
                if($content !== false && strlen(trim($content,' ')) > 0){
                    echo str_replace('< ','',$content);
                    system('cp -rf '.$filename.' '.$tempfile);
                }
                sleep(2);
            }

        }
        exit();
    }
}