package com.mx.osce.camera;

import java.io.BufferedOutputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;

import android.content.Context;
import android.graphics.Bitmap;
import android.os.Environment;
import android.util.Log;
import android.widget.Toast;

public class CameraUtils {
	/**
	 * 将相机图片保存到本地
	 * 
	 * @param context
	 * @param bitmap
	 * 
	 * 
	 */
	public static void SavePicInLocal(Context context, Bitmap bitmap) {

		FileOutputStream fileOut = null;
		BufferedOutputStream bufferOut = null;
		ByteArrayOutputStream ByteArrayOut = null; // 字节数组输出流
		try {
			ByteArrayOut = new ByteArrayOutputStream();
			bitmap.compress(Bitmap.CompressFormat.PNG, 100, ByteArrayOut);
			byte[] byteArray = ByteArrayOut.toByteArray();// 字节数组输出流转换成字节数组
			String saveDir = Environment.getExternalStorageDirectory().toString() + File.separator
					+ context.getPackageName() + File.separator + "Camera/";
			File dir = new File(saveDir);
			if (!dir.exists()) {
				dir.mkdir(); // 创建文件夹
			}
			String fileName = saveDir + "/" + System.currentTimeMillis() + ".PNG";
			File file = new File(fileName);
			file.delete();
			if (!file.exists()) {
				file.createNewFile();// 创建文件
				Log.e("PicDir", file.getPath());
				Toast.makeText(context, fileName, Toast.LENGTH_LONG).show();
			}
			// 将字节数组写入到刚创建的图片文件中
			fileOut = new FileOutputStream(file);
			bufferOut = new BufferedOutputStream(fileOut);
			bufferOut.write(byteArray);

		} catch (Exception e) {
			e.printStackTrace();

		} finally {
			if (ByteArrayOut != null) {
				try {
					ByteArrayOut.close();
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
			if (bufferOut != null) {
				try {
					bufferOut.close();
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
			if (fileOut != null) {
				try {
					fileOut.close();
				} catch (Exception e) {
					e.printStackTrace();
				}
			}

		}

	}
}
