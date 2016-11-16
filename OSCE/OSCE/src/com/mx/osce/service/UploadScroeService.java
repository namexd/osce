package com.mx.osce.service;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.android.volley.Request.Method;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.Response.Listener;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.Volley;
import com.mx.osce.BaseActivity;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.UploadScoreBean;
import com.mx.osce.db.DBManager;
import com.mx.osce.log.SaveNetRequestLog2Local;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;

import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Binder;
import android.os.IBinder;
import android.util.Log;

public class UploadScroeService extends Service {
	public String TAG="UploadScroeService";
	private ConnectivityManager connectivityManager;
	private ConnectionChangeReceiver myReceiver;
	private NetworkInfo  wifiNetInfo;
	private boolean wifiConnect=true;
	/**
	 * 进度条的最大值
	 */
	public int MAX_PROGRESS = 100;
	/**
	 * 进度条的进度值
	 */
	private int progress = 0;
	static DBManager mgr;//add static
	
	/**
	 * 更新进度的回调接口
	 */
	private OnProgressListener onProgressListener;
	private List<UploadScoreBean> ScoreList=new ArrayList<UploadScoreBean>();
	
	
	/**
	 * 注册回调接口的方法，供外部调用
	 * @param onProgressListener2
	 */
	public void setOnProgressListener(OnProgressListener onProgressListener2) {
		this.onProgressListener = onProgressListener2;
	}

	/**
	 * 增加get()方法，供Activity调用
	 * @return 下载进度
	 */
	public int getProgress() {
		return progress;
	}

	/**
	 * 上传任务
	 */
	public void startUploadLoad(){	
		new Thread(new Runnable() {		
			@Override
			public void run() {
				 synchronized(ScoreList){
				if (ScoreList.size()>0) {
					for(UploadScoreBean mUploadScoreBean:ScoreList){
						mUploadScoreBean.setStates("2");
						mgr.updateStates(mUploadScoreBean);
						Log.v("UploadScroeService", "上传中。。。");
						try {
							Thread.sleep(5000);
						} catch (InterruptedException e) {
							// TODO Auto-generated catch block
							e.printStackTrace();
						}
						uploadScoreRequest(mUploadScoreBean);
						Log.v("UploadScroeService", "上传结束。。。");
//						mUploadScoreBean.setStates("1");
//						mgr.updateStates(mUploadScoreBean);
					}	
				}
				ScoreList.clear();
				 }	
			}
		}).start();
	}
	/** 上传本地成绩 */
	private void uploadScoreRequest(final UploadScoreBean UploadScore) {
		Map<String, String> params = new HashMap<String, String>();
//		String str = SaveNetRequestLog2Local.getScoreFromLocal(ReTransmitNames);
//		JSONObject json;
		if (UploadScore==null) 
			return;
		RequestQueue requestQueue = Volley.newRequestQueue(getApplicationContext());
//			json = new JSONObject(str);
			params.put("student_id", UploadScore.getStudent_id()+"");
			params.put("station_id", UploadScore.getStation_id()+"");
			params.put("teacher_id", UploadScore.getTeacher_id()+"");
			params.put("exam_screening_id", UploadScore.getExam_screening_id()+"");
			params.put("begin_dt", UploadScore.getBegin_dt()+"");
			params.put("end_dt", UploadScore.getEnd_dt()+"");
			params.put("operation", UploadScore.getOperation()+"");
			params.put("skilled", UploadScore.getSkilled()+"");
			params.put("patient",UploadScore.getPatient()+"");
			params.put("affinity", UploadScore.getAffinity()+"");
			params.put("evaluate",UploadScore.getEvaluate()+"");
			params.put("upload_image_return", UploadScore.getUpload_image_return()+"");
			params.put("score", UploadScore.getScore()+"");
		
		try {
			GsonRequest<BaseInfo> uploadResult = new GsonRequest<BaseInfo>(Method.POST,
					UploadScore.getUrl(), BaseInfo.class, null, params, new Listener<BaseInfo>() {

						@Override
						public void onResponse(BaseInfo arg0) {

							if (arg0.getCode() == 1) {
//								Toast.makeText(mContext, "本地成绩上传成功", Toast.LENGTH_SHORT).show();
//								ReTransmitUtil.removeReTransmitNames(mContext, ReTransmitNames);
								UploadScore.setStates("1");
								mgr.updateStates(UploadScore);
								if(SaveNetRequestLog2Local.CopeScore2Success(UploadScore.getStudent_id()+"_"+UploadScore.getStation_id()+"_"+UploadScore.getTeacher_id())){
									SaveNetRequestLog2Local.delLoaclFile(UploadScore.getStudent_id()+"_"+UploadScore.getStation_id()+"_"+UploadScore.getTeacher_id());	
								};
							} else {
//								Toast.makeText(mContext, "本地成绩上传失败", Toast.LENGTH_SHORT).show();
								Log.i("request return String", arg0.toString());
								Log.i(TAG, arg0.getCode() + "");
								Log.e(TAG, arg0.getMessage());
								UploadScore.setStates("0");
								mgr.updateStates(UploadScore);
							}
						}
					}, errorListener(UploadScore));
			requestQueue.add(uploadResult);			
		} catch (Exception e) {
//			Toast.makeText(mContext, "上传本地成绩返回数据有误！", Toast.LENGTH_SHORT).show();
			UploadScore.setStates("0");
			mgr.updateStates(UploadScore);
		}
	}

	private Response.ErrorListener errorListener(final UploadScoreBean UploadScore) {
		return new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
//				Toast.makeText(mContext, "本地成绩上传失败", Toast.LENGTH_SHORT).show();
				UploadScore.setStates("0");
				mgr.updateStates(UploadScore);
				error.printStackTrace();
			}
		};
	}
	public void UpdateList(){
		new Thread(new Runnable() {
			@Override
			public void run() {
				// TODO Auto-generated method stub
				if (wifiConnect) {
					 synchronized(ScoreList){
						 ArrayList<UploadScoreBean> Scores = new ArrayList<UploadScoreBean>();  
						 Scores=mgr.query("0");
					 for(UploadScoreBean Score:Scores){
						 ScoreList.add(Score);
						 Log.v("UploadScroeService", "添加数据。。。");
						 Score.setStates("3");//位于上传队列
						 mgr.updateStates(Score);
					 }					 
				}
					 startUploadLoad();
				}	 	
		}
		}).start();
		 	 
	}


	/**
	 * 返回一个Binder对象
	 */
	@Override
	public IBinder onBind(Intent intent) {
		
		Log.v("UploadScroeService", "UploadScroeService onBind");
		return new MsgBinder();
	}
	
	public class MsgBinder extends Binder{
		/**
		 * 获取当前Service的实例
		 * @return
		 */
		public UploadScroeService getService(){
			return UploadScroeService.this;
		}
	}
	public interface OnProgressListener {  
	    void onProgress(int progress);  
	}
	public void onCreate() {
		super.onCreate();
		registerReceiver();
		Log.v("UploadScroeService", "UploadScroeService onCreate");
		
	}
	public void resetDB(){
		if (mgr==null) {
			 mgr = new DBManager(this);
		}
		 ArrayList<UploadScoreBean> Scores = new ArrayList<UploadScoreBean>(); 
		 Scores=mgr.query("2");
		 for(UploadScoreBean d:Scores){
			 d.setStates("0");
			 mgr.updateStates(d);
		 }
		 Scores.clear();
		 Scores=mgr.query("3");
		 for(UploadScoreBean d:Scores){
			 d.setStates("0");
			 mgr.updateStates(d);
		 }
	}
	private void registerReceiver() {
		IntentFilter filter = new IntentFilter(ConnectivityManager.CONNECTIVITY_ACTION);
		myReceiver = new ConnectionChangeReceiver();
		this.registerReceiver(myReceiver, filter);
	}

	public class ConnectionChangeReceiver extends BroadcastReceiver {
		@Override
		public void onReceive(Context context, Intent intent) {
			connectivityManager = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
			wifiNetInfo = connectivityManager.getNetworkInfo(ConnectivityManager.TYPE_WIFI);
			if (wifiNetInfo.isConnected()) {
				resetDB();
				UpdateList();
				wifiConnect=true;
			} else{
				wifiConnect=false;
			}
			
		}
	}
	@SuppressWarnings("deprecation")
	@Override
	public void onStart(Intent intent, int startId) {
		Log.v("UploadScroeService", "UploadScroeService onStart");

		super.onStart(intent, startId);
	}

	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		Log.v("UploadScroeService", "UploadScroeService onStartCommand");
		
		return super.onStartCommand(intent, flags, startId);
	}

	public void onDestroy() {
		Log.v("UploadScroeService", "UploadScroeService onDestroy");
		unregisterReceiver(myReceiver);
		mgr.closeDB();
		super.onDestroy();
	}
	@Override
	public boolean onUnbind(Intent intent) {
		Log.v("UploadScroeService", "UploadScroeService onUnbind");
		super.onUnbind(intent);
		return true;
	}

}