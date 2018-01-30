package com.mx.test.fragment;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;
import java.util.Vector;

import com.android.volley.Request;
import com.google.gson.Gson;
import com.mx.test.MainActivity;
import com.mx.test.R;
import com.mx.test.TApplication;
import com.mx.test.adapter.TagAdapter;
import com.mx.test.bean.GradePointBean_Net;
import com.mx.test.custom.FlowLayout;
import com.mx.test.custom.ScorePreView_Dialog.Dialogcallback;
import com.mx.test.custom.TagFlowLayout;
import com.mx.test.custom.TagFlowLayout.OnSelectListener;
import com.mx.test.custom.TagView;
import com.mx.test.exception.ControlException;
import com.mx.test.util.Constant;
import com.mx.test.util.NetStatus;
import com.mx.test.util.Out;
import com.mx.test.util.RequestManager;
import com.mx.test.util.Utils;

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

/***
 * 评价碎片
 * 
 */
public class FragmentEvaluate extends Fragment implements OnClickListener {

	public static final String TAG = "FragmentEvaluate";

	// add 2016-06-17 dsx
	private String[] mTagArr = new String[] { "操作顺序错误", "无菌区域污染", "医患沟通能力差", "没有保护患者隐私意识", "物品污染", "基本操作熟练", "无菌区观念强",
			"医患沟通能力好", "有患者隐私保护意识" };

	private TagAdapter<String> mTagAdapter;

	private LayoutInflater mInflater;
	//
	Show_PreViewInf_uponFragmentEvaluate mListener;

	public interface onEvaluateListener {
		void onEvaluate();
	}

	private TextView mTextName;// 考生姓名

	private EditText mEditEvaluate;// 评价

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

	private String TextScore_s;

	private Button mBtnCheck;// 修改成绩

	private Button mBtnCommit;// 提交成绩

	private Button btn_score_preview;// 评分预览

	private ArrayList<Integer> imageArray = new ArrayList<Integer>();

	private ArrayList<GradePointBean_Net> mUploadSocore;

	private Dialogcallback mDialogcallback;

	private boolean mIsSubmitting = true;

	private Context mContext;

	private boolean mEvaluateIsNormalEnd = false;// 判断上一次是否是正常结束，默认是false

	private TagFlowLayout mFlowLayout;

	String student_id;

	String station_id;

	String teacher_id;

	String exam_screening_id;

	String begin_dt;

	String endTime;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		View view = inflater.inflate(R.layout.fragment_evaluate_new, null);
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
		mDialogcallback = new Dialogcallback() {

			@Override
			public void dialogdoreturn(String string) {
				getFragmentManager().popBackStack();
			}

			@Override
			public void dialogdoupload(String string) {

			}
		};
	}

	// 计算考生考试用时
	private void showExamTime() {
		String startTime = (Utils.getSharedPrefrences(mContext, "startTime"));
		String endTime = (Utils.getSharedPrefrences(mContext, "endTime"));
		String name = Utils.getSharedPrefrences(mContext, "student_name");
		long totalSeconds = Utils.Str2Long(endTime, "yyyy-MM-dd HH:mm:ss")
				- Utils.Str2Long(startTime, "yyyy-MM-dd HH:mm:ss");
		int min = (int) (totalSeconds / 60000);
		int seconds = (int) (totalSeconds % 60000 / 1000);
		// mTextName = (TextView) view.findViewById(R.id.text_name);
		mTextName.setText(name + "考试用时" + min + "分钟" + seconds + "秒");
	}

	// 初始化 控件
	private void initWidget(View view) {
		// add 2016-06-17
		mInflater = LayoutInflater.from(mContext);

		mFlowLayout = (TagFlowLayout) view.findViewById(R.id.flowlayout);
		//
		// showExamTime();
		mTextName = (TextView) view.findViewById(R.id.text_name);
		String name = Utils.getSharedPrefrences(mContext, "student_name");
		mTextName.setText((null == name) ? "" : name);
		mEditEvaluate = (EditText) view.findViewById(R.id.edit_evaluate);
		mStarOperate = (RatingBar) view.findViewById(R.id.star_operate);
		mStarProficiency = (RatingBar) view.findViewById(R.id.star_proficiency);
		mStarCare = (RatingBar) view.findViewById(R.id.star_care);
		mStarCommunicate = (RatingBar) view.findViewById(R.id.star_communicate);
		mTextScore = (TextView) view.findViewById(R.id.text_score);
		mBtnCheck = (Button) view.findViewById(R.id.btn_check);
		mBtnCheck.setOnClickListener(this);
		mBtnCommit = (Button) view.findViewById(R.id.btn_commit);
		mBtnCommit.setOnClickListener(this);
		btn_score_preview = (Button) view.findViewById(R.id.btn_score_preview);
		btn_score_preview.setOnClickListener(this);

		showScore();// 页面可见时候统计分数

		if (!ControlException.checkEvaluateIsNormal(mContext)) {// 回复数据
			mStarOperate.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "operation")));
			mStarProficiency.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "skilled")));
			mStarCare.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "patient")));
			mIntCare = Integer.parseInt(Utils.getSharedPrefrencesByName(mContext, "Exception", "patient"));
			mStarCommunicate.setRating(
					(int) Float.parseFloat(Utils.getSharedPrefrencesByName(mContext, "Exception", "affinity")));
			mIntCommunicate = Integer.parseInt(Utils.getSharedPrefrencesByName(mContext, "Exception", "affinity"));
			mEditEvaluate.setText(Utils.getSharedPrefrencesByName(mContext, "Exception", "evaluate"));

		}
	}

	// 点击事件
	private void onClickEvents() {

		// mBtnCheak.setOnClickListener(this);
		mEditEvaluate.addTextChangedListener(new TextWatcher() {
			@Override
			public void onTextChanged(CharSequence s, int start, int before, int count) {
				String clickpostion = "";
				Utils.saveSharedPrefrencesByName(mContext, "Exception", "clickpostion", clickpostion);
				Vector<Integer> v = new Vector<>();
				String[] Evaluate = s.toString().split("、");
				for (int i = 0; i < Evaluate.length; i++) {
					for (int j = 0; j < mTagArr.length; j++) {
						if (mTagArr[j].equals(Evaluate[i])) {
							// j dianji
							v.add(j);
							break;
						}
					}
				}
				for (int r = 0; r < mTagArr.length; r++) {
					TagView tagView = (TagView) mFlowLayout.getChildAt(r);
					tagView.setChecked(false);
					for (Integer p : v) {
						if (r == p) {
							tagView.setChecked(true);
							clickpostion += r + ",";
							Utils.saveSharedPrefrencesByName(mContext, "Exception", "clickpostion", clickpostion);
						}
					}
				}
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count, int after) {
			}

			@Override
			public void afterTextChanged(Editable s) {
				mEvaluateString = mEditEvaluate.getText().toString().trim();
				if (null == mEvaluateString) {
					mEvaluateString = "";
				}
				Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluate", mEvaluateString);
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
		// add 2016-06-17
		mTagAdapter = new TagAdapter<String>(mTagArr) {

			@Override
			public View getView(FlowLayout parent, int position, String t) {
				TextView tag = (TextView) mInflater.inflate(R.layout.tv, null);
				tag.setText(t);
				return tag;
			}
		};
		mFlowLayout.setAdapter(mTagAdapter);

		mFlowLayout.setOnSelectListener(new OnSelectListener() {

			@Override
			public void onSelected(String str, Set<Integer> selectPosSet) {
				if (!selectPosSet.isEmpty()) {
					String evaluate = "";
					String clickpostion = "";
					if (Utils.getSharedPrefrencesByName(mContext, "Exception", "evaluate") == null) {
						evaluate = str + "、";
					} else {
						evaluate = str + "、" + Utils.getSharedPrefrencesByName(mContext, "Exception", "evaluate");
					}
					for (int tagIndex : selectPosSet) {
						clickpostion += tagIndex + ",";
						Utils.saveSharedPrefrencesByName(mContext, "Exception", "clickpostion", clickpostion);
					}
					Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluate", evaluate);
					mEditEvaluate.setText(evaluate);
					// mEditEvaluate.setText(mEvaluateString + evaluate);
				}
			}

			@Override
			public void onCancel(String str, Set<Integer> selectPosSet) {
				// TODO Auto-generated method stub
				String evaluatestr = "";
				if (!selectPosSet.isEmpty()) {
					String clickpostion = "";
					String[] evaluate = Utils.getSharedPrefrencesByName(mContext, "Exception", "evaluate").split("、");
					for (int i = 0; i < evaluate.length; i++) {
						if (evaluate[i].equalsIgnoreCase(str)) {
							evaluate[i] = "";
						}
					}
					for (String str1 : evaluate) {
						if (!str1.equalsIgnoreCase("")) {
							evaluatestr += str1 + "、";
						}
					}
					Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluate", evaluatestr);
					for (int tagIndex : selectPosSet) {
						clickpostion += tagIndex + ",";
						Utils.saveSharedPrefrencesByName(mContext, "Exception", "clickpostion", clickpostion);
					}
					mEditEvaluate.setText(evaluatestr);
					// mEditEvaluate.setText(mEvaluateString + evaluate);
				}

			}
		});
		if (Utils.getSharedPrefrencesByName(mContext, "Exception", "clickpostion") != null) {
			String[] clickpostion = Utils.getSharedPrefrencesByName(mContext, "Exception", "clickpostion").split(",");
			Set<Integer> mSelectedView = new HashSet<Integer>();
			for (String postion : clickpostion) {
				if (postion != null && !postion.equalsIgnoreCase("")) {
					TagView tagView = (TagView) mFlowLayout.getChildAt(Integer.parseInt(postion));
					if (tagView != null) {
						mSelectedView.add(Integer.parseInt(postion));
						tagView.setChecked(true);
					}
				}
			}
			mFlowLayout.setSelectedList(mSelectedView);
		}
	}

	/** 统计总分 */
	public void showScore() {
		int scoreNor = 0;
		int scoreSpec = 0;
		int scoreReal = 0;
		for (int i = 0; i < mUploadSocore.size(); i++) {

			if (mUploadSocore.get(i).getTag().equals(Constant.NORMAL_TAG)) {

				for (int j = 0; j < mUploadSocore.get(i).getTest_term().size(); j++) {

					scoreNor = Integer.parseInt(mUploadSocore.get(i).getTest_term().get(j).getReal()) + scoreNor;
				}

			} else if (mUploadSocore.get(i).getTag().equals(Constant.SPECIAL_TAG)) {

				int s = Integer.parseInt(mUploadSocore.get(i).getSubtract());

				scoreSpec = scoreSpec + s;

			}
		}
		scoreReal = ((scoreNor - scoreSpec) < 0) ? 0 : (scoreNor - scoreSpec);

		mTextScore.setText(String.valueOf(scoreReal));

		TextScore_s = String.valueOf(scoreReal);
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

		case R.id.btn_check:
			getFragmentManager().getBackStackEntryCount();
			getFragmentManager().popBackStack();

			// getFragmentManager().beginTransaction().replace(R.id.fragment_draw,
			// new FragmentGrade()).commit();

			break;
		case R.id.btn_commit:// 点击提交时间处理
		if(!TApplication.isstop) {
			Intent intent = new Intent(getActivity(), MainActivity.class);
			intent.putExtra("studentname", "李四");
			intent.putExtra("studentcode", "A002");
			intent.putExtra("nextstudent", "没有考生");
			startActivity(intent);
			getActivity().finish();
			TApplication.isstop = true;
			Utils.showToast(mContext, "提交成功");
		}else {
			Utils.showToast(mContext, "提交成功，考试结束");
			getActivity().finish();
		}
			break;
		case R.id.btn_score_preview:// 评分预览
			mListener.show(mUploadSocore, TextScore_s, mDialogcallback);
		}
	}

	// 将网络的考点数据的实体类，转换为本地的实体类
	// private ArrayList<GradePointBean_Net>
	// convertNet2Local(ArrayList<GradePointBean_Net> netDataList) {
	//
	// ArrayList<GradePointBean_Net> localList = new
	// ArrayList<GradePointBean_Net>();
	// localList.clear();
	// for (int i = 0; i < netDataList.size(); i++) {
	//
	// localList.add(GradePointBean_Net.convert2Local(netDataList.get(i)));
	// }
	//
	// return localList;
	// }

	private boolean executeRequest(Request<?> request) {
		// Out.out("xxxxx===" + request.getUrl());
		if (!NetStatus.isNetworkConnected(mContext)) {
			Out.Toast(mContext, "网络异常！");
			mIsSubmitting = false;
			return false;
		}
		RequestManager rmg = new RequestManager(mContext);
		rmg.addRequest(request, mContext);
		return true;
	}


	/**
	 * 2016-6-3修改成绩上传为service中异步进行 try { GsonRequest<BaseInfo> uploadResult =
	 * new GsonRequest<BaseInfo>(Method.POST, BaseActivity.mSUrl +
	 * Constant.UPLOAD_SCORE, BaseInfo.class, null, params, new Listener
	 * <BaseInfo>() {
	 * 
	 * @Override public void onResponse(BaseInfo arg0) {
	 * 
	 *           if (arg0.getCode() == 1) {
	 * 
	 *           Utils.saveSharedPrefrences(mContext, "isShowSp", "true");
	 * 
	 *           mEvaluateIsNormalEnd = true; Toast.makeText(mContext, "成绩上传成功",
	 *           Toast.LENGTH_SHORT).show();
	 * 
	 *           startActivity(new Intent(mContext, MainActivity.class));
	 * 
	 *           ((Activity) mContext).finish();
	 * 
	 *           } else { Toast.makeText(mContext, "成绩上传失败,成绩已为您保存至本地",
	 *           Toast.LENGTH_SHORT).show(); Log.i("request return String",
	 *           arg0.toString()); Log.i(TAG, arg0.getCode() + ""); Log.e(TAG,
	 *           arg0.getMessage()); ReTransmitUtil.addReTransmitNames(mContext,
	 *           student_id + "_" + station_id + "_" + teacher_id);
	 *           startActivity(new Intent(mContext, MainActivity.class));
	 * 
	 *           ((Activity) mContext).finish(); }
	 * 
	 *           mIsSubmitting = false; } }, errorListener());
	 *           executeRequest(uploadResult); } catch (Exception e) {
	 *           mListener.dimissDialog(); Toast.makeText(mContext,
	 *           "上传成绩返回数据有误！成绩已为您保存至本地", Toast.LENGTH_SHORT).show(); } }
	 * 
	 *           private Response.ErrorListener errorListener() { return new
	 *           Response.ErrorListener() {
	 * @Override public void onErrorResponse(VolleyError error) { mIsSubmitting
	 *           = false; ReTransmitUtil.addReTransmitNames(mContext, student_id
	 *           + "_" + station_id + "_" + teacher_id);
	 *           Utils.saveSharedPrefrences(mContext, "isshow", "true");
	 *           error.printStackTrace(); }
	 * 
	 *           }; }
	 **/
	// 保存异常退出的数据
	private void saveDataOnStop(Context context) {

		if (mEvaluateIsNormalEnd == false) {// 异常退出，保存相关数据
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluateIsNormalEnd", "false");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "operation", mIntOperate + "");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "skilled", mIntProficiency + "");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "patient", mIntCare + "");
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "affinity", mIntCommunicate + "");

		} else {
			// 考生正常结束考试后，数据丢弃
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluateIsNormalEnd", null);
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "gradeIsNormalEnd", null);
			Utils.saveSharedPrefrences(mContext, "warn", null);// 丢弃本地标记为警告
			Utils.saveSharedPrefrences(mContext, "endTime", null);
			Utils.saveSharedPrefrences(mContext, "startTime", null);
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "evaluate", null);
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "clickpostion", null);
		}
	}

	@Override
	public void onStop() {
		super.onStop();
		mFlowLayout.onSaveInstanceState();
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

	@Override
	public void onAttach(Activity activity) {
		super.onAttach(activity);
		try {
			mListener = (Show_PreViewInf_uponFragmentEvaluate) activity;
		} catch (ClassCastException e) {
			throw new ClassCastException(activity.toString() + "must implement Show_PreViewInf_uponFragmentEvaluate");
		}
	}

	public interface Show_PreViewInf_uponFragmentEvaluate {
		public void show(ArrayList<GradePointBean_Net> mGradePointBean_Local, String TextScore_s,
				Dialogcallback mDialogcallback);

		public void showDialog();

		public void dimissDialog();
	}

}
