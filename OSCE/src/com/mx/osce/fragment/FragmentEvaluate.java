package com.mx.osce.fragment;

import java.io.File;
import java.io.ObjectInputStream.GetField;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import com.android.volley.Request;
import com.android.volley.Request.Method;
import com.android.volley.Response;
import com.android.volley.Response.Listener;
import com.android.volley.VolleyError;
import com.google.gson.Gson;
import com.mx.osce.BaseActivity;
import com.mx.osce.GradeActivity;
import com.mx.osce.MainActivity;
import com.mx.osce.R;
import com.mx.osce.MediaPlayerActivity;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.GradePointBean;
import com.mx.osce.custom.CustomAnticlockwise;
import com.mx.osce.exception.ControlException;
import com.mx.osce.save.FileUtils;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.NetStatus;
import com.mx.osce.util.Out;
import com.mx.osce.util.RequestManager;
import com.mx.osce.util.Utils;

import android.app.Activity;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RatingBar;
import android.widget.RatingBar.OnRatingBarChangeListener;
import android.widget.TextView;
import android.widget.Toast;

/***
 * 评价碎片
 * 
 * @author PengCangXu
 *
 */
public class FragmentEvaluate extends Fragment implements OnClickListener {

	public static final String TAG = "FragmentEvaluate";

	public interface onEvaluateListener {
		void onEvaluate();
	}

	private TextView mTextName;// 考生姓名
	private EditText mEditEvaluate;
	private String mEvaluateString;
	private RatingBar mStarOperate;// 连贯性
	private int mIntOperate;
	private RatingBar mStarProficiency;// 娴熟度
	private int mIntProficiency;
	private RatingBar mStarCare;// 病人关怀
	private int mIntCare;
	private RatingBar mStarCommunicate;// 沟通
	private int mIntCommunicate;
	private TextView mTextScore;// 总分
	private Button mBtnCheck;// 查看详情
	private Button mBtnCommit, mBtnCheak;// 提交成绩
	private ArrayList<Integer> imageArray = new ArrayList<Integer>();
	private ArrayList<GradePointBean> mUploadSocore;
	private Context mContext;
	private boolean mEvaluateIsNormalEnd = false;// 判断上一次是否是正常结束，默认是false

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		View view = inflater.inflate(R.layout.fragment_evaluate, null);
		mContext = getActivity();
		reciveData();
		initWidget(view);
		onClickEvents();
		return view;
	}

	// 接受数据
	private void reciveData() {
		Bundle bundle = getArguments();
		mUploadSocore = bundle.getParcelableArrayList("uploadScoreList");
		imageArray = bundle.getIntegerArrayList("imageArray");
		Gson gson = new Gson();
		String jsonStr = gson.toJson(mUploadSocore);
		Log.i(TAG + "json", jsonStr);
	}

	// 初始化 控件
	private void initWidget(View view) {
		String startTime = (Utils.getSharedPrefrences(mContext, "startTime"));
		String endTime = (Utils.getSharedPrefrences(mContext, "endTime"));
		long totalSeconds = Utils.Str2Long(endTime, "yyyy-MM-dd HH:mm:ss")
				- Utils.Str2Long(startTime, "yyyy-MM-dd HH:mm:ss");
		int min = (int) (totalSeconds / 60000);
		int seconds = (int) (totalSeconds % 60000 / 1000);
		mTextName = (TextView) view.findViewById(R.id.text_name);
		mTextName.setText(Utils.getSharedPrefrences(mContext, "student_name") + "考试用时:" + min + "分钟" + seconds + "秒");
		mEditEvaluate = (EditText) view.findViewById(R.id.edit_evaluate);
		mStarOperate = (RatingBar) view.findViewById(R.id.star_operate);
		mStarProficiency = (RatingBar) view.findViewById(R.id.star_proficiency);
		mStarCare = (RatingBar) view.findViewById(R.id.star_care);
		mStarCommunicate = (RatingBar) view.findViewById(R.id.star_communicate);
		mTextScore = (TextView) view.findViewById(R.id.text_score);
		mBtnCheck = (Button) view.findViewById(R.id.btn_check);
		mBtnCommit = (Button) view.findViewById(R.id.btn_commit);

		showScore();// 页面可见时候统计分数

		if (!ControlException.checkEvaluateIsNormal(mContext)) {// 回复数据
			mStarOperate.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "operation")));
			mStarProficiency.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "skilled")));
			mStarCare.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "patient")));
			mStarCommunicate.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "affinity")));
			mEditEvaluate.setText(Utils.getSharedPrefrencesByName(mContext, "Exception", "evaluate"));
		}

	}

	// 点击事件
	private void onClickEvents() {
		mBtnCommit.setOnClickListener(this);
		// mBtnCheak.setOnClickListener(this);
		mEditEvaluate.addTextChangedListener(new TextWatcher() {
			@Override
			public void onTextChanged(CharSequence s, int start, int before, int count) {
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count, int after) {
				mEvaluateString = mEditEvaluate.getText().toString().trim();
			}

			@Override
			public void afterTextChanged(Editable s) {
				mEvaluateString = mEditEvaluate.getText().toString().trim();
			}
		});
		// 连贯性星级
		mStarOperate.setOnRatingBarChangeListener(new OnRatingBarChangeListener() {
			@Override
			public void onRatingChanged(RatingBar ratingBar, float rating, boolean fromUser) {
				mIntOperate = (int) rating;
			}
		});

		// 娴熟星级
		mStarProficiency.setOnRatingBarChangeListener(new OnRatingBarChangeListener() {
			@Override
			public void onRatingChanged(RatingBar ratingBar, float rating, boolean fromUser) {
				mIntProficiency = (int) rating;
			}
		});

		// 关怀星级
		mStarCare.setOnRatingBarChangeListener(new OnRatingBarChangeListener() {
			@Override
			public void onRatingChanged(RatingBar ratingBar, float rating, boolean fromUser) {
				mIntCare = (int) rating;
			}
		});

		// 沟通星级
		mStarCommunicate.setOnRatingBarChangeListener(new OnRatingBarChangeListener() {
			@Override
			public void onRatingChanged(RatingBar ratingBar, float rating, boolean fromUser) {
				mIntCommunicate = (int) rating;
			}
		});
	}

	/** 统计总分 */
	public void showScore() {
		int score = 0;
		for (int i = 0; i < mUploadSocore.size(); i++) {
			for (int j = 0; j < mUploadSocore.get(i).test_term.size(); j++) {
				score = Integer.parseInt(mUploadSocore.get(i).getTest_term().get(j).getReal()) + score;
			}
		}
		mTextScore.setText(score + "");
	}

	@Override
	public void onStart() {
		super.onStart();
		// 暂停计时
		Button ponit = (Button) getActivity().findViewById(R.id.btn_point);
		ponit.setVisibility(View.GONE);// 隐藏
		View line = getActivity().findViewById(R.id.image_line);
		line.setVisibility(View.GONE);
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		// TODO 切换到评分详情碎片
		case R.id.btn_check:
			getFragmentManager().beginTransaction().replace(R.id.fragment_draw, new FragmentGrade()).commit();
			break;
		case R.id.btn_commit:// 点击提交时间处理
			uploadScoreRequest();
			break;
		}
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
		// Out.out("xxxxx===" + request.getUrl());
		if (!NetStatus.isNetworkConnected(mContext)) {
			Out.Toast(mContext, "网络异常！");
			return false;
		}
		RequestManager rmg = new RequestManager(mContext);
		rmg.addRequest(request, mContext);
		return true;
	}

	/** 上传成绩 */
	private void uploadScoreRequest() {
		Map<String, String> params = new HashMap<String, String>();
		params.put("student_id", Utils.getSharedPrefrences(mContext, "student_id"));
		params.put("station_id", Utils.getSharedPrefrences(mContext, "station_id"));
		params.put("teacher_id", Utils.getSharedPrefrences(mContext, "user_id"));
		params.put("exam_screening_id", Utils.getSharedPrefrences(mContext, "exam_screening_id"));
		params.put("begin_dt", Utils.getSharedPrefrences(mContext, "startTime"));
		params.put("end_dt", Utils.getSharedPrefrences(mContext, "endTime"));
		params.put("operation", mIntOperate + "");
		params.put("skilled", mIntProficiency + "");
		params.put("patient", mIntCare + "");
		params.put("affinity", mIntCommunicate + "");
		params.put("evaluate", mEvaluateString + "");
		params.put("upload_image_return", paser(imageArray));
		Gson gson = new Gson();
		String jsonStr = gson.toJson(mUploadSocore);
		Log.i(TAG + "json", jsonStr);
		params.put("score", jsonStr);
		try {
			GsonRequest<BaseInfo> uploadResult = new GsonRequest<BaseInfo>(Method.POST,
					BaseActivity.mSUrl + Constant.UPLOAD_SCORE, BaseInfo.class, null, params, new Listener<BaseInfo>() {

						@Override
						public void onResponse(BaseInfo arg0) {

							if (arg0.getCode() == 1) {
								mEvaluateIsNormalEnd = true;
								Toast.makeText(mContext, getResources().getString(R.string.upload_score_success),
										Toast.LENGTH_SHORT).show();

								startActivity(new Intent(mContext, MainActivity.class));
								((Activity) mContext).finish();
							} else {
								Toast.makeText(mContext, getResources().getString(R.string.upload_score_false),
										Toast.LENGTH_SHORT).show();
								Log.i("request return String", arg0.toString());
								Log.i(TAG, arg0.getCode() + "");
								Log.e(TAG, arg0.getMessage());
							}
						}
					}, errorListener());
			executeRequest(uploadResult);
		} catch (Exception e) {
			Toast.makeText(mContext, "上传成绩返回数据有误！", Toast.LENGTH_SHORT).show();
		}
	}

	// 保存异常退出的数据
	private void saveDataOnStop(Context context) {

		if (mEvaluateIsNormalEnd == false) {// 异常退出，保存相关数据
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluateIsNormalEnd", "false");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "operation", mIntOperate + "");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "skilled", mIntProficiency + "");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "patient", mIntCare + "");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "affinity", mIntCommunicate + "");
			if (mEvaluateString != null) {
				Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluate", mEvaluateString);
			}
		} else {
			// 考生正常结束考试后，数据丢弃
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluateIsNormalEnd", null);
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "gradeIsNormalEnd", null);
			Utils.saveSharedPrefrences(mContext, "warn", null);// 丢弃本地标记为警告
		}
	}

	@Override
	public void onStop() {
		super.onStop();
		saveDataOnStop(mContext);
	}

	/** 处理上传图片返回id */
	private String paser(ArrayList<Integer> array) {

		if (array == null || array.size() == 0) {
			return "";
		} else {
			StringBuffer sb = new StringBuffer();
			for (int i : array) {
				sb.append(i + ",");
			}
			return sb.toString().substring(0, sb.toString().length() - 1);
		}

	}
}
