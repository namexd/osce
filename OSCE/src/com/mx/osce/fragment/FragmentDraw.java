package com.mx.osce.fragment;

import java.util.ArrayList;

import com.mx.osce.R;
import com.mx.osce.adapter.GridViewAdapter;
import com.mx.osce.bean.CurrentGroupStudentBean;
import com.mx.osce.json.StudentDataJson;
import com.mx.osce.material.CircleProgressBar;
import com.mx.osce.util.Constant;
import com.mx.osce.util.Utils;

import android.app.Fragment;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.ViewGroup.LayoutParams;
import android.widget.FrameLayout;
import android.widget.GridView;
import android.widget.RelativeLayout;
import android.widget.TextView;

public class FragmentDraw extends Fragment {
	private onReaderSuccessListener mReaderLitener;
	// 下一组
	private TextView mNextGroup;
	// 当前小组考生信息
	private GridView mCurrentGroup;
	// grid适配器
	private GridViewAdapter mAdapter;
	// grid数据源
	private ArrayList<CurrentGroupStudentBean> mStuList;
	// 抽签成功界面
	private FrameLayout mDrawSuccess;
	// 等待抽签界面
	private FrameLayout mDrawWait;
	private Context mContext;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		View view = inflater.inflate(R.layout.fragment_draw, null);
		mContext = getActivity();
		findWidget(view);
		return view;
	}

	// 设置监听器
	public void setOnReaderDrawSuccessListener(onReaderSuccessListener listener) {
		mReaderLitener = listener;
	}

	// 初始化控件
	private void findWidget(View view) {
		mNextGroup = (TextView) view.findViewById(R.id.texiView_nextGroup);
		mCurrentGroup = (GridView) view.findViewById(R.id.gridView_currentGroup);
		mStuList = new ArrayList<CurrentGroupStudentBean>();
		mDrawSuccess = (FrameLayout) view.findViewById(R.id.fragment_drawSuccess);
		mDrawWait = (FrameLayout) view.findViewById(R.id.fragment_drawWait);
		mNextGroup.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				CircleProgressBar bar = new CircleProgressBar(mContext);
				bar.setCircleBackgroundEnabled(false);
				bar.setShowArrow(true);
				bar.setVisibility(View.VISIBLE);
			}
		});
	}

	// 初始化界面
	private void initView() {
		// Intent intent = getActivity().getIntent();
		mAdapter = new GridViewAdapter(getActivity(), mStuList);
		mCurrentGroup.setAdapter(mAdapter);
	}

	// 加载默认数据
	private ArrayList<CurrentGroupStudentBean> addDefaultData() {

		ArrayList<CurrentGroupStudentBean> stuList = new ArrayList<CurrentGroupStudentBean>();

		CurrentGroupStudentBean bean1 = new CurrentGroupStudentBean();
		bean1.setStudent_name("等待确认");
		bean1.setStudent_code("1");
		stuList.add(bean1);

		CurrentGroupStudentBean bean2 = new CurrentGroupStudentBean();
		bean2.setStudent_name("等待确认");
		bean2.setStudent_code("2");
		stuList.add(bean2);

		CurrentGroupStudentBean bean3 = new CurrentGroupStudentBean();
		bean3.setStudent_name("等待确认");
		bean3.setStudent_code("3");
		stuList.add(bean3);

		CurrentGroupStudentBean bean4 = new CurrentGroupStudentBean();
		bean4.setStudent_name("等待确认");
		bean4.setStudent_code("4");
		stuList.add(bean4);

		return stuList;

	}

	class StudentInfoTask extends AsyncTask<String, Void, ArrayList<CurrentGroupStudentBean>> {

		protected ArrayList<CurrentGroupStudentBean> doInBackground(String... arg0) {
			return StudentDataJson.studentJson(Utils.getHtmlString(arg0[0]));
		}

		protected void onPostExecute(ArrayList<CurrentGroupStudentBean> result) {
			super.onPostExecute(result);
			if (result.size() != 0) {
				mStuList.addAll(result);
				mAdapter.notifyDataSetChanged();
			} else {
				mStuList.addAll(addDefaultData());
				mAdapter.notifyDataSetChanged();
			}
		}
	}

	interface onReaderSuccessListener {
		void onReaderSuccess();
	}
}
