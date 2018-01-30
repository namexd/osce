package com.mx.osce.service;

import java.io.IOException;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.CoreConnectionPNames;
import org.apache.http.util.EntityUtils;

import android.app.Service;
import android.content.Intent;
import android.os.IBinder;
import android.util.Log;

public class PollingService extends Service {

	public static String POLLING_ACTION = "PollingAction";

	public static String MESSAGE_ACTION = "PollingMessage";

	private boolean isPolling;

	/** HeartbeatService的Tag */
	private static final String TAG = "PollingService";
	/** 网络连接超时：30秒 */
	private static final long OUTTIME = 5 * 1000;
	/** 心跳检测时间 */
	private static final long POLLING_TIME = 10 * 1000;
	/** 访问地址 */
	private static final String ADDRESS = "http://wx.good-doctor.cn/wifi/get_wifi_info.php";
	/** 最近发送心跳包的时间 */
	private long SendTime = 0;
	/** 读取消息线程 */
	private HreatbeatThread mReadThread;

	@Override
	public IBinder onBind(Intent intent) {
		return null;
	}

	public class HreatbeatThread extends Thread {

		@Override
		public void run() {
			while (isPolling) {
				try {
					startPolling();
					Thread.sleep(30 * 1000);
				} catch (InterruptedException e) {
					e.printStackTrace();
				}
			}
		}
	}

	@Override
	public void onCreate() {
		new HreatbeatThread().start();
		super.onCreate();
	}

	@Override
	public void onDestroy() {
		stopPolling();
		super.onDestroy();
	}

	public void startPolling() {
		isPolling = true;
		HttpGet get = new HttpGet(ADDRESS);

		try {

			HttpClient client = new DefaultHttpClient();
			// 请求超时30秒
			client.getParams().setParameter(CoreConnectionPNames.CONNECTION_TIMEOUT, POLLING_TIME);
			HttpResponse httpResponse = client.execute(get);
			// 网络连接成功
			if (httpResponse.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {

				String result = EntityUtils.toString(httpResponse.getEntity());

				Log.e("Heartbeat---result", result);
			}
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	public void stopPolling() {
		isPolling = false;
	}

}
