package com.mx.osce.net;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONException;
import org.json.JSONObject;

import com.mx.osce.util.Constant;

import android.util.Log;

public class NetRequest {
	/***
	 * 网络请求授权
	 * 
	 * @param userName
	 *            用户名
	 * @param userPassword
	 *            用户密码
	 * @return 授权码
	 */
	public static String getAccessToken(String userName, String userPassword) {
		HttpPost post = new HttpPost(Constant.BasciUrl+Constant.LOGIN);
		HttpClient client = new DefaultHttpClient();
		// 请求参数
		List<NameValuePair> params = new ArrayList<NameValuePair>();
		params.add(new BasicNameValuePair("username", userName));
		params.add(new BasicNameValuePair("password", userPassword));
		params.add(new BasicNameValuePair("grant_type", "password"));
		params.add(new BasicNameValuePair("client_id", "ios"));
		params.add(new BasicNameValuePair("client_secret", "111"));
		// 设置编码
		try {
			post.setEntity(new UrlEncodedFormEntity(params, HTTP.UTF_8));
			HttpResponse httpResponse = client.execute(post);
			int responseCode = httpResponse.getStatusLine().getStatusCode();
			Log.i("getAccessToken---responseCode", responseCode + "");
			String jsonStr = EntityUtils.toString(httpResponse.getEntity());
			if (responseCode == HttpStatus.SC_OK) {
				Log.i("Json", jsonStr);
				JSONObject root = new JSONObject(jsonStr);
				String access_token = (String) root.get("jsonStr");
				Log.i("access_token", access_token);
				return jsonStr;
			} else {
				Log.i("错误信息", jsonStr);
			}
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		} finally {
			// 关掉client
			client.getConnectionManager().shutdown();
		}
		return null;
	}

}
