package com.mx.osce.adapter;

import java.util.ArrayList;

import com.mx.osce.R;
import com.mx.osce.bean.CurrentGroupStudentBean;
import com.mx.osce.util.CommonTool;

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
		// 设置学生头像图片地址为头像图片Tag
		String httptUrl = stuList.get(position).getStudent_avator();
		// 给 ImageView 设置一个 tag
		holder.image_show.setTag(httptUrl);
		// 预设一个图片
		holder.image_show.setImageResource(R.drawable.pad_pic);

		// 通过 tag 来防止图片错位
		if (holder.image_show.getTag() != null && holder.image_show.getTag().equals(httptUrl)) {
			CommonTool.getBitmapUtils(context).display(holder.image_show, httptUrl);
		}

		return convertView;
	}

	class ViewHolder {

		public ImageView image_show;

		public TextView text_name, text_num;

	}
}
