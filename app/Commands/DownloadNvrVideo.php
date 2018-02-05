<?php

namespace App\Commands;

//use App\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\TestResult;

class DownloadNvrVideo implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $result_id = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($result_id)
    {
        //
        $this->result_id = $result_id;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('download nvr video!', ['time' => time(), 'result' => $this->result_id]);
        //
        $id = $this->result_id;
        $model = TestResult::find($id);
        try {
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
                        $begin_dt = date('Y m d H i s', strtotime($model->begin_dt));
                        $end_dt = date('Y m d H i s', strtotime($model->end_dt));
                        $cmd = "{$nvr['dir']}{$nvr['exec_command']} {$nvr['host']} {$nvr['port']} {$nvr['username']} {$nvr['password']} {$begin_dt} {$end_dt}";
                        // 正在转换
                        $model->update(['video_status' => 1]);
                        // 执行下载解码视频命令
                        exec($cmd, $o);
//                    rename('/data/www/osce_child/aaaa', public_path('videos/aaaa'));
//                    pclose(popen($cmd, 'r'));
                        $fileName = date('Y-m-d H-i-s', strtotime($model->begin_dt)) . "_" . date('Y-m-d H-i-s', strtotime($model->end_dt)) . '.mp4';
                        //var_dump($cmd, $nvr['dir'] . $fileName, public_path('videos/') . $fileName);exit;
                        // 转换完成
                        \Log::info('cmd', ['cmd' => $cmd, $nvr['dir'] . $fileName]);
                        if (file_exists($nvr['dir'] . $fileName)) {
                            $path = public_path('videos\\') . 'nvr_video_' . $id . '.mp4';
                            rename($nvr['dir'] . $fileName, $path);
                            $model->update(['video_path' => $path, 'video_status' => 2]);
                            \Log::info('Conversion success');
                        } else {
                            $model->update(['video_status' => 3]);
                            \Log::error('Conversion fail');
                        }
                    } else {
                        \Log::error('No vcr');
                    }
                } else {
                    \Log::error('Nvr config error');
                }
            } else {
                \Log::error('No result id or is the result is finish');
            }
        } catch (\Exception $e) {
            \Log::error('Have error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
