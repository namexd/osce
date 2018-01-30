package com.mx.test;

import java.util.ArrayList;

import com.mx.test.adapter.GridViewAdapter;
import com.mx.test.bean.CurrentGroupStudentBean;
import com.mx.test.fragment.FragmentDrawSuccess;
import com.mx.test.fragment.FragmentReady;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.FragmentManager;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.GridView;
import android.widget.TextView;
import android.widget.Toast;

/** 抽签，开始考试 */
public class MainActivity extends Activity{


	// 当前考站
	private TextView mTextTitleStationName;

	// 当前时间
	private TextView mTextTitleTime;

//	private TextView mTextVideo;

	private TextView mTextCancel;

	// 下一组
	private TextView mNextGroup;

	// 刷新
	private TextView mRefreshStudent, mRefreshCurrent;

	// 当前小组考生信息
	private GridView mCurrentGroup;

	// grid适配器
	private GridViewAdapter mAdapter;

	// grid数据源
	private ArrayList<CurrentGroupStudentBean> mStuList;

	private FragmentManager mFgManager;

	// 准备抽签碎片
	private FragmentReady mDrawReady;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_main);

		this.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

		findWidget();
	}



	/** 初始化视图控件 */
	private void findWidget() {
		// 添加初始碎片
		mDrawReady = new FragmentReady();

		mFgManager = getFragmentManager();

		if (mFgManager.findFragmentById(R.id.fragment_draw) == null) {

			mFgManager.beginTransaction().add(R.id.fragment_draw, mDrawReady, "wait").commit();
		} else {
			mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawReady, "wait").commit();
		}
		// 站名
		mTextTitleStationName = (TextView) findViewById(R.id.textView_testStation);
		mTextTitleStationName.setText("第一考场");

		// 限制时间
		mTextTitleTime = (TextView) findViewById(R.id.textView_testTime);
		mTextTitleTime.setText("限时:5:00分钟");

		// 实时视频
//		mTextVideo = (TextView) findViewById(R.id.tv_vdieo);
//		mTextVideo.setVisibility(View.VISIBLE);
//		mTextVideo.setOnClickListener(new OnClickListener() {
//
//			@Override
//			public void onClick(View v) {
//
//				startActivity(new Intent(MainActivity.this, VideoActivity.class));
//			}
//		});

		// 初始化隐藏我的考生刷新按钮
		mRefreshStudent = (TextView) findViewById(R.id.refrushStudent);
		mRefreshStudent.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				Toast.makeText(MainActivity.this,"正在刷新，请等待...",Toast.LENGTH_SHORT).show();
				FragmentManager manager = mFgManager;
				FragmentDrawSuccess drawSuccess = new FragmentDrawSuccess();
				Bundle bundle = new Bundle();
				bundle.putString("name",getIntent().getStringExtra("studentname"));
				bundle.putString("code",getIntent().getStringExtra("studentcode"));
				bundle.putString("sfz","320689199501189089");
				bundle.putString("zkz","3123456");
				drawSuccess.setArguments(bundle);
				manager.beginTransaction().replace(R.id.fragment_draw, drawSuccess).commit();
			}

		});
//		mRefreshStudent.setVisibility(View.GONE);

		// 刷新当前小组
		mRefreshCurrent = (TextView) findViewById(R.id.refrushCourrentGroup);
		mRefreshCurrent.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
					Toast.makeText(MainActivity.this,"正在刷新，请等待...",Toast.LENGTH_SHORT).show();
			}
		});

		// 下一组
		mNextGroup = (TextView) findViewById(R.id.texiView_nextGroup);

		// 当前组
		mStuList = new ArrayList<CurrentGroupStudentBean>();

		CurrentGroupStudentBean student = new CurrentGroupStudentBean();
		student.setStudent_name(getIntent().getStringExtra("studentname"));
		student.setStudent_code(getIntent().getStringExtra("studentcode"));
		mStuList.add(student);
		mCurrentGroup = (GridView) findViewById(R.id.gridView_currentGroup);
		mAdapter = new GridViewAdapter(MainActivity.this, mStuList);
		mCurrentGroup.setAdapter(mAdapter);

		mNextGroup.setText("下一组考生:" + getIntent().getStringExtra("nextstudent"));


		// 去除黄色背景
		mCurrentGroup.setSelector(new ColorDrawable(Color.TRANSPARENT));

//		mCurrentGroup.setOnItemLongClickListener(new AdapterView.OnItemLongClickListener() {
//
//			@Override
//			public boolean onItemLongClick(AdapterView<?> arg0, View arg1, final int arg2, long arg3) {
//				// TODO Auto-generated method stub
//				new AlertDialog.Builder(MainActivity.this).
//						setTitle("请选择").
//						setItems(new String[]{"弃考","排到最后"}, new DialogInterface.OnClickListener() {
//							@Override
//							public void onClick(DialogInterface dialog, int which) {
//								switch (which){
//									case 0:
//										//弃考
//										mStuList.remove(arg2);
//										mAdapter.notifyDataSetChanged();
//										CurrentGroupStudentBean student = new CurrentGroupStudentBean();
//										student.setStudent_name(students[1]);
//										student.setStudent_code(students1[1]);
//										mStuList.add(student);
//										mCurrentGroup = (GridView) findViewById(R.id.gridView_currentGroup);
//										mAdapter = new GridViewAdapter(MainActivity.this, mStuList);
//										mCurrentGroup.setAdapter(mAdapter);
//										Toast.makeText(MainActivity.this,"已做弃考处理",Toast.LENGTH_SHORT).show();
//										mNextGroup.setText("下一组考生:" + students[2]);
//										break;
//									case 1:
//										//拍到最后
//										CurrentGroupStudentBean student1 = new CurrentGroupStudentBean();
//										student1.setStudent_name(students[1]);
//										student1.setStudent_code(students1[1]);
//										mStuList.add(student1);
//										mCurrentGroup = (GridView) findViewById(R.id.gridView_currentGroup);
//										mAdapter = new GridViewAdapter(MainActivity.this, mStuList);
//										mCurrentGroup.setAdapter(mAdapter);
//										Toast.makeText(MainActivity.this,"已安排到最后",Toast.LENGTH_SHORT).show();
//										mNextGroup.setText("下一组考生:" + students[2]);
//										break;
//								}
//							}
//						}).show();
//				return false;
//			}
//		});
		// 注销
		mTextCancel = (TextView)findViewById(R.id.tv_cancel);
		mTextCancel.setVisibility(View.VISIBLE);
		mTextCancel.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {
				// TODO Auto-generated method stub
				new AlertDialog.Builder(MainActivity.this).setTitle("确认注销？").setIcon(
						android.R.drawable.stat_sys_warning).setPositiveButton("确定", new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						Toast.makeText(MainActivity.this,"注销成功",Toast.LENGTH_SHORT).show();
						startActivity(new Intent(MainActivity.this,LoginActivity.class));
						finish();
					}
				})
						.setNegativeButton("取消", null).show();

			}
		});
	}

}
