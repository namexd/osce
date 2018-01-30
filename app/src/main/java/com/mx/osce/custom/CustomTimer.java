package com.mx.osce.custom;

import com.mx.osce.util.Constant;
import com.mx.osce.util.Utils;

import android.content.Context;
import android.os.CountDownTimer;
import android.os.SystemClock;
import android.view.WindowManager;
import android.widget.TextView;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

public class CustomTimer extends CountDownTimer {

	static final String Tag = "CountTimer";

	final long oneHourInMillis = 3600000;// 一小时毫秒

	final long oneMinuteInMillis = 60000;// 一分钟毫秒

	private TextView mShowRemindTime;

	private long startMillis;// 开始计时时间

	private long endMillis;// 结束计时时间

	private long remindMillis;// 剩余计时时间

	private Context ct;

	public CustomTimer(Context ct, TextView showTv, long millisInFuture, long countDownInterval) {
		super(millisInFuture, countDownInterval);
		this.ct = ct;
		mShowRemindTime = showTv;
		startMillis = SystemClock.elapsedRealtime();
		endMillis = (startMillis + millisInFuture);
	}

	@Override
	public void onTick(long millisUntilFinished) {

		long[] temp = computingTime(getRemindTime());
		mShowRemindTime.setText("倒计时:" + getRemindTimeString(temp));
	}

	@Override
	public void onFinish() {

		long use = endMillis - startMillis;

		long[] temp = computingTime(use);

		String tips = getRemindTimeString(temp);

		mShowRemindTime.setText("用时:" + tips);

		String teacher_type = Utils.getSharedPrefrences(ct, "teacher_type");

		if (!teacher_type.equals(Constant.TEACHER_TYPE_SP)) {
			// 提示对话框
			showTimeUseOutDialog();
		}

	}

	/** 得到剩余的时间 */
	private long getRemindTime() {

		remindMillis = endMillis - SystemClock.elapsedRealtime();
		if (remindMillis >= 0) {
			return remindMillis;
		} else {
			return 0;
		}
	}

	/***
	 * 得到使用时间的字符串
	 * 
	 * @param stopMisllis
	 *            停止的时间
	 * @return
	 */
	public String getUsedTime() {
		
		this.cancel();

		String examStartStr = Utils.getSharedPrefrences(ct, "startTime");

		String examEndStr = Utils.getSharedPrefrences(ct, "endTime");

		long usedMillis;

		if ((null != examStartStr) && (null != examEndStr)) {

			usedMillis = Utils.Str2Long(examEndStr, "yyyy-MM-dd HH:mm:ss")
					- Utils.Str2Long(examStartStr, "yyyy-MM-dd HH:mm:ss");
		} else {

			usedMillis = SystemClock.elapsedRealtime() - startMillis;

		}
		long[] temp = computingTime(usedMillis);

		return getRemindTimeString(temp);
	}

	/**
	 * 考试超时提示框
	 */
	private void showTimeUseOutDialog() {

		final SweetAlertDialog tipDialog = new SweetAlertDialog(ct, SweetAlertDialog.WARNING_TYPE, false);

		tipDialog.setTitleText("考试已结束！");
		tipDialog.setContentText("考试时间已用完！");
		tipDialog.setCanceledOnTouchOutside(false);
		tipDialog.setCancelable(false);
		tipDialog.getWindow().setType(WindowManager.LayoutParams.TYPE_SYSTEM_ALERT);
		tipDialog.setConfirmClickListener(new OnSweetClickListener() {
			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {
				tipDialog.dismiss();
			}
		});
		tipDialog.show();
	}

	/**
	 * 将剩余的毫秒转换为->时：分：秒
	 * 
	 * @param millisUntilFinished
	 *            距离倒计时结束剩余的毫秒
	 * @return 返回对应的[时，分，秒]
	 */
	private long[] computingTime(long millisUntilFinished) {

		long hours = -1;
		long minutes = -1;
		long seconds = -1;

		if (millisUntilFinished >= oneHourInMillis) {

			hours = (millisUntilFinished / oneHourInMillis);

			if (millisUntilFinished % oneHourInMillis > 0) {

				minutes = (millisUntilFinished % oneHourInMillis) / oneMinuteInMillis;

				seconds = (millisUntilFinished % oneHourInMillis) % oneMinuteInMillis / 1000;
			}
		} else {
			minutes = (millisUntilFinished / oneMinuteInMillis);

			seconds = (millisUntilFinished % oneMinuteInMillis) / 1000;
		}
		return new long[] { hours, minutes, seconds };
	}

	/**
	 * 返回显示时间的字符串
	 * <p>
	 * 以"00:00:00"的格式
	 * </p>
	 * 
	 * @param i
	 * @return
	 */
	private String getRemindTimeString(long[] i) {

		String[] tempArr = new String[i.length];

		for (int j = 0; j < i.length; j++) {

			String temp = null;

			long seconds = i[j];

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
