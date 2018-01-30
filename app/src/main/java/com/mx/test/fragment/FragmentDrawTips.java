package com.mx.test.fragment;

import com.mx.test.R;
import com.mx.test.util.Constant;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.TextView;

public class FragmentDrawTips extends Fragment {

	public interface onChangeMessageListener {
		void onChangMessage(String message);
	}

	public static final String TAG = "FragmentDrawTips";
	private TextView mTextShowMsg;
	// private Button mButtonNext;
	private onChangeMessageListener mListener;
	private Context mContext;
	private RecoverHandler mHandler;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		super.onCreateView(inflater, container, savedInstanceState);
		View view = inflater.inflate(R.layout.fragment_draw_defeat, null);
		mContext = getActivity();
		mHandler = new RecoverHandler();
		findView(view);
		// setListener();
		getTipsData();
		return view;
	}

	@Override
	public void onResume() {
		super.onResume();
		mHandler.sendEmptyMessageDelayed(RecoverHandler.REFRESH, 10000);
	}

	private void findView(View view) {
		mTextShowMsg = (TextView) view.findViewById(R.id.testView_waitDraw);
		// mButtonNext = (Button) view.findViewById(R.id.button_next);
		// 点击刷新
		mTextShowMsg.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

			}
		});
	}

	public void getTipsData() {

		Bundle tips = getArguments();

		if (tips != null) {

			if ("success".equals(tips.getString("TipsData"))) {
				mTextShowMsg.setText("抽签成功!");
			} else {
				mTextShowMsg.setText(tips.getString("TipsData"));
			}
			mHandler.sendEmptyMessageDelayed(RecoverHandler.RECOVER, 3000);
		} else {
			mTextShowMsg.setText("正在等待考生刷取腕表...");
		}
	}

	public void setTips(String tips) {
		if ("success".equals(tips)) {
			mTextShowMsg.setText("抽签成功!");
		} else {
			mTextShowMsg.setText(tips);
		}
		mHandler.sendEmptyMessageDelayed(RecoverHandler.RECOVER, 3000);
	}

	private void setListener() {

		// mButtonNext.setOnClickListener(new OnClickListener() {
		//
		// @Override
		// public void onClick(View v) {
		// nextStudentRequest();
		// }
		// });
	}


	@SuppressLint("HandlerLeak")
	private class RecoverHandler extends Handler {
		static final int RECOVER = 11;
		static final int REFRESH = 12;

		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			switch (msg.what) {
			case RECOVER:
				mTextShowMsg.setVisibility(View.VISIBLE);
				mTextShowMsg.setText("刷新");
				break;

			case REFRESH:
				Intent intent = new Intent();
				intent.setAction(Constant.ACTION_REFRESH);
				mContext.sendBroadcast(intent);
				break;
			default:
				break;
			}
		}
	}

}