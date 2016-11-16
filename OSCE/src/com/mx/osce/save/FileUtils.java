package com.mx.osce.save;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;

import android.content.Context;
import android.util.Log;

public class FileUtils {

	/** 异常退出时，分数保存的文件名 */
	public static final String EXCEPTION_SCORE_FILE = "ExceptionStudentScoreData.txt";
	/** 异常退出时，操作保存的文件名 */
	public static final String EXCEPTION_OPERTE_FILE = "ExceptionPopupWindowData.txt";
	/** 异常退出时，上传图片保存 */
	public static final String EXCEPTION_UOLOAD_FILE = "ExceptionUploadData.txt";

	/**
	 * 到本地写文件
	 * 
	 * @param inputFileString
	 *            输入流的String內容
	 */

	public static void savaDataFromException(Context context, String fileName, String inputFileString) {
		Log.e("Check Save Data", inputFileString);
		if (inputFileString == null) {
			return;
		}
		FileOutputStream outStream = null;
		try {
			outStream = context.openFileOutput(fileName, Context.MODE_PRIVATE);
			outStream.write(inputFileString.getBytes("UTF-8"));
			outStream.close();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		} finally {
			if (outStream != null) {
				outStream = null;
			}

		}
	}

	/**
	 * 从本地读取文件
	 * 
	 * @param context
	 *            使用上下文
	 * @return 返回本地文件读取的String
	 */

	public static String recoveryDataToActivity(Context context, String fileName) {

		FileInputStream fileInoutStream;
		try {
			fileInoutStream = context.openFileInput(fileName);
			byte[] buffer = new byte[1024];
			int index = 0;
			StringBuffer sb = new StringBuffer("UTF-8");
			while ((index = fileInoutStream.read(buffer)) != -1) {
				sb.append(new String(buffer, 0, index));
			}
			fileInoutStream.close();
			Log.e("dashixiong", sb.toString());
			return sb.toString();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}

	/**
	 * 删除单个文件
	 * 
	 * @param filePath
	 *            被删除文件的文件名
	 * @return 文件删除成功返回true，否则返回false
	 */
	public static boolean deleteFile(String filePath) {
		File file = new File(filePath);
		if (file.isFile() && file.exists()) {
			return file.delete();
		}
		return false;
	}

	/**
	 * 删除文件夹以及目录下的文件
	 * 
	 * @param filePath
	 *            被删除目录的文件路径
	 * @return 目录删除成功返回true，否则返回false
	 */
	public static boolean deleteDirectory(String filePath) {
		boolean flag = false;
		// 如果filePath不以文件分隔符结尾，自动添加文件分隔符
		if (!filePath.endsWith(File.separator)) {
			filePath = filePath + File.separator;
		}
		File dirFile = new File(filePath);
		if (!dirFile.exists() || !dirFile.isDirectory()) {
			return false;
		}
		flag = true;
		File[] files = dirFile.listFiles();
		// 遍历删除文件夹下的所有文件(包括子目录)
		for (int i = 0; i < files.length; i++) {
			if (files[i].isFile()) {
				// 删除子文件
				flag = deleteFile(files[i].getAbsolutePath());
				if (!flag)
					break;
			} else {
				// 删除子目录
				flag = deleteDirectory(files[i].getAbsolutePath());
				if (!flag)
					break;
			}
		}
		if (!flag)
			return false;
		// 删除当前空目录
		return dirFile.delete();
	}

	/**
	 * 根据路径删除指定的目录或文件，无论存在与否
	 * 
	 * @param filePath
	 *            要删除的目录或文件
	 * @return 删除成功返回 true，否则返回 false。
	 */
	public static boolean DeleteFolder(String filePath) {
		File file = new File(filePath);
		if (!file.exists()) {
			return false;
		} else {
			if (file.isFile()) {
				// 为文件时调用删除文件方法
				return deleteFile(filePath);
			} else {
				// 为目录时调用删除目录方法
				return deleteDirectory(filePath);
			}
		}
	}

}
