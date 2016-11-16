package com.mx.osce.subscriber;

import com.mx.osce.broadcast.SubscriberReciver;
import com.mx.osce.service.Program.OnCount;

import android.content.Context;
import android.content.Intent;
import android.util.Log;
import redis.clients.jedis.JedisPubSub;

/** 订阅者 */
public class Subscriber extends JedisPubSub {
	// public static Context mContext;
	//
	// public Subscriber(Context con) {
	// this.mContext = con;
	// }
	//
	// @Override
	// public void onMessage(String channel, String message) {
	// Intent intent = new Intent();
	// Log.e("Subscriber", "订阅接受的消息");
	// intent.setAction(SubscriberReciver.ACTION_SUBSCRIBE);
	// intent.putExtra("Message", message);
	// Log.e("Subscriber", "订阅接受的消息" + message);
	// mContext.sendBroadcast(intent);
	//
	// }
	//
	// @Override
	// public void onPMessage(String pattern, String channel, String message) {
	// }
	//
	// @Override
	// public void onSubscribe(String channel, int subscribedChannels) {
	// }
	//
	// @Override
	// public void onUnsubscribe(String channel, int subscribedChannels) {
	// }
	//
	// @Override
	// public void onPSubscribe(String arg0, int arg1) {
	//
	// }
	//
	// @Override
	// public void onPUnsubscribe(String arg0, int arg1) {
	// // TODO Auto-generated method stub
	//
	// }

	private Context mContext;

	private OnCount moncount;

	public Subscriber(Context mContext, OnCount oncount) {
		this.mContext = mContext;
		this.moncount = oncount;

	}

	@Override
	public void onMessage(String channel, String message) {
		moncount.onCount(1);
		Intent intent = new Intent(SubscriberReciver.ACTION_SUBSCRIBE); //
		intent.putExtra("netstates", "1");
		intent.putExtra("Message", message);
		intent.setAction(SubscriberReciver.ACTION_SUBSCRIBE); //
		mContext.sendBroadcast(intent);
	}

	@Override
	public void onPMessage(String pattern, String channel, String message) {

	}

	@Override
	public void onSubscribe(String channel, int subscribedChannels) {

	}

	@Override
	public void onUnsubscribe(String channel, int subscribedChannels) {
		System.out.println("取消订阅");
	}

	@Override
	public void onPUnsubscribe(String pattern, int subscribedChannels) {

	}

	@Override
	public void onPSubscribe(String pattern, int subscribedChannels) {

	}

}
