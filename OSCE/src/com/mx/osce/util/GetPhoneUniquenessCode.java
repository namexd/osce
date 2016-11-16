package com.mx.osce.util;



import android.content.Context;
import android.telephony.TelephonyManager;

/**
 * 此类用于得到移动设备终端的唯一标识符,其中getIMEI()方法返回的是手机的唯一标识符,getIMSI()返回的是SMI卡的唯一标识符.
 * 
 * @author 彭沧旭
 *
 */
public class GetPhoneUniquenessCode {
	// 需要添加获取手机唯一标示IMEI的权限
	// uses-permission android:name="android.permission.READ_PHONE_STATE
	/**
	 * 获取手机的唯一标示IMEI码
	 * 
	 * @param context
	 *            参数上下文，便于得到TelephonyManager
	 * @return 手机设备的唯一IMEI标识码
	 */
	public static String getIMEI(Context context) {
		TelephonyManager phoneManager = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);
		String phoneIMEI = phoneManager.getDeviceId();
		return phoneIMEI;
	}

	/**
	 * 获取手机SMI卡的唯一标示码
	 * 
	 * @param context
	 * @return 手机SMI卡的唯一IMSI标识码
	 */
	public static String getIMSI(Context context) {
		TelephonyManager phoneManager = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);
		String smiIMEI = phoneManager.getSubscriberId();
		return smiIMEI;
	}
}
