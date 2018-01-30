package com.mx.test.fragment;

import java.lang.reflect.Type;
import java.util.ArrayList;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.hb.views.PinnedSectionListView;
import com.mx.test.R;
import com.mx.test.adapter.ContentListViewAdapter;
import com.mx.test.adapter.ContentListViewAdapter.GetScoreInterface;
import com.mx.test.adapter.MenuListAdapter;
import com.mx.test.bean.DataBean;
import com.mx.test.bean.GradePointBean_Net;
import com.mx.test.bean.Item;
import com.mx.test.bean.PointTermBean;
import com.mx.test.camera.CropHelper;
import com.mx.test.custom.CustomPopupWindow;
import com.mx.test.custom.LoadingDialog;
import com.mx.test.save.FileUtils;
import com.mx.test.util.Constant;
import com.mx.test.util.NetStatus;
import com.mx.test.util.RequestManager;
import com.mx.test.util.Utils;

import android.app.Activity;
import android.app.Fragment;
import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;

import cn.pedant.SweetAlert.widget.SweetAlertDialog;
import cn.pedant.SweetAlert.widget.SweetAlertDialog.OnSweetClickListener;

/**
 * 评分碎片
 * 
 * @author PengCangXu
 *
 */
public class FragmentGrade extends Fragment implements OnClickListener {

	public static final String TAG = "FragmentGrade";

	public static final int RECOVER_POP = 255;

	private Context mContext;

	private Button mBtnComplete, mBtnOperate;// 按钮：考生完成考试，记录与标记

	private ArrayList<Integer> mUploadReturnList;// 上传图片返回数据集合

	private ArrayList<GradePointBean_Net> mAllPoints;// 考核标准的数据

	private ArrayList<Integer> mNotScore = new ArrayList<Integer>();// 没有打分的考点索引集合

	private FragmentManager mManager;

	private boolean mIsNormalEnd = false;// 是否正常退出，默认为false

	// 考核评分页面修改用
	private LayoutInflater mInflater;

	private ArrayList<Item> menuListData;

	private boolean isClick = false;

	private int mContentHeaderIDBK;
	// 目录列表
	private ListView mMenuListview;
	// 内容列表
	private PinnedSectionListView mContentListview;
	// 目录适配器
	private MenuListAdapter mMenuListAdapter;
	// 内容适配器
	private ContentListViewAdapter mContentListAdapter;

	private TextView mTextGetScore;

	private LoadingDialog loadingDialog;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		View view;
		view = inflater.inflate(R.layout.fragment_grade, null);
		mInflater = inflater;
		mContext = getActivity();
		findWidget(view);
		reciveData();
		showScore(mAllPoints);
		return view;
	}

	public void onStop() {
		super.onStop();
		saveDataOnStop(mContext);
	}

	// 检查上一考点分数是否打完
	/*
	 * private int checkPrevHasDone(int sectionID) { int done = -1; if
	 * (sectionID > 0) { int index = sectionID - 1;
	 * ArrayList<GradePointBean_Net> modifiedData =
	 * mContentListAdapter.getModifiedData(); if (null != modifiedData &&
	 * modifiedData.size() > 0 && null !=
	 * modifiedData.get(index).getTest_term()) {
	 * 
	 * for (PointTermBean item : modifiedData.get(index).getTest_term()) { if
	 * (item.isScored()) { done = -1; } else { done = index; continue; } } }
	 * else { done = -1; } if (done == -1 && index > 0) { done =
	 * checkPrevHasDone(index); } } return done; }
	 */

	// 定位
	/*
	 * private int getListPositionBySection(int section) { int re = -1; for
	 * (Item item : menuListData) { if (item.sectionPosition == section) { re =
	 * item.listPosition; break; } } return re; }
	 */

	// 接受Activity网络请求的数据
	private void reciveData() {

		Bundle bundle = getFragmentManager().findFragmentByTag("grade").getArguments();

		if (bundle != null) {

			// 网络的标准考点
			if (null == mAllPoints) {
				mAllPoints = new ArrayList<GradePointBean_Net>();
				mAllPoints = bundle.getParcelableArrayList("pointList");
			}

			generateDatasetIsSpExam();

			// 左边的考点详情
			// mAllPointsLocal = convertNet2Local(mAllPoints);
			mContentListAdapter = new ContentListViewAdapter(mContext, R.layout.acticity_title, mAllPoints);

			mContentListAdapter.setGetScoreInterface(new GetScoreInterface() {

				@Override
				public void afterClick(ArrayList<GradePointBean_Net> data) {

					showScore(data);
				}
			});

			// 右边的考点导航
			menuListData = new ArrayList<Item>();
			menuListData = mContentListAdapter.getMenuListData();
			mMenuListAdapter = new MenuListAdapter(mContext, mAllPoints);
			mMenuListview.setAdapter(mMenuListAdapter);

			mContentHeaderIDBK = 0;
			mMenuListAdapter.setSelect(0);
			mMenuListview.setOnItemClickListener(new OnItemClickListener() {

				@Override
				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

					int nowIndex = position - 1;
					// int mNotScored = checkPrevHasDone(nowIndex);
					//
					// if (mNotScored != -1) {
					// Log.e(">>>getListPositionBySection(mNotScored)<<",
					// getListPositionBySection(mNotScored) + "");
					// Log.e(">>>mNotScored<<", mNotScored + "");
					// }

					for (Item item : menuListData) {
						// if (null != item.id &&
						// mAllPointsLocal.get(nowIndex).getLevel().equals("1")
						// &&
						// item.id.equals(mAllPointsLocal.get(nowIndex).getId()))
						// {
						if (null != item.id && item.id.equals(mAllPoints.get(nowIndex).getId())) {
							isClick = true;
							mContentListview.setSelection(item.listPosition);
							mMenuListAdapter.setSelect(nowIndex);
							break;
						}
					}

				}
			});
			mContentListview.setAdapter(mContentListAdapter);
//			mContentListview.setOnScrollListener(new OnScrollListener() {
//
//				@Override
//				public void onScrollStateChanged(AbsListView view, int scrollState) {
//
//					if (scrollState == SCROLL_STATE_TOUCH_SCROLL) {
//						if (isClick) {
//							isClick = false;
//						}
//					}
//				}
//
//				@Override
//				public void onScroll(AbsListView view, int firstVisibleItem, int visibleItemCount, int totalItemCount) {
//					// int mNotScored = checkPrevHasDone(mContentHeaderIDBK);
//					// if (mNotScored != -1) {
//					// //
//					// mContentListview.setSelection(getListPositionBySection(mNotScored));
//					// // mMenuListAdapter.setSelect(mNotScored);
//					// Log.e(">>>getListPositionBySection(mNotScored)<<",
//					// getListPositionBySection(mNotScored) + "");
//					// Log.e(">>>mNotScored<<", mNotScored + "");
//					//
//					// }
//					if (!isClick) {
//						int nowIndex = menuListData.get(firstVisibleItem).sectionPosition;
//						if (mContentHeaderIDBK != nowIndex) {
//							mMenuListAdapter.setSelect(nowIndex);
//							mContentHeaderIDBK = nowIndex;
//						}
//					}
//				}
//
//			});

		}
	}

	/** 判断是否是Sp考试，如果是,将所有分值为1的考点全部都设置为满分 */
	private void generateDatasetIsSpExam() {

		String examType = Utils.getSharedPrefrences(mContext, "teacher_type");

		if (examType != null && examType.equals(Constant.TEACHER_TYPE_SP)) {

			for (int i = 0; i < this.mAllPoints.size(); i++) {

				if (mAllPoints.get(i).getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {

					PointTermBean termBean = null;

					for (int j = 0; j < mAllPoints.get(i).getTest_term().size(); j++) {

						termBean = mAllPoints.get(i).getTest_term().get(j);

						if (null != termBean && false == termBean.isScored()
								&& "1".equalsIgnoreCase(termBean.getScore())) {

							termBean.setReal("1");
							termBean.setScored(true);
						}
					}
				}
			}
		} else {
			return;
		}
	}

	/** 得到单一考点中所有打分的所有考项 */
	public int getNotScored(int PointIndex, ArrayList<GradePointBean_Net> allPoints) {

		if (allPoints.get(PointIndex).getTag().equals(Constant.SPECIAL_TAG)) {// 特殊评分点

			if ("-1".equalsIgnoreCase(allPoints.get(PointIndex).getSubtract())) {

				return PointIndex;
			}

		} else if (allPoints.get(PointIndex).getTag().equals(Constant.NORMAL_TAG)) {// 正常评分点

			for (int i = 0; i < allPoints.get(PointIndex).getTest_term().size(); i++) {
				if (!allPoints.get(PointIndex).getTest_term().get(i).isScored()) {

					return PointIndex;

				}
			}
		}
		return -1;
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {

		super.onActivityResult(requestCode, resultCode, data);

		if (requestCode == Constant.CAMERA_REQUEST && resultCode == Activity.RESULT_OK) {

			// 通过url拿到图片
			// InputStream in =
			// getActivity().getContentResolver().openInputStream(CustomPopupWindow.CAMERA_URI);
			// Bitmap bitmap = BitmapFactory.decodeStream(in);

			long setNameByTime = System.currentTimeMillis();
			// 本地存储,返回存储路径
			String savePath = CropHelper.saveImg2SD(mContext, CustomPopupWindow.CAMERA_URI, setNameByTime + "");
			// 更新popList视图
			DataBean dataBean = new DataBean(DataBean.CAMERA);

			dataBean.setFilePath(savePath);

		} else if (resultCode == Activity.RESULT_CANCELED) {

		}

	}




	// 初始化评分fragment上的控件
	private void findWidget(View view) {

		mManager = getFragmentManager();

		mBtnOperate = (Button) view.findViewById(R.id.btn_operate);
		mBtnOperate.setOnClickListener(this);
		mBtnComplete = (Button) view.findViewById(R.id.btn_point_complete);
		mBtnComplete.setOnClickListener(this);
		// 判断是否点击过
		String endStr = Utils.getSharedPrefrences(mContext, "endTime");
		if (null != endStr) {
			mBtnComplete.setBackgroundResource(R.drawable.gray_button_background);
			mBtnComplete.setTextColor(Color.WHITE);
			mBtnComplete.setClickable(false);
		}

		mTextGetScore = (TextView) view.findViewById(R.id.tv_student_real_time_score);

		mMenuListview = (ListView) view.findViewById(R.id.menu_list);

		mContentListview = (PinnedSectionListView) view.findViewById(R.id.content_list);
		View contentFooter = mInflater.inflate(R.layout.contentlist_footer, null);
		Button exam_complete = (Button) contentFooter.findViewById(R.id.exam_complete);
		exam_complete.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
					normalRepalceFragment();
			}
		});
		mContentListview.addFooterView(contentFooter);

		View headerView = mInflater.inflate(R.layout.list_menu_top, null);
		mMenuListview.addHeaderView(headerView);
		View footerView = mInflater.inflate(R.layout.list_menu_end, null);
		mMenuListview.addFooterView(footerView);

		// 上传图片的返回
		mUploadReturnList = new ArrayList<Integer>();

	}

	// 统计学生实时得分
	private void showScore(ArrayList<GradePointBean_Net> Localdata) {
		int socore = 0;
		int scoreNor = 0;// 正常考点总分
		int scoreSpec = 0;// 特殊考点总分
		String pointTag = null;// 考点标签

		// 正常考点的临时变量
		ArrayList<PointTermBean> termTemp = null;// 正常考点的单个考项
		String realScore = null;// 真实得分

		String realSubtract = null;

		for (int i = 0; i < Localdata.size(); i++) {

			pointTag = Localdata.get(i).getTag();

			if (null != pointTag && pointTag.equals(Constant.NORMAL_TAG)) {

				termTemp = Localdata.get(i).getTest_term();

				for (int j = 0; j < termTemp.size(); j++) {

					realScore = termTemp.get(j).getReal();

					if (null != realScore) {
						scoreNor = Integer.parseInt(realScore) + scoreNor;
					}
				}

			} else if (pointTag.equals(Constant.SPECIAL_TAG)) {

				realSubtract = Localdata.get(i).getSubtract();

				int s = 0;

				if ("-1".equalsIgnoreCase(realSubtract) || "".equalsIgnoreCase(realSubtract)) {
					s = 0;
				} else {
					s = Integer.parseInt(realSubtract);
				}

				scoreSpec = scoreSpec + s;
			}
		}
		socore = ((scoreNor - scoreSpec) < 0) ? 0 : (scoreNor - scoreSpec);

		mTextGetScore.setText("总成绩：" + socore);
	}

	/** 点击事件 */
	// @Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.btn_operate:

			break;
		case R.id.btn_point_complete:

			String endStr = Utils.getSharedPrefrences(mContext, "endTime");

			if (null != endStr) {
				return;
			} else {
			}
			break;
		default:
			break;
		}
	}

	// 提示继续打分
	private void continueSocre() {
		String str = "以下考点没有完成打分:";
		String strTemp = str;
		// 拼接提示对话框标题
		if (!mNotScore.isEmpty()) {

			for (int j = 0; j < mNotScore.size(); j++) {
				strTemp += (mNotScore.get(j) + 1) + ",";
			}
		}
		// 去掉最后一个逗号
		if (strTemp.length() > str.length()) {
			strTemp = (String) strTemp.subSequence(0, strTemp.length() - 1);
		}

		if (!str.equals(strTemp)) {// 还有未打分.
			final SweetAlertDialog tipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE, false);
			tipsDialog.setTitleText(strTemp);
			tipsDialog.setContentText("点击继续,完成未打分考点");
			tipsDialog.setConfirmText("继续打分");//
			tipsDialog.setCancelable(false);
			tipsDialog.setConfirmClickListener(new OnSweetClickListener() {

				@Override
				public void onClick(SweetAlertDialog sweetAlertDialog) {
					// 每次去第一次沒有完成打分考点的索引
					int nowIndex = mNotScore.get(0);

					for (Item item : menuListData) {

						if (null != item.id && item.id.equals(mAllPoints.get(nowIndex).getId())) {
							isClick = true;
							mContentListview.setSelection(item.listPosition);
							mMenuListAdapter.setSelect(nowIndex);
						}
					}
					tipsDialog.dismiss();
				}
			});
			tipsDialog.show();
		}
	}

	/*** 检查是否有没有没有打分的考点,isReplace==true,可以直接跳转界面 */

	private boolean checkExamIsComplete() {

		mNotScore.clear();

		for (int i = 0; i < mAllPoints.size(); i++) {
			int temp = getNotScored(i, mAllPoints);
			if (temp != -1) {
				mNotScore.add(temp);
			}
		}
		return mNotScore.isEmpty();
	}




	// 按鈕不可點擊
	private void buttonDisable() {

		mBtnComplete.setBackgroundResource(R.drawable.gray_button_background);
		mBtnComplete.setTextColor(Color.WHITE);
		mBtnComplete.setClickable(false);
	}

	// 正常进入评价碎片
	public void normalRepalceFragment() {
		mIsNormalEnd = true;
		Bundle bundle = new Bundle();
		bundle.putParcelableArrayList("uploadScoreList", mAllPoints);
		bundle.putIntegerArrayList("imageArray", mUploadReturnList);
		FragmentEvaluate evaluste = new FragmentEvaluate();
		evaluste.setArguments(bundle);
		FragmentTransaction transaction = mManager.beginTransaction();
		transaction.replace(R.id.frame, evaluste, "evaluste");
		transaction.addToBackStack(TAG);
		mManager.getBackStackEntryCount();
		transaction.commit();
	}

	public void setListViewVisiable(boolean b) {
		if (b == false) {
			mContentListAdapter.isShowAnswer();
		} else if (b == true) {
			mContentListAdapter.isHideAnswer();
		}
	}



	private Response.ErrorListener errorListener() {
		return new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				// Out.Toast(mContext, "网络连接异常");
				error.printStackTrace();
			}
		};
	}

	private boolean executeRequest(Request<?> request) {
		if (!NetStatus.isNetworkConnected(mContext)) {
			return false;
		}
		RequestManager rmg = new RequestManager(mContext);
		RequestManager.addRequest(request, mContext);
		return true;
	}

	public void openProgressDialog() {
		if (loadingDialog == null) {
			loadingDialog = new LoadingDialog(mContext);
		}
		if (!loadingDialog.isShowing())
			loadingDialog.show();
	}

	public void closeProgressDialog() {
		if (loadingDialog != null) {
			loadingDialog.cancel();
		}
	}

	/**
	 * 程序异常退出时，保存相关数据，包括学生的得分以及本地的操作
	 * 
	 * @param context
	 *            使用上下文
	 */
	private void saveDataOnStop(Context context) {
		if (mIsNormalEnd == false) {
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "gradeIsNormalEnd", "false");// 存储异常的退出状态
		} else if (mIsNormalEnd == true) {
			Utils.saveSharedPrefrencesByName(mContext, "Exception", "gradeIsNormalEnd", "true");// 存储异常的退出状态
		}

		// mAllPoints = convertLocal2Net(mAllPoints);

		// 考点数据保存
		Type pointType = new TypeToken<ArrayList<GradePointBean_Net>>() {
		}.getType();
		Gson gson = new Gson();
		String exceptionString = gson.toJson(mAllPoints, pointType);

		FileUtils.savaDataFromException(context, FileUtils.EXCEPTION_SCORE_FILE, exceptionString);
		// 上传的保存
		if (mUploadReturnList.size() > 0) {
			String uploadString = gson.toJson(mUploadReturnList, new TypeToken<ArrayList<Integer>>() {
			}.getType());
			FileUtils.savaDataFromException(context, FileUtils.EXCEPTION_UOLOAD_FILE, uploadString);
		}
	}

	// 开启计时器
	/*
	 * private void begingAnticlockwise(long limit_m, long limit_s) { //
	 * mAnticlockwise = (CustomAnticlockwise) //
	 * getActivity().findViewById(R.id.textView_Time);
	 * mAnticlockwise.initTime(limit_m, limit_s);
	 * 
	 * mTeacherType = Utils.getSharedPrefrences(mContext, "teacher_type");
	 * 
	 * if (mTeacherType != null &&
	 * mTeacherType.equalsIgnoreCase(Constant.TEACHER_TYPE_SP)) {
	 * mAnticlockwise.setVisibility(View.GONE); return; } else {
	 * mAnticlockwise.reStart();// 启动
	 * mAnticlockwise.setOnTimeCompleteListener(new
	 * CustomAnticlockwise.OnTimeCompleteListener() {
	 * 
	 * @Override public void onTimeComplete() { showTimeUseOutDialog(); } }); }
	 * }
	 */

}
