package com.mx.test;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.List;

import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.mx.test.bean.GradePointBean_Net;
import com.mx.test.bean.PointTermBean;
import com.mx.test.custom.ConutDownTimer_Dialog_New;
import com.mx.test.custom.CustomTimer;
import com.mx.test.custom.LoadingDialog;
import com.mx.test.custom.ScorePreView_Dialog;
import com.mx.test.custom.ScorePreView_Dialog.Dialogcallback;
import com.mx.test.custom.TimerTextView;
import com.mx.test.exception.ControlException;
import com.mx.test.fragment.FragmentEvaluate;
import com.mx.test.fragment.FragmentEvaluate.Show_PreViewInf_uponFragmentEvaluate;
import com.mx.test.fragment.FragmentGrade;
import com.mx.test.save.FileUtils;
import com.mx.test.util.Constant;
import com.mx.test.util.Utils;

import android.app.Activity;
import android.app.FragmentManager;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;

import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

/** 评分Activity */
public class GradeActivity extends Activity implements Show_PreViewInf_uponFragmentEvaluate {

	public static final String TAG = "GradeActivity";

	private View mImageLine;

	private Button mBtnOpen;

	// 评分的碎片
	private FragmentGrade mFragmentGrade;

	// 考点详情
	private ArrayList<GradePointBean_Net> mGradeListData;

	// 考站名字，考试剩余时间
	private TextView mTextTitleStationName;

	private TimerTextView mTvTimeTips;

	public CustomTimer mTimer;

	private SweetAlertDialog mTipsDialog;

	private ArrayList<Integer> mUploadImage;

	// private CustomAnticlockwise mTimer;

	private ConutDownTimer_Dialog_New conutDownTimer_Dialog;

	private LoadingDialog loadingDialog;

	private long mRemindTimeSeconds;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_grade);

		this.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

		mGradeListData = new ArrayList<GradePointBean_Net>();
		GradePointBean_Net data = new GradePointBean_Net();
		data.setContent("术前准备");
		data.setScore("5");
		data.setTag("normal");
		ArrayList<PointTermBean> points = new ArrayList<>();
		PointTermBean point = new PointTermBean();
		point.setContent("用消毒洗手液洗手、戴口罩、帽子佩戴标准；检查物品");
		point.setScore("10");
		points.add(point);
		data.setTest_term(points);
		mGradeListData.add(data);

		GradePointBean_Net data1 = new GradePointBean_Net();
		data1.setContent("操作");
		data1.setScore("100");
		data1.setTag("normal");
		ArrayList<PointTermBean> points1 = new ArrayList<>();
		PointTermBean point1 = new PointTermBean();
		point1.setContent("检查者面向孕妇头部，两手置于宫底部，了解子宫形状；以两手指腹相对轻推，判断宫底部的胎儿部分。");
		point1.setScore("15");
		points1.add(point1);
		PointTermBean point1s = new PointTermBean();
		point1s.setContent("检查者面向孕妇头部，左右手分别置于腹部左右侧，一手固定，另手轻轻深按检查，两手交替，仔细分辨胎背及胎儿肢体的位置。");
		point1s.setScore("10");
		points1.add(point1s);
		data1.setTest_term(points1);
		mGradeListData.add(data1);


		GradePointBean_Net data2 = new GradePointBean_Net();
		data2.setContent("问诊");
		data2.setScore("100");
		data2.setTag("normal");
		ArrayList<PointTermBean> points2 = new ArrayList<>();
		PointTermBean point2 = new PointTermBean();
		point2.setContent("请考官任选一题提问： 问题1: 经产妇和初产妇的宫颈外观有何区别？     问题2：宫颈癌患者的宫颈检查有何异常？");
		point2.setScore("10");
		points2.add(point2);
		data2.setTest_term(points2);
		mGradeListData.add(data2);

		mFragmentGrade = new FragmentGrade();

		findWidget();
		initCustomActionBar();
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

	Bundle recoveryExceptionData = new Bundle();

	// 加载异常退出的碎片
	private void addFragmentException() {

		Type type = new TypeToken<List<GradePointBean_Net>>() {
		}.getType();

		String scoreValue = FileUtils.recoveryDataToActivity(this, FileUtils.EXCEPTION_SCORE_FILE);

		if (scoreValue == null) {// 解决本地为保存数据为null的异常

			return;
		}

		mGradeListData = new Gson().fromJson(scoreValue, type);


		recoveryExceptionData.putString("names",getIntent().getStringExtra("names"));

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
		mTextTitleStationName.setText("第一考场");
		mTvTimeTips = (TimerTextView) findViewById(R.id.tv_time_tips);
		mTvTimeTips.setTimes(300000L);
		if(!mTvTimeTips.isRun()){
			mTvTimeTips.start();
		}
		addGradeFragment(mGradeListData);
	}

	// 初始化ActionBar
	private void initCustomActionBar() {

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

				mTipsDialog.dismiss();
			}
		});
		mTipsDialog.show();
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