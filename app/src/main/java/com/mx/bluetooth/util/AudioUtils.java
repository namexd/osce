package com.mx.bluetooth.util;

import java.io.File;

import android.content.Context;
import android.os.Environment;
import android.widget.Toast;

public class AudioUtils {
	/**
	 * 得到录音文件保存的地址
	 * 
	 * @param context
	 *            使用上下文
	 * @param audioSimpleName
	 *            录音文件的simpleName(考站id+创建录音文件的时间)
	 * @return 录音文件的绝对路径
	 */
	public String getAudioPath(Context context, String audioSimpleName) {
		String audioDir = Environment.getExternalStorageDirectory().toString() + File.separator
				+ context.getPackageName() + File.separator + "AudioRecord/";
		File file = new File(audioDir);
		if (!file.exists()) {
			file.mkdirs();
		}
		String audioPath = audioDir + audioSimpleName;
		return audioPath;
	}

	/**
	 * 检查SD卡是否正常使用
	 */
	public boolean checkSdCard(Context context) {
		if (android.os.Environment.getExternalStorageState().equals(android.os.Environment.MEDIA_MOUNTED)) {
			return true;
		} else {
			Toast.makeText(context, "SD卡状态异常", Toast.LENGTH_SHORT).show();
			return false;
		}
	}
}
