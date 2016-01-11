<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use DB;

class UsersPm extends Model
{

    public  $incrementing	=	true;
    public  $timestamps	=	true;
    protected $connection	=	'sys_mis';
    protected $table 		= 	'users_pm';
    protected $primaryKey	=	'id';
    protected $guarded 		= 	[];
    protected $fillable 	=	['title','content','accept_user_id','send_user_id','pid','level','status',]  ;


    public function getList($accept,$sender=null,$module=null,$status=1,$pageSize=10,$pageIndex=0){

        $total=DB::connection($this->connection)
            ->table($this->table)
            ->where('accept_user_id','=',$accept)
            ->Where(function($query) use ($sender,$module,$status) {
                 $query ->orWhere('send_user_id','=',$sender)
                        ->orWhere('module','=',$module)
                        ->orWhere('status','=',$status);
            })
            ->count();

        if($total>0){

            return [
                'total' =>  $total,
                'data'  =>  DB::connection($this->connection)
                    ->table($this->table)
                    ->where('accept_user_id','=',$accept)
                    ->Where(function($query) use ($sender,$module,$status) {
                        $query->orWhere('send_user_id','=',$sender)
                            ->orWhere('module','=',$module)
                            ->orWhere('status','=',$status);
                    })
                    ->skip($pageIndex*$pageSize)
                    ->take($pageSize)
                    ->get()];
        }
        else{
            return [
                'total'=>0,
                'data'=>[]
            ];
        }
    }
}
