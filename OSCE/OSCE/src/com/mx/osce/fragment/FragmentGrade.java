package com.mx.osce.fragment;

import java.io.File;
import java.io.IOException;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.android.volley.Request;
import com.android.volley.Request.Method;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.hb.views.PinnedSectionListView;
import com.mx.osce.BaseActivity;
import com.mx.osce.GradeActivity;
import com.mx.osce.R;
import com.mx.osce.adapter.ContentListViewAdapter;
import com.mx.osce.adapter.ContentListViewAdapter.GetScoreInterface;
import com.mx.osce.adapter.MenuListAdapter;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.DataBean;
import com.mx.osce.bean.EndExamBean;
import com.mx.osce.bean.GradePointBean_Net;
import com.mx.osce.bean.Item;
import com.mx.osce.bean.PointTermBean;
import com.mx.osce.camera.CropHelper;
import com.mx.osce.custom.CustomPopupWindow;
import com.mx.osce.custom.CustomTimer;
import com.mx.osce.custom.LoadingDialog;
import com.mx.osce.exception.ControlException;
import com.mx.osce.save.FileUtils;
import com.mx.osce.upload.UploadUtil_New;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.NetStatus;
import com.mx.osce.util.RequestManager;
import com.mx.osce.util.Utils;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ActivityManager.TaskDescription;
import android.app.Fragment;
import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.AbsListView;
import android.widget.AbsListView.OnScrollListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
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

	public static final String ERROR = "Error";

	public static final int RECOVER_POP = 255;

	private Context mContext;

	public boolean mIsCanEnd = true;

	/** FragmentGrade接口 */
	public interface onGradeListener {
		void onUpdataListener();
	}

	private Button mBtnComplete, mBtnOperate;// 按钮：考生完成考试，记录与标记

	private ArrayList<Integer> mUploadReturnList;// 上传图片返回数据集合

	private ArrayList<GradePointBean_Net> mAllPoints;// 考核标准的数据

	private ArrayList<Integer> mNotScore = new ArrayList<Integer>();// 没有打分的考点索引集合

	private int mCurrentPoint = 0;// 当前考核点，默认从0开始

	private CustomPopupWindow popWindow;// 操作与标记

	private FragmentManager mManager;

	private boolean mIsNormalEnd = false;// 是否正常退出，默认为false

	private RecoverPopupHandler mHandler;// 用于“操作与标记”的数据恢复

	GetScoreInterface mGetScoreInterface;

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

		View view = inflater.inflate(R.layout.fragment_grade, null);
		mInflater = inflater;
		mContext = getActivity();
		findWidget(view);
		reciveData();
		showScore(mAllPoints);
		mHandler = new RecoverPopupHandler();
		mHandler.sendEmptyMessage(RECOVER_POP);
		return view;
	}

	public void onStop() {
		super.onStop();
		saveDataOnStop(mContext);

		// 防止意外操作导致Fragment切换到评价Fragment,Popupwindow仍然存在
		if (popWindow != null) {
			popWindow.dismiss();
		}
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
			mContentListview.setOnScrollListener(new OnScrollListener() {

				@Override
				public void onScrollStateChanged(AbsListView view, int scrollState) {

					if (scrollState == SCROLL_STATE_TOUCH_SCROLL) {
						if (isClick) {
							isClick = false;
						}
					}
				}

				@Override
				public void onScroll(AbsListView view, int firstVisibleItem, int visibleItemCount, int totalItemCount) {
					// int mNotScored = checkPrevHasDone(mContentHeaderIDBK);
					// if (mNotScored != -1) {
					// //
					// mContentListview.setSelection(getListPositionBySection(mNotScored));
					// // mMenuListAdapter.setSelect(mNotScored);
					// Log.e(">>>getListPositionBySection(mNotScored)<<",
					// getListPositionBySection(mNotScored) + "");
					// Log.e(">>>mNotScored<<", mNotScored + "");
					//
					// }
					if (!isClick) {
						int nowIndex = menuListData.get(firstVisibleItem).sectionPosition;
						if (mContentHeaderIDBK != nowIndex) {
							mMenuListAdapter.setSelect(nowIndex);
							mContentHeaderIDBK = nowIndex;
						}
					}
				}

			});

			if (popWindow == null) {
				popWindow = new CustomPopupWindow((GradeActivity) mContext, mAllPoints, mCurrentPoint);
			}
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

			popWindow.mAdapter.getDataList().add(dataBean);

			popWindow.mAdapter.notifyDataSetChanged();

			popWindow.showPopupWindow(mBtnComplete);
			// 文件上传
			File file = new File(savePath);
			doUpload(file, mAllPoints.get(mCurrentPoint).getId(), Utils.getSharedPrefrences(mContext, "student_id"),
					Utils.getSharedPrefrences(mContext, "station_id"));

		} else if (resultCode == Activity.RESULT_CANCELED) {
			// 调用相机球取消后返回Fragment的逻辑处理
			if (judgePopWindowStillShow()) {
				popWindow.showPopupWindow(mBtnComplete);
			}
		}

	}

	// 判断启动相机返回后是否仍然显示Popwindow
	private boolean judgePopWindowStillShow() {
		if (popWindow != null | popWindow.mAdapter.getDataList().size() > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 上传图片
	 * 
	 * @param imageFile
	 *            照片地址
	 * @param point_id
	 *            考点id
	 * @param student_id
	 *            学生id
	 * @param station_id
	 *            考站id
	 */
	public void doUpload(final File imageFile, final String point_id, final String student_id,
			final String station_id) {
		new Thread() {
			@Override
			public void run() {
				uploadFile(imageFile, point_id, student_id, station_id);
			}
		}.start();
	}

	/**
	 * 上传文件
	 * 
	 * @param imageFile
	 *            文件
	 * @param standard_id
	 *            考试标准id
	 * @param student_id
	 *            学生id
	 * @param station_id
	 *            考站id
	 * @return 上传是否成功
	 */
	public boolean uploadFile(File imageFile, String standard_id, String student_id, String station_id) {

		final Map<String, String> params = new HashMap<String, String>();
		params.put("student_id", student_id);
		params.put("station_id", station_id);
		params.put("standard_id", standard_id);

		final Map<String, File> files = new HashMap<String, File>();

		files.put("photo", imageFile);

		try {
			final String request = UploadUtil_New.post(BaseActivity.mSUrl + Constant.UOLOAD_IMAGE, params, files);

			try {
				JSONObject json = new JSONObject(request);
				JSONArray j = json.getJSONArray("data");
				int data = j.getInt(0);
				System.out.println(data);
				mUploadReturnList.add(data);
			} catch (JSONException e) {
				e.printStackTrace();
			}
			System.out.println(request);
		} catch (IOException e) {
			e.printStackTrace();
			return false;
		}
		return true;

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
				// 是否完成所有打分
				if (!checkExamIsComplete()) {
					continueSocre();
					return;
				}
				// 是否结束考生状态
				if (null == Utils.getSharedPrefrences(mContext, "endTime")) {
					changeStudnetState(true);
				} else {
					normalRepalceFragment();
				}
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

			popWindow.showPopupWindow(mBtnComplete);

			break;
		case R.id.btn_point_complete:

			String endStr = Utils.getSharedPrefrences(mContext, "endTime");

			if (null != endStr) {
				return;
			} else {
				changeStudnetState(false);
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

	/**
	 * 结束当前考生的考试 ,并且判断是否切换到评价碎片
	 * 
	 * @param isChangeUi
	 *            是否切換
	 */
	public void changeStudnetState(final boolean isChangeUi) {

		RequestQueue requestQueue = Volley.newRequestQueue(mContext);

		final String url = BaseActivity.mSUrl + Constant.CHANGE_STATUS + "?student_id="
				+ Utils.getSharedPrefrences(mContext, "student_id") + "&user_id="
				+ Utils.getSharedPrefrences(mContext, "user_id") + "&station_id="
				+ Utils.getSharedPrefrences(mContext, "station_id");

		Log.i(">>>changeStudnetStatu<<<", url);

		StringRequest stringRequest = new StringRequest(url, new Response.Listener<String>() {
			@Override
			public void onResponse(String response) {
				mIsCanEnd = true;
				EndExamBean statu = null;
				closeProgressDialog();

				try {
					statu = new Gson().fromJson(response, EndExamBean.class);
				} catch (Exception e) {
					Toast.makeText(mContext, "结束考试返回数据有误！", Toast.LENGTH_SHORT).show();
					return;
				}

				if (statu.getCode() == 1) {

					Log.i(TAG, "endTime = " + statu.getData().getEnd_time() + "");

					// 相关数据的保存
					String endTime = statu.getData().getEnd_time();

					String endTimeStr = Utils.getSharedPrefrences(mContext, "endTime");

					if (null == endTimeStr && null != statu.getData() && null != endTime) {
						// 存入时间
						Utils.saveSharedPrefrences(mContext, "endTime", endTime);
						// 停止倒计时
						stopTimer();

					}
					// 成功结束状态，按钮失效
					buttonDisable();

					if (isChangeUi) {
						normalRepalceFragment();
					} else {
						return;
					}
				} else {
					stopTimer();
					buttonDisable();
					Toast.makeText(mContext, "结束考试失败，请重试" + "\n" + "错误码:" + statu.getCode(), Toast.LENGTH_SHORT).show();
				}
			}
		}, new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				closeProgressDialog();
				mIsCanEnd = true;
				Log.e(TAG, error.getStackTrace().toString(), error);
			}
		});

		openProgressDialog();
		requestQueue.add(stringRequest);

		return;
	}

	// 停止計時
	private void stopTimer() {

		CustomTimer mTimer = ((GradeActivity) getActivity()).getTimer();

		TextView showTimer = (TextView) getActivity().findViewById(R.id.tv_time_tips);

		if (null != mTimer) {

			String useStr = mTimer.getUsedTime();

			if (null != showTimer) {

				showTimer.setText("用时:" + useStr);
			}
		}
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

	/**
	 * 得到PopupWindow中所有操作记录中的时间描点信息
	 * 
	 * @return 一个包含所有时间描点信息的数组
	 */
	public ArrayList<DataBean> getUploadTimeData() {
		ArrayList<DataBean> dataArray = new ArrayList<DataBean>();

		if (popWindow.mAdapter.getDataList().size() > 0) {

			for (DataBean OperateBean : popWindow.mAdapter.getDataList()) {
				if (OperateBean.getmTag() == DataBean.TIMEPOINT) {
					dataArray.add(OperateBean);
				}
			}
		}
		return dataArray;
	}

	/**
	 * 上传时间描点信息
	 * 
	 * @param uploadTimeData
	 *            所有操作的描点信息
	 * @return 上传是否成功
	 */
	public void uploadTimesRequest(ArrayList<DataBean> uploadTimeData) {

		HashMap<String, String> params = new HashMap<String, String>();

		params.put("student_id", Utils.getSharedPrefrences(mContext, "student_id"));

		params.put("exam_id", Utils.getSharedPrefrences(mContext, "exam_id"));

		params.put("station_id", Utils.getSharedPrefrences(mContext, "station_id"));

		params.put("user_id", Utils.getSharedPrefrences(mContext, "user_id"));

		StringBuffer sb = new StringBuffer();

		for (int i = 0; i < uploadTimeData.size(); i++) {

			sb.append(uploadTimeData.get(i).getTimePoint() / 1000 + ",");

		}

		String timeValue = sb.toString();

		String subValue = timeValue.substring(0, timeValue.length() - 1);

		params.put("time_anchors", subValue);

		try {
			GsonRequest<BaseInfo> timeUploadRequest = new GsonRequest<BaseInfo>(Method.POST,
					BaseActivity.mSUrl + Constant.UPLOAD_TIMES,

					BaseInfo.class, null, params, new Response.Listener<BaseInfo>() {

						@Override
						public void onResponse(BaseInfo arg0) {
							if (arg0.getCode() != 1) {
								Toast.makeText(mContext, "上传时间描点失败", Toast.LENGTH_SHORT).show();
							}
						}
					}, errorListener());
			executeRequest(timeUploadRequest);
		} catch (Exception e) {
			Toast.makeText(mContext, "上传时间描点数据有误！", Toast.LENGTH_SHORT).show();
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
		// 保存本地操作
		// if (popWindow.mAdapter.getDataList() != null &&
		// popWindow.mAdapter.getDataList().size() > 0) {
		if (!popWindow.mAdapter.getDataList().isEmpty()) {

			Type popwindowDataType = new TypeToken<ArrayList<DataBean>>() {
			}.getType();
			String popwindowString = new Gson().toJson(popWindow.mAdapter.getDataList(), popwindowDataType);
			FileUtils.savaDataFromException(context, FileUtils.EXCEPTION_OPERTE_FILE, popwindowString);
		}
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

	// 异常退出恢复Handler
	@SuppressLint("HandlerLeak")
	private class RecoverPopupHandler extends Handler {
		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			switch (msg.what) {
			case RECOVER_POP:
				RecoverPopupTask TaskDescription = new RecoverPopupTask();
				TaskDescription.execute();
				break;
			default:
				break;
			}
		}
	}

	// 异步线程回复数据
	private class RecoverPopupTask extends AsyncTask<Void, Void, ArrayList<DataBean>> {
		@Override
		protected ArrayList<DataBean> doInBackground(Void... params) {
			if (!ControlException.checkGradeIsNormal(mContext)) {
				String popData = FileUtils.recoveryDataToActivity(mContext, FileUtils.EXCEPTION_OPERTE_FILE);
				if (popData == null) {
					return null;
				} else {
					// String subStr = popData.substring(5, popData.length());

					return new Gson().fromJson((popData), new TypeToken<ArrayList<DataBean>>() {
					}.getType());
				}
			}
			return null;
		}

		@Override
		protected void onPostExecute(ArrayList<DataBean> result) {
			super.onPostExecute(result);
			popWindow.mAdapter.recoverData(result);
		}
	}

}
