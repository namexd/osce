package com.mx.osce.net;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import com.google.gson.Gson;

import android.util.Log;

public class GetJsonString {
	public static String execute(String urlStr) {
		InputStream inputStream = null;
		HttpURLConnection urlConnection = null;

		try {
			// read responseURLEncoder.encode(para, "GBK");
			// String urlWithParams = DOMAIN_ADDRESS + MEMBER_REQUEST_TOKEN_URL
			// + "?userName=" + java.net.URLEncoder.encode(params[0],"utf-8") +
			// "&password=" + params[1];
			URL url = new URL(urlStr);
			urlConnection = (HttpURLConnection) url.openConnection();

			/* optional request header */
			urlConnection.setRequestProperty("Content-Type", "application/json; charset=UTF-8");

			/* optional request header */
			urlConnection.setRequestProperty("Accept", "application/json");

			/* for Get request */
			urlConnection.setRequestMethod("GET");
			int statusCode = urlConnection.getResponseCode();

			/* 200 represents HTTP OK */
			if (statusCode == 200) {
				inputStream = new BufferedInputStream(urlConnection.getInputStream());
				String response = HttpUtils.convertInputStreamToString(inputStream);
				// Gson gson = new Gson();
				// UserDto dto = gson.fromJson(response, UserDto.class);
				// if (dto != null && dto.getToken() != null) {
				// Log.i("token", "find the token = " + dto.getToken());
				// }
				return response;
			}

		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			if (inputStream != null) {
				try {
					inputStream.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
			if (urlConnection != null) {
				urlConnection.disconnect();
			}
		}
		return null;
	}
}
