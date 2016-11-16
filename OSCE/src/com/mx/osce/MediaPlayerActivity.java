package com.mx.osce;

import java.io.IOException;

import com.mx.osce.util.Utils;

import android.content.Context;
import android.media.MediaPlayer;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Bundle;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;

public class MediaPlayerActivity extends BaseActivity {// 测试
	private Button mBtnOpen, mBtnClose;
	private MediaPlayer mPlayer;
	private RecyclerView mRecyclerView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.mediaplayer_activity);
		mBtnOpen = (Button) findViewById(R.id.btnOpen);
		mBtnClose = (Button) findViewById(R.id.btnClose);
		onClickEvents();
	}

	private void onClickEvents() {
		mBtnOpen.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				startAlarm(MediaPlayerActivity.this, getSystemDefultRingtoneUri(MediaPlayerActivity.this));
			}
		});
		mBtnClose.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				if (mPlayer != null) {
					stopAlarm(mPlayer);
				} else {
					return;
				}
			}
		});

	}

	@Override
	public void onClick(View v) {
		// TODO Auto-generated method stub

	}

	// 开启手机闹铃
	public void startAlarm(Context con, Uri uri) {
		if (mPlayer != null) {
			mPlayer.stop();
		}
		mPlayer = MediaPlayer.create(con, Utils.getSystemDefultRingtoneUri(con));
		mPlayer.setLooping(true);
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