package com.mx.bluetooth;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import com.android.volley.Request.Method;
import com.android.volley.Response;
import com.android.volley.Response.Listener;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.mx.bluetooth.bean.BaseInfo;
import com.mx.bluetooth.bean.GradePointBeanInfor;
import com.mx.bluetooth.bean.GradePointBean_Net;
import com.mx.bluetooth.bean.RemianTimeBean;
import com.mx.bluetooth.bean.RemianTimeBean.Time;
import com.mx.bluetooth.custom.ConutDownTimer_Dialog_New;
import com.mx.bluetooth.custom.CustomTimer;
import com.mx.bluetooth.custom.LoadingDialog;
import com.mx.bluetooth.custom.ScorePreView_Dialog;
import com.mx.bluetooth.custom.ScorePreView_Dialog.Dialogcallback;
import com.mx.bluetooth.exception.ControlException;
import com.mx.bluetooth.fragment.FragmentEvaluate;
import com.mx.bluetooth.fragment.FragmentEvaluate.Show_PreViewInf_uponFragmentEvaluate;
import com.mx.bluetooth.fragment.FragmentGrade;
import com.mx.bluetooth.log.SaveNetRequestLog2Local;
import com.mx.bluetooth.save.FileUtils;
import com.mx.bluetooth.util.Constant;
import com.mx.bluetooth.util.GsonRequest;
import com.mx.bluetooth.util.Utils;
import android.app.FragmentManager;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

/** 评分Activity */
public class GradeActivity extends BaseActivity implements Show_PreViewInf_uponFragmentEvaluate {

	public static final String TAG = "GradeActivity";

	private TextView mTextVideo;

	private View mImageLine;

	private Button mBtnOpen;

	// 评分的碎片
	private FragmentGrade mFragmentGrade;

	// 考点详情
	private ArrayList<GradePointBean_Net> mGradeListData;

	// 考站名字，考试剩余时间
	private TextView mTextTitleStationName, mTvTimeTips;

	public CustomTimer mTimer;

	private SweetAlertDialog mTipsDialog;

	private ArrayList<Integer> mUploadImage;

	// private CustomAnticlockwise mTimer;

	private ConutDownTimer_Dialog_New conutDownTimer_Dialog;

	private LoadingDialog loadingDialog;

	private long mRemindTimeSeconds;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState, this);

		setContentView(R.layout.activity_grade);

		this.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

		mGradeListData = new ArrayList<GradePointBean_Net>();

		mFragmentGrade = new FragmentGrade();

		// 判断加载数据来源
		if (ControlException.checkEvaluateIsNormal(this) && ControlException.checkGradeIsNormal(this)) {
			if (SaveNetRequestLog2Local.CheckExamCache(Utils.getSharedPrefrences(this, "station_id"))) {
				getLocalDataMessage(Utils.getSharedPrefrences(this, "station_id"));
			} else {
				getNetDataMessage();
			}
		} else {
			addFragmentException();
		}
		findWidget();
		initCustomActionBar();
		getExamRemindTime();
	}

	// 得到考试剩余时间
	private void getExamRemindTime() {

		String examTimeUrl = BaseActivity.mSUrl + Constant.GET_TIME + "?student_id="
				+ Utils.getSharedPrefrences(this, "student_id") + "&exam_id="
				+ Utils.getSharedPrefrences(this, "exam_id");

		Log.e(">>>examTimeUrl<<<", examTimeUrl);

		GsonRequest<RemianTimeBean> timeRequest = new GsonRequest<RemianTimeBean>(examTimeUrl, RemianTimeBean.class,
				new Listener<RemianTimeBean>() {

					@Override
					public void onResponse(RemianTimeBean arg0) {
						Time bean = null;

						switch (arg0.getCode()) {
						case 1:
							bean = arg0.getTime();
							int timeSeconds = bean.getRemainTime();

							initTimer(timeSeconds);
							if (conutDownTimer_Dialog != null) {
								conutDownTimer_Dialog.setExam_time(timeSeconds);
							}

							break;
						default:
							break;
						}
					}
				}, errorListener());
		executeRequest(timeRequest);
	}

	private void initTimer(long timeSeconds) {
		if (mTimer != null) {
			mTimer.cancel();
		}
		if (null != mTvTimeTips) {
			mTimer = new CustomTimer(GradeActivity.this, mTvTimeTips, timeSeconds * 1000, 1000);
			mTimer.start();
		}
	}

	// private void ifSPExam(long spTimeSeconds) {
	private void ifSPExam() {
		String teacher_type = Utils.getSharedPrefrences(this, "teacher_type");
		String exam_name = Utils.getSharedPrefrences(this, "station_name");
		String exam_LimitTime = Utils.getSharedPrefrences(this, "exam_LimitTime");

		if (teacher_type != null && teacher_type.equalsIgnoreCase(Constant.TEACHER_TYPE_SP)) {

			showSPDialog(exam_name, Long.parseLong(exam_LimitTime));
		}
	}

	// 当考试类型为SP时 弹出倒计时窗口
	private void showSPDialog(String exam_name, long exam_time) {

		conutDownTimer_Dialog = new ConutDownTimer_Dialog_New(this);
		conutDownTimer_Dialog.setExam_name(exam_name);
		conutDownTimer_Dialog.setExam_time(exam_time * 60);
		// conutDownTimer_Dialog.setExam_time(exam_time);
		conutDownTimer_Dialog.setDialogCallback(new ConutDownTimer_Dialog_New.Dialogcallback() {

			@Override
			public void dialogdo(String string) {
				mTvTimeTips.setVisibility(View.VISIBLE);
				// 手动点击计算用时
				String endStr = Utils.getSharedPrefrences(GradeActivity.this, "endTime");

				if (null != endStr) {
					return;
				} else {
					mFragmentGrade.changeStudnetState(false);
				}
			}

		});
		conutDownTimer_Dialog.show();

	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {

		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == RESULT_OK || resultCode == RESULT_CANCELED) {
			if (requestCode == Constant.CAMERA_REQUEST) {
				mFragmentGrade.onActivityResult(requestCode, resultCode, data);
			}
		}
	}

	// 加载异常退出的碎片
	private void addFragmentException() {

		Type type = new TypeToken<List<GradePointBean_Net>>() {
		}.getType();

		String scoreValue = FileUtils.recoveryDataToActivity(this, FileUtils.EXCEPTION_SCORE_FILE);

		if (scoreValue == null) {// 解决本地为保存数据为null的异常

			return;
		}

		mGradeListData = new Gson().fromJson(scoreValue, type);

		Bundle recoveryExceptionData = new Bundle();

		if (!ControlException.checkGradeIsNormal(this)) {// 评分碎片异常退出

			recoveryExceptionData.putParcelableArrayList("pointList", mGradeListData);

			mFragmentGrade.setArguments(recoveryExceptionData);

			FragmentManager manager = getFragmentManager();

			manager.beginTransaction().add(R.id.frame, mFragmentGrade, "grade").commit();

		} else if (!ControlException.checkEvaluateIsNormal(this)) {// 评价碎片异常退出

			recoveryExceptionData.putParcelableArrayList("uploadScoreList", mGradeListData);

			String uploadValue = FileUtils.recoveryDataToActivity(this, FileUtils.EXCEPTION_UOLOAD_FILE);

			if (uploadValue == null) {

				FragmentEvaluate evaluate = new FragmentEvaluate();

				evaluate.setArguments(recoveryExceptionData);

				FragmentManager manager = getFragmentManager();

				manager.beginTransaction().add(R.id.frame, evaluate, "evaluate").commit();

				return;
			} else {

				mUploadImage = new ArrayList<Integer>();

				mUploadImage = new Gson().fromJson(uploadValue, new TypeToken<ArrayList<Integer>>() {

				}.getType());

				recoveryExceptionData.putIntegerArrayList("imageArray", mUploadImage);
			}

			FragmentEvaluate evaluate = new FragmentEvaluate();

			evaluate.setArguments(recoveryExceptionData);

			FragmentManager manager = getFragmentManager();

			manager.beginTransaction().add(R.id.frame, evaluate, "evaluate").commit();

		}
	}

	// 正常加载碎片
	private void addGradeFragment(ArrayList<GradePointBean_Net> data) {

		Bundle gradeBundle = new Bundle();

		gradeBundle.putParcelableArrayList("pointList", data);

		mFragmentGrade.setArguments(gradeBundle);

		FragmentManager manager = getFragmentManager();

		manager.beginTransaction().add(R.id.frame, mFragmentGrade, "grade").commit();

	}

	// 初始化控件
	private void findWidget() {

		mTextTitleStationName = (TextView) findViewById(R.id.textView_testStation);
		String stationName = Utils.getSharedPrefrences(GradeActivity.this, "station_name");
		if (null != stationName) {
			mTextTitleStationName.setText(Utils.getSharedPrefrences(GradeActivity.this, "station_name"));
		}
		mTvTimeTips = (TextView) findViewById(R.id.tv_time_tips);
		// mTimer = (CustomAnticlockwise) findViewById(R.id.textView_Time);

		String teacher_type = Utils.getSharedPrefrences(this, "teacher_type");

		if (teacher_type != null && teacher_type.equalsIgnoreCase(Constant.TEACHER_TYPE_SP)) {
			mTvTimeTips.setVisibility(View.GONE);
		}
	}

	// 初始化ActionBar
	private void initCustomActionBar() {

		mTextVideo = (TextView) findViewById(R.id.tv_vdieo);

		mTextVideo.setVisibility(View.VISIBLE);

		mTextVideo.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				startActivity(new Intent(GradeActivity.this, VideoActivity.class));
			}
		});

		mImageLine = (View) findViewById(R.id.image_line);

		mImageLine.setVisibility(View.VISIBLE);

		mBtnOpen = (Button) findViewById(R.id.btn_point);

		mBtnOpen.setVisibility(View.VISIBLE);

		mBtnOpen.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				if ("关闭评分标准".equals(mBtnOpen.getText().toString())) {
					mFragmentGrade.setListViewVisiable(true);
					mBtnOpen.setText("打开评分标准");
				} else {
					mFragmentGrade.setListViewVisiable(false);
					mBtnOpen.setText("关闭评分标准");
				}
			}
		});
	}

	/**
	 * 从缓存中得到考点详情加载页面
	 * 
	 * @param station_id
	 */
	private void getLocalDataMessage(String station_id) {
		List<GradePointBean_Net> data = SaveNetRequestLog2Local.GetExamInfoFormCache(station_id);
		if (data != null && data.size() > 0) {

			for (int i = 0; i < data.size(); i++) {

				mGradeListData.add(data.get(i));
			}
			addGradeFragment(mGradeListData);
		} else {
			Toast("从缓存中获取考点失败！");
			getNetDataMessage();
		}

	}

	// 得到考点详情的网络请求，Get;
	private void getNetDataMessage() {

		openProgressDialog();

		String url = BaseActivity.mSUrl + Constant.GRADE_OPINT + "?station_id="

				+ Utils.getSharedPrefrences(GradeActivity.this, "station_id");

		Log.e(">>>Grade Point Url<<<", url);
		try {
			GsonRequest<GradePointBeanInfor> gradeRequest = new GsonRequest<GradePointBeanInfor>(Method.GET, url,

					GradePointBeanInfor.class, null, null, new Response.Listener<GradePointBeanInfor>() {

						@Override
						public void onResponse(GradePointBeanInfor infor) {

							closeProgressDialog();

							if (infor.getCode() == 1) {

								Gson gon = new Gson();

								Log.e("~~~Check Point Json~~~", gon.toJson(infor));

								List<GradePointBean_Net> data = infor.getData();

								if (data.size() > 0 && data != null) {

									for (int i = 0; i < data.size(); i++) {

										mGradeListData.add(data.get(i));
									}

									addGradeFragment(mGradeListData);

									SaveNetRequestLog2Local.ExamCache2Local(
											Utils.getSharedPrefrences(GradeActivity.this, "station_id"),
											mGradeListData);
								}
							} else {

								showGetPointDefaultDialog(SweetAlertDialog.ERROR_TYPE, "主动获取考点详情失败",
										"错误码" + infor.getCode());
							}
						}
					}, errorListener());

			executeRequest(gradeRequest);

		} catch (Exception e) {
			closeProgressDialog();
			showGetPointDefaultDialog(SweetAlertDialog.ERROR_TYPE, "主动获取考点详情失败", "点击手动获取考点详情");
			Toast("获取考点失败");
		}
	}

	@Override
	public void onClick(View v) {
	}

	// 考点网络失败提示
	private void showGetPointDefaultDialog(int type, String title, String content) {

		mTipsDialog = new SweetAlertDialog(GradeActivity.this, type, false);

		if (title != null) {
			mTipsDialog.setTitleText(title);
		}
		if (content != null) {
			mTipsDialog.setContentText(content);
		}

		mTipsDialog.setCanceledOnTouchOutside(false);

		mTipsDialog.setCancelable(false);

		mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {

			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {

				getNetDataMessage();

				mTipsDialog.dismiss();
			}
		});
		mTipsDialog.show();
	}

	/** 屏蔽掉菜单中的返回按钮 */
	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {

		if (keyCode == KeyEvent.KEYCODE_BACK) {

			Toast("当前考试还没有结束，请不要退出！");
		}
		return false;
	}

	// 只有在技能考试的时候，由监控老师终止被发现作弊考生的考试的时候需要上传成绩再跳转到抽签页面
	@SuppressWarnings("unused")
	private void uploadScoreRequest() {
		Map<String, String> params = new HashMap<String, String>();
		params.put("student_id", Utils.getSharedPrefrences(GradeActivity.this, "student_id"));
		params.put("station_id", Utils.getSharedPrefrences(GradeActivity.this, "station_id"));
		params.put("teacher_id", Utils.getSharedPrefrences(GradeActivity.this, "user_id"));
		params.put("exam_screening_id", Utils.getSharedPrefrences(GradeActivity.this, "exam_screening_id"));
		params.put("begin_dt", Utils.getSharedPrefrences(GradeActivity.this, "startTime"));
		params.put("end_dt", Utils.getSharedPrefrences(GradeActivity.this, "endTime"));
		params.put("operation", "");
		params.put("skilled", "");
		params.put("patient", "");
		params.put("affinity", "");
		params.put("evaluate", "");
		params.put("upload_image_return", "");
		Gson gson = new Gson();
		String jsonStr = gson.toJson(mGradeListData);
		Log.i(TAG + "json", jsonStr);
		params.put("score", jsonStr);
		try {
			GsonRequest<BaseInfo> uploadResult = new GsonRequest<BaseInfo>(Method.POST,
					BaseActivity.mSUrl + Constant.UPLOAD_SCORE, BaseInfo.class, null, params, new Listener<BaseInfo>() {

						@Override
						public void onResponse(BaseInfo arg0) {

							if (arg0.getCode() == 1) {
								Toast.makeText(GradeActivity.this,
										getResources().getString(R.string.upload_score_success), Toast.LENGTH_SHORT)
										.show();

								startActivity(new Intent(GradeActivity.this, MainActivity.class));
							} else {
								Toast.makeText(GradeActivity.this,
										getResources().getString(R.string.upload_score_false), Toast.LENGTH_SHORT)
										.show();
								Log.e("request return String", arg0.toString());
								Log.i(TAG, arg0.getCode() + "");
								Log.e(TAG, arg0.getMessage());
							}
						}
					}, errorListener());
			executeRequest(uploadResult);
		} catch (Exception e) {
			Toast.makeText(GradeActivity.this, "上传成绩返回数据有误！", Toast.LENGTH_SHORT).show();
		}
	}

	@Override
	public void onResume() {
		super.onResume();
		if (conutDownTimer_Dialog == null
				&& !"false".equals(Utils.getSharedPrefrences(GradeActivity.this, "isShowSp"))) {
			ifSPExam();
		} else {

		}
	}

	public void showScore_preView(ArrayList<GradePointBean_Net> mArrayListGradePointBean_Local, String TextScore_s,
			Dialogcallback mDialogcallback) {
		ScorePreView_Dialog mScorePreView_Dialog = new ScorePreView_Dialog(this, mArrayListGradePointBean_Local,
				TextScore_s, mDialogcallback);
		mScorePreView_Dialog.show();
	}

	@Override
	public void show(ArrayList<GradePointBean_Net> mGradePointBean_Local, String TextScore_s,
			Dialogcallback mDialogcallback) {
		showScore_preView(mGradePointBean_Local, TextScore_s, mDialogcallback);

	}

	@Override
	public void showDialog() {

		if (loadingDialog == null) {
			loadingDialog = new LoadingDialog(GradeActivity.this);
		}
		if (!loadingDialog.isShowing())
			loadingDialog.show();

	}

	@Override
	public void dimissDialog() {

		if (loadingDialog != null) {
			loadingDialog.cancel();
		}
	}

	public CustomTimer getTimer() {

		return mTimer;
	}

}