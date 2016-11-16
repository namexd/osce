//package com.mx.osce;
//
//import android.app.Activity;
//import android.os.Bundle;
//import android.support.v7.widget.DefaultItemAnimator;
//import android.support.v7.widget.GridLayoutManager;
//import android.support.v7.widget.RecyclerView;
//import android.support.v7.widget.RecyclerView.LayoutManager;
//import android.view.View;
//import android.view.ViewGroup;
//import android.widget.BaseAdapter;
//import android.widget.LinearLayout;
//
//public class RecyclerViewActivity extends Activity {//替代ListView/GradView
//
//	private RecyclerView mRecycler;
//	private RecyclerViewAdapter mRecylerAdapter;
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		// TODO Auto-generated method stub
//		super.onCreate(savedInstanceState);
//		setContentView(R.layout.recycler_activity);
//		mRecycler = (RecyclerView) findViewById(R.id.recylerPoint);
//		// 设置布局管理器
//		mRecycler.setLayoutManager(new LinearLayout(RecyclerViewActivity.this));
//		// 设置adapter
//		mRecycler.setAdapter(mRecylerAdapter);
//		// 设置Item增加、移除动画
//		mRecycler.setItemAnimator(new DefaultItemAnimator());
//		// 添加分割线
//		mRecycler.addItemDecoration(
//				new DividerItemDecoration(RecyclerViewActivity.this, DividerItemDecoration.HORIZONTAL_LIST));
//	}
//
//	private class RecyclerViewAdapter extends BaseAdapter {
//
//		@Override
//		public int getCount() {
//			// TODO Auto-generated method stub
//			return 0;
//		}
//
//		@Override
//		public Object getItem(int position) {s
//			// TODO Auto-generated method stub
//			return null;
//		}
//
//		@Override
//		public long getItemId(int position) {
//			// TODO Auto-generated method stub
//			return 0;
//		}
//
//		@Override
//		public View getView(int position, View convertView, ViewGroup parent) {
//			// TODO Auto-generated method stub
//			return null;
//		}
//
//	}
//
//}
