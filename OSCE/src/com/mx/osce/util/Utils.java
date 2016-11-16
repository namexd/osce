package com.mx.osce.util;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.security.MessageDigest;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

import com.acs.audiojack.Result;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.Response.ErrorListener;
import com.android.volley.VolleyError;
import com.mx.osce.bean.LoginResultBean;

import android.app.Activity;
import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.MediaPlayer;
import android.media.RingtoneManager;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Build;
import android.os.Process;
import android.os.Vibrator;
import android.util.DisplayMetrics;
import android.util.Log;
import android.widget.Toast;

public class Utils {

	public static String result;

	private static long firstTime;

	private static HashMap<String, String> mSmap;

	/**
	 * 的到手机屏幕X轴的像素
	 * 
	 * @param context
	 * @return
	 */
	public static int getDensityWidth(Context context) {
		DisplayMetrics dm = new DisplayMetrics();
		((Activity) context).getWindowManager().getDefaultDisplay().getMetrics(dm);
		int width = dm.widthPixels;// 宽度
		return width;
	}

	/**
	 * 的到手机屏幕Y轴的像素
	 * 
	 * @param context
	 * @return
	 */
	public static int getDensityHeight(Context context) {
		DisplayMetrics dm = new DisplayMetrics();
		((Activity) context).getWindowManager().getDefaultDisplay().getMetrics(dm);
		int heiget = dm.heightPixels;
		return heiget;
	}

	/**
	 * 保存token
	 */
	public static void saveToken(String key, String token) {
		if (mSmap == null) {
			mSmap = new HashMap<String, String>();
		}

		mSmap.put(key, token);

	}

	public static void jumpAtivity(Context context, Class<?> cls) {
		context.startActivity(new Intent(context, cls));
	}

	/**
	 * Toast 二次封装
	 * 
	 * @param context
	 *            使用上下文
	 * @param toast
	 *            提示消息
	 */
	public static void showToast(Context context, String toast) {
		Toast.makeText(context, toast, Toast.LENGTH_SHORT).show();
	}

	/**
	 * 保存数据到默认XML中
	 * 
	 * @param context
	 *            使用上下文
	 * @param key
	 *            保存键
	 * @param value
	 *            存储值
	 */
	public static void saveSharedPrefrences(Context context, String key, String value) {
		SharedPreferences share = context.getSharedPreferences("Message", Context.MODE_PRIVATE);
		Editor editor = share.edit();
		editor.putString(key, value);
		editor.commit();
	}

	/**
	 * 从默认的XML中拿取数据,默认XML名字为Message
	 * 
	 * @param context
	 *            使用上下文
	 * @param key
	 *            保存键
	 * @return 存储值,找不到返回为null
	 */
	public static String getSharedPrefrences(Context context, String key) {
		SharedPreferences share = context.getSharedPreferences("Message", Context.MODE_PRIVATE);
		return share.getString(key, null);
	}

	/**
	 * 从指定的XML中拿取数据
	 * 
	 * @param context
	 *            使用上下文
	 * @param shareName
	 *            指定XML名字
	 * @param key
	 *            保存键
	 * @return 存储值,找不到返回为null
	 */
	public static String getSharedPrefrencesByName(Context context, String shareName, String key) {

		SharedPreferences share = context.getSharedPreferences(shareName, Context.MODE_PRIVATE);
		return share.getString(key, null);

	}

	/**
	 * 键值对形式存储数据在指定Name的XML中
	 * 
	 * @param context
	 *            使用上下文
	 * @param shareName
	 *            指定XML名字
	 * @param key
	 *            保存键
	 * @param value
	 *            存储值
	 */
	public static void saveSharedPrefrencesByName(Context context, String shareName, String key, String value) {
		SharedPreferences share = context.getSharedPreferences(shareName, Context.MODE_PRIVATE);
		Editor editor = share.edit();
		editor.putString(key, value);
		editor.commit();
	}

	/**
	 * 删除指定名字的XML
	 * 
	 * @param context
	 *            使用上下文
	 * @param shareName
	 *            指定XML名字
	 */
	public static void deleteSharedPrefrencesByName(Context context, String shareName) {
		SharedPreferences share = context.getSharedPreferences(shareName, Context.MODE_PRIVATE);
		if (share != null) {
			Editor editor = share.edit();
			editor.clear();
			editor.commit();
		} else {
			return;
		}
	}

	/** 删除默认SharedPrefrences所有保存数据 */
	public static void deleteSharedPrefrences(Context context) {
		SharedPreferences share = context.getSharedPreferences("Message", Context.MODE_PRIVATE);
		if (share != null) {
			Editor editor = share.edit();
			editor.clear();
			editor.commit();
		} else {
			return;
		}
	}

	/**
	 * 删除指定名字的XML
	 * 
	 * @param context使用上下文
	 * @param shareName
	 *            XML名字
	 */
	public static void deletSharedPrefrencesByName(Context context, String shareName) {
		SharedPreferences share = context.getSharedPreferences("shareName", Context.MODE_PRIVATE);
		if (share != null) {
			Editor editor = share.edit();
			editor.clear();
			editor.commit();
		} else {
			return;
		}
	}

	/**
	 * 连接网络获取数据
	 * 
	 * @param url
	 *            装载数据的网络地址
	 * @return
	 */
	public static String getHtmlString(String url) {
		HttpGet get = new HttpGet(url);
		HttpClient client = new DefaultHttpClient();
		try {
			Log.i("getHtmlString----url", url);
			HttpResponse response = client.execute(get);
			// 根据回应的状态码判断是否连接成功
			if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
				// 将得到的实体数据保存到对象中
				result = EntityUtils.toString(response.getEntity());
				return result;
			}
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}

	/** 获取屏幕的密度 */
	public static float getDensity(Context context) {
		DisplayMetrics dm = new DisplayMetrics();
		((Activity) context).getWindowManager().getDefaultDisplay().getMetrics(dm);
		float density = dm.density;
		return density;
	}

	/**
	 * 判断是否有网络连接
	 * 
	 * @param context
	 * @return
	 */
	public static boolean isNetworkAvailable(Context context) {
		ConnectivityManager connectivity = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
		if (connectivity != null) {
			NetworkInfo info = connectivity.getActiveNetworkInfo();
			if (info != null && info.isConnected()) {
				// 当前网络是连接的
				if (info.getState() == NetworkInfo.State.CONNECTED) {
					// 当前所连接的网络可用
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 
	 * 判断网络连接状态
	 */
	public static boolean isNetworkConnected(Context context) {
		if (context != null) {
			ConnectivityManager mConnectivityManager = (ConnectivityManager) context
					.getSystemService(Context.CONNECTIVITY_SERVICE);
			NetworkInfo mNetworkInfo = mConnectivityManager.getActiveNetworkInfo();
			if (mNetworkInfo != null) {
				return mNetworkInfo.isAvailable();
			}
		}
		return false;
	}

	/**
	 * 设置网络
	 * 
	 * @param paramContext
	 */
	public static void startToSettings(Context paramContext) {
		if (paramContext == null)
			return;
		try {
			if (Build.VERSION.SDK_INT > 10) {
				paramContext.startActivity(new Intent("android.settings.SETTINGS"));
				return;
			}
		} catch (Exception localException) {
			localException.printStackTrace();
			return;
		}
		paramContext.startActivity(new Intent("android.settings.WIRELESS_SETTINGS"));
	}

	/**
	 * 
	 * 判断网络状态是否为WIFI
	 */
	public static boolean isWifi(Context context) {
		ConnectivityManager connectivityManager = (ConnectivityManager) context
				.getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo activeNetInfo = connectivityManager.getActiveNetworkInfo();
		if (activeNetInfo != null && activeNetInfo.getType() == ConnectivityManager.TYPE_WIFI) {
			return true;
		}
		return false;
	}

	/**
	 * 点击两次提示消息
	 * 
	 * @param context
	 * @param tips
	 *            提示语句
	 */
	public static void clickBtnTwiceTips(Context context, String tips) {
		long clickTime = System.currentTimeMillis();
		if (clickTime - firstTime > 2000) {
			firstTime = clickTime;
			Toast.makeText(context, tips, Toast.LENGTH_SHORT).show();
		} else {
			((Activity) context).finish();
			System.exit(0);
			Process.killProcess(Process.myPid());
		}
	}

	/**
	 * 
	 * 到Url地址上去下载图片，并回传Bitmap回來
	 * 
	 * 
	 * 
	 * @param imgUrl
	 *            * @return
	 */
	public static Bitmap getBitmapFromUrl(String imgUrl) {
		URL url;
		Bitmap bitmap = null;
		try {
			url = new URL(imgUrl);
			InputStream is = url.openConnection().getInputStream();
			BufferedInputStream bis = new BufferedInputStream(is);
			// bitmap = BitmapFactory.decodeStream(bis); 注释1
			byte[] b = getBytes(is);
			bitmap = BitmapFactory.decodeByteArray(b, 0, b.length);
			bis.close();
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return bitmap;
	}

	/**
	 * 
	 * 将InputStream对象转换为Byte[]
	 * 
	 * @param is
	 * 
	 * @return
	 * 
	 * @throws IOException
	 */
	public static byte[] getBytes(InputStream is) throws IOException {
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		byte[] b = new byte[1024];
		int len = 0;
		while ((len = is.read(b, 0, 1024)) != -1) {
			baos.write(b, 0, len);
			baos.flush();
		}
		byte[] bytes = baos.toByteArray();
		return bytes;
	}

	/**
	 * 获取网落图片资源
	 * 
	 * @param url
	 * @return
	 */
	public static Bitmap getHttpBitmap(String url) {
		URL myFileURL;
		Bitmap bitmap = null;
		try {
			myFileURL = new URL(url);
			// 获得连接
			HttpURLConnection conn = (HttpURLConnection) myFileURL.openConnection();
			// 设置超时时间为6000毫秒，conn.setConnectionTiem(0);表示没有时间限制
			conn.setConnectTimeout(6000);
			// 连接设置获得数据流
			conn.setDoInput(true);
			// 不使用缓存
			conn.setUseCaches(false);
			// 这句可有可无，没有影响
			// conn.connect();
			// 得到数据流
			InputStream is = conn.getInputStream();
			// 解析得到图片
			bitmap = BitmapFactory.decodeStream(is);
			// 关闭数据流
			is.close();
		} catch (Exception e) {
			e.printStackTrace();
		}

		return bitmap;

	}

	/**
	 * Converts the byte array to HEX string.
	 * 
	 * @param buffer
	 *            the buffer.
	 * @return the HEX string.
	 */
	public static String toHexString(byte[] buffer) {

		String bufferString = "";

		if (buffer != null) {

			for (int i = 0; i < buffer.length; i++) {

				String hexChar = Integer.toHexString(buffer[i] & 0xFF);
				if (hexChar.length() == 1) {
					hexChar = "0" + hexChar;
				}

				bufferString += hexChar.toUpperCase(Locale.US) + " ";
			}
		}

		return bufferString;
	}

	/**
	 * Converts the error code to string.
	 * 
	 * @param errorCode
	 *            the error code.
	 * @return the error code string.
	 */
	public static String toErrorCodeString(int errorCode) {

		String errorCodeString = null;

		switch (errorCode) {
		case Result.ERROR_SUCCESS:
			errorCodeString = "The operation completed successfully.";
			break;
		case Result.ERROR_INVALID_COMMAND:
			errorCodeString = "The command is invalid.";
			break;
		case Result.ERROR_INVALID_PARAMETER:
			errorCodeString = "The parameter is invalid.";
			break;
		case Result.ERROR_INVALID_CHECKSUM:
			errorCodeString = "The checksum is invalid.";
			break;
		case Result.ERROR_INVALID_START_BYTE:
			errorCodeString = "The start byte is invalid.";
			break;
		case Result.ERROR_UNKNOWN:
			errorCodeString = "The error is unknown.";
			break;
		case Result.ERROR_DUKPT_OPERATION_CEASED:
			errorCodeString = "The DUKPT operation is ceased.";
			break;
		case Result.ERROR_DUKPT_DATA_CORRUPTED:
			errorCodeString = "The DUKPT data is corrupted.";
			break;
		case Result.ERROR_FLASH_DATA_CORRUPTED:
			errorCodeString = "The flash data is corrupted.";
			break;
		case Result.ERROR_VERIFICATION_FAILED:
			errorCodeString = "The verification is failed.";
			break;
		case Result.ERROR_PICC_NO_CARD:
			errorCodeString = "No card in PICC slot.";
			break;
		default:
			errorCodeString = "Error communicating with reader.";
			break;
		}

		return errorCodeString;
	}

	/**
	 * Converts the HEX string to byte array.
	 * 
	 * @param hexString
	 *            the HEX string.
	 * @return the number of bytes.
	 */
	public static int toByteArray(String hexString, byte[] byteArray) {

		char c = 0;
		boolean first = true;
		int length = 0;
		int value = 0;
		int i = 0;

		for (i = 0; i < hexString.length(); i++) {

			c = hexString.charAt(i);
			if ((c >= '0') && (c <= '9')) {
				value = c - '0';
			} else if ((c >= 'A') && (c <= 'F')) {
				value = c - 'A' + 10;
			} else if ((c >= 'a') && (c <= 'f')) {
				value = c - 'a' + 10;
			} else {
				value = -1;
			}

			if (value >= 0) {

				if (first) {

					byteArray[length] = (byte) (value << 4);

				} else {

					byteArray[length] |= value;
					length++;
				}

				first = !first;
			}

			if (length >= byteArray.length) {
				break;
			}
		}

		return length;
	}

	/**
	 * Converts the integer to HEX string.
	 * 
	 * @param i
	 *            the integer.
	 * @return the HEX string.
	 */
	public static String toHexString(int i) {

		String hexString = Integer.toHexString(i);

		if (hexString.length() % 2 == 1) {
			hexString = "0" + hexString;
		}

		return hexString.toUpperCase(Locale.US);
	}

	/**
	 * Converts the HEX string to byte array.
	 * 
	 * @param hexString
	 *            the HEX string.
	 * @return the byte array.
	 */
	public static byte[] toByteArray(String hexString) {

		byte[] byteArray = null;
		int count = 0;
		char c = 0;
		int i = 0;

		boolean first = true;
		int length = 0;
		int value = 0;

		// Count number of hex characters
		for (i = 0; i < hexString.length(); i++) {

			c = hexString.charAt(i);
			if (c >= '0' && c <= '9' || c >= 'A' && c <= 'F' || c >= 'a' && c <= 'f') {
				count++;
			}
		}

		byteArray = new byte[(count + 1) / 2];
		for (i = 0; i < hexString.length(); i++) {

			c = hexString.charAt(i);
			if (c >= '0' && c <= '9') {
				value = c - '0';
			} else if (c >= 'A' && c <= 'F') {
				value = c - 'A' + 10;
			} else if (c >= 'a' && c <= 'f') {
				value = c - 'a' + 10;
			} else {
				value = -1;
			}

			if (value >= 0) {

				if (first) {

					byteArray[length] = (byte) (value << 4);

				} else {

					byteArray[length] |= value;
					length++;
				}

				first = !first;
			}
		}

		return byteArray;
	}

	/** 得到系统时间yyyy年MM月dd日 HH:mm:ss */
	public static String getCurrentTime() {
		SimpleDateFormat formatter = new SimpleDateFormat("yyyy年MM月dd日    HH:mm:ss");
		Date curDate = new Date(System.currentTimeMillis());// 获取当前时间
		String str = formatter.format(curDate);
		return str;

	}

	public static long Str2Long(String time, String format) {

		try {
			SimpleDateFormat sdf = new SimpleDateFormat(format);
			Date d = sdf.parse(time);
			return d.getTime();
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return 0;
	}

	/** 将long型时间转化为一定格式的日期 */
	public static String long2SimpleData(String dataFormat, long mills) {
		SimpleDateFormat sdf = new SimpleDateFormat(dataFormat);
		String dateString = sdf.format(new Date(mills));
		return dateString;
	}

	/**
	 * 一直震動多少毫秒
	 * 
	 * @param activity
	 * @param milliseconds
	 */
	public static void Vibrate(final Activity activity, long milliseconds) {

		Vibrator vib = (Vibrator) activity

		.getSystemService(Service.VIBRATOR_SERVICE);

		vib.vibrate(milliseconds);

	}

	/**
	 * 按照我们传进去的数组进行间歇性的震动
	 * 
	 * @param activity
	 * @param pattern
	 * @param isRepeat
	 */
	public static void Vibrate(final Activity activity, long[] pattern,

	boolean isRepeat) {

		Vibrator vib = (Vibrator) activity

		.getSystemService(Service.VIBRATOR_SERVICE);

		vib.vibrate(pattern, isRepeat ? 1 : -1);

	}

	/**
	 * 停止震动
	 * 
	 * @param activity
	 */
	public static void StopVibrate(final Activity activity) {

		Vibrator vib = (Vibrator) activity

		.getSystemService(Service.VIBRATOR_SERVICE);

		vib.cancel();

	}

	/** 加密32 */
	public static String MD5(String str) {
		MessageDigest md5 = null;
		try {
			md5 = MessageDigest.getInstance("MD5");
		} catch (Exception e) {
			e.printStackTrace();
			return "";
		}

		char[] charArray = str.toCharArray();
		byte[] byteArray = new byte[charArray.length];

		for (int i = 0; i < charArray.length; i++) {
			byteArray[i] = (byte) charArray[i];
		}
		byte[] md5Bytes = md5.digest(byteArray);

		StringBuffer hexValue = new StringBuffer();
		for (int i = 0; i < md5Bytes.length; i++) {
			int val = ((int) md5Bytes[i]) & 0xff;
			if (val < 16) {
				hexValue.append("0");
			}
			hexValue.append(Integer.toHexString(val));
		}
		return hexValue.toString();
	}

	public static void startAlarm(Context con, Uri uri) {
		MediaPlayer mMediaPlayer = MediaPlayer.create(con, Utils.getSystemDefultRingtoneUri(con));
		mMediaPlayer.setLooping(true);
		try {
			mMediaPlayer.prepare();
		} catch (IllegalStateException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		mMediaPlayer.start();
	}

	/**
	 * 停止手机铃音
	 * 
	 * @param mMediaPlayer
	 */
	public static void stopAlarm(MediaPlayer mMediaPlayer) {

		mMediaPlayer.stop();
	}

	/**
	 * 获取系统默认铃声的Uri
	 * 
	 * @return
	 */
	public static Uri getSystemDefultRingtoneUri(Context con) {
		return RingtoneManager.getActualDefaultRingtoneUri(con, RingtoneManager.TYPE_RINGTONE);
	}
}
