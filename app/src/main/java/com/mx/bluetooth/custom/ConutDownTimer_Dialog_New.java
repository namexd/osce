package com.mx.bluetooth.custom;

import java.util.HashMap;

import com.mx.bluetooth.R;
import com.mx.bluetooth.util.Utils;

import android.app.Dialog;
import android.content.Context;
import android.media.AudioManager;
import android.media.SoundPool;
import android.os.CountDownTimer;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

public class ConutDownTimer_Dialog_New {
	Context context;
	Dialogcallback dialogcallback;
	Dialog dialog;
	Button confirm_button;
	TextView exam_time;
	TextView exam_name;
	String exam_name_t;
	long exam_time_t;
	private TimeCount time;
	private SoundPool mSoundPool = null;
	HashMap<Integer, Integer> soundmap = new HashMap<Integer, Integer>();
	private boolean states = false;// 闹铃是否已经响过

	public ConutDownTimer_Dialog_New(Context con) {
		this.context = con;
		this.dialog = new Dialog(context, R.style.dialog);
		dialog.setCanceledOnTouchOutside(false);
		dialog.setCancelable(false);
		mSoundPool = new SoundPool(2, AudioManager.STREAM_SYSTEM, 5);
		soundmap.put(1, mSoundPool.load(context.getResources().openRawResourceFd(R.raw.aa), 1));
		soundmap.put(2, mSoundPool.load(context.getResources().openRawResourceFd(R.raw.bb), 1));

		dialog.setContentView(R.layout.time_countdowndialog);
		exam_time = (TextView) dialog.findViewById(R.id.exam_time);
		confirm_button = (Button) dialog.findViewById(R.id.confirm_button);
		exam_name = (TextView) dialog.findViewById(R.id.exam_name);
		confirm_button.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {

				time.cancel();
				mSoundPool.release();
				mSoundPool = null;
				dialog.dismiss();
				Utils.saveSharedPrefrences(context, "isShowSp", "false");
				dialogcallback.dialogdo("");
			}
		});
	}

	public interface Dialogcallback {
		public void dialogdo(String string);

		// public void timeCountFinsh();
	}

	public void setDialogCallback(Dialogcallback dialogcallback) {
		this.dialogcallback = dialogcallback;
	}

	class TimeCount extends CountDownTimer {
		public TimeCount(long millisInFuture, long countDownInterval) {
			super(millisInFuture, countDownInterval);// 参数依次为总时长,和计时的时间间隔
		}

		@Override
		public void onFinish() {// 计时完毕时触发
			exam_time.setText("00:00:00");
			if (mSoundPool != null) {
				mSoundPool.play(soundmap.get(2), 1, 1, 0, -1, 1);
			}
			dialogcallback.dialogdo("");
		}

		@Override
		public void onTick(long millisUntilFinished) {// 计时过程显示
			exam_time.setText(changeTime(millisUntilFinished / 1000));
			if (mSoundPool != null && millisUntilFinished / 1000 <= 3 * 60 && !states) {
				mSoundPool.play(soundmap.get(1), 1, 1, 0, 2, 1);
				states = true;
			}
		}

	}

	public String changeTime(long second) {
		int h = 0;
		int d = 0;
		int s = 0;
		int temp = (int) second % 3600;
		if (second > 3600) {
			h = (int) second / 3600;
			if (temp != 0) {
				if (temp > 60) {
					d = temp / 60;
					if (temp % 60 != 0) {
						s = temp % 60;
					}
				} else {
					s = temp;
				}
			}
		} else {
			d = (int) second / 60;
			if (second % 60 != 0) {
				s = (int) second % 60;
			}
		}
		String h2 = "";
		String d2 = "";
		String s2 = "";
		if (h < 10) {
			h2 = "0" + h;
		} else {
			h2 = "" + h;
		}
		if (d < 10) {
			d2 = "0" + d;
		} else {
			d2 = d + "";
		}
		if (s < 10) {
			s2 = "0" + s;
		} else {
			s2 = s + "";
		}

		return h2 + ":" + d2 + ":" + s2;

	}

	/**
	 * Get the Text of the EditText
	 */

	public void show() {
		dialog.show();
		time = new TimeCount(exam_time_t * 1000, 1000);
		time.start();
	}

	public void hide() {
		dialog.hide();
	}

	public boolean isshow() {
		return dialog.isShowing();
	}

	public void dismiss() {
		dialog.dismiss();
	}

	/**
	 * @category Set The Content of the TextView
	 */
	public void setExam_name(String content) {
		exam_name.setText(content);
	}

	public void setExam_time(long content) {
		exam_time_t = content;
		exam_time.setText(changeTime(content));
		if(time!=null){
			time.cancel();	
			time = new TimeCount(exam_time_t * 1000, 1000);
			time.start();
		}
	}

	public void onclose() {
		if (time != null)
			time.cancel();
		if (mSoundPool != null)
			mSoundPool.release();
		mSoundPool = null;
		if (dialog != null)
			dialog.dismiss();
	}
}
