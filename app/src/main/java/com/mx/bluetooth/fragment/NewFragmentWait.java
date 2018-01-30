package com.mx.bluetooth.fragment;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.Response.ErrorListener;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;
import com.mx.bluetooth.BaseActivity;
import com.mx.bluetooth.R;
import com.mx.bluetooth.bean.NextBean;
import com.mx.bluetooth.util.Constant;
import com.mx.bluetooth.util.Utils;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

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

	@Override
	public void onResume() {
		super.onResume();
		mHandler.sendEmptyMessageDelayed(RecoverHandler.REFRESH, 10000);
	}

	private void findView(View view) {
		mTextShowMsg = (TextView) view.findViewById(R.id.testView_waitDraw);
		// mButtonNext = (Button) view.findViewById(R.id.button_next);
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

	private void nextStudentRequest() {

		RequestQueue requestQueue = Volley.newRequestQueue(mContext);

		String nextUrl = null;

		if (Utils.getSharedPrefrences(mContext, "exam_queue_id") == null) {

			nextUrl = BaseActivity.mSUrl + Constant.NEXT_STUDENT + "?station_id="
					+ Utils.getSharedPrefrences(mContext, "station_id") + "&teacher_id="
					+ Utils.getSharedPrefrences(mContext, "user_id");
		} else {
			nextUrl = BaseActivity.mSUrl + Constant.NEXT_STUDENT + "?exam_queue_id="
					+ Utils.getSharedPrefrences(mContext, "student_exam_queue_id") + "&station_id="
					+ Utils.getSharedPrefrences(mContext, "station_id") + "teacher_id="
					+ Utils.getSharedPrefrences(mContext, "user_id");
		}

		Log.e("nextUrl", nextUrl);

		StringRequest nextRequest = new StringRequest(nextUrl, new Response.Listener<String>() {

			@Override
			public void onResponse(String arg0) {

				NextBean nextBean = null;

				try {
					nextBean = new Gson().fromJson(arg0, NextBean.class);

				} catch (Exception e) {
					Toast.makeText(mContext, "获得下一个数据出错", Toast.LENGTH_SHORT).show();
					return;
				}
				if (nextBean != null && nextBean.getCode() == 1) {

					Toast.makeText(mContext, "刷新成功", Toast.LENGTH_SHORT).show();
				} else if (nextBean == null) {

					Toast.makeText(mContext, "刷新失败", Toast.LENGTH_SHORT).show();

				} else if (nextBean.getCode() != 1) {

					Toast.makeText(mContext, nextBean.getMessage(), Toast.LENGTH_SHORT).show();
				}
			}
		}, new ErrorListener() {

			@Override
			public void onErrorResponse(VolleyError arg0) {
				Log.e(TAG, arg0.getMessage(), arg0);
			}
		});
		requestQueue.add(nextRequest);
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
