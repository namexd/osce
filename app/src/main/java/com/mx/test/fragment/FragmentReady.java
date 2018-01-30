package com.mx.test.fragment;

import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.mx.test.MainActivity;
import com.mx.test.R;
import com.mx.test.custom.CustomTextView;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

public class FragmentReady extends Fragment {

	private static final String TAG = "FragmentReady";

	private RecoverHandler mHandler;

	private String mNfcCode;

	public interface OnReadyListener {
		void onReady();
	}

	private CustomTextView mTextReadyTips;// 提示考站准备

	private ImageButton mImageBtn;// 发送准备完成的请求

	private OnReadyListener mListener;

	private Context mContext;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		super.onCreateView(inflater, container, savedInstanceState);

		// View view = inflater.inflate(R.layout.fragment_draw_wait, null);
		View view = inflater.inflate(R.layout.fragment_ready, null);

		mContext = getActivity();

		findView(view);

		setListener();

		return view;
	}

	private void findView(View view) {

		mTextReadyTips = (CustomTextView) view.findViewById(R.id.tv_ready_tips);

		mImageBtn = (ImageButton) view.findViewById(R.id.testView_readyExam);

		mHandler = new RecoverHandler();
	}

	private void setListener() {

		mImageBtn.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				FragmentManager manager = ((MainActivity) mContext).getFragmentManager();
				manager.beginTransaction().replace(R.id.fragment_draw, new NewFragmentWait()).commit();
				/**
				 * 2016-6-3 修改后台成绩异步成绩提交时修改 if (ReTransmitNames != null &&
				 * ReTransmitNames.length > 0 &&
				 * !ReTransmitNames[0].equalsIgnoreCase("")) {
				 * ShowDialog(ReTransmitNames); } else { sendReadyRequest(); }
				 **/
			}
		});

	}

	public void ShowDialog(final String[] ReTransmitNames) {
		final SweetAlertDialog tipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE, false);
		tipsDialog.setTitleText("当前还有" + ReTransmitNames.length + "个考生本地成绩未提交");
		tipsDialog.setConfirmText("继续打分");//
		tipsDialog.setCancelText("暂不提交");// 下一个考点
		tipsDialog.setConfirmText("立即提交");
		tipsDialog.setCancelable(false);
		tipsDialog.showCancelButton(true);

		// 暂不提交
		tipsDialog.setCancelClickListener(new OnSweetClickListener() {

			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {
				tipsDialog.dismiss();
			}
		});
		// 立即提交
		tipsDialog.setConfirmClickListener(new OnSweetClickListener() {

			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {
				for (int i = 0; i < ReTransmitNames.length; i++) {
					if (!ReTransmitNames[i].equalsIgnoreCase("")) {
					}
				}
				tipsDialog.dismiss();
			}
		});
		tipsDialog.show();
	}

	public void setOnReadyListener(OnReadyListener onReadyListener) {
		mListener = onReadyListener;
	}



	private Response.ErrorListener errorListener() {
		return new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				Toast.makeText(mContext, "本地成绩上传失败", Toast.LENGTH_SHORT).show();
				error.printStackTrace();
			}

		};
	}

	@SuppressLint("HandlerLeak")
	private class RecoverHandler extends Handler {
		static final int RECOVER = 10;

		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			switch (msg.what) {
			case RECOVER:
				mTextReadyTips.setText(mContext.getResources().getString(R.string.fragment_wait_tv_ready_tips));
				break;
			default:
				break;
			}
		}
	}
}