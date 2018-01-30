package com.mx.test.custom;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;

import com.mx.test.R;
import com.mx.test.adapter.PopAdapter;
import com.mx.test.audio.AudioRecorderButton;
import com.mx.test.audio.AudioRecorderButton.AudioFinishRecorderListenter;
import com.mx.test.bean.DataBean;
import com.mx.test.bean.GradePointBean_Net;
import com.mx.test.upload.UploadUtils;
import com.mx.test.util.Constant;
import com.mx.test.util.Utils;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.ColorDrawable;
import android.net.Uri;
import android.os.Environment;
import android.provider.MediaStore;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.PopupWindow;
import android.widget.TextView;

public class CustomPopupWindow extends PopupWindow {

	public static final Uri CAMERA_URI = Uri.fromFile(Environment.getExternalStorageDirectory()).buildUpon()
			.appendPath("image.jpg").build();

	// popupwindow的视图
	private View conentView;

	// popupWindow上的控件
	private LinearLayout mLinearCamera, mLinearMic, mLinearMark;

	private ImageView mImageCamera, mImageMic, mImageMark;

	private TextView mTextCamera, mTextMic, mTextMark;

	// private PopAdapter mAdapter;
	private com.mx.test.audio.AudioRecorderButton mAudioButton;

	private String currentTimeString = "";

	public PopAdapter mAdapter;

	private ArrayList<DataBean> mDataList;

	private ListView mListView;

	private Context mContext;

	private ArrayList<GradePointBean_Net> mAllPoints;

	private int mIndex;

	public CustomPopupWindow(final Activity context, ArrayList<GradePointBean_Net> allPoints, int indexOfAllPoints) {
		mContext = context;
		mAllPoints = new ArrayList<GradePointBean_Net>();
		mAllPoints.addAll(allPoints);
		mIndex = indexOfAllPoints;

		LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		conentView = inflater.inflate(R.layout.popupwindow, null);
		mDataList = new ArrayList<DataBean>();// pop数据源
		int h = context.getWindowManager().getDefaultDisplay().getHeight();
		int w = context.getWindowManager().getDefaultDisplay().getWidth();
		// 设置SelectPicPopupWindow的View
		this.setContentView(conentView);
		// 设置SelectPicPopupWindow弹出窗体的宽
		this.setWidth(w / 3);
		// 设置SelectPicPopupWindow弹出窗体的高
		this.setHeight(LayoutParams.MATCH_PARENT);
		// 设置SelectPicPopupWindow弹出窗体可点击
		this.setFocusable(true);
		this.setOutsideTouchable(true);
		// 刷新状态
		this.update();
		// 实例化一个ColorDrawable颜色为半透明
		ColorDrawable dw = new ColorDrawable(0000000000);
		// 点back键和其他地方使其消失,设置了这个才能触发OnDismisslistener ，设置其他控件变化等操作
		this.setBackgroundDrawable(dw);
		// 设置SelectPicPopupWindow弹出窗体动画效果
		this.setAnimationStyle(android.R.style.Animation_Dialog);

		findPopupWindowView(conentView);

		// 点击相机，调用系统相机
		// mImageCamera.setOnClickListener(new OnClickListener() {

		mLinearCamera.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				mAudioButton.setVisibility(View.GONE);
				// 指定调用相机拍照后照片的存储路径
				Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE).putExtra(MediaStore.EXTRA_OUTPUT,
						CAMERA_URI);

				context.startActivityForResult(intent, Constant.CAMERA_REQUEST);

			}
		});

		// 点击录音
		mLinearMic.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				mAudioButton.setVisibility(View.VISIBLE);
			}
		});

		// 点击描点
		mLinearMark.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				mAudioButton.setVisibility(View.GONE);
				DataBean dataBean = new DataBean(DataBean.TIMEPOINT);
				// 描点时间:服务器时间+客户端登陆时间-客户端描点时间

				if (Utils.getSharedPrefrences(mContext, "service_time") != null) {
					dataBean.setTimePoint(Long.parseLong(Utils.getSharedPrefrences(mContext, "service_time"))
							+ System.currentTimeMillis()
							- Long.parseLong(Utils.getSharedPrefrences(mContext, "client_time")));
				} else {
					dataBean.setTimePoint(System.currentTimeMillis());
				}

				mAdapter.getDataList().add(dataBean);
				mAdapter.notifyDataSetChanged();

			}
		});

		mAudioButton.setAudioFinishRecorderListenter(new AudioFinishRecorderListenter() {

			@Override
			public void onFinish(float seconds, String FilePath) {
				// TODO Auto-generated method stub

				currentTimeString = System.currentTimeMillis() + "";

				DataBean dataBean = new DataBean(DataBean.AUDIO);
				dataBean.setTime(seconds);
				dataBean.setFilePath(FilePath);
				dataBean.setmCurrentTime(currentTimeString);
				mAdapter.getDataList().add(dataBean);
				mAdapter.notifyDataSetChanged();

				HashMap<String, String> params = new HashMap<String, String>();
				params.put("student_id", Utils.getSharedPrefrences(context, "student_id"));
				params.put("station_id", Utils.getSharedPrefrences(context, "station_id"));
				params.put("standard_id", mAllPoints.get(mIndex).getId());
				Log.i("Upload Audio ID", mAllPoints.get(mIndex).getId());
				// 测试
				// params.put("student_id", "29");
				// params.put("station_id", "3");
				// params.put("standard_id", "10");
				// Log.i("audio file path", dataBean.getFilePath());
				UploadUtils.doUpload2(Constant.UPLOAD_AUDIO, "radio", params, new File(dataBean.getFilePath()));
				Log.i("***Upload_Audio***", "***");

			}
		});

	}

	/**
	 * 找到poouoWindow的子控件
	 * 
	 * @param view
	 *            popoupWindow的视图
	 */
	private void findPopupWindowView(View view) {

		// 相机
		mLinearCamera = (LinearLayout) view.findViewById(R.id.linear_camera);
		// 录音
		mLinearMic = (LinearLayout) view.findViewById(R.id.linear_mic);
		// 描点
		mLinearMark = (LinearLayout) view.findViewById(R.id.linear_mark);
		//
		mImageCamera = (ImageView) view.findViewById(R.id.imageView_camera);
		mImageMic = (ImageView) view.findViewById(R.id.imageView_mic);
		mImageMark = (ImageView) view.findViewById(R.id.imageView_mark);
		mTextCamera = (TextView) view.findViewById(R.id.testView_camera);
		mTextMic = (TextView) view.findViewById(R.id.textView_mic);
		mTextMark = (TextView) view.findViewById(R.id.textView_mark);
		mListView = (ListView) view.findViewById(R.id.poplist);
		mAudioButton = (AudioRecorderButton) view.findViewById(R.id.recorderButton);
		// 多条目ListView
		// 数据过多会造成ANR
		// if (!ControlException.checkGradeIsNormal(mContext)) {
		// String popData = FileUtils.recoveryDataToActivity(mContext,
		// FileUtils.EXCEPTION_OPERTE_FILE);
		// if (popData == null) {
		// return;
		// } else {
		// mDataList = new Gson().fromJson(popData.substring(5,
		// popData.length()),
		// new TypeToken<ArrayList<DataBean>>() {
		// }.getType());
		// }
		// }
		mAdapter = new PopAdapter(mContext, mDataList, mAllPoints);

		mListView.setAdapter(mAdapter);

	}

	public void showPopupWindow(View parent) {
		if (!this.isShowing()) {

			this.showAsDropDown(parent, -32, 16);
		} else {
			this.dismiss();
		}
	}
}
