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
import android.view.ViewGroup;
import android.widget.TextView;

public class NewFragmentWait extends Fragment {

	public interface onChangeMessageListener {
		void onChangMessage(String message);
	}

	public static final String TAG = "NewFragmentWait";

	private TextView mTextShowMsg;

	// private Button mButtonNext;
	private onChangeMessageListener mListener;
	private Context mContext;
	private RecoverHandler mHandler;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		super.onCreateView(inflater, container, savedInstanceState);
		View view = inflater.inflate(R.layout.framgnt_wait, null);
		mContext = getActivity();
		mHandler = new RecoverHandler();
		findView(view);
		// setListener();
		// getTipsData();
		return view;
	}


	private void findView(View view) {
		mTextShowMsg = (TextView) view.findViewById(R.id.testView_waitDraw);
		// mButtonNext = (Button) view.findViewById(R.id.button_next);
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
