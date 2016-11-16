package com.mx.osce.volley;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.ImageLoader;
import com.android.volley.toolbox.Volley;

import android.content.Context;
import android.text.TextUtils;

public class VolleyQueueController {

	// 创建一个TAG，方便调试或Log
	private static final String TAG = "VolleyController";

	// 创建一个全局的请求队列
	private RequestQueue reqQueue;

	// 创建一个static VolleyController对象，便于全局访问
	private static VolleyQueueController mInstance;

	// 使用上下文
	private Context mContext;

	private VolleyQueueController(Context context) {
		mContext = context;
	}

	/**
	 * 以下为需要我们自己封装的添加请求取消请求等方法
	 */

	// 用于返回一个VolleyController单例
	public static VolleyQueueController getInstance(Context context) {

		if (mInstance == null) {

			synchronized (VolleyQueueController.class) {

				if (mInstance == null) {

					mInstance = new VolleyQueueController(context);
				}
			}
		}
		return mInstance;
	}

	/**
	 * 用于返回全局RequestQueue对象，如果为空则创建它
	 * 
	 * @return
	 */
	public RequestQueue getRequestQueue() {
		if (reqQueue == null) {
			synchronized (VolleyQueueController.class) {
				if (reqQueue == null) {
					reqQueue = Volley.newRequestQueue(mContext);
				}
			}
		}
		return reqQueue;
	}

	/**
	 * 
	 * 
	 * 将Request对象添加进RequestQueue，由于Request有*StringRequest,JsonObjectResquest...
	 * 等多种类型，所以需要用到*泛型。同时可将*tag作为可选参数以便标示出每一个不同请求
	 * 
	 * @param req
	 *            网络请求
	 * @param tag
	 *            请求tag
	 */
	public <T> void addToRequestQueue(Request<T> req, String tag) {
		// 如果tag为空的话，就是用默认TAG
		req.setTag(TextUtils.isEmpty(tag) ? TAG : tag);

		getRequestQueue().add(req);
	}

	public <T> void addToRequestQueue(Request<T> req) {
		req.setTag(TAG);
		getRequestQueue().add(req);
	}

	/**
	 * 通过各Request对象的Tag属性取消请求
	 * 
	 * @param tag
	 *            请求Tag
	 */
	public void cancelPendingRequests(Object tag) {
		if (reqQueue != null) {
			reqQueue.cancelAll(tag);
		}
	}
}
