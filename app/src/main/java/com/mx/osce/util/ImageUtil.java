package com.mx.osce.util;


import java.io.ByteArrayOutputStream;
import java.io.InputStream;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.BitmapFactory.Options;

public class ImageUtil {
	/**
	 * 将图片进行缩放
	 * 
	 * @param data
	 *            装bitmap的byte数组
	 * @param imgWidth
	 *            将要设置图片的大小
	 * @return
	 */
	public static Bitmap decodeBitmap(byte[] data, int imgWidth) {
		Options opts = new BitmapFactory.Options();
		// 将图片边框画出，但并没有图片
		opts.inJustDecodeBounds = true;
		// 将bitmap转化为byte数组
		BitmapFactory.decodeByteArray(data, 0, data.length, opts);
		// 设置图片要的大小
		opts.inSampleSize = 1;
		// 将图片边框画出，并且有图片
		opts.inJustDecodeBounds = false;
		// 将图片以byte数组形式返回出去
		return BitmapFactory.decodeByteArray(data, 0, data.length, opts);
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
