package com.mx.osce;

import java.util.HashMap;
import java.util.Map;

import com.android.volley.Request;
import com.android.volley.Response;
import com.google.gson.Gson;
import com.mx.osce.bean.LoginResultBean;
import com.mx.osce.bean.StationInfo;
import com.mx.osce.custom.LoadingDialog;
import com.mx.osce.exception.ControlException;
import com.mx.osce.service.SubscriberService;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.Utils;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetEditListener;

/** 登陆 */
public class LoginActivity extends BaseActivity {
	private long firstTime;
	private EditText mEdName;
	private EditText mEdPasword;
	private Button mLogin;
	private String mName;
	private String mPassword;
	private SweetAlertDialog mDialog;
	private SweetAlertDialog mSettingDialog;
	private String mToken;
	private String mUser_id;
	private LoadingDialog loadingDialog;
	private Handler handler;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState, this);
		setContentView(R.layout.activity_login);
		if (!ControlException.checkGradeIsNormal(this) || !ControlException.checkEvaluateIsNormal(this)) {
			showAlertDialog(this, "异常退出,请您继续考试", "检测到异常退出");
		} else {
			Utils.deleteSharedPrefrences(LoginActivity.this);
		}
		handler = new Handler();
		initActionBar();
		findWidget();
		onClickEvents();
	}

	// 初始化ActionBar
	private void initActionBar() {
		ImageButton arrowImageBtn = (ImageButton) findViewById(R.id.image_arrow);
		arrowImageBtn.setVisibility(View.VISIBLE);
		arrowImageBtn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				mBaseApp.exit();// 退出App
			}
		});

		ImageButton imageBtn = (ImageButton) findViewById(R.id.imagBtn_setting);
		imageBtn.setVisibility(View.VISIBLE);
		imageBtn.setOnClickListener(new OnClickListener() {
			// SweetAlertDialog settingDialog;
			@Override
			public void onClick(View v) {
				// settingDialog = new SweetAlertDialog(LoginActivity.this);
				mSettingDialog = new SweetAlertDialog(LoginActivity.this, false);
				mSettingDialog.setTitleText("设置考试网址");
				mSettingDialog.setContentText("例如：osce.cd.misrobot.com");
				if (BaseActivity.mSUrl == null || BaseActivity.mSUrl.length() == 0) {
					mSettingDialog.setSettingEdit("默认:osce.cd.misrobot.com");
				} else {
					mSettingDialog.setSettingEdit("当前地址：" + BaseActivity.mEasyUrl);
				}
				mSettingDialog.setCancelText("取消").setConfirmText("确定");
				mSettingDialog.showCancelButton(true);
				mSettingDialog.setConfirmClickListener(new OnSweetClickListener() {

					@Override
					public void onClick(SweetAlertDialog sweetAlertDialog) {
						((MyApplicaton) getApplication()).stopservice();

						new Thread(new Runnable() {

							@Override
							public void run() {
								handler.postDelayed(new Runnable() {
									@Override
									public void run() {
										startService(MyApplicaton.getmSubscriberIntent());
										mSettingDialog.dismiss();
									}
								}, 1000);
							}
						}).start();
					}
				});
				mSettingDialog.setSettingEditListener(new OnSweetEditListener() {
					@Override
					public void addTextChangedListener(EditText mSettingEditText) {
						// Toast(mSettingEditText.getText().toString());
						String newUrl = "http://" + mSettingEditText.getText().toString().trim();
<<<<<<< remotes/origin/osce_pad.3.3
=======
<<<<<<< HEAD

=======
>>>>>>> 2ec0224ae8695f41e9d7e211b59762a4529dbdb8
>>>>>>> local
						if (newUrl != null && newUrl.length() > 7) {
							BaseActivity.mSUrl = newUrl.replace(" ", "");
							Utils.saveSharedPrefrencesByName(LoginActivity.this, "BasciUrl", "url", BaseActivity.mSUrl);
							settingBasicUrl();
						}
					}
				});
				mSettingDialog.show();
			}
		});
	}

	private void showAlertDialog(final Context context, String title, String message) {
		mDialog = new SweetAlertDialog(context, SweetAlertDialog.ERROR_TYPE, false);
		mDialog.setTitleText(title).setContentText(message).show();
		mDialog.setCanceledOnTouchOutside(false);
		mDialog.setCancelable(false);
		mDialog.setConfirmClickListener(new OnSweetClickListener() {
			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {
				startActivity(new Intent(LoginActivity.this, GradeActivity.class));
			}
		});
	}

	private void onClickEvents() {
		mEdName.addTextChangedListener(new TextWatcher() {
			@Override
			public void onTextChanged(CharSequence s, int start, int before, int count) {
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count, int after) {
			}

			@Override
			public void afterTextChanged(Editable s) {
				mName = mEdName.getText().toString().trim();
			}
		});
		mEdPasword.addTextChangedListener(new TextWatcher() {
			@Override
			public void onTextChanged(CharSequence s, int start, int before, int count) {
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count, int after) {
			}

			@Override
			public void afterTextChanged(Editable s) {
				mPassword = mEdPasword.getText().toString().trim();
			}
		});
		mLogin.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {

				long clickTime = System.currentTimeMillis();
				if (clickTime - firstTime > 3000) {
					firstTime = clickTime;
					if (!checkInputLoginMessage(mName, mPassword)) {
						return;
					} else {

					}
<<<<<<< remotes/origin/osce_pad.3.3
					startService(MyApplicaton.getmSubscriberIntent());
					Utils.saveSharedPrefrences(LoginActivity.this, "user_phone", mName);
					openProgressDialog();
					doLogin(mName, mPassword);

=======
<<<<<<< HEAD
					doLogin(mName, mPassword);
					// startActivity(new Intent(LoginActivity.this,
					// TestActivity.class));
=======
					startService(MyApplicaton.getmSubscriberIntent());
					Utils.saveSharedPrefrences(LoginActivity.this, "user_phone", mName);
					openProgressDialog();
					doLogin(mName, mPassword);

>>>>>>> 2ec0224ae8695f41e9d7e211b59762a4529dbdb8
>>>>>>> local
				} else {
					Toast.makeText(LoginActivity.this, "请不要重复登陆...", Toast.LENGTH_SHORT).show();
				}
			}
		});
	}

	@Override
	public void onClick(View v) {
	}

	/** 登录网络请求，Post */
	private void doLogin(String name, String psw) {
		Utils.saveSharedPrefrences(LoginActivity.this, "phone", name);
		String url = BaseActivity.mSUrl + Constant.LOGIN;
		Map<String, String> params = new HashMap<String, String>();
		params.put("username", name);
		params.put("password", psw);
		params.put("grant_type", "password");
		params.put("client_id", "ios");
		params.put("client_secret", "111");
		try {
			// 登陆网络请求
			GsonRequest<LoginResultBean> request = new GsonRequest<LoginResultBean>(Request.Method.POST, url,
<<<<<<< remotes/origin/osce_pad.3.3
=======
<<<<<<< HEAD

			LoginResultBean.class, null, params, new Response.Listener<LoginResultBean>() {

				@Override
				public void onResponse(LoginResultBean response) {

					Log.i(TAG + "dologin---", response.getUser_id());

					if (!response.getAccess_token().equals("defeat")) {
						String userid = response.getUser_id();
						// 存储登陆者id
						Utils.saveSharedPrefrences(LoginActivity.this, "user_id", userid);
						// 存储认证
						Utils.saveSharedPrefrences(LoginActivity.this, "token", response.getAccess_token());

						// 拿到考站相关信息
						getStationId(userid);
					} else {
						Toast("输入的用户名或者密码有误");
					}
				}

			}, errorListener());
=======
>>>>>>> local
					LoginResultBean.class, null, params, new Response.Listener<LoginResultBean>() {
						@Override
						public void onResponse(LoginResultBean response) {
							Log.e("DSX", new Gson().toJson(response));
							Log.i(TAG + "---dologin---", response.getUser_id());
							if (!response.getAccess_token().equals("defeat")) {
								closeProgressDialog();
								// 存储登陆者id
								mUser_id = response.getUser_id();
								mToken = response.getAccess_token();
								// 拿到考站相关信息
								getStationId(mUser_id);

							} else {
								closeProgressDialog();
								Toast("输入的用户名或者密码有误");
							}
						}
					}, errorListener());
<<<<<<< remotes/origin/osce_pad.3.3
=======
>>>>>>> 2ec0224ae8695f41e9d7e211b59762a4529dbdb8
>>>>>>> local

			executeRequest(request);
		} catch (Exception e) {
			// mLoadingDialog.setVisibility(View.INVISIBLE);
			// 显示异常
			closeProgressDialog();
			Utils.showToast(LoginActivity.this, e.toString());
		}
	}

	/**
	 * 检查用户登录信息是否有误
	 * 
	 * @param name
	 *            用户名
	 * @param pas
	 *            密码
	 * @return 验证输入信息是否正确
	 */
	private boolean checkInputLoginMessage(String name, String pas) {
		if (name == null || name.length() != 11) {
			Toast("请重新输入用户名");
			return false;
		}
		if (pas == null || pas.length() == 0) {
			Toast("请输入密码");
			return false;
		}
		return true;
	}

	/**
	 * 网络请求，得到考站相关信息，Get
	 * 
	 * @param userid
	 *            用户登录请求返回的用户id
	 */
	private void getStationId(String userid) {
		String url = BaseActivity.mSUrl + Constant.GET_STATIONID + "?id=" + userid;
		Log.e(">>>Login Sation Url<<<", url);
		try {
			openProgressDialog();
			GsonRequest<StationInfo> stationRequest = new GsonRequest<StationInfo>(Request.Method.GET, url,
					StationInfo.class, null, null, new Response.Listener<StationInfo>() {
						@Override
						public void onResponse(StationInfo response) {
							if (response.getCode() != 1) {
								closeProgressDialog();
								String message = response.getMessage();
								if (message == null) {
									Toast("系统发生未知异常");
								} else {
									Toast(message);
								}
							} else {
								closeProgressDialog();
								if (mToken != null) {
									Utils.saveSharedPrefrences(LoginActivity.this, "user_id", mUser_id);
									Utils.saveSharedPrefrences(LoginActivity.this, "token", mToken);
								}
								// 考站id
								Utils.saveSharedPrefrences(LoginActivity.this, "station_id",
										response.getData().getId());
								//
								Utils.saveSharedPrefrences(LoginActivity.this, "station_name",
										response.getData().getName());
								// 考试id
								Utils.saveSharedPrefrences(LoginActivity.this, "exam_id",
										response.getData().getExam_id());
								// 当前考试限制时间
								Utils.saveSharedPrefrences(LoginActivity.this, "exam_LimitTime",
										response.getData().getMins());
								// 当前考站的房间编号
								Utils.saveSharedPrefrences(LoginActivity.this, "room_id",
										response.getData().getRoom_id());
								// 记录当前服务器的时间
								Utils.saveSharedPrefrences(LoginActivity.this, "service_time",
										response.getData().getService_time() + "");
								// 考试类型
								Utils.saveSharedPrefrences(LoginActivity.this, "type", response.getData().getType());

								Utils.saveSharedPrefrences(LoginActivity.this, "client_time",
										System.currentTimeMillis() + "");

								Utils.saveSharedPrefrences(LoginActivity.this, "exam_screening_id",
										response.getData().getExam_screening_id() + "");

								Utils.saveSharedPrefrences(LoginActivity.this, "sequence_mode",
										response.getData().getSequence_mode());
								Utils.saveSharedPrefrences(LoginActivity.this, "teacher_type",
										response.getData().getTeacher_type());
								closeProgressDialog();
								startActivity(new Intent(LoginActivity.this, MainActivity.class));
								finish();
							}
						}
					}, errorListener());

<<<<<<< remotes/origin/osce_pad.3.3
			executeRequest(stationRequest);

=======
<<<<<<< HEAD
		Log.i("Login---sation", url);

		GsonRequest<StationInfo> stationRequest = new GsonRequest<StationInfo>(Request.Method.GET, url,
				StationInfo.class, null,

		null, new Response.Listener<StationInfo>() {

			@Override
			public void onResponse(StationInfo response) {

				if (response.getCode() != 1) {

					// if (response.getCode() == -998) {
					// Toast("当前没有正在进行的考试");
					// } else if (response.getCode() == -997) {
					// Toast("当前没有正在进行的考试");
					// } else {
					// Toast("考试还没有开始或者已结束。");
					// }

					String message = response.getMessage();

					if (message == null) {
						Toast("系统发生未知异常");
					} else {
						Toast(message);
					}

					Log.i("LoginActivity---stationMessage Error Json", response.getMessage());

				} else {

					// 考站id
					Utils.saveSharedPrefrences(LoginActivity.this, "station_id", response.getData().getId());
					//
					Utils.saveSharedPrefrences(LoginActivity.this, "station_name", response.getData().getName());
					// 考试id
					Utils.saveSharedPrefrences(LoginActivity.this, "exam_id", response.getData().getExam_id());
					// 当前考试限制时间
					Utils.saveSharedPrefrences(LoginActivity.this, "exam_LimitTime", response.getData().getMins());
					// 当前考站的房间编号
					Utils.saveSharedPrefrences(LoginActivity.this, "room_id", response.getData().getRoom_id());
					// 记录当前服务器的时间
					Utils.saveSharedPrefrences(LoginActivity.this, "service_time",
							response.getData().getService_time());
					// 考试类型
					Utils.saveSharedPrefrences(LoginActivity.this, "type", response.getData().getType());

					Utils.saveSharedPrefrences(LoginActivity.this, "client_time", System.currentTimeMillis() + "");

					Utils.saveSharedPrefrences(LoginActivity.this, "exam_screening_id",
							response.getData().getExam_screening_id() + "");

					Log.i("LoginActivity---getStationMessage--stationId", response.getData().getId());

					Log.i("LoginActivity---getStationMessage--ExamId", response.getData().getExam_id());

					if ((response.getData().getType()).equals(Constant.THEORY_STATION)) {

						startActivity(new Intent(LoginActivity.this, TheoryActivity.class));

						// startActivity(new Intent(LoginActivity.this,
						// TestActivity.class));

					} else {

						startActivity(new Intent(LoginActivity.this, MainActivity.class));
					}
				}
			}

		}, errorListener());

		executeRequest(stationRequest);

	}

	@Override
	protected void initView() {
=======
			executeRequest(stationRequest);

>>>>>>> local
		} catch (Exception e) {
			closeProgressDialog();
			Toast("考站信息认证失败");
		}
<<<<<<< remotes/origin/osce_pad.3.3
=======
>>>>>>> 2ec0224ae8695f41e9d7e211b59762a4529dbdb8
>>>>>>> local
	}

	private void findWidget() {
		mEdName = (EditText) findViewById(R.id.username_edit);
		mEdPasword = (EditText) findViewById(R.id.password_edit);
		mLogin = (Button) findViewById(R.id.button_login);
	}

	public void openProgressDialog() {
		if (loadingDialog == null) {
			loadingDialog = new LoadingDialog(this);
		}
		if (!loadingDialog.isShowing())
			loadingDialog.show();
	}

	public void closeProgressDialog() {
		if (loadingDialog != null) {
			loadingDialog.cancel();
		}
	}

}
