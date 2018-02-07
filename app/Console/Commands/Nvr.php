<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\TestResult;

class Nvr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nvr {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nvr video command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        sleep(5);
//        \Artisan::queue('migrate');
        $id = $this->argument('id');
        $model = TestResult::find($id);
        if ($model && $model->video_status == 0) {
            $nvr = config('nvr');
            if ($nvr && $nvr['enable']) {
                // 获取所属的摄像头信息
                $vcr = StationVcr::leftJoin('vcr','vcr.id', '=', 'station_vcr.vcr_id')
                    ->where('station_vcr.station_id', '=', $model->station_id)
                    ->select(['vcr.*'])
                    ->first();
                // 如果存在摄像头获取考试视频
                if ($vcr) {
                    $begin_dt = date('Y m d h i s', strtotime($model->begin_dt));
                    $end_dt = date('Y m d h i s', strtotime($model->end_dt));
                    $cmd = "{$nvr['dir']}{$nvr['exec_command']} {$nvr['host']} {$nvr['port']} {$nvr['username']} {$nvr['password']} {$begin_dt} {$end_dt}";
                    // 正在转换
//                    $model->update(['status' => 1]);
                    // 执行下载解码视频命令
                    exec($cmd, $o);
//                    rename('/data/www/osce_child/aaaa', public_path('videos/aaaa'));
//                    pclose(popen($cmd, 'r'));
                    $fileName = date('Y-m-d h-i-s', strtotime($model->begin_dt)) . "_" . date('Y-m-d h-i-s', strtotime($model->end_dt)) . '.mp4';
                    //var_dump($cmd, $nvr['dir'] . $fileName, public_path('videos/') . $fileName);exit;
                    // 转换完成
                    if (file_exists($nvr['dir'] . $fileName)) {
                        $path = public_path('videos\\') . 'nvr_video_' . $id . '.mp4';
                        rename($nvr['dir'] . $fileName, $path);
                        $model->update(['video' => $path, 'status' => 2]);
                        $this->info('Conversion success');
                    } else {
//                        echo 'file ' . $nvr['dir'] . $fileName;
                        $model->update(['status' => 0]);
                        $this->error('Conversion fail');
                    }
                } else {
                    $this->error('No vcr');
                }
            }
        }
    }
}
