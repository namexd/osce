package com.mx.osce.util;

import android.content.Context;
import android.util.Log;
import android.widget.Toast;

public class Out {
	public static void Toast(Context ct, String msg) {

		Toast t = android.widget.Toast.makeText(ct, msg, android.widget.Toast.LENGTH_SHORT);
		t.show();
	}

	public static void out(String msg) {
		Log.e("XXXErrorXXX", msg);
	}

}
