package com.mx.bluetooth.volley;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.List;
import java.util.Map;

import org.apache.http.NameValuePair;
import org.apache.http.client.utils.URLEncodedUtils;
import org.json.JSONObject;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.NetworkResponse;
import com.android.volley.ParseError;
import com.android.volley.Response;
import com.android.volley.Response.ErrorListener;
import com.android.volley.Response.Listener;
import com.android.volley.toolbox.HttpHeaderParser;
import com.android.volley.toolbox.JsonRequest;

/**
 * 二次封装Volley网络请求工具类
 * 
 */
public class NetworkRequest extends JsonRequest<JSONObject> {
	// 优先级
	private Priority mPriority = Priority.HIGH;

	/**
	 * 可选请求方式构造函数
	 * 
	 * @param method
	 *            请求方式
	 * @param url
	 *            请求网络地址
	 * @param postParams
	 *            Post请求的参数
	 * @param listener
	 *            JSONObject返回监听
	 * @param errorListener
	 *            错误监听
	 */
	public NetworkRequest(int method, String url, Map<String, String> postParams, Listener<JSONObject> listener,
			ErrorListener errorListener) {
		super(method, url, paramstoString(postParams), listener, errorListener);
		// 设置重连
		setRetryPolicy(new DefaultRetryPolicy(30000, 0, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
	}

	/**
	 * 含参Get构造
	 * 
	 * @param url
	 *            请求网络地址
	 * @param params
	 *            Get请求的参数
	 * @param listener
	 * @param errorListener
	 */
	public NetworkRequest(String url, List<NameValuePair> params, Listener<JSONObject> listener,
			ErrorListener errorListener) {
		this(Method.GET, urlBuilder(url, params), null, listener, errorListener);
	}

	/***
	 * 无参构Get造函数
	 * 
	 * @param url
	 *            网址
	 * @param listener
	 *            JSONObj
	 * @param errorListener
	 *            错误接口
	 */
	public NetworkRequest(String url, Listener<JSONObject> listener, ErrorListener errorListener) {
		this(Method.GET, url, null, listener, errorListener);
	}

	/**
	 * 解析参数
	 * 
	 * @param params
	 * @return
	 */
	private static String paramstoString(Map<String, String> params) {
		if (params != null && params.size() > 0) {
			String paramsEncoding = "UTF-8";
			StringBuilder encodedParams = new StringBuilder();
			try {
				for (Map.Entry<String, String> entry : params.entrySet()) {
					encodedParams.append(URLEncoder.encode(entry.getKey(), paramsEncoding));
					encodedParams.append('=');
					encodedParams.append(URLEncoder.encode(entry.getValue(), paramsEncoding));
					encodedParams.append('&');
				}
				return encodedParams.toString();
			} catch (UnsupportedEncodingException uee) {
				throw new RuntimeException("Encoding not supported: " + paramsEncoding, uee);
			}
		}
		return null;
	}

	/**
	 * 接受网络请求返回的JSONObject
	 * 
	 * @param params
	 *            发出的网络请求的
	 * @return 返回的JSONObject
	 */
	@Override
	protected Response<JSONObject> parseNetworkResponse(NetworkResponse response) {

		try {

			JSONObject jsonObject = new JSONObject(new String(response.data, "UTF-8"));

			return Response.success(jsonObject, HttpHeaderParser.parseCacheHeaders(response));

		} catch (Exception e) {

			return Response.error(new ParseError(e));

		}
	}

	/**
	 * 返回优先级
	 */
	@Override
	public Priority getPriority() {
		return mPriority;
	}

	/**
	 * 设置优先级
	 * 
	 * @param priority
	 */
	public void setPriority(Priority priority) {
		mPriority = priority;
	}

	/***
	 * 将含参数的网络请求与请求网络地址拼接起来
	 * 
	 * @param url
	 *            基本网址
	 * @param params
	 *            请求参数
	 * @return 网络请求的完整网址
	 */
	private static String urlBuilder(String url, List<NameValuePair> params) {
		return url + "?" + URLEncodedUtils.format(params, "UTF-8");
	}
}
