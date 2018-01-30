package com.mx.bluetooth.fragment;

import java.util.HashMap;
import java.util.Map;

import org.json.JSONException;
import org.json.JSONObject;

import com.android.volley.Request.Method;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.Response.Listener;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;
import com.mx.bluetooth.BaseActivity;
import com.mx.bluetooth.MainActivity;
import com.mx.bluetooth.R;
import com.mx.bluetooth.bean.BaseInfo;
import com.mx.bluetooth.custom.CustomTextView;
import com.mx.bluetooth.log.SaveNetRequestLog2Local;
import com.mx.bluetooth.util.Constant;
import com.mx.bluetooth.util.GsonRequest;
import com.mx.bluetooth.util.ReTransmitUtil;
import com.mx.bluetooth.util.Utils;

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
				String[] ReTransmitNames = ReTransmitUtil.getReTransmitNames(mContext);
				/**
				 * 2016-6-3 修改后台成绩异步成绩提交时修改 if (ReTransmitNames != null &&
				 * ReTransmitNames.length > 0 &&
				 * !ReTransmitNames[0].equalsIgnoreCase("")) {
				 * ShowDialog(ReTransmitNames); } else { sendReadyRequest(); }
				 **/
				sendReadyRequest();
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
				sendReadyRequest();
				tipsDialog.dismiss();
			}
		});
		// 立即提交
		tipsDialog.setConfirmClickListener(new OnSweetClickListener() {

			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {
				for (int i = 0; i < ReTransmitNames.length; i++) {
					if (!ReTransmitNames[i].equalsIgnoreCase("")) {
						uploadScoreRequest(ReTransmitNames[i]);
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

					String msg = baseReady.getMessage();

					mTextReadyTips.setText((null == msg) ? "准备失败" : msg);

					mHandler.sendEmptyMessageDelayed(RecoverHandler.RECOVER, 2000);// 2秒后恢复正常界面

				} else if (baseReady.getCode() == 1) {// 准备成功,切换到抽签碎片

					FragmentManager manager = ((MainActivity) mContext).getFragmentManager();
					manager.beginTransaction().replace(R.id.fragment_draw, new NewFragmentWait()).commit();
					// manager.beginTransaction().replace(R.id.fragment_draw,
					// new FragmentDrawTips()).commit();
				}
			}
		}, new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				Log.e(TAG, error.getMessage(), error);
			}
		});
		requestQueue.add(stringRequest);
	}

	/** 上传本地成绩 */
	private void uploadScoreRequest(final String ReTransmitNames) {
		Map<String, String> params = new HashMap<String, String>();
		String str = SaveNetRequestLog2Local.getScoreFromLocal(ReTransmitNames);
		JSONObject json;
		RequestQueue requestQueue = Volley.newRequestQueue(mContext);
		try {
			json = new JSONObject(str);
			params.put("student_id", json.getString("student_id"));
			params.put("station_id", json.getString("station_id"));
			params.put("teacher_id", json.getString("teacher_id"));
			params.put("exam_screening_id", json.getString("exam_screening_id"));
			params.put("begin_dt", json.getString("begin_dt"));
			params.put("end_dt", json.getString("end_dt"));
			params.put("operation", json.getString("operation"));
			params.put("skilled", json.getString("skilled"));
			params.put("patient", json.getString("patient"));
			params.put("affinity", json.getString("affinity"));
			params.put("evaluate", json.getString("evaluate"));
			params.put("upload_image_return", json.getString("upload_image_return"));
			params.put("score", json.getString("score"));
		} catch (JSONException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
			return;
		}
		try {
			GsonRequest<BaseInfo> uploadResult = new GsonRequest<BaseInfo>(Method.POST,
					BaseActivity.mSUrl + Constant.UPLOAD_SCORE, BaseInfo.class, null, params, new Listener<BaseInfo>() {

						@Override
						public void onResponse(BaseInfo arg0) {

							if (arg0.getCode() == 1) {
								Toast.makeText(mContext, "本地成绩上传成功", Toast.LENGTH_SHORT).show();
								ReTransmitUtil.removeReTransmitNames(mContext, ReTransmitNames);

							} else {
								Toast.makeText(mContext, "本地成绩上传失败", Toast.LENGTH_SHORT).show();
								Log.i("request return String", arg0.toString());
								Log.i(TAG, arg0.getCode() + "");
								Log.e(TAG, arg0.getMessage());
							}
						}
					}, errorListener());
			requestQueue.add(uploadResult);
		} catch (Exception e) {
			Toast.makeText(mContext, "上传本地成绩返回数据有误！", Toast.LENGTH_SHORT).show();
		}
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