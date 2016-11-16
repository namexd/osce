package com.mx.osce.custom;

import java.io.IOException;

import com.mx.osce.MediaPlayerActivity;
import com.mx.osce.R;
import com.mx.osce.util.Utils;

import android.app.Dialog;
import android.content.Context;
import android.media.MediaPlayer;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.CountDownTimer;
import android.view.KeyEvent;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class ConutDownTimer_Dialog { 
Context context; 
Dialogcallback dialogcallback; 
Dialog dialog; 
Button confirm_button; 
TextView exam_time; 
TextView exam_name;
String exam_name_t;
long exam_time_t;
private TimeCount time;
private MediaPlayer mPlayer;
private int btnstates;//初始状态0--开始评分，1--关闭闹钟
private boolean states=false;//闹铃是否已经响过
/** 
* init the dialog 
* @return 
*/ 
public ConutDownTimer_Dialog(Context con) { 
this.context = con; 
this.dialog = new Dialog(context, R.style.dialog);
dialog.setCanceledOnTouchOutside(false);
dialog.setCancelable(false);
mPlayer = MediaPlayer.create(con, Utils.getSystemDefultRingtoneUri(con));
mPlayer.setLooping(true);
dialog.setContentView(R.layout.time_countdowndialog); 
exam_time = (TextView) dialog.findViewById(R.id.exam_time); 
confirm_button = (Button) dialog.findViewById(R.id.confirm_button); 
exam_name = (TextView) dialog.findViewById(R.id.exam_name); 
confirm_button.setOnClickListener(new View.OnClickListener() { 
@Override 
public void onClick(View v) {
	switch (btnstates) {
	case 0:
		if (mPlayer.isPlaying()) {
			stopAlarm(mPlayer);
		} 
		time.cancel();
		dialog.dismiss(); 
		break;
	case 1:
		if (mPlayer.isPlaying()) {
			stopAlarm(mPlayer);
		} 
		states=true;
		btnstates=0;
		confirm_button.setText(R.string.start_point);
		break;
	default:
		break;
	}
	
} 
}); 
} 
/** 
* 设定一个interfack接口,使mydialog可以處理activity定義的事情 
* @author sfshine 
* 
*/ 
public interface Dialogcallback { 
public void dialogdo(String string); 
} 
public void setDialogCallback(Dialogcallback dialogcallback) { 
this.dialogcallback = dialogcallback; 
} 
/** 
* @category Set The Content of the TextView 
* */ 
public void setExam_name(String content) { 
	exam_name.setText(content); 
}
public void setExam_time(long content) {
	exam_time_t=content;
	exam_time.setText(changeTime(content)); 
}
/* 定义一个倒计时的内部类 */
class TimeCount extends CountDownTimer {
public TimeCount(long millisInFuture, long countDownInterval) {
super(millisInFuture, countDownInterval);//参数依次为总时长,和计时的时间间隔
}
@Override
public void onFinish() {//计时完毕时触发
	exam_time.setText("00:00:00");
	if(mPlayer.isPlaying()){
	
	}else{
		startAlarm(context, getSystemDefultRingtoneUri(context));	
	}
	
}
@Override
public void onTick(long millisUntilFinished){//计时过程显示
	exam_time.setText(changeTime(millisUntilFinished/1000));
	if(millisUntilFinished/1000<=3*60&&!mPlayer.isPlaying()){
		if(!states){
		btnstates=1;
		confirm_button.setText(R.string.close_media);
		startAlarm(context, getSystemDefultRingtoneUri(context));	
		}
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
* */ 

public void show() { 
dialog.show();
time = new TimeCount(exam_time_t*1000, 1000);
time.start();
} 
public void hide() { 
dialog.hide(); 
} 
public void dismiss() { 
dialog.dismiss(); 
} 
//开启手机闹铃
	public void startAlarm(Context con, Uri uri) {
		
		try {
			mPlayer.prepare();
		} catch (IllegalStateException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		mPlayer.start();
		
	}

	/**
	 * 停止手机铃音
	 * 
	 * @param mMediaPlayer
	 */
	public void stopAlarm(MediaPlayer mMediaPlayer) {

		mMediaPlayer.stop();
		mMediaPlayer.release();
		mPlayer = MediaPlayer.create(context, Utils.getSystemDefultRingtoneUri(context));
		mPlayer.setLooping(true);
	}

	/**
	 * 获取系统默认铃声的Uri
	 * 
	 * @return
	 */
	public Uri getSystemDefultRingtoneUri(Context con) {
		return RingtoneManager.getActualDefaultRingtoneUri(con, RingtoneManager.TYPE_RINGTONE);
	}
	
} 