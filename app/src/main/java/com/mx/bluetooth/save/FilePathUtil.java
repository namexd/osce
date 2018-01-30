package com.mx.bluetooth.save;

import java.io.File;
import java.io.IOException;

/**
 * 获取抓拍、录像路径类
 *
 * @author misrobot
 * @Data 2016-04-10
 * @since V1.0
 */
public class FilePathUtil {

	/**
	 * 获取图片目录
	 *
	 * @return Pictrue dir path.
	 * @since V1.0
	 */
	public static String getPictureDirPath() {
		File SDFile = null;
		File Folder = null;
		try {
			SDFile = android.os.Environment.getExternalStorageDirectory();
			String path = SDFile.getAbsolutePath() + File.separator + "com.mx.bluetooth";
			Folder = new File(path);
			if ((null != Folder) && (!Folder.exists())) {
				Folder.mkdir();
				Folder.createNewFile();
			}
			String PicturePath = path + File.separator + "pictures";
			Folder = new File(PicturePath);
			if (null != Folder && !Folder.exists()) {
				Folder.mkdir();
				Folder.createNewFile();
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return Folder.getPath() + File.separator;
	}

	/**
	 * 获取录像目录
	 *
	 * @return Video dir path.
	 * @since V1.0
	 */
	public static String getVideoDirPath() {
		File SDFile = null;
		File Folder = null;
		try {
			SDFile = android.os.Environment.getExternalStorageDirectory();
			String path = SDFile.getAbsolutePath() + File.separator + "com.mx.bluetooth";
			Folder = new File(path);
			if ((null != Folder) && (!Folder.exists())) {
				Folder.mkdir();
				Folder.createNewFile();
			}
			String videoPath = path + File.separator + "video";
			Folder = new File(videoPath);
			if (null != Folder && !Folder.exists()) {
				Folder.mkdir();
				Folder.createNewFile();
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		return Folder.getPath() + File.separator;
	}

	/**
	 * 获取日志目录
	 *
	 * @return Video dir path.
	 * @since V1.0
	 */
	public static String getLogDirPath() {
		File SDFile = null;
		File Folder = null;
		try {
			SDFile = android.os.Environment.getExternalStorageDirectory();
			String path = SDFile.getAbsolutePath() + File.separator + "com.mx.bluetooth";
			Folder = new File(path);
			if ((null != Folder) && (!Folder.exists())) {
				Folder.mkdir();
				Folder.createNewFile();
			}
			String logPath = path + File.separator + "log";
			Folder = new File(logPath);
			if (null != Folder && !Folder.exists()) {
				Folder.mkdir();
				Folder.createNewFile();
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		return Folder.getPath() + File.separator;
	}

	/**
	 * 获取记录目录
	 *
	 * @return Video dir path.
	 * @since V1.0
	 */
	public static String getRecordDirPath() {
		File SDFile = null;
		File Folder = null;
		try {
			SDFile = android.os.Environment.getExternalStorageDirectory();
			String path = SDFile.getAbsolutePath() + File.separator + "com.mx.bluetooth";
			Folder = new File(path);
			if ((null != Folder) && (!Folder.exists())) {
				Folder.mkdir();
				Folder.createNewFile();
			}
			String logPath = path + File.separator + "record";
			Folder = new File(logPath);
			if (null != Folder && !Folder.exists()) {
				Folder.mkdir();
				Folder.createNewFile();
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
		return Folder.getPath() + File.separator;
	}

}
