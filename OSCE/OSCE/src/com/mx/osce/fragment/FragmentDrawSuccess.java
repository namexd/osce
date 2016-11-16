package com.mx.osce.fragment;

import java.util.HashMap;

import com.android.volley.Request;
import com.android.volley.Request.Method;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.Response.Listener;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;
import com.mx.osce.BaseActivity;
import com.mx.osce.GradeActivity;
import com.mx.osce.MainActivity;
import com.mx.osce.R;
import com.mx.osce.bean.AbnormalStudentBean;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.EndExamBean;
import com.mx.osce.bean.StudentStatuBean;
import com.mx.osce.util.CommonTool;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.NetStatus;
import com.mx.osce.util.Out;
import com.mx.osce.util.RequestManager;
import com.mx.osce.util.Utils;

import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

public class FragmentDrawSuccess extends Fragment {
	private static String TAG = "FragmentDrawSuccess";
	private ImageView mImagePhoto;
	// 姓名,学号,身份证,考号,异常原因
	private TextView mName, mCode, mCardId, mTestCode,mreason;
	// private TextView mbeginTest, mWarn;
	private TextView mbeginTest;
	private Context mContext;
	// 控制开始考试
	public static final int BEGIN = 1;
	// 控制结束考试
	public static final int END = -1;
	// 初始控制为开始考试
	private int mState = BEGIN;
	private SweetAlertDialog mTipsDialog;
	private boolean sendWarn_state = false;
	//2016-6-17异常考生时添加
	private int StudentState =1;//判断学生状态 1为正常，0为异常；
	private String reason;//异常原因

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		View view = inflater.inflate(R.layout.fragment_draw_success, null);
		mContext = getActivity();
		findWidget(view);
		validateStudent();
		setListener();
		return view;
	}

	private void findWidget(View view) {

		mImagePhoto = (ImageView) view.findViewById(R.id.image_student_photo);
		mName = (TextView) view.findViewById(R.id.tv_student_name);
		mCode = (TextView) view.findViewById(R.id.tv_studentId);
		mCardId = (TextView) view.findViewById(R.id.tv_idCard);
		mTestCode = (TextView) view.findViewById(R.id.tv_text_Num);
		mbeginTest = (TextView) view.findViewById(R.id.textview_begin);
		mreason= (TextView) view.findViewById(R.id.reason);
		// mWarn = (TextView) view.findViewById(R.id.text_warn);
		CommonTool.getBitmapUtils(mContext).display(mImagePhoto, Utils.getSharedPrefrences(mContext, "student_avator"));
		mName.setText(Utils.getSharedPrefrences(mContext, "student_name"));
		mCode.setText(Utils.getSharedPrefrences(mContext, "student_code"));
		mCardId.setText(Utils.getSharedPrefrences(mContext, "student_idcard"));
		mTestCode.setText(Utils.getSharedPrefrences(mContext, "student_exam_sequence"));
		
	}
	private void validateStudent() {
		if (!Utils.getSharedPrefrences(mContext, "controlMark").equalsIgnoreCase("null")&&!Utils.getSharedPrefrences(mContext, "controlMark").equalsIgnoreCase("-1")) {
			StudentState=0;
			reason=Utils.getSharedPrefrences(mContext, "reason");
			mreason.setText(reason);
			mbeginTest.setText("下一个");
		};
		
	}

	/** 设置控件监听 */
	private void setListener() {

		mbeginTest.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				if (StudentState==0) {//异常考生处理
					
					handleAbnormalStudent();
					
				}else {
					if (Utils.getSharedPrefrences(mContext, "type").equals(Constant.THEORY_STATION)) {
						chooseRequestType(mState);
						mState = mState * -1;
					}
					getStartTime();
				}
				
			}
		});

		// mWarn.setOnClickListener(new OnClickListener() {
		// @Override
		// public void onClick(View v) {
		// sureWarnType();
		// }
		// });
	}
	// 发送异常考试的网络请求
		private void handleAbnormalStudent() {

			RequestQueue requestQueue = Volley.newRequestQueue(mContext);

			String url = BaseActivity.mSUrl + Constant.End_AbnormalStudent_EXAM + "?student_id="
					+ Utils.getSharedPrefrences(mContext, "student_id") + "&station_id="
					+ Utils.getSharedPrefrences(mContext, "station_id") + "&user_id="
					+ Utils.getSharedPrefrences(mContext, "user_id")+"&room_id="+Utils.getSharedPrefrences(mContext, "room_id");

			Log.i(">>>handleAbnormalStudent<<<", url);

			StringRequest stringRequest = new StringRequest(url, new Response.Listener<String>() {

				@Override
				public void onResponse(String response) {

					Log.e(TAG, response);

					Gson gson = new Gson();

					AbnormalStudentBean statu = new AbnormalStudentBean();

					statu = gson.fromJson(response, AbnormalStudentBean.class);

					if (statu.getCode() == 1) {

						Log.e(TAG, "异常考生处理成功");
						FragmentManager manager = ((MainActivity) mContext).getFragmentManager();
						manager.beginTransaction().replace(R.id.fragment_draw, new FragmentReady()).commit();

						
					} else {
						mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.ERROR_TYPE, false);
						mTipsDialog.setTitleText("异常考生处理失败");
						mTipsDialog.setContentText("错误码=" + statu.getCode());
						mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

							@Override
							public void onClick(SweetAlertDialog sweetAlertDialog) {
								mTipsDialog.dismiss();
							}
						});
						mTipsDialog.show();
					}
				}
			}, errorListener());
			requestQueue.add(stringRequest);
		}

	// 发送开始考试的网络请求
	private void getStartTime() {

		RequestQueue requestQueue = Volley.newRequestQueue(mContext);

		String url = BaseActivity.mSUrl + Constant.BEGIN_EXAM + "?student_id="
				+ Utils.getSharedPrefrences(mContext, "student_id") + "&station_id="
				+ Utils.getSharedPrefrences(mContext, "station_id") + "&user_id="
				+ Utils.getSharedPrefrences(mContext, "user_id");

		Log.i(">>>getStartTime<<<", url);

		StringRequest stringRequest = new StringRequest(url, new Response.Listener<String>() {

			@Override
			public void onResponse(String response) {

				Log.e(TAG, response);

				Gson gson = new Gson();

				StudentStatuBean statu = new StudentStatuBean();

				statu = gson.fromJson(response, StudentStatuBean.class);

				if (statu.getCode() == 1) {

					Log.e(TAG, statu.getData().getStart_time());

					Utils.saveSharedPrefrences(mContext, "startTime", statu.getData().getStart_time());

					if (!Utils.getSharedPrefrences(mContext, "type").equals(Constant.THEORY_STATION)) {
						Intent intent = new Intent(mContext, GradeActivity.class);
						startActivity(intent);
					}
				} else {
					mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.ERROR_TYPE, false);
					mTipsDialog.setTitleText("请求失败");
					mTipsDialog.setContentText("错误码=" + statu.getCode());
					mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

						@Override
						public void onClick(SweetAlertDialog sweetAlertDialog) {
							mTipsDialog.dismiss();
						}
					});
					mTipsDialog.show();
				}
			}
		}, errorListener());
		requestQueue.add(stringRequest);
	}

	public void chooseRequestType(int methodCode) {

		if (methodCode == BEGIN) {

			mbeginTest.setText("结束当前考生的考试");

		} else if (methodCode == END) {

			mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE, false);
			mTipsDialog.setTitleText("确定结束考试?");
			mTipsDialog.setContentText("考试结束不可恢复!");
			mTipsDialog.setCancelText("取消");
			mTipsDialog.setConfirmText("确定");
			mTipsDialog.showCancelButton(true);
			mTipsDialog.setCancelClickListener(new SweetAlertDialog.OnSweetClickListener() {
				@Override
				public void onClick(SweetAlertDialog sDialog) {

					mTipsDialog.dismiss();

				}
			});
			mTipsDialog.setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
				@Override
				public void onClick(SweetAlertDialog sDialog) {

					mTipsDialog.dismiss();

					forceEnd();// 强制结束
				}
			});
			mTipsDialog.show();
		}
	}

	// 确认是否为替考警告
	private void sureWarnType() {

		final HashMap<String, String> params = new HashMap<>();
		params.put("exam_id", Utils.getSharedPrefrences(mContext, "exam_id"));
		params.put("student_id", Utils.getSharedPrefrences(mContext, "student_id"));
		params.put("exam_screening_id", Utils.getSharedPrefrences(mContext, "exam_screening_id"));
		// 只标记为替考
		mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE, true);
		mTipsDialog.setTitleText("确定标记当前考生为替考");
		mTipsDialog.setCancelText("标记为替考");
		mTipsDialog.setConfirmText("终止考试");
		mTipsDialog.showCancelButton(true);
		// 取消
		mTipsDialog.setCancelClickListener(new SweetAlertDialog.OnSweetClickListener() {
			@Override
			public void onClick(final SweetAlertDialog sDialog) {

				params.put("mode", "1");// 标记处理模式
				sDialog.setTitleText("该考生已标记为替考");
				sDialog.setContentText("发送替考请求，考试继续！");
				sDialog.setConfirmText("确认");
				sDialog.showCancelButton(false);
				sDialog.setCancelClickListener(null);
				sDialog.setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {

					@Override
					public void onClick(SweetAlertDialog sweetAlertDialog) {
						sendWarnRequest(params);
						while (sendWarn_state) {
							sDialog.dismiss();
							break;
						}
					}
				}).changeAlertType(SweetAlertDialog.SUCCESS_TYPE);
			}
		});
		// 直接终止考试
		mTipsDialog.setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {
			@Override
			public void onClick(final SweetAlertDialog sDialog) {
				params.put("mode", "2");// 终止处理模式
				Utils.saveSharedPrefrences(mContext, "warn", "true");// 本地标记为警告
				sendWarnRequest(params);
				while (sendWarn_state) {
					sDialog.setTitleText("当前考生已终止考试！");
					sDialog.setConfirmText("确认");
					sDialog.showCancelButton(false);
					sDialog.setCancelClickListener(null);
					sDialog.setConfirmClickListener(new SweetAlertDialog.OnSweetClickListener() {

						@Override
						public void onClick(SweetAlertDialog sweetAlertDialog) {

							sDialog.dismiss();

							// 替换到抽签碎片
							((MainActivity) mContext).getFragmentManager().beginTransaction()
									.replace(R.id.fragment_draw, new FragmentDrawTips()).commit();
						}
					}).changeAlertType(SweetAlertDialog.SUCCESS_TYPE);
					break;
				}
			}
		});
		mTipsDialog.show();
	}

	// 发送替考警告或直接终止考试请求
	private void sendWarnRequest(final HashMap<String, String> pramas) {
		try {
			GsonRequest<BaseInfo> warnRequest = new GsonRequest<BaseInfo>(Method.POST,
					BaseActivity.mSUrl + Constant.WARN_EXAM, BaseInfo.class, null, pramas, new Listener<BaseInfo>() {

						@Override
						public void onResponse(BaseInfo arg0) {

							int returnCode = arg0.getCode();

							if (arg0.getCode() == 1) {

								sendWarn_state = true;

								Utils.saveSharedPrefrences(mContext, "warn", "true");

								if (pramas.get("mode") == "1") {
									Toast.makeText(mContext, "替考标记成功", Toast.LENGTH_SHORT).show();
								}
								if (pramas.get("mode") == "2") {
									Toast.makeText(mContext, "终止考试成功", Toast.LENGTH_SHORT).show();
									FragmentManager manager = ((MainActivity) mContext).getFragmentManager();
									manager.beginTransaction()
											.replace(R.id.fragment_draw, new FragmentDrawTips(), "tips").commit();
								}
							} else if (returnCode != 1) {

								sendWarn_state = false;

								if (pramas.get("mode") == "1") {
									mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE, false);
									mTipsDialog.setTitleText("替考标记失败");
									mTipsDialog.setContentText("错误码" + arg0.getCode());
									mTipsDialog.setCancelText("取消");
									mTipsDialog.setConfirmText("确定");
									mTipsDialog.showCancelButton(true);
									mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

										@Override
										public void onClick(SweetAlertDialog sweetAlertDialog) {
											mTipsDialog.dismiss();
										}
									});
								}
								if (pramas.get("mode") == "2") {
									mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE, false);
									mTipsDialog.setTitleText("终止考试失败");
									mTipsDialog.setContentText("错误码" + arg0.getCode());
									mTipsDialog.setCancelText("取消");
									mTipsDialog.setConfirmText("确定");
									mTipsDialog.showCancelButton(true);
									mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

										@Override
										public void onClick(SweetAlertDialog sweetAlertDialog) {
											mTipsDialog.dismiss();
										}
									});
								}
								mTipsDialog.show();
							}
						}
					}, errorListener());
			executeRequest(warnRequest);
		} catch (

		Exception e) {
			sendWarn_state = false;
			Toast.makeText(mContext, "发送替考警告返回数据有误！", Toast.LENGTH_SHORT).show();

		}
	}

	// 结束当前考试
	private void forceEnd() {

		RequestQueue requestQueue = Volley.newRequestQueue(mContext);

		String url = BaseActivity.mSUrl + Constant.CHANGE_STATUS + "?student_id="
				+ Utils.getSharedPrefrences(mContext, "student_id") + "&user_id="
				+ Utils.getSharedPrefrences(mContext, "user_id") + "&station_id="
				+ Utils.getSharedPrefrences(mContext, "station_id");
		Log.e(">>>End Exam Url<<<", url);
		StringRequest stringRequest = new StringRequest(url, new Response.Listener<String>() {
			@Override
			public void onResponse(String response) {
				Log.e(TAG, response);
				Gson gson = new Gson();
				EndExamBean statu = null;

				try {
					statu = gson.fromJson(response, EndExamBean.class);
				} catch (Exception e) {

					Toast.makeText(mContext, "结束考试数据有误！", Toast.LENGTH_SHORT).show();
					return;
				}

				if (statu.getCode() == 1) {

					mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.SUCCESS_TYPE, false);

					mTipsDialog.setTitleText("考试已结束!");
					mTipsDialog.setContentText("当前考试已结束!");
					mTipsDialog.setConfirmText("OK");
					mTipsDialog.showCancelButton(false);
					mTipsDialog.setCancelClickListener(null);
					mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

						@Override
						public void onClick(SweetAlertDialog sweetAlertDialog) {

							mTipsDialog.dismiss();

							FragmentManager manager = ((MainActivity) mContext).getFragmentManager();
							manager.beginTransaction().replace(R.id.fragment_draw, new FragmentDrawTips(), "tips")
									.commit();
						}
					}).show();
				} else {

					mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.ERROR_TYPE, false);

					mTipsDialog.setTitleText("考试结束失败");
					mTipsDialog.setContentText("错误码" + statu.getCode());
					mTipsDialog.setConfirmText("OK");
					mTipsDialog.showCancelButton(false);
					mTipsDialog.setCancelClickListener(null);
					mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

						@Override
						public void onClick(SweetAlertDialog sweetAlertDialog) {

							mTipsDialog.dismiss();
						}
					}).show();
				}
			}
		}, errorListener());
		requestQueue.add(stringRequest);
	}

	private Response.ErrorListener errorListener() {
		return new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				error.printStackTrace();
			}
		};
	}

	private boolean executeRequest(Request<?> request) {
		if (!NetStatus.isNetworkConnected(mContext)) {
			Out.Toast(mContext, "网络异常！");
			return false;
		}
		RequestManager rmg = new RequestManager(mContext);
		rmg.addRequest(request, mContext);
		return true;
	}
}
