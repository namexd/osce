package com.mx.osce.fragment;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;
import com.mx.osce.BaseActivity;
import com.mx.osce.MainActivity;
import com.mx.osce.R;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.ReadyBean;
import com.mx.osce.util.Constant;
import com.mx.osce.util.Utils;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

public class FragmentDrawWait extends Fragment {

	private static final String TAG = "FragmentDrawWait";
	private RecoverHandler mHandler;
	private String mNfcCode;

	public interface OnReadyListener {
		void onReady();
	}

	private TextView mTextReady;
	private OnReadyListener mListener;
	private Context mContext;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		super.onCreateView(inflater, container, savedInstanceState);
		View view = inflater.inflate(R.layout.fragment_draw_wait, null);
		mContext = getActivity();
		findView(view);
		setListener();
		return view;
	}

	private void findView(View view) {
		mTextReady = (TextView) view.findViewById(R.id.testView_readyExam);
		mHandler = new RecoverHandler();
	}

	private void setListener() {
		mTextReady.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				sendReadyRequest();
			}
		});

	}

	public void setOnReadyListener(OnReadyListener onReadyListener) {
		mListener = onReadyListener;
	}

	/** 1.点击完成准备， 发送准备完成的网络请求 */
	protected void sendReadyRequest() {

		RequestQueue requestQueue = Volley.newRequestQueue(mContext);

		String readyUrl = BaseActivity.mSUrl + Constant.READ_COMPLETE + "?exam_id="
				+ Utils.getSharedPrefrences(mContext, "exam_id") + "&exam_screening_id="
				+ Utils.getSharedPrefrences(mContext, "exam_screening_id") + "&station_id="
				+ Utils.getSharedPrefrences(mContext, "station_id") + "&teacher_id="
				+ Utils.getSharedPrefrences(mContext, "user_id") + "&room_id="
				+ Utils.getSharedPrefrences(mContext, "room_id");

		Log.e(">>>Station Reday Url<<<", readyUrl);
		try {
			StringRequest stringRequest = new StringRequest(readyUrl, new Response.Listener<String>() {

				@Override
				public void onResponse(String arg0) {
					BaseInfo baseReady = null;
					try {
						baseReady = new Gson().fromJson(arg0, BaseInfo.class);
					} catch (Exception e) {
						Toast.makeText(mContext, "发送准备完成数据有误！", Toast.LENGTH_SHORT).show();
						return;
					}
					if (baseReady.getCode() != 1) {// 准备失败

						mTextReady.setText(baseReady.getMessage());

						mHandler.sendEmptyMessageDelayed(RecoverHandler.RECOVER, 2000);// 2秒后恢复正常界面

					} else if (baseReady.getCode() == 1) {// 准备成功,切换到抽签碎片

						FragmentManager manager = ((MainActivity) mContext).getFragmentManager();

						manager.beginTransaction().replace(R.id.fragment_draw, new FragmentDrawTips()).commit();
					}
				}
			}, new Response.ErrorListener() {
				@Override
				public void onErrorResponse(VolleyError error) {
					Log.e(TAG, error.getMessage(), error);
				}
			});
			requestQueue.add(stringRequest);
		} catch (Exception e) {

		}
	}

	@SuppressLint("HandlerLeak")
	private class RecoverHandler extends Handler {
		static final int RECOVER = 10;

		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			switch (msg.what) {
			case RECOVER:
				mTextReady.setText("点击准备完成，开始抽签");
				break;
			default:
				break;
			}
		}
	}
}