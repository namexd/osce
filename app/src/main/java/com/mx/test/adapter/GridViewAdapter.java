package com.mx.test.adapter;

import java.util.ArrayList;

import com.mx.test.R;
import com.mx.test.bean.CurrentGroupStudentBean;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class GridViewAdapter extends BaseAdapter {

	private Context context;
	private ArrayList<CurrentGroupStudentBean> stuList;

	public GridViewAdapter(Context context, ArrayList<CurrentGroupStudentBean> mStuList) {
		this.context = context;
		this.stuList = mStuList;

	}

	public int getCount() {
		return stuList.size() == 0 ? 0 : stuList.size();
	}

	public Object getItem(int position) {
		return stuList.get(position);
	}

	public long getItemId(int position) {
		return 0;
	}

	public View getView(int position, View convertView, ViewGroup parent) {
		ViewHolder holder;
		if (convertView == null) {
			convertView = LayoutInflater.from(context).inflate(R.layout.gridview_item, null);
			holder = new ViewHolder();
			holder.image_show = (ImageView) convertView.findViewById(R.id.image_photo);
			holder.text_name = (TextView) convertView.findViewById(R.id.textView_testName);
			holder.text_num = (TextView) convertView.findViewById(R.id.textview_testCode);

			convertView.setTag(holder);
		} else {

			holder = (ViewHolder) convertView.getTag();
		}

		// 设置学生姓名
		holder.text_name.setText(stuList.get(position).getStudent_name());
		// 设置学生考号
		holder.text_num.setText(stuList.get(position).getStudent_code());
		// 预设一个图片
		holder.image_show.setImageResource(R.drawable.pic);


		return convertView;
	}

	class ViewHolder {

		public ImageView image_show;

		public TextView text_name, text_num;

	}
}
