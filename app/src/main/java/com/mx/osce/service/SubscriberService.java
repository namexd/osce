package com.mx.osce.service;

import java.util.Date;
import java.util.Observable;
import java.util.Observer;
import java.util.Timer;
import java.util.TimerTask;

import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.IBinder;
import android.os.PowerManager;
import android.os.PowerManager.WakeLock;
import android.util.Log;

public class SubscriberService extends Service {
	public static final String TAG = "SubscriberService";
	public static final String ACTION_RECIVE_MESSAGE = "com.mx.osce.service.SubscriberService";
	private ConnectionChangeReceiver myReceiver;
	private boolean isEnable = false;
	private Timer timer;// 定时重启任务
	private Listener listen;
	private ConnectivityManager connectivityManager;
	private NetworkInfo mobNetInfo, wifiNetInfo;
	private PowerManager powerManager = null;
	private WakeLock wakeLock = null;
	RThread rThread;
	Program program ;
	boolean closed=false;

	@Override
	public IBinder onBind(Intent intent) {
		Log.v(TAG, "MessageService onBind");
		return null;
	}

	@Override
	public void onCreate() {
		Log.v(TAG, "MessageService onCreate");
		this.powerManager = (PowerManager) this.getSystemService(Context.POWER_SERVICE);
		this.wakeLock = this.powerManager
				.newWakeLock(PowerManager.SCREEN_BRIGHT_WAKE_LOCK | PowerManager.ON_AFTER_RELEASE, TAG);
		this.wakeLock.acquire();
		registerReceiver();
		 program = new Program(SubscriberService.this);
		 rThread=new RThread();
		super.onCreate();

	}

	@SuppressWarnings("deprecation")
	@Override
	public void onStart(Intent intent, int startId) {
		Log.v(TAG, "MessageService onStart");
		super.onStart(intent, startId);
	}

	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		Log.v(TAG, "MessageService onStartCommand");
		flags = START_STICKY;
		return super.onStartCommand(intent, flags, startId);
	}

	public void onDestroy() {
		Log.v(TAG, "MessageService onDestroy");
		this.unregisterReceiver(myReceiver);
		this.wakeLock.release();
		closed=true;
		rThread.setClosed(closed);
		program.disconnectsub();
		super.onDestroy();
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
			Intent intent1 = new Intent();
			if (!wifiNetInfo.isConnected()) {
				// Itent就是我们要发送的内容
				isEnable = true;
				intent1.putExtra("Message", "");
				intent1.putExtra("netstates", "0");
				intent1.setAction(TAG); // 设置你这个广播的action，只有和这个action一样的接受者才能接受者才能接收广播

			} else {
				if (!isEnable) {
					RunThread runnaable = new RunThread();
					listen = new Listener();
					if(!closed){
					runnaable.addObserver(listen);
					rThread=new RThread();
					rThread.setClosed(closed);
					rThread.setrunnaable(runnaable);
					rThread.start();
					Log.v(TAG, "线程：" + rThread.getId() + rThread.getName());
					}
				}
				intent1.putExtra("Message", "");
				intent1.putExtra("netstates", "1");
				intent1.setAction(TAG);
			}
			SubscriberService.this.sendBroadcast(intent1);
		}
	}

	public class RunThread extends Observable implements Runnable {
		// 监听线程
		public void doRestart() {
			if (true) {
				super.setChanged();
			}
			notifyObservers();
		}

		@Override
		public void run() {
			Log.v(TAG, "线程：");
			program = new Program(SubscriberService.this);
			try {
				program.Subscriberchannel();
			} catch (Exception e) {
				// TODO Auto-generated catch block
				Log.v(TAG, "订阅失败" + e.toString());
				isEnable = false;
				doRestart();
				e.printStackTrace();
				return;
			}

		}

	}

	public class Listener implements Observer {
		@Override
		public void update(Observable o, Object arg) {

			if (timer == null) {
				timer = new Timer();
			}
			TimerTask timerTask = new TimerTask() {
				@Override
				public void run() {
					// TODO Auto-generated method stub
					if(!closed){
					System.out.println("RunThread异常");
					RunThread runnaable = new RunThread();
					runnaable.addObserver(listen);
					RThread e= new RThread();
					e.setClosed(closed);
					e.setrunnaable(runnaable);
					e.start();
					System.out.println("RunThread重启在" + new Date().toGMTString());
					Intent intent1 = new Intent();
					intent1.putExtra("Message", "");
					intent1.putExtra("netstates", "0");
					intent1.setAction(TAG);
					SubscriberService.this.sendBroadcast(intent1);
					}
				}
			};
			if (!isEnable) {
				timer.schedule(timerTask, 5000);
				isEnable = true;
			}

		}
	}
	public class RThread extends Thread{
		private boolean closed=true;
		private RunThread runnaable;

		public void setClosed(boolean closed){
		  this.closed =closed;
		}
		public void setrunnaable(RunThread runnaable){
			  this.runnaable =runnaable;
			}

		public void run(){

		if(!closed)
			runnaable.run();
		}
	}

}
