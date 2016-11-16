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
import com.mx.osce.BaseActivity;
import com.mx.osce.GradeActivity;
import com.mx.osce.R;
import com.mx.osce.adapter.ListViewAdapter;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.DataBean;
import com.mx.osce.bean.EndExamBean;
import com.mx.osce.bean.GradePointBean;
import com.mx.osce.camera.CropHelper;
import com.mx.osce.custom.CustomAnticlockwise;
import com.mx.osce.custom.CustomPopupWindow;
import com.mx.osce.custom.CustomSeekBarNotUse;
import com.mx.osce.custom.SeekView;
import com.mx.osce.exception.ControlException;
import com.mx.osce.save.FileUtils;
import com.mx.osce.upload.UploadUtil_New;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.NetStatus;
import com.mx.osce.util.Out;
import com.mx.osce.util.RequestManager;
import com.mx.osce.util.Utils;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Fragment;
import android.app.FragmentManager;
import android.content.Context;
import android.content.Intent;
import android.media.MediaPlayer;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
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

	private CustomAnticlockwise mAnticlockwise;

	private Context mContext;

	private SweetAlertDialog mTipsDialog;

	public boolean mIsCanEnd = true;

	/** FragmentGrade接口 */
	public interface onGradeListener {

		void onUpdataListener();

	}

	private TextView mTextPrePoint, mTextNextPoint, mTextOperate, mTextCurrentPoint;

	/** 考试进度条 */
	private CustomSeekBarNotUse mProcessBar;

	private SeekView mProcessBar1;

	private boolean isScreen;

	// 考核点与考核点的注意事项，点击考核点，考核点的注意事项
	private TextView mTextPoint, mTextTerm;

	// 加载考试考核点与考核项
	private ListView mListview;

	private ArrayList<Integer> mUploadReturnList;

	private ListViewAdapter mAdapter;

	// 考核标准的数据
	private ArrayList<GradePointBean> mAllPoints;

	// 当前考核点，默认从0开始
	private int mCurrentPoint = 0;

	private CustomPopupWindow popWindow;

	private FragmentManager mManager;

	private boolean hasNext = true;

	private boolean mIsNormalEnd = false;// 是否正常退出，默认为false

	private RecoverPopupHandler mHandler;

	private Long mLimitTime;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		View view = inflater.inflate(R.layout.fragment_grade, null);
		mContext = getActivity();
		findWidget(view);
		reciveData();
		mHandler = new RecoverPopupHandler();
		mHandler.sendEmptyMessage(RECOVER_POP);
		return view;

	}

	@Override
	public void onResume() {
		super.onResume();
		String limitTimeStr = Utils.getSharedPrefrences(mContext, "exam_LimitTime");
		mLimitTime = Long.parseLong(limitTimeStr);// 总限制时间（分钟）
		begingAnticlockwise(mLimitTime, 0);

	}

	public void onStop() {
		super.onStop();
		saveDataOnStop(mContext);

		// 防止意外操作导致Fragment切换到评价Fragment,Popupwindow仍然存在
		if (popWindow != null) {
			popWindow.dismiss();
		}
	}

	/** 接受Activity网络请求的数据 */
	private void reciveData() {

		Bundle bundle = getFragmentManager().findFragmentByTag("grade").getArguments();

		if (bundle != null) {

			mAllPoints = bundle.getParcelableArrayList("pointList");

			mAdapter = new ListViewAdapter(mContext, mAllPoints);

			mListview.setAdapter(mAdapter);

			// 进度
			mProcessBar1.setMAX(mAllPoints.size() - 1);

			// CustomSeekbar与ListView的联动
			if (mAllPoints.size() - 1 == 0) {

				mProcessBar1.setProgressBypositon(1);

				hasNext = false;

				mAdapter.setGradePointBeanIndex(0);

				mAdapter.notifyDataSetChanged();

				// ListView 数据改变时，默认滑动到顶部
				mListview.setSelection(0);

				mTextPoint.setText("当前考核点：" + mAllPoints.get(0).getSort());

				mTextTerm.setText(mAllPoints.get(0).getContent());

				mTextPrePoint.setText("考核已开始");

				mTextNextPoint.setText("考核已完成");

			} else {

				UpdataUi(0);
			}

			onClickEvent();

			popWindow = new CustomPopupWindow((GradeActivity) mContext, mAllPoints, mCurrentPoint);
		}
	}

	/**
	 * 更新界面
	 * 
	 * @param position
	 *            当前展示的考点在所有考点的索引
	 */
	private void UpdataUi(int position) {

		mAdapter.setGradePointBeanIndex(position);

		mProcessBar1.setProgressBypositon(position);

		mAdapter.notifyDataSetChanged();

		// ListView 数据改变时，默认滑动到顶部
		mListview.setSelection(0);

		mTextPoint.setText("当前考核点：" + mAllPoints.get(position).getSort());

		mTextTerm.setText(mAllPoints.get(position).getContent());

		if (!hasNext) {

			mTextPrePoint.setText("考核已开始");

			mTextNextPoint.setText("考核已完成");

			return;
		}

		if (position == 0) {

			mTextPrePoint.setText("考核已开始");

			mTextNextPoint.setText("下一考核点");

		} else if (position < mAllPoints.size() - 1 && position > 0) {

			mTextPrePoint.setText("上一考核点");

			mTextNextPoint.setText("下一考核点");

		} else if (position == mAllPoints.size() - 1) {

			mTextPrePoint.setText("上一考核点");

			mTextNextPoint.setText("考核已完成");

		}

	}

	// 点击事件
	private void onClickEvent() {

		mTextPrePoint.setOnClickListener(this);

		mTextNextPoint.setOnClickListener(this);

		mTextOperate.setOnClickListener(this);

		mProcessBar1.setOnSeekBarChangeListener(new SeekView.OnSeekBarChangeListener() {
			@Override
			public void onProgressBefore() {

				isScreen = true;
			}

			@Override
			public void onProgressAfter(int i) {

				isScreen = false;

				mCurrentPoint = i;

				UpdataUi(mCurrentPoint);
			}

			@Override
			public void onProgressChanged(SeekView seekView, double progressLow) {

				mProcessBar1.defaultScreenLow = progressLow;
			}
		});

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

			popWindow.showPopupWindow(mTextPrePoint);
			// 文件上传
			File file = new File(savePath);
			doUpload(file, mAllPoints.get(mCurrentPoint).getId(), Utils.getSharedPrefrences(mContext, "student_id"),
					Utils.getSharedPrefrences(mContext, "station_id"));

		} else if (resultCode == Activity.RESULT_CANCELED) {
			// 调用相机球取消后返回Fragment的逻辑处理
			if (judgePopWindowStillShow()) {
				popWindow.showPopupWindow(mTextPrePoint);
			}
		}

	}

	/** 判断启动相机返回后是否仍然显示Popwindow */
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

	/**
	 * 初始化评分fragment上的控件
	 * 
	 * @param view
	 */
	private void findWidget(View view) {

		mManager = getFragmentManager();

		// 考试的进度条显示
		mProcessBar1 = (SeekView) view.findViewById(R.id.seekBar_tg2);
		// 考点注意事项
		mTextTerm = (TextView) view.findViewById(R.id.testView_testPointDetail);
		// 考点序号
		mTextPoint = (TextView) view.findViewById(R.id.textView_testPoint);
		// 上
		mTextPrePoint = (TextView) view.findViewById(R.id.textView_prePoint);
		// 下
		mTextNextPoint = (TextView) view.findViewById(R.id.textView_nextPoint);
		// 操作
		mTextOperate = (TextView) view.findViewById(R.id.textView_operate);
		// 显示相关考点信息
		mListview = (ListView) view.findViewById(R.id.listView_testStep);
		Log.e("******", "mListview=" + mListview);
		// 上传图片的返回
		mUploadReturnList = new ArrayList<Integer>();

	}

	/** 点击事件 */
	@Override
	public void onClick(View v) {

		switch (v.getId()) {

		case R.id.textView_prePoint:// 点击上一考核点

			if (mCurrentPoint == 0) {

			} else {
				mCurrentPoint = mCurrentPoint - 1;
				UpdataUi(mCurrentPoint);
				mProcessBar1.setProgressBypositon(mCurrentPoint);
			}

			break;
		case R.id.textView_nextPoint:// 点击下一考核点

			if (mCurrentPoint == mAllPoints.size() - 1) {

				mAnticlockwise.onPause();// 停止计时

				if (mIsCanEnd) {
					mIsCanEnd = false;
					changeTestStatu();// 改变考生状态
				} else {

				}

				// 上传时间描点
				if (getUploadTimeData().size() > 0) {

					uploadTimesRequest(getUploadTimeData());// 上传时间描点
				}

			} else if (hasNext) {
				mCurrentPoint = mCurrentPoint + 1;
				mProcessBar1.setProgressBypositon(mCurrentPoint);
				UpdataUi(mCurrentPoint);

			}

			break;
		case R.id.textView_operate:// 点击操作

			popWindow.showPopupWindow(mTextPrePoint);

			break;

		default:
			break;
		}
	}

	// 考试超时提示框
	private void showTimeUseOutDialog() {
		if (mTipsDialog == null) {
			mTipsDialog = new SweetAlertDialog(mContext, SweetAlertDialog.WARNING_TYPE,false);
		}
		mTipsDialog.setTitleText("考试已结束！");
		mTipsDialog.setContentText("考试时间已用完！");
		mTipsDialog.setCanceledOnTouchOutside(false);
		mTipsDialog.setCancelable(false);
		mTipsDialog.setConfirmClickListener(new OnSweetClickListener() {
			@Override
			public void onClick(SweetAlertDialog sweetAlertDialog) {
				changeTestStatu();
				mTipsDialog.dismiss();
			}
		});
		mTipsDialog.show();
	}

	/** 结束当前考生的考试 ,并且切换到评价碎片 */
	public void changeTestStatu() {
		RequestQueue requestQueue = Volley.newRequestQueue(mContext);
		String url = BaseActivity.mSUrl + Constant.CHANGE_STATUS + "?student_id="
				+ Utils.getSharedPrefrences(mContext, "student_id") + "&user_id="
				+ Utils.getSharedPrefrences(mContext, "user_id") + "&station_id="
				+ Utils.getSharedPrefrences(mContext, "station_id");

		Log.i(">>><<<", url + "");

		StringRequest stringRequest = new StringRequest(url, new Response.Listener<String>() {
			@Override
			public void onResponse(String response) {
				mIsCanEnd = true;
				EndExamBean statu = null;

				try {
					statu = new Gson().fromJson(response, EndExamBean.class);
				} catch (Exception e) {
					mIsCanEnd = true;
					Toast.makeText(mContext, "结束考试返回数据有误！", Toast.LENGTH_SHORT).show();
					return;
				}

				if (statu.getCode() == 1) {

					Log.i(TAG, "endTime = " + statu.getData().getEnd_time());
					// Log.i(TAG, "exam_screening_id = " +
					// statu.getData().getExam_screening_id());
					// 相关数据的保存
					Utils.saveSharedPrefrences(mContext, "endTime", statu.getData().getEnd_time());
					// Utils.saveSharedPrefrences(mContext,
					// "exam_screening_id",
					// statu.getData().getExam_screening_id());
					mIsNormalEnd = true;// 正常进入评价碎片
					// 相关数据存储后，改变界面
					Bundle bundle = new Bundle();
					bundle.putParcelableArrayList("uploadScoreList", mAllPoints);
					bundle.putIntegerArrayList("imageArray", mUploadReturnList);
					FragmentEvaluate evaluste = new FragmentEvaluate();
					evaluste.setArguments(bundle);
					mManager.beginTransaction().replace(R.id.frame, evaluste, "evaluste").commit();

				} else {
					Toast.makeText(mContext, "提交成绩失败，请重试" + "\n" + "错误码:" + statu.getCode(), Toast.LENGTH_SHORT).show();
				}
			}
		}, new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				mIsCanEnd = true;
				Log.e(TAG, error.getMessage(), error);
			}
		});
		requestQueue.add(stringRequest);
	}

	public void setListViewVisiable(boolean b) {
		if (b == false) {
			mAdapter.isHideItemPoint();
		} else if (b == true) {
			mAdapter.isShowItemPoint();
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
		// 考点数据保存
		Type pointType = new TypeToken<ArrayList<GradePointBean>>() {
		}.getType();
		Gson gson = new Gson();
		String exceptionString = gson.toJson(mAllPoints, pointType);
		FileUtils.savaDataFromException(context, FileUtils.EXCEPTION_SCORE_FILE, exceptionString);
		// 保存本地操作
		if (popWindow.mAdapter.getDataList() != null && popWindow.mAdapter.getDataList().size() > 0) {
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
	private void begingAnticlockwise(long limit_m, long limit_s) {
		mAnticlockwise = (CustomAnticlockwise) getActivity().findViewById(R.id.textView_Time);
		mAnticlockwise.initTime(limit_m, limit_s);
		mAnticlockwise.reStart();// 启动
		mAnticlockwise.setOnTimeCompleteListener(new CustomAnticlockwise.OnTimeCompleteListener() {
			@Override
			public void onTimeComplete() {
				showTimeUseOutDialog();
			}
		});
	}

	// 异常退出恢复Handler
	@SuppressLint("HandlerLeak")
	private class RecoverPopupHandler extends Handler {
		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			switch (msg.what) {
			case RECOVER_POP:
				new RecoverPopupTask().execute();
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
					return new Gson().fromJson(popData.substring(5, popData.length()),
							new TypeToken<ArrayList<DataBean>>() {
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
