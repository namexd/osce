package com.mx.osce.adapter;

import java.util.ArrayList;

import com.mx.osce.R;
import com.mx.osce.bean.GradePointBean_Net;
import com.mx.osce.util.Constant;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class MenuListAdapter extends BaseAdapter {
	private ArrayList<GradePointBean_Net> inData;
	private Context mContext;
	private int mSeleted = -1;

	public MenuListAdapter(Context context, ArrayList<GradePointBean_Net> data) {
		this.mContext = context;
		this.inData = data;
	}

	@Override
	public int getCount() {
		if (null != inData && inData.size() > 0) {
			return inData.size();
		}
		return 0;
	}

	@Override
	public GradePointBean_Net getItem(int position) {
		if (null != inData && inData.size() > 0) {
			return inData.get(position);
		}
		return null;
	}

	@Override
	public long getItemId(int position) {
		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		TextView mMenuNMTxt = null;
		ImageView imageView1 = null;
		if (null == convertView) {
			convertView = LayoutInflater.from(mContext).inflate(R.layout.list_menu, null);

		}
		mMenuNMTxt = (TextView) convertView.findViewById(R.id.txt_name);
		imageView1 = (ImageView) convertView.findViewById(R.id.imageView1);
		GradePointBean_Net item = getItem(position);

		if (null != item) {

			if (item.getTag().equalsIgnoreCase(Constant.NORMAL_TAG)) {
				mMenuNMTxt.setText(item.getContent());
			} else if (item.getTag().equalsIgnoreCase(Constant.SPECIAL_TAG)) {
				mMenuNMTxt.setText("特殊考点");
			}
		}
		if (mSeleted > -1 && mSeleted == position) {
			imageView1.setImageDrawable(mContext.getResources().getDrawable(R.drawable.menu_selected));
			mMenuNMTxt.setTextColor(mContext.getResources().getColor(R.color.menu_seld_color));
		} else {
			imageView1.setImageDrawable(mContext.getResources().getDrawable(R.drawable.timeline_point));
			mMenuNMTxt.setTextColor(mContext.getResources().getColor(android.R.color.black));
		}

		return convertView;
	}

	public void setSelect(int position) {
		this.mSeleted = position;
		this.notifyDataSetChanged();
	}

	public int getSelected() {
		return this.mSeleted;
	}

}
