package com.mx.osce;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.android.volley.Request.Method;
import com.android.volley.Response.Listener;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.GradePointBean;
import com.mx.osce.bean.GradePointBeanInfor;
import com.mx.osce.bean.StudentStatuBean;
<<<<<<< remotes/origin/osce_pad.3.3
import com.mx.osce.custom.ConutDownTimer_Dialog;
=======
<<<<<<< HEAD
=======
import com.mx.osce.custom.ConutDownTimer_Dialog;
>>>>>>> 2ec0224ae8695f41e9d7e211b59762a4529dbdb8
>>>>>>> local
import com.mx.osce.custom.CustomAnticlockwise;
import com.mx.osce.exception.ControlException;
import com.mx.osce.fragment.FragmentEvaluate;
import com.mx.osce.fragment.FragmentGrade;
import com.mx.osce.save.FileUtils;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.Utils;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.FragmentManager;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

/** 评分Activity */
@SuppressLint("NewApi")
public class GradeActivity extends BaseActivity {

	private TextView mTextVideo;

	private View mImageLine;

	private Button mBtnOpen;

	public static final String TAG = "GradeActivity";
<<<<<<< remotes/origin/osce_pad.3.3

=======
<<<<<<< HEAD
	
>>>>>>> local
	// 评分的碎片
	private FragmentGrade mFragmentGrade;

	// 考点详情
	private ArrayList<GradePointBean> mGradeListData;

	// 考站名字
	private TextView mTextTitleStationName;
<<<<<<< remotes/origin/osce_pad.3.3
=======
	
	// 考试倒计时
	// private CustomAnticlockwise mTimer;
	private TextView mTimer;

	private ReceiveBroadCast receiveBroadCast;

	Handler hander;

	String message = "";

	Intent intent;
=======

	// 评分的碎片
	private FragmentGrade mFragmentGrade;

	// 考点详情
	private ArrayList<GradePointBean> mGradeListData;

	// 考站名字
	private TextView mTextTitleStationName;
>>>>>>> 2ec0224ae8695f41e9d7e211b59762a4529dbdb8
>>>>>>> local

	private SweetAlertDialog mTipsDialog;

	private ArrayList<Integer> mUploadImage;

	private CustomAnticlockwise mTime;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState, this);

		setContentView(R.layout.activity_grade);

		mGradeListData = new ArrayList<GradePointBean>();

		mFragmentGrade = new FragmentGrade();

		// 判断加载数据来源
		if (ControlException.checkEvaluateIsNormal(this) && ControlException.checkGradeIsNormal(this)) {
			getNetDataMessage();
			startExamRequest();
		} else {
			addFragmentException();
		}
		findWidget();
		initCustomActionBar();
		ifSPExam();
	}
	private void ifSPExam(){
		String teacher_type=Utils.getSharedPrefrences(this, "teacher_type");
		String exam_name=Utils.getSharedPrefrences(this, "station_name");
		String exam_LimitTime=Utils.getSharedPrefrences(this, "exam_LimitTime");
		if(teacher_type!=null&&teacher_type.equalsIgnoreCase(Constant.TEACHER_TYPE_SP)){
			showSPDialog(exam_name,Long.parseLong(exam_LimitTime));
		}
	}
	// 当考试类型为SP时 弹出倒计时窗口
			private void showSPDialog(String exam_name, long exam_time) {
				ConutDownTimer_Dialog conutDownTimer_Dialog=new ConutDownTimer_Dialog(this);
				conutDownTimer_Dialog.setExam_name(exam_name);
				conutDownTimer_Dialog.setExam_time(exam_time);
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
		Type type = new TypeToken<List<GradePointBean>>() {
		}.getType();

		String scoreValue = FileUtils.recoveryDataToActivity(this, FileUtils.EXCEPTION_SCORE_FILE);

		if (scoreValue == null) {// 解决本地为保存数据为null的异常

			return;
		}
		String str = scoreValue.substring(5, scoreValue.length());

		mGradeListData = new Gson().fromJson(str, type);

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
	private void addGradeFragment(ArrayList<GradePointBean> data) {

		Bundle gradeBundle = new Bundle();

		gradeBundle.putParcelableArrayList("pointList", data);

		mFragmentGrade.setArguments(gradeBundle);

		FragmentManager manager = getFragmentManager();

		manager.beginTransaction().add(R.id.frame, mFragmentGrade, "grade").commit();

	}

	// 初始化控件
	private void findWidget() {
		mTextTitleStationName = (TextView) findViewById(R.id.textView_testStation);
		mTextTitleStationName.setText(Utils.getSharedPrefrences(GradeActivity.this, "station_name"));
		mTime = (CustomAnticlockwise) findViewById(R.id.textView_Time);
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
				if ("打开评分标准".equals(mBtnOpen.getText().toString())) {
					mFragmentGrade.setListViewVisiable(true);
					mBtnOpen.setText("关闭评分标准");
				} else {
					mFragmentGrade.setListViewVisiable(false);
					mBtnOpen.setText("打开评分标准");
				}
			}
		});
	}

	// 发送考点详情的网络请求，Get;
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

					if (infor.getCode() == 1) {

						closeProgressDialog();

						Gson gon = new Gson();

						Log.e("~~~Check Point Json~~~", gon.toJson(infor));

						List<GradePointBean> data = infor.getData();

						if (data.size() > 0 && data != null) {

							for (int i = 0; i < data.size(); i++) {

								mGradeListData.add(data.get(i));
							}
							addGradeFragment(mGradeListData);
						}
					} else {
						closeProgressDialog();

						showGetPointDefaultDialog(SweetAlertDialog.ERROR_TYPE, "主动获取考点详情失败", "点击手动获取考点详情");
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

	// 网络请求，记录学生开始考试时间
	private void startExamRequest() {

		String url = BaseActivity.mSUrl + Constant.BEGIN_EXAM + "?student_id="
				+ Utils.getSharedPrefrences(GradeActivity.this, "student_id") + "&station_id="
				+ Utils.getSharedPrefrences(GradeActivity.this, "station_id") + "&user_id="
				+ Utils.getSharedPrefrences(GradeActivity.this, "user_id");

		Log.e(">>>Start Exam Url<<<", url);

		try {

			StringRequest stringRequest = new StringRequest(url, new Response.Listener<String>() {

				@Override
				public void onResponse(String response) {

					StudentStatuBean statu = new Gson().fromJson(response, StudentStatuBean.class);

					if (statu.getCode() == 1) {

						Utils.saveSharedPrefrences(GradeActivity.this, "startTime", statu.getData().getStart_time());

					} else {

						showStartDefalutDialog(SweetAlertDialog.ERROR_TYPE, "手动点击开始考试", "主动开始考试失败");
					}
				}
			}, new Response.ErrorListener() {
				@Override
				public void onErrorResponse(VolleyError error) {
					Log.e(TAG, error.getMessage(), error);
				}
			});

			executeRequest(stringRequest);

		} catch (Exception e) {

			Toast("开启考试异常");

		}

	}

	@Override
	public void onClick(View v) {
	}

	// 考点网络失败提示
	private void showGetPointDefaultDialog(int type, String title, String content) {
		if (mTipsDialog == null) {
			mTipsDialog = new SweetAlertDialog(GradeActivity.this, type,false);

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
		} else {
			mTipsDialog.show();
		}
	}

	// 记录考生开始考试时间提示
	private void showStartDefalutDialog(int type, String title, String content) {
		if (mTipsDialog == null) {
			mTipsDialog = new SweetAlertDialog(GradeActivity.this, type,false);

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

					startExamRequest();

					mTipsDialog.dismiss();
				}
			});
			mTipsDialog.show();
		} else {
			mTipsDialog.show();
		}

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
								Log.i("request return String", arg0.toString());
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
}