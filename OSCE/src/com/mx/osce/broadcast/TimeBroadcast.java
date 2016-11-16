package com.mx.osce.broadcast;

import java.text.SimpleDateFormat;
import java.util.Date;

import com.mx.osce.MainActivity;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.widget.TextView;

public class TimeBroadcast extends BroadcastReceiver {

	private static final String UPDATE = "updateTime";

	private TextView timeDateText;

	public TimeBroadcast(TextView timeDateText) {
		this.timeDateText = timeDateText;
	}

	@Override
	public void onReceive(Context context, Intent intent) {
		if (intent.getAction().equals(MainActivity.UPDATE)) {
			Date date = new Date();
			SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy年MM月dd日 HH:mm:ss");
			timeDateText.setText(dateFormat.format(date));
		}
	}
}
