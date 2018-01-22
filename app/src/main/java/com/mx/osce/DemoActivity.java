package com.mx.osce;

import android.app.Activity;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.SystemClock;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;

public class DemoActivity extends Activity {
	static final long Total = 10 * 1000;
	static final long Interval = 1000;

	private TextView mTvTimeTip;
	private CountTimer mTimer;
	private Button mBtnFinsh;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.recycler_activity);
		mTvTimeTip = (TextView) findViewById(R.id.timer);
		mBtnFinsh = (Button) findViewById(R.id.finsh_timer);
		mBtnFinsh.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				mTimer.onFinish();
				mTimer.cancel();
			}
		});
		intiTimer();
	}

	private void intiTimer() {
		mTimer = new CountTimer(mTvTimeTip, 10000, 1000);
		mTimer.start();
	}

	private class CountTimer extends CountDownTimer {

		final String Tag = "CountTimer";

		final long oneHourInMillis = 3600000;// 一小时毫秒

		final long oneMinuteInMillis = 60000;// 一分钟毫秒

		private TextView mShowRemindTime;

		private long startMillis;// 开始计时时间

		private long endMillis;// 结束计时时间

		private long remindMillis;// 剩余计时时间

		public CountTimer(TextView showTv, long millisInFuture, long countDownInterval) {
			super(millisInFuture, countDownInterval);
			mShowRemindTime = showTv;
			startMillis = SystemClock.elapsedRealtime();
			endMillis = (startMillis + millisInFuture);
			Log.e("开始", "endMillis - startMillis =" + (endMillis - startMillis));
		}

		@Override
		public void onTick(long millisUntilFinished) {

			long[] temp = computingTime(getRemindTime());
			mShowRemindTime.setText("计时:" + getRemindTimeString(temp));
			Log.e(Tag, mShowRemindTime.getText().toString());
			Log.e(Tag, String.valueOf(remindMillis));
		}

		@Override
		public void onFinish() {
			Log.e(Tag, "onFinish");

			long[] useTime = computingTime(SystemClock.elapsedRealtime() - startMillis);
			String string = getRemindTimeString(useTime);
			mShowRemindTime.setText("用時:" + string);
		}

		private long getRemindTime() {

			remindMillis = endMillis - SystemClock.elapsedRealtime();
			if (remindMillis >= 0) {
				return remindMillis;
			} else {
				return 0;
			}

		}

		// 返回对应的[时，分，秒]
		private long[] computingTime(long millisUntilFinished) {

			long hours = -1;
			long minutes = -1;
			long seconds = -1;

			if (millisUntilFinished >= oneHourInMillis) {

				hours = (millisUntilFinished / oneHourInMillis);

				if (millisUntilFinished % oneHourInMillis > 0) {

					minutes = (millisUntilFinished % oneHourInMillis) / oneMinuteInMillis;

					seconds = (millisUntilFinished % oneHourInMillis) % oneMinuteInMillis;
				}
			} else {
				minutes = (millisUntilFinished / oneMinuteInMillis);

				seconds = (millisUntilFinished % oneMinuteInMillis);
			}
			return new long[] { hours, minutes, seconds };
		}

		// 返回显示时间的字符串
		private String getRemindTimeString(long[] i) {

			String[] tempArr = new String[i.length];

			for (int j = 0; j < i.length; j++) {

				String temp = null;

				long seconds = i[j] / 1000;

				if (-1 == seconds) {
					temp = "00";
				} else if (seconds < 10) {
					temp = "0" + seconds + "";
				} else {
					temp = String.valueOf(seconds);
				}
				tempArr[j] = temp;
			}
			return tempArr[0] + ":" + tempArr[1] + ":" + tempArr[2];
		}

	}
}