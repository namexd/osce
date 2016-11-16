package com.mx.osce.service;

import java.security.MessageDigest;
import java.util.Timer;
import java.util.TimerTask;

import com.lidroid.xutils.cache.MD5FileNameGenerator;
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
	Jedis subscriberJedis;

	public Program(Context mContext) {
		this.mContext = mContext;
	}

	private static final String TAG = "Program";
	public static final String CHANNEL_NAME = "pad_message";

	public void Subscriberchannel() throws Exception {

		JedisPoolConfig poolConfig = new JedisPoolConfig();
		JedisPool jedisPool = new JedisPool(poolConfig, "cloud.misrobot.com", 6379, 0, "gogoMisrobot123");
		subscriberJedis = jedisPool.getResource();
		OnCount onCount = new OnCount() {

			@Override
			public void onCount(int count) {
				timeCount(count);// 返回count=1，表示收到消息，使timecount置0；
			}
		};

		final Subscriber subscriber = new Subscriber(mContext, onCount);
		try {
			Log.v(TAG, "开始订阅");
			timeCount(0);// 启动时间监听，监听消息恢复服务，
			
			Log.e(">>>MD5<<<", BaseActivity.mEasyUrl+"\n"+Utils.MD5(BaseActivity.mEasyUrl)+CHANNEL_NAME);
			
			subscriberJedis.subscribe(subscriber, Utils.MD5(BaseActivity.mEasyUrl)+CHANNEL_NAME);
		} catch (Exception e) {
			timer.cancel();
			Log.v(TAG, "订阅异常" + e.toString());
			throw new Exception(e.toString());
		}
		return;

	}

	public interface OnCount {
		public void onCount(int count);
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
		TimerTask timerTask = new TimerTask() {
			@Override
			public void run() {
				// TODO Auto-generated method stub
				timecount++;
				Log.v(TAG, "timecount" + timecount);
				if (timecount >= 35) {
					timer.cancel();
					subscriberJedis.disconnect();
				}
			}
		};
		timer.schedule(timerTask, 0, 10000);
		Log.v(TAG, "回调");
	}
	public void disconnectsub(){
		timer.cancel();
		subscriberJedis.disconnect();
	}

}
