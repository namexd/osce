package com.mx.test.volley;

import java.util.List;
import java.util.Map;

import org.apache.http.NameValuePair;
import org.json.JSONObject;

import com.android.volley.Request.Method;
import com.android.volley.Response;
import com.android.volley.Response.ErrorListener;
import com.android.volley.VolleyError;

import android.content.Context;
import android.util.Log;

/**
 * Volley封装抽象类
 * 
 * 处理Json数据
 * 
 */
public abstract class NetworkHelper<T> implements Response.Listener<JSONObject>, ErrorListener {

	private Context context;

	public NetworkHelper(Context context) {
		this.context = context;
	}

	protected Context getContext() {
		return context;
	}

	/**
	 * 生成含参Get网络的请求
	 * 
	 * @param url
	 *            基本请求地址
	 * @param params
	 *            Get参数
	 * @return 含参Get网络的请求
	 */
	protected NetworkRequest getRequestForGet(String url, List<NameValuePair> params) {
		if (params == null) {
			return new NetworkRequest(url, this, this);
		} else {
			return new NetworkRequest(url, params, this, this);
		}

	}

	/**
	 * 生成含参Post网络的请求
	 * 
	 * @param url
	 *            基本请求地址
	 * @param params
	 *            Post的参数
	 * @return 含参Post网络的请求
	 */
	protected NetworkRequest getRequestForPost(String url, Map<String, String> params) {
		return new NetworkRequest(Method.POST, url, params, this, this);
	}

	/**
	 * 发送Get请求
	 * 
	 * @param url
	 *            发送Get请求的网址
	 * @param params
	 *            请求的参数
	 */
	public void sendGETRequest(String url, List<NameValuePair> params) {
		VolleyQueueController.getInstance(context).getRequestQueue().add(getRequestForGet(url, params));
	}

	/**
	 * 发送Post请求
	 * 
	 * @param url
	 *            Post请求网址
	 * @param params
	 *            请求参数
	 */
	public void sendPostRequest(String url, Map<String, String> params) {
		VolleyQueueController.getInstance(context).getRequestQueue().add(getRequestForPost(url, params));
	}

	@Override
	public void onErrorResponse(VolleyError error) {
		// Log.d("Amuro", error.getMessage());
		disposeVolleyError(error);
	}

	protected abstract void disposeVolleyError(VolleyError error);

	@Override
	public void onResponse(JSONObject response) {
		Log.d("Amuro", response.toString());
		disposeResponse(response);
	}

	/** 处理原始Json */
	protected abstract void disposeResponse(JSONObject response);

	/** 数据处理接口 */
	private UIDataListener<T> uiDataListener;

	public void setUiDataListener(UIDataListener<T> uiDataListener) {
		this.uiDataListener = uiDataListener;
	}

	protected void notifyDataChanged(T data) {
		if (uiDataListener != null) {
			uiDataListener.onDataChanged(data);
		}
	}

	protected void notifyErrorHappened(String errorCode, String errorMessage) {
		if (uiDataListener != null) {
			uiDataListener.onErrorHappened(errorCode, errorMessage);
		}
	}

}
