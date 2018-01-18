package com.mx.osce.service;

import java.util.Timer;

import com.mx.osce.BaseActivity;
import com.mx.osce.subscriber.Subscriber;
import com.mx.osce.util.Utils;

import android.content.Context;
import android.content.Intent;
import android.util.Log;
import redis.clients.jedis.Jedis;
import redis.clients.jedis.JedisPool;
import redis.clients.jedis.JedisPoolConfig;

public class Program {
	static Context mContext;
	Timer timer;
	int timecount;
	Jedis subscriberJedis, PingJedis;
	boolean isalive = true;

	private static final String TAG = "Program";

	// public static final String mSHost = "192.168.11.254";

	// public static final String mSHost = BaseActivity.mEasyUrl;

	public static final String mSHost = "gitlab.atapp.com";

	public static final int mSPort = 60379;

	public static final String mSPass = "4f38ad";

	public static final String CHANNEL_NAME = "pad_message";

	public Program(Context mContext) {
		this.mContext = mContext;
	}

	public void Subscriberchannel() throws Exception {

		JedisPoolConfig poolConfig = new JedisPoolConfig();
		JedisPool jedisPool = new JedisPool(poolConfig, mSHost, mSPort, 0, mSPass);
		subscriberJedis = jedisPool.getResource();
		PingJedis = jedisPool.getResource();
		OnCount onCount = new OnCount() {

			@Override
			public void onCount(int count) {
				timeCount(count);// 返回count=1，表示收到消息，使timecount置0；
			}

			@Override
			public void isValid(long pingTimestamp) {
				//
				if (System.currentTimeMillis() - pingTimestamp > 15000) {
					isalive = false;
				} else {
					isalive = true;
				}
			}
		};

		final Subscriber subscriber = new Subscriber(mContext, onCount);
		try {
			Log.v(TAG, "开始订阅");
			timeCount(0);// 启动时间监听，监听消息恢复服务，

			Log.e(">>>MD5<<<", BaseActivity.mEasyUrl + "\n" + Utils.MD5(BaseActivity.mEasyUrl) + CHANNEL_NAME);
			subscriberJedis.subscribe(subscriber, Utils.MD5(BaseActivity.mEasyUrl) + CHANNEL_NAME);
		} catch (Exception e) {
			jedisPool.returnBrokenResource(subscriberJedis);
			timer.cancel();
			Log.v(TAG, "订阅异常" + e.toString());
			throw new Exception(e.toString());
		}
		return;

	}

	public interface OnCount {
		public void onCount(int count);
		public void isValid(long pingTimestamp);
	}

	public void timeCount(int type) {

		Intent intent1 = new Intent();
		intent1.putExtra("Message", "");
		intent1.putExtra("netstates", "1");
		intent1.setAction(TAG);
		mContext.sendBroadcast(intent1);
		timecount = 0;
		if (type == 1) {
			return;
		}
		if (timer == null) {
			timer = new Timer();
		} else {
			return;
		}
		// TimerTask timerTask = new TimerTask() {
		// @Override
		// public void run() {
		//
		// timecount++;
		// Log.v(TAG, "timecount" + timecount);
		// if (timecount >= 30) {
		// timer.cancel();
		// subscriberJedis.disconnect();
		// }
		// }
		// };
		// timer.schedule(timerTask, 0, 10000);

		startThread();

		Log.v(TAG, "回调");
	}

	public void disconnectsub() {
		isalive=false;
		if (timer != null)
			timer.cancel();
		if (subscriberJedis != null)
			subscriberJedis.disconnect();
		
	}

	public void startThread() {
		JedisMonitor jedisMonitor = new JedisMonitor();
		jedisMonitor.start();
	}

	class JedisMonitor extends Thread {
		@Override
		public void run() {
			//
			try {
				Jedis jedis = new Jedis(mSHost, mSPort);
				jedis.auth(mSPass);
				while (isalive) {
					Log.v("Program", "发送pong");
					jedis.publish(Utils.MD5(BaseActivity.mEasyUrl) + CHANNEL_NAME, "PONG");
					sleep(10000);
				}
				disconnectsub();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}

}
