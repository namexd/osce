package com.mx.bluetooth.util;

import java.io.UnsupportedEncodingException;
import java.lang.reflect.Type;
import java.util.Map;

import com.android.volley.AuthFailureError;
import com.android.volley.NetworkResponse;
import com.android.volley.ParseError;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.Response.ErrorListener;
import com.android.volley.Response.Listener;
import com.android.volley.toolbox.HttpHeaderParser;
import com.google.gson.Gson;
import com.google.gson.JsonSyntaxException;
import com.mx.bluetooth.log.SaveNetRequestLog2Local;

/**
 * Created by storm on 14-3-25.
 */
public class GsonRequest<T> extends Request<T> {
	private final Gson mGson = new Gson();
	private final Class<T> mClazz;
	private final Listener<T> mListener;
	private final Map<String, String> mHeaders;
	private Type mType;
	private final Map<String, String> mMapParam;
	private String tag = "";
	//

	public GsonRequest(String url, Class<T> clazz, Listener<T> listener, ErrorListener errorListener) {
		this(Method.GET, url, clazz, null, null, listener, errorListener);
	}

	public GsonRequest(String url, Type type, Listener<T> listener, ErrorListener errorListener) {
		this(Method.GET, url, type, null, null, listener, errorListener);
	}

	public GsonRequest(int method, String url, Class<T> clazz, Map<String, String> headers, Map<String, String> params,
			Listener<T> listener, ErrorListener errorListener) {
		super(method, url, errorListener);
		this.mMapParam = params;
		this.mClazz = clazz;
		this.mHeaders = headers;
		this.mListener = listener;
	}

	public GsonRequest(int method, String url, Type type, Map<String, String> headers, Map<String, String> params,
			Listener<T> listener, ErrorListener errorListener) {
		super(method, url, errorListener);
		this.mMapParam = params;
		this.mType = type;
		this.mClazz = null;
		this.mHeaders = headers;
		this.mListener = listener;
		// TODO 记录网络请求
		SaveNetRequestLog2Local.SavNetLogLocal(url, params, null);
	}

	@Override
	public Map<String, String> getHeaders() throws AuthFailureError {
		return mHeaders != null ? mHeaders : super.getHeaders();
	}

	@Override
	protected void deliverResponse(T response) {
		mListener.onResponse(response);
	}

	@SuppressWarnings("unchecked")
	@Override
	protected Response<T> parseNetworkResponse(NetworkResponse response) {
		try {
			String json = new String(response.data, HttpHeaderParser.parseCharset(response.headers));
			//Out.out("request json String---" + json);
			// return (Response<T>) JSON.parseObject(json, mClazz);
			SaveNetRequestLog2Local.SavNetLogLocal(getUrl(), mMapParam, json);// 加入网络请求Log日志本地保存
			return mClazz != null
					? Response.success(mGson.fromJson(json, mClazz), HttpHeaderParser.parseCacheHeaders(response))
					: (Response<T>) Response.success(mGson.fromJson(json, mType),
							HttpHeaderParser.parseCacheHeaders(response));
			// return Response.success(mGson.fromJson(json, mClazz),
			// HttpHeaderParser.parseCacheHeaders(response));
		} catch (UnsupportedEncodingException e) {
			return Response.error(new ParseError(e));
		} catch (JsonSyntaxException e) {
			// TODO 保存返回的错误Json
			return Response.error(new ParseError(e));
		}
	}

	public void setTag(String tag) {
		this.tag = tag;
	}

	@Override
	protected Map<String, String> getParams() throws AuthFailureError {
		return mMapParam;
	}

}
