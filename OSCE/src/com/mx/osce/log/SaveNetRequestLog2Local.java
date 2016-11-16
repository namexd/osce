package com.mx.osce.log;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.Iterator;
import java.util.Map;

import com.mx.osce.BaseActivity;
import com.mx.osce.util.Utils;

public class SaveNetRequestLog2Local {

	public static final String ROOT_DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
			+ "NetRequestLog/";

	public static void SavNetLogLocal(String url, Map<String, String> params, String content) {
		// 一个登陆用户一个文件
		String subSaveDir = ROOT_DIR + Utils.getSharedPrefrences(BaseActivity.mScontext, "user_phone") + File.separator;
		File subSaveDirFile = new File(subSaveDir);
		if (!subSaveDirFile.exists()) {
			subSaveDirFile.mkdirs();
		}
		// 网络请求日志文件以时间命名
		String requestDetailFile = new String(subSaveDirFile + File.separator
				+ Utils.long2SimpleData("yyyy年MM月dd日    HH:mm:ss", System.currentTimeMillis()) + ".txt");
		StringBuffer sb = null;
		sb = new StringBuffer();
		BufferedWriter bufferWriter = null;
		try {
			sb = new StringBuffer();
			// 写入地址
			sb.append("\n");
			sb.append(">>>Request Url<<<" + "\n" + url + "\n");
			// 写入Post参数
			if (params != null) {
				String key = null;
				sb.append(">>>Post Params<<<" + "\n");
				// 取得Post请求中参数params集合的键的iterator对象
				Iterator<String> paramsIterator = params.keySet().iterator();
				while (paramsIterator.hasNext()) {
					key = paramsIterator.next();// HashMap中的每个键
					sb.append(key + "=" + params.get(key) + "\n");
					key = null;
				}
			}
			if (content != null) {
				sb.append(">>>Recive<<<" + "\n" + content + "\n");
			}

			bufferWriter = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(requestDetailFile, true)));
			bufferWriter.write(sb.toString());
			sb = null;
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			try {
				if (bufferWriter != null) {
					bufferWriter.close();
				}
				if (sb != null) {
					sb = null;
				}
			} catch (IOException e) {
				e.printStackTrace();
			}
		}

	}

}
