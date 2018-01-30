package com.mx.bluetooth.service;

import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Service;
import android.content.Intent;
import android.os.IBinder;
import android.util.Log;

public class TimeService extends Service {
	private static final String TAG = "TimeService";
	public static final String ACTION = "com.mx.bluetooth.service.TimeService";
	Thread th;
	int TIME;
	boolean flag = true;

	@Override
	public void onCreate() {
		Log.v(TAG, "Service onCreate");
		super.onCreate();
	}

	@Override
	public void onStart(Intent intent, int startId) {
		Log.v(TAG, "Service onStart");
		// if(intent!=null){
		TIME = intent.getIntExtra("TIME", 0);
		if (TIME == 0) {
			return;
		}
		// }else{
		// TIME=saveTIME;
		// }
		// Log.v(TAG, "TIME"+TIME);

		th = new Thread(new Runnable() {
			public void run() {
				while (flag) {
					TIME--;
					try {
						th.sleep(1000);
					} catch (InterruptedException e) {
						e.printStackTrace();
					}
					// Log.v(TAG, "TIME"+TIME);
					Intent intent = new Intent(); // Itent就是我们要发送的内容
					intent.putExtra("data", FormatData(TIME));
					intent.setAction(TAG); // 设置你这个广播的action，只有和这个action一样的接受者才能接受者才能接收广播
					sendBroadcast(intent);
					if (TIME == 0) {
						flag = false;
					}
					// saveTIME=TIME;
				}

			}
		});
		th.start();
	}

	@Override
	public void onDestroy() {
		Log.v(TAG, "Service onDestroy");
		flag = false;
		super.onDestroy();
	}

	public String FormatData(int time) {

		// 准备第一个模板，从字符串中提取出日期数字
		String pat1 = "HH:mm:ss";
		// 准备第二个模板，将提取后的日期数字变为指定的格式
		String pat2 = "mm:ss";
		SimpleDateFormat sdf1 = new SimpleDateFormat(pat1);
		SimpleDateFormat sdf2 = new SimpleDateFormat(pat2); // 实例化模板对象
		// 实例化模板对象
		Date d = null;

		if (time >= 3600) {
			int hour = time / 3600;
			int miun = (time - 3600 * hour) / 60;
			int s = (time - 3600 * hour) - miun * 60;
			String strDate = "" + hour + ":" + miun + ":" + s;
			try {
				d = sdf1.parse(strDate); // 将给定的字符串中的日期提取出来
			} catch (Exception e) { // 如果提供的字符串格式有错误，则进行异常处理
				e.printStackTrace(); // 打印异常信息
			}

			return sdf1.format(d);
		} else {
			int miun = time / 60;
			int s = time - miun * 60;
			String strDate = "" + miun + ":" + s;
			try {
				d = sdf2.parse(strDate); // 将给定的字符串中的日期提取出来
			} catch (Exception e) { // 如果提供的字符串格式有错误，则进行异常处理
				e.printStackTrace(); // 打印异常信息
			}
			return sdf2.format(d);
		}

	}

	@Override
	public IBinder onBind(Intent intent) {
		// TODO Auto-generated method stub
		return null;
	}

}
