package com.mx.osce.util;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;


import android.content.Context;
import android.graphics.Bitmap;

public class HttpImg {
	/**
	 * 从网络下载图片，将流转换为bitmap类型
	 * 
	 * @param url
	 *            网络地址
	 * @param context
	 *            上下文
	 * @return
	 */
	public static Bitmap connectUrl(String url, Context context) {
		try {
			URL mUrl = new URL(url);
			// 开启联网通信
			HttpURLConnection connection = (HttpURLConnection) mUrl.openConnection();
			// 将联网得到的数据转化为流
			InputStream is = connection.getInputStream();
			// 将图片进行缩放，并设置图片的宽，高
			Bitmap bitmap = ImageUtil.decodeBitmap(readStream(is), (int) (75 * Utils.getDensity(context)));
			is.close();
			return bitmap;
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (Exception e) {
			e.printStackTrace();
		}

		return null;
	}

	public static InputStream connectImgUrl(String url) {
		try {
			URL mUrl = new URL(url);
			// 开启联网通信
			HttpURLConnection connection = (HttpURLConnection) mUrl.openConnection();
			// 将联网得到的逐句转化为流
			InputStream is = connection.getInputStream();

			return is;
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} catch (Exception e) {
			e.printStackTrace();
		}

		return null;
	}

	/*
	 * 得到图片字节流 数组大小
	 */
	public static byte[] readStream(InputStream inStream) throws Exception {
		ByteArrayOutputStream outStream = new ByteArrayOutputStream();
		byte[] buffer = new byte[1024];
		int len = 0;
		while ((len = inStream.read(buffer)) != -1) {
			outStream.write(buffer, 0, len);
		}
		outStream.close();
		inStream.close();
		return outStream.toByteArray();
	}
}
