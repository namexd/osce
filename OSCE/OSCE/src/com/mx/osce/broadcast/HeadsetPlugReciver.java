package com.mx.osce.broadcast;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.widget.Toast;

public class HeadsetPlugReciver extends BroadcastReceiver {

	@Override
	public void onReceive(Context context, Intent intent) {

		if (intent.getAction().equals(Intent.ACTION_HEADSET_PLUG)) {

			if (intent.getIntExtra("state", 0) == 1) { // 插入

				Log.i("**PLUG**", "is plug");
			} else {// 不插入

				Log.i("**PLUG**", "no plug");
			}
		}
	}
}
