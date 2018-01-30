package com.mx.test.upload;

import java.io.File;
import java.util.HashMap;

import android.util.Log;

public class UploadUtils {
	/**
	 * 文件上传工具类
	 * 
	 * @param url
	 *            上传地址
	 * @param parameterName
	 *            上传文件的域头
	 * @param params
	 *            上传参数
	 * @param uploadFile
	 *            上传文件
	 * 
	 * @return 上传是否成功
	 */
	public static boolean uploadFile2(String url, String parameterName, HashMap<String, String> params,
			File uploadFile) {

		try {
			FormFile formfile = new FormFile(uploadFile.getName(), uploadFile, parameterName,
					"application/octet-stream");
			SocketHttpRequester socketHttpRequester = new SocketHttpRequester();
			socketHttpRequester.post(url, params, formfile);
			Log.i("***Upload***", "success");
			return true;
		} catch (Exception e) {
			Log.i("***Upload***", "defeat");
			e.printStackTrace();
			return false;
		}
	}

	/**
	 * 上传文件
	 * 
	 * @param url
	 *            上传地址
	 * @param parameterName
	 *            域头
	 * @param params
	 *            上传参数
	 * @param uploadFile
	 *            上传文件
	 */
	public static void doUpload2(final String url, final String parameterName, final HashMap<String, String> params,
			final File uploadFile) {
		new Thread() {
			@Override
			public void run() {
				// TODO Auto-generated method stub
				UploadUtils.uploadFile2(url, parameterName, params, uploadFile);
			}
		}.start();
	}
}
